<?PHP

		function parseXML($sxml, $tag)
		{
			$arTable=array();
			
				foreach($sxml->children() as $element)
		  	{
		  		if ( $element->getName() == $tag )
		  		{
		  				$objet['id']	    = intval  ( $element['id']      );
		  				$objet['name']	  = strval  ( $element['name']    );
							$objet['country'] = strval  ( $element['country'] );
							$objet['lat']     = floatval( $element['lat']     );
							$objet['lon']     = floatval( $element['lon']     );
							$objet['pop']     = intval  ( $element['pop']     );
							$objet['lvl']     = intval  ( $element['lvl']     );
							$objet['tz']      = intval  ( $element['tz']      );
							
							$arTable[ $objet['name'] ] = $objet;
		  		}
		      parseXML($element, $tag);
		  	}
		  	
		  	return($arTable);
		 }



		require_once("simple_html_dom.php");
		require_once("cm_api.inc.php");
		require_once("sql.php");

		if(isset($_POST['debug']))
				$debug = true;
		else
				$debug = false;

		if(isset($_POST['user']))
				$user = $_POST['user'];
		else
		{
				echo "Erreur : Le 'user' est requis";
				exit;
		}
		if(isset($_POST['pass']))
				$pass = $_POST['pass'];
		else
		{
				echo "Erreur : Le 'pass Api' est requis";
				exit;
		}
		
		if(isset($_POST['data']))
				$data = $_POST['data'];
		else
		{
				echo "Erreur : aucune donnée reçue";
				exit;
		}
		
		$xml = readXML( "http://www.croquemonster.com/api/agency.xml?name=".urlencode($user)."&pass=".$pass );
		if( substr($xml, 0, 6)== "Erreur" )
		{
				echo $xml;
				exit;
		}
		else
		{
				if ($debug)
						echo "Vérification de l'utilisateur Ok !<br>\n";	
		}

		$xml = readXML( "http://www.croquemonster.com/xml/cities.xml" );
		if( substr($xml, 0, 6)== "Erreur" )
		{
				echo $xml;
				exit;
		}
		else
		{
				if ($debug)
						echo "Lecture des villes Ok !<br>\n";	
		}

		
		$arVilles = parseXML($xml, 'city');
		
		$data = urldecode($data);
		$data = stripslashes($data);
		// Create a DOM object
		$html = new simple_html_dom();

		// Load HTML from a string
  	$html->load($data);
		
		$port_ville = trim(substr($html->find('div.pcity',0)->plaintext, strlen(" Ville :") ));
		$port_pays  = trim(substr($html->find('div.pcountry',0)->plaintext, strlen("Pays :") ));
		$port_lvl   = substr($html->find('div.plvl',0)->plaintext, 0, 1);
		$port_def   = substr($html->find('div.pdef',0)->plaintext, 0, 1);
		$lignes=$html->find('tr');
		
		if ( !connect() )
		{
				echo "Erreur : Problèmes d'accès à la base de donnée, merci de réessayer plus tard.";
				exit;
		}
		else
		{
				if ($debug)
						echo "Connexion à la base de données Ok !<br>\n";	
		}

		
	  $ret = insert("update  cm_updates set upd_inferno=NOW() where login='".$user."'");
		if ( $ret	== 0 )
		{
				insert("insert into cm_updates (login, upd_inferno) values ('".$user."', NOW())");
		}
		
		foreach ($lignes as $line)
		{
				$ville = trim($line->find('th', 0)->plaintext);
				$reputation = str_replace(".", "", $line->find('span.reputation',0)->plaintext) ;
				$inferno = trim(substr($line->find('div.xpNb',0)->plaintext, 0, -1));
		
				if ( !isset( $arVilles[$ville] ) )
				{
						echo "$ville : ville inconnue<BR/>\n";
						continue;
				}
		
				if($ville == $port_ville)
						$acces_direct="OUI";
				else
						$acces_direct="NON";
				
				$acces_direct=($ville == $port_ville)?"Direct":$port_ville;
				$pays=($ville == $port_ville)?$port_pays:$arVilles[$ville]['country'];
				$def=($ville == $port_ville)?$port_def:"-";
				$lvl=($ville == $port_ville)?$port_lvl:"-";
				
				
				$stmt="UPDATE cm_portails SET "
						 ." reputation = '".$reputation."',"
						 ." inferno = '".$inferno."',"
             ." acces_via = '".$acces_direct."',"
             ." defense = '".$def."',"
             ." niveau = '".$lvl."',"
             ." last_maj=NOW() "
             ." WHERE cm_portails.login = '".$user."' "
             ."   AND cm_portails.ville = '".$ville."'"
             ."   AND cm_portails.pays = '".$pays."' ";	
				if ($debug)
						echo $stmt;		
				$ret = insert($stmt);
				if ($debug)
						echo "&nbsp; => $ret<br/>\n";		
				$sortie = "<tr><th>".$ville."</th><td><span class=\"reputation\">".$reputation."</span></td><td></td><td></td><td><div class=\"xpBox\"><div class=\"xpBorder\"><div class=\"xpBar\" style=\"width: ".$inferno."%;\"></div><div class=\"xpNb\">".$inferno."%</div></div></div></td><td>&nbsp;</td>";
				

				if ( $ret	== 0 )
				{
						$stmt = "INSERT INTO cm_portails (login, ville, pays, reputation, inferno, acces_via, defense, niveau, last_maj)"
                   ." VALUES ('".$user."', '".$ville."', '".$pays."', '".$reputation."', '".$inferno."', '".$acces_direct."', '".$def."', '".$lvl."', NOW() )";
						if ($debug)
								echo $stmt;
								
						$ret = insert($stmt);
						if ($debug)
								echo "&nbsp; => $ret<br/>\n";		
								
						if( $ret != 0 )
							  $sortie = $sortie."<td><span style=\"color:blue;\">New !</span></td></tr>";
						else
							  $sortie = $sortie."<td><span style=\"color:red;\">KO</span></td></tr>";
				}
				else
						$sortie = $sortie."<td><span style=\"color:green;\">MàJ !</span></td></tr>";
				
				echo $sortie;
		}
		
?>