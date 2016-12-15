<?PHP
		include_once('maj_stats_w5.inc.php');

		function parse_api_cities($sxml)
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
							
							$arCities[ $city['name'].';'.$city['country'] ] = $city;
		  		}
		      parse_api_cities($element);
		  }
			return ($arCities);
		}


		function parse_api_portails($sxml, $tag)
		{
			$arTable=array();
			
				foreach($sxml->children() as $element)
		  	{
		  		if ( $element->getName() == $tag )
		  		{
		  				$objet['id']	    = intval( $element['id']      );
		  				$objet['city']	  = utf8_decode(strval($element['city']   ));
							$objet['country'] = utf8_decode(strval($element['country']));
							$objet['level']   = intval( $element['level']     );
							$objet['timezone']= intval( $element['timezone']     );
							$objet['defense'] = intval( $element['defense']     );
							
							$arTable[ $objet['city'].';'.$objet['country'] ] = $objet;
		  		}
		      parse_api_portails($element, $tag);
		  	}
		  	
		  	return($arTable);
		 }


		function parse_xml_cities($sxml, $tag)
		{
			$arTable=array();
			
				foreach($sxml->children() as $element)
		  	{
		  		if ( $element->getName() == $tag )
		  		{
		  				$objet['id']	    = intval  ( $element['id']      );
		  				$objet['name']	  = utf8_decode(strval  ( $element['name']   ));
							$objet['country'] = utf8_decode(strval  ( $element['country']));
							$objet['lat']     = floatval( $element['lat']     );
							$objet['lon']     = floatval( $element['lon']     );
							$objet['pop']     = intval  ( $element['pop']     );
							$objet['lvl']     = intval  ( $element['lvl']     );
							$objet['tz']      = intval  ( $element['tz']      );
							
							$arTable[ $objet['name'].';'.$objet['country'] ] = $objet;
		  		}
		      parse_xml_cities($element, $tag);
		  	}
		  	
		  	return($arTable);
		 }


		require_once("sql.php");
		connect();	

		maj_villes_a_point();

		$stats=true;

		$sens_tri = "ville";	

		if( isset($_POST['maj']) )
		{
				if( isset($_POST['filtre_ville']) )
						$filtre_ville = $_POST['filtre_ville'];
				else
						$filtre_ville = array();
						
				if( isset($_POST['fav_ville']) )
						$fav_ville = $_POST['fav_ville'];
				else
						$fav_ville = array();
						
				if( isset($_POST['interdit_ville']) )
						$interdit_ville = $_POST['interdit_ville'];
				else
						$interdit_ville = array();
						
				if( isset($_POST['sens_tri']) )
						$sens_tri = $_POST['sens_tri'];
				else
						$sens_tri = "ville";		
						
				$stmt="update cm_preferences set"
							. " filtre_villes='".implode(";",$filtre_ville)."' "
							. ",fav_villes='".implode(";",$fav_ville)."' "
							. ",interdit_villes='".implode(";",$interdit_ville)."' "
							. " where login='".$_SESSION['name']."'";
				$ret = insert($stmt);
			
				if($ret == 0)
				{
						$stmt="insert into cm_preferences (login, filtre_villes, fav_villes, interdit_villes)"
								 ."	 values('".$_SESSION['name']."', '".implode(";",$filtre_ville)."', '".implode(";",$fav_ville)."', '".implode(";",$interdit_ville)."')";
			
						$ret = insert($stmt);
				}
		}
		else
		{
				$stmt="select filtre_villes, fav_villes, interdit_villes from cm_preferences where login='".$_SESSION['name']."'";
				select( $stmt, $arVilles);
				
				$filtre_ville   =explode(";", $arVilles[0]['filtre_villes']) ; 		
				$fav_ville      =explode(";", $arVilles[0]['fav_villes']) ; 		
				$interdit_ville =explode(";", $arVilles[0]['interdit_villes']) ; 		
		}
		


		
		$url = "http://www.croquemonster.com/api/cities.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xmlCities = readXML( $url );
		if( substr($xmlCities, 0, 6)== "Erreur" )
		{
				echo $xmlCities;
				exit;
		}
		$arCities = parse_api_cities($xmlCities);
		
		
		$url = "http://www.croquemonster.com/api/portails.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xmlPortails = readXML( $url );
		if( substr($xmlPortails, 0, 6)== "Erreur" )
		{
				echo $xmlPortails;
				exit;
		}
		$arPortails = parse_api_portails($xmlPortails, 'portail');
		
		
		$xml = readXML( "http://www.croquemonster.com/xml/cities.xml" );
		if( substr($xml, 0, 6)== "Erreur" )
		{
				echo $xml;
				exit;
		}
		$arVilles = parse_xml_cities($xml, 'city');

		
		$idx=0;
		
		foreach ( $arCities as &$city)
		{
				if( array_key_exists ( $city['name'].';'.$city['country'] , $arPortails ) === true )
				{
						$city['acces_via']= $city['name'].';'.$city['country'];
						$city['defense']  = $arPortails[ $city['name'].';'.$city['country'] ]['defense'];
						$city['niveau']   = $arPortails[ $city['name'].';'.$city['country'] ]['level'];
				}				
				else
				{
						$lat = $arVilles[ $city['name'].';'.$city['country'] ]['lat'];
						$lon = $arVilles[ $city['name'].';'.$city['country'] ]['lon'];
						
						foreach( $arPortails as $portail )
						{
								$lat_chk = $arVilles[ $portail['city'].';'.$portail['country'] ]['lat'];
								$lon_chk = $arVilles[ $portail['city'].';'.$portail['country'] ]['lon'];
								$lvl_chk = $portail['level'];
							
								if ( is_accessible($lat, $lon, $lat_chk, $lon_chk, $lvl_chk ) )
								{
									$city['acces_via']= $portail['city'].';'.$portail['country'];
								}
						}
				}
				
				$city['tz']  = $arVilles[ $city['name'].';'.$city['country'] ]['tz'];
				$city['lvl'] = $arVilles[ $city['name'].';'.$city['country'] ]['lvl'];
				
		} 
		
		    
    $arCity_sel   = array();
		$arCity_fav   = array();
		$arCity_int   = array();
		$arVille      = array();
		$arPays       = array();
		$arNiveau     = array();
		$arDef        = array();
		$arReput      = array();
		$arPct_inf    = array();
		$arNbr_inf    = array();
		$arAcces      = array();
		$arTimezone   = array();
		$arDifficulty = array();
		foreach($arCities as $key => $arValues)
		{
				$arCity_sel  [$key] = (in_array($arValues['name'], $filtre_ville) )?1:0;
				$arCity_fav  [$key] = (in_array($arValues['name'], $fav_ville) )?1:0;
				$arCity_int  [$key] = (in_array($arValues['name'], $interdit_ville) )?1:0;
				$arVille     [$key] = $arValues['name']      ;
				$arPays      [$key] = $arValues['country']   ;
				$arNiveau    [$key] = $arValues['niveau']    ;
				$arDef       [$key] = $arValues['defense']   ;
				$arReput     [$key] = $arValues['reputation'];
				$arPct_inf   [$key] = $arValues['percent']   ;
				$arNbr_inf   [$key] = $arValues['infernos']  ;
				$arAcces     [$key] = $arValues['acces_via'] ;
				$arTimezone  [$key] = $arValues['tz']        ;
				$arDifficulty[$key] = $arValues['lvl']       ;
		
		}
		
		
		switch ($sens_tri)
		{
				case 'city_sel': array_multisort($arCity_sel  , SORT_DESC, $arVille, SORT_ASC, $arPays, SORT_ASC, $arCities ); break;
				case 'city_fav': array_multisort($arCity_fav  , SORT_DESC, $arVille, SORT_ASC, $arPays, SORT_ASC, $arCities ); break;
				case 'city_int': array_multisort($arCity_int  , SORT_DESC, $arVille, SORT_ASC, $arPays, SORT_ASC, $arCities ); break;
				case 'ville'   : array_multisort($arVille     , SORT_ASC , $arPays , SORT_ASC, $arCities ); break;
				case 'pays'    : array_multisort($arPays      , SORT_ASC , $arVille, SORT_ASC, $arCities ); break;
				case 'niveau'  : array_multisort($arNiveau    , SORT_DESC, $arVille, SORT_ASC, $arPays, SORT_ASC, $arCities ); break;
				case 'def'     : array_multisort($arDef       , SORT_DESC, $arVille, SORT_ASC, $arPays, SORT_ASC, $arCities ); break;
				case 'reput'   : array_multisort($arReput     , SORT_DESC, $arVille, SORT_ASC, $arPays, SORT_ASC, $arCities ); break;
				case 'pct_inf' : array_multisort($arPct_inf   , SORT_DESC, $arVille, SORT_ASC, $arPays, SORT_ASC, $arCities ); break;
				case 'nbr_inf' : array_multisort($arNbr_inf   , SORT_DESC, $arVille, SORT_ASC, $arPays, SORT_ASC, $arCities ); break;
				case 'acces'   : array_multisort($arAcces     , SORT_ASC , $arVille, SORT_ASC, $arPays, SORT_ASC, $arCities ); break;
				case 'time'    : array_multisort($arTimezone  , SORT_DESC, $arVille, SORT_ASC, $arPays, SORT_ASC, $arCities ); break;
				case 'diff'    : array_multisort($arDifficulty, SORT_DESC, $arVille, SORT_ASC, $arPays, SORT_ASC, $arCities ); break;
		}

		$stmt = " SELECT TIMESTAMPDIFF(MINUTE , upd_guerre5, now( ) ) diff"
					. " FROM cm_updates"
          . " WHERE login = '".$_SESSION['name']."'";

		select($stmt, $arStmtPortails);
		$ret = 999;
		if( (count($arStmtPortails) == 1) && ($arStmtPortails[0]['diff']!= NULL) )
				$ret = $arStmtPortails[0]['diff'];

		echo "<h2>Portails</h2>";
