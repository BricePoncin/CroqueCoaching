<?PHP
		require_once("sql.php");
		include_once('maj_stats_w5.inc.php');


function standard_deviation($aValues, $bSample = false)
{
    $fMean = array_sum($aValues) / count($aValues);
    $fVariance = 0.0;
    foreach ($aValues as $i)
    {
        $fVariance += pow($i - $fMean, 2);
    }
    $fVariance /= ( $bSample ? count($aValues) - 1 : count($aValues) );
    return (float) sqrt($fVariance);
}

function villes_a_point($sxml)
{
	  //Acc&egrave;s aux enfants de l'&eacute;l&eacute;ment courant
	  foreach($sxml->children() as $element)
	  {
	  		if ( $element->getName() == "c" )
	  		{
	  				$city['id']=intval($element['id']);
						
						$arCities[ intval($element['id']) ] = $city;
	  		}
	      villes_a_point($element);
	  }
		return ($arCities);
}

function compte_ville_seuil ($aValues, $nSeuil)
{
    $compte = 0;
    foreach ($aValues as $i)
    {
        $compte += ($i >= $nSeuil);
    }
    return ($compte);
}


function agences($sxml)
{
  //Acc&egrave;s aux enfants de l'&eacute;l&eacute;ment courant
  foreach($sxml->children() as $element)
  {
  		if ( $element->getName() == "agency" )
  		{
  				$agency['id']=intval($element['id']);
					$agency['name']=strval($element['name']);
					$agency['score']=intval($element['score']);
					$agency['reputation']=intval($element['reputation']);
					$agency['level']=intval($element['level']);
					$agency['fartBox']=utf8_decode(strval($element['fartBox']));
					
					$arAgencies[] = $agency;
  		}
      agences($element);
  }
	return ($arAgencies);
}