?>
<a href="index.php?pg=portails2">vers les portails manquants</a>
<form action="#self" method="POST" name="frm_villes">
<input type="hidden" id="maj" name="maj" value="1">
<input type="hidden" id="sens_tri" name="sens_tri" value="<?PHP echo $sens_tri; ?>">     
<div style="margin: 10px 0;">
							<a href="#" onClick="document.forms['frm_villes'].submit()"
										style="border: 1px solid black; background-color: #EEEEEE; font-weight: bold; padding: 5px; margin-top: 5px;">Valider</a>
					</div>
<table id="portails" class="head_fixe">
	<thead>
		<tr>
				<th><a onclick="document.getElementById('sens_tri').value='city_sel'; document.forms['frm_villes'].submit();"><img src="images/city_selected.png"></a></th>
				<th><a onclick="document.getElementById('sens_tri').value='city_fav'; document.forms['frm_villes'].submit();"><img src="images/city_favorite.png"></a></th>
				<th><a onclick="document.getElementById('sens_tri').value='city_int'; document.forms['frm_villes'].submit();"><img src="images/interdit.jpg"></a></th>
				<th><a onclick="document.getElementById('sens_tri').value='ville'   ; document.forms['frm_villes'].submit();">Ville</a></th>
				<th><a onclick="document.getElementById('sens_tri').value='pays'    ; document.forms['frm_villes'].submit();">Pays</a></th>
				<th><a onclick="document.getElementById('sens_tri').value='niveau'  ; document.forms['frm_villes'].submit();">Niveau</a></th>
				<th><a onclick="document.getElementById('sens_tri').value='def'     ; document.forms['frm_villes'].submit();">D&eacute;fense</a></th>
				<th><a onclick="document.getElementById('sens_tri').value='reput'   ; document.forms['frm_villes'].submit();">R&eacute;putation</a></th>
				<th><a onclick="document.getElementById('sens_tri').value='pct_inf' ; document.forms['frm_villes'].submit();">Inferno</a></th>
				<th><a onclick="document.getElementById('sens_tri').value='nbr_inf' ; document.forms['frm_villes'].submit();">Nbr Infernos</a></th>
				<th><a onclick="document.getElementById('sens_tri').value='acces'   ; document.forms['frm_villes'].submit();">Acc&egrave;s via</a></th>
				<th><a onclick="document.getElementById('sens_tri').value='time'    ; document.forms['frm_villes'].submit();"><img src="images/time.gif"></a></th>
				<th><a onclick="document.getElementById('sens_tri').value='diff'    ; document.forms['frm_villes'].submit();"><img src="images/lvl.gif"></a></th>
		</tr>
	</thead>
	<tbody>
<?PHP

		$idx=0;
		foreach($arCities as $city)
		{
				if (($idx%2) == 0)
					$class="pair";
				else
					$class="impair";
			
				if($city['acces_via'] == $city['name'].';'.$city['country'])
					$class .= " direct";
				else
					$class .= " indirect";
			
				if( in_array($city['name'], $filtre_ville ) === true  )
						$check1="checked";
				else
						$check1="";
				
				if( in_array($city['name'], $fav_ville ) === true  )
						$check2="checked";
				else
						$check2="";
				
				if( in_array($city['name'], $interdit_ville ) === true  )
						$check3="checked";
				else
						$check3="";
				
				
				if($city['acces_via']==$city['name'].';'.$city['country'])
						$acces = "Direct";
				else
				{
						list($ville,$pays)=explode(';', $city['acces_via']);
						$acces = "$ville($pays)";//$city['acces_via']."(".$arPortails[ $city['acces_via'] ]['country'].")";
				}
				echo "\t<tr class=\"$class\">"
									."<td  onMouseOver=\"javascript:showTooltip('Activer l\'affichage permanent des contrats de cette ville');\" onMouseOut=\"javascript:hideTooltip();\" style=\"width: 20px;\">"
																				."<input type=\"checkbox\" name=\"filtre_ville[]\" value=\"".$city['name']."\" $check1></td>"
									."<td  onMouseOver=\"javascript:showTooltip('Activer l\'affichage permanent des contrats de cette ville sous forme de contrat spécial');\" onMouseOut=\"javascript:hideTooltip();\" style=\"width: 20px;\">"
																				."<input type=\"checkbox\" name=\"fav_ville[]\" value=\"".$city['name']."\" $check2></td>"
									."<td  onMouseOver=\"javascript:showTooltip('Bloquer de manière permanente l\'affichage des contrats de cette ville');\" onMouseOut=\"javascript:hideTooltip();\" style=\"width: 20px;\">"
																				."<input type=\"checkbox\" name=\"interdit_ville[]\" value=\"".$city['name']."\" $check3></td>"
									."<td>".$city['name']."</td>"
									."<td>".$city['country']."</td>"
									."<td>".$city['niveau']."</td>"
									."<td>".$city['defense']."</td>"
									."<td>".$city['reputation']."</td>"
									."<td>".$city['percent']."</td>"
									."<td>".$city['infernos']."</td>"
									."<td>".$acces."</td>"
									."<td>".$city['tz']."</td>"
									."<td class=\"last_col\">".$city['lvl']."</td>"
							."</tr>\n";
				
				
				
				
				$idx++;

				if( $ret >= 60 )
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

		}
		
		if( $ret >= 60 )
		{
				$stmt = " UPDATE cm_updates SET upd_guerre5=NOW() WHERE login = '".$_SESSION['name']."'";
				$ret_cod = insert(	$stmt );
				if( $ret_cod == 0 )
				{
						$stmt = "INSERT INTO cm_updates (login, upd_guerre5 ) "
							    ."VALUES ( '".$_SESSION['name']."', NOW() )";
						insert( $stmt ); 
				}
		}
		disconnect();

?>
	</tbody>
</table>
</form>