function parseXML($sxml, $tag)
{
	$arTable=array();
	
		foreach($sxml->children() as $element)
  	{
  		if ( $element->getName() == $tag )
  		{
  				$objet['id']	    = intval  ( $element['id']      );
  				$objet['name']	  = utf8_decode(strval  ( $element['name']    ));
					$objet['country'] = utf8_decode(strval  ( $element['country'] ));
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

function cities($sxml)
{
  //Acc&egrave;s aux enfants de l'&eacute;l&eacute;ment courant
  foreach($sxml->children() as $element)
  {
  		if ( $element->getName() == "city" )
  		{
  				$city['id']=intval($element['id']);
					$city['name']=utf8_decode(strval($element['name']));
					$city['country']=utf8_decode(strval($element['country']));
					$city['reputation']=intval($element['reputation']);
					$city['percent']=intval($element['percent']);
					$city['infernos']=intval($element['infernos']);
					
					$arCities[ $city['name'] ] = $city;
  		}
      cities($element);
  }
	return ($arCities);
}

/****************************************************/ 

		if( isset($_POST['sens_tri']))
				$sens_tri = $_POST['sens_tri'];
		else
				$sens_tri = 'pays';

		if( isset($_POST['ctrl_min']))
				$ctrl_min = $_POST['ctrl_min'];
		else
				$ctrl_min = 3;

		if( isset($_POST['inferno_min']))
				$inferno_min = $_POST['inferno_min'];
		else
				$inferno_min = 50;

/****************************************************/ 

		connect();

		maj_villes_a_point();

		$url = "http://www.croquemonster.com/api/syndicate.xml?id=".$_SESSION['syndic_id'].";user=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xml = readXML( $url );
		if( substr($xml, 0, 6)== "Erreur" )
		{
				echo $xml;
				exit;
		}
		
		$arAgencies = array();
					
		$arAgencies = agences($xml);    
		$lstAgences = array();
		foreach($arAgencies as $agency)
		{
				$lstAgences[] = "'".$agency['name']."'";
		}
		$strAgences = implode(",", $lstAgences);
		
		$stmt  = "DELETE FROM cm_portails ";
		$stmt .= "WHERE syndic_id = ".$_SESSION['syndic_id'];
		$stmt .= "  AND login NOT IN (".$strAgences.")";
		insert($stmt);
		
		$stmt  = "DELETE FROM cm_portails ";
		$stmt .= "WHERE syndic_id = ".$_SESSION['syndic_id'];
		$stmt .= "  AND login COLLATE latin1_general_cs <> '".$_SESSION['name']."'";
		$stmt .= "  AND login COLLATE latin1_general_cs = '".strtolower($_SESSION['name'])."'";
		insert($stmt);
		
		$stmt = " SELECT TIMESTAMPDIFF(MINUTE , upd_guerre5, now( ) ) diff"
					. " FROM cm_updates"
          . " WHERE login = '".$_SESSION['name']."'";

		select($stmt, $arStmtPortails);
		$ret = 999;
		if( (count($arStmtPortails) == 1) && ($arStmtPortails[0]['diff']!= NULL) )
				$ret = $arStmtPortails[0]['diff'];

		if( $ret >= 60 )
		{
				$url = "http://www.croquemonster.com/api/cities.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
				$xmlCities = readXML( $url );
				if( substr($xmlCities, 0, 6)== "Erreur" )
				{
						echo $xmlCities;
						exit;
				}
				$arCities = cities($xmlCities);
				
				foreach($arCities as $city)
				{
						$stmt = "UPDATE cm_portails SET inferno = ".$city['percent']
									. " WHERE syndic_id = ".$_SESSION['syndic_id']
									. "   AND login = '".$_SESSION['name']."'"
									. "   AND ville = '".utf8_encode($city['name'])."'"
									. "   AND pays = '".utf8_encode($city['country'])."'";
						$ret_cod = insert( $stmt ); 
						if( $ret_cod == 0 )
						{
								$stmt = "INSERT INTO cm_portails (syndic_id, login, ville, pays, inferno ) "
									    ."VALUES ('".$_SESSION['syndic_id']."', '".$_SESSION['name']."', '".utf8_encode($city['name'])."', '".utf8_encode($city['country'])."', ".$city['percent'].")";
								insert( $stmt ); 
						}
				}	
		
				$stmt = " UPDATE cm_updates SET upd_guerre5=NOW() WHERE login = '".$_SESSION['name']."'";
				insert(	$stmt );
		}


/****************************************************/ 

		$xml = readXML( "http://www.croquemonster.com/xml/cities.xml" );
		if( substr($xml, 0, 6)== "Erreur" )
		{
				echo $xml;
				exit;
		}
		$arVilles = parseXML($xml, 'city');
		
		
    $url = "http://www.croquemonster.com/syndicate/domination.xml?id=".$_SESSION['syndic_id'];
		$tmp = direct_get( $url, true );
		$data = new SimpleXMLElement($tmp);
		
		$lst_villes = villes_a_point($data);
		foreach( $lst_villes as $ville )
		{
				foreach( $arVilles as $city )
				{
						if( $city['id'] == $ville['id'] )
						{
								$arVillesPoints[] = utf8_encode(strval($city['name']));
						}
				}
		}

		
		
		$stmt = "select login, ville, pays, inferno"
		      . " from cm_portails"
		      . " where syndic_id = ".$_SESSION['syndic_id'] ;
		$ret = select($stmt, $arStmtPortails);
	
		disconnect();	
		
		$arPortails=array();
		foreach($arStmtPortails as $line)
		{
				$arPort['ville']   = utf8_decode($line['ville']);
				$arPort['pays']    = utf8_decode($line['pays']);
				$arPort['inferno'] = $line['inferno'];
				$arPort['login']   = $line['login'];
				
				$arPortails[ $arPort['ville'] ][ $line['login'] ] = $arPort;
				$arLogins[ $line['login'] ] = $line['login'];
		}

		foreach( $arVilles as $ville )
		{
				$arVals = array();
				foreach($arLogins as $login => $lnLogins)
				{
						if( !isset($arPortails[ $ville['name'] ][ $login ]) )
								$arPortails[ $ville['name'] ][ $login ]['inferno'] = -1;
						else
								$arVals[] = $arPortails[ $ville['name'] ][ $login ]['inferno'];
				
						$arLoginVilles[ $login ][ $ville['name'] ] = $arPortails[ $ville['name'] ][ $login ]['inferno'];
				}
				
				if( count($arVals) >= $ctrl_min )
					$arPortails[ $ville['name'] ]['affich']=1;
				else
					$arPortails[ $ville['name'] ]['affich']=0;
				
				if( (count($arVals) > 0) && ( compte_ville_seuil ($arVals, $inferno_min) < $ctrl_min ) )
					$arPortails[ $ville['name'] ]['affich']=0;
				
				$arVilles[$ville['name']] = $ville['name'];
				$arPays[$ville['name']] = $ville['country'];

/*		
				if( count($arVals) > 0 )
				{
						$arPortails[ $ville['name'] ][ $login ]['inferno'] = number_format(standard_deviation( $arVals ), 2, ".", " ");
						$arLoginVilles[ 'ecart_type' ][ $ville['name'] ]=number_format(standard_deviation( $arVals ), 2, ".", " ");
				}
				else
				{
						$arPortails[ $ville['name'] ][ $login ]['inferno'] = 999;
						$arLoginVilles[ 'ecart_type' ][ $ville['name'] ]= 999;
				}
				*/
				if( count($arVals) == 0 )
				{
						$arPortails[ $ville['name'] ][ $login ]['inferno'] = 999;
						$arLoginVilles[ 'ecart_type' ][ $ville['name'] ]= 999;
				}
		}
		
		foreach($arPortails as $key => $value)
		{
				if( in_array($key, $arVilles) === false )	
						echo $key."<br/>\n";
		}
		
		
		if( $sens_tri == 'ville')
				array_multisort( $arVilles, SORT_ASC, $arPays, SORT_ASC, $arPortails );
		else if ( $sens_tri == 'pays')
				array_multisort( $arPays, SORT_ASC, $arVilles, SORT_ASC, $arPortails );
		/*
		else if ( $sens_tri == 'ecart_type')
				array_multisort( $arLoginVilles[ $sens_tri ], SORT_ASC, $arPays, SORT_ASC, $arVilles, SORT_ASC, $arPortails );
	  */
		else
				array_multisort( $arLoginVilles[ $sens_tri ], SORT_DESC, $arPays, SORT_ASC, $arVilles, SORT_ASC, $arPortails );

?>

	<form action="#self" method="POST" name="frm_tri">	
	<input type="text" name="ctrl_min" value="<?PHP echo $ctrl_min;?>"> co-syndiqués minimum contrôlent la ville et ont 
	<input type="text" name="inferno_min" value="<?PHP echo $inferno_min;?>">% d'inferno minimum sur la ville.<BR/>
<div style="margin: 10px;" >
<a class="lien_bouton" onclick="document.forms['frm_tri'].submit();">Valider</a><br/>
</div>

		<input type="hidden" id="sens_tri" name="sens_tri" value="<?PHP echo $sens_tri;?>">
<?PHP	
		echo "<table border=\"1\">";
		echo "<tr><th><a onclick=\"document.getElementById('sens_tri').value='ville'; document.forms['frm_tri'].submit();\">Ville</a></th>";
		echo "<th><a onclick=\"document.getElementById('sens_tri').value='pays'; document.forms['frm_tri'].submit();\">Pays</a></th>";
		foreach( $arLogins as $login => $lnLogins )
				echo "<th><a onclick=\"document.getElementById('sens_tri').value='".$login."'; document.forms['frm_tri'].submit();\">".$login."</a></th>";
		//echo "<th>Ecart type</th></tr>";
echo "</form>";

		$idx=0;
		foreach( $arPays as $ville => $pays )
		{
				if( $arPortails[ $ville ]['affich'] == 0 )
						continue;
			
				
				if( $idx % 2 == 0 )
					$class="pair";
				else	
					$class="impair";

				if( in_array( utf8_encode($ville), $arVillesPoints) === true)
					$class = " ville_a_points";

			
				$idx++;
				
				echo "<tr class=\"$class\"><td>".$ville."</td><td>".$pays."</td>";
				foreach( $arLogins as $login => $lnLogins )
				{
						$inferno = ($arPortails[$ville][ $login ]['inferno']==-1)?'-':$arPortails[$ville][ $login ]['inferno'];
						echo "<td>".$inferno."</td>";
				}
				echo "</tr>";
		
		}
		echo "</table>";
?>