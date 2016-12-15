<?PHP
//	require_once("contrats.inc.php");

/*************************************************/
/*                                               */
/*                                               */
/*                                               */
/*************************************************/

		maj_villes_a_point();
		maj_stats_membres();

		$url = "http://www.croquemonster.com/api/contracts.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xmlContracts = readXML( $url );
		if( substr($xmlContracts, 0, 6)== "Erreur" )
		{
				echo $xmlContracts;
				exit;
		}

		$url = "http://www.croquemonster.com/api/monsters.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xmlMonsters = readXML( $url );
		if( substr($xmlMonsters, 0, 6)== "Erreur" )
		{
				echo $xmlMonsters;
				exit;
		}

		$url = "http://www.croquemonster.com/api/cities.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xmlCities = readXML( $url );
		if( substr($xmlCities, 0, 6)== "Erreur" )
		{
				echo $xmlCities;
				exit;
		}

		$arVillesPoints=array();
		if( isset($_SESSION['syndic_id']) )
		{
				$url = "http://www.croquemonster.com/syndicate/domination.xml?id=".$_SESSION['syndic_id'];
				$tmp = direct_get( $url, true );
				try
				{
						$data = new SimpleXMLElement($tmp);
				}
				catch (Exception $e)
				{
						echo "Problème (".$e->getMessage()."), probablement dû à un passage de fuseau<BR/>Réessayez d'ici quelques minutes.<br/>\n";
						echo "Si le problème persiste, signalez-le moi, merci !<br/>\n";
						exit;
				}
				
				$lst_villes = villes_a_point($data);
				foreach( $lst_villes as $ville )
				{
					foreach( $xmlCities as $city )
					{
						if( $city['id'] == $ville['id'] )
						{
							$arVillesPoints[] = strval($city['name']);
						}
					}
				}
		}	

		$accueil = direct_get( "http://www.croquemonster.com/news", true);
		if( $accueil == false )
		{
				return;
		}
		else
		{
			
			if( strstr( $accueil, "C'est la pleine lune, mouahahaha !" ) )
				$bonusMoon = 5;
			else
				$bonusMoon = 0;
		}
		unset($accueil);
		unset($html);
		
		if( isset($_SESSION['syndic_id']) )
		{
			$url = "http://www.croquemonster.com/api/syndicate.xml?id=".$_SESSION['syndic_id'].";user=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
			$xmlSyndic = readXML( $url );
			if( substr($xmlSyndic, 0, 6)== "Erreur" )
			{
			}
			else
			{
				$arAgencies = agences($xmlSyndic);    
			}
		}
		else		
				$arAgencies = array();

		unset($xmlSyndic);
		
		$arAgences=array();
		
		foreach( $arAgencies as $agence )
				$arAgences[] = $agence['fartBox'];
							
		
		$contracts = array();
		$monsters  = array();
		$cities    = array();
		$reussiteContrats=array();

		$reussiteContrats = process_contrats($xmlContracts, $xmlMonsters, $xmlCities, $contracts, $monsters, $gain_max_par_monstre);
		
		meilleur_contrat_par_monstre( &$reussiteContrats, $filtre_gain, $filtre_reussite );

		$arContratsSpeciaux = list_contrats_speciaux( $contracts );

		$espeGains = calcul_esperance_gain($contracts, $monsters);

		$arVilles = list_villes( $contracts );
		
		//
		// Affichage de l'entête de la table
		//
?>
		<table id="aide_contrats" class="display" style="width:100%">
		<thead>
		
		<tr><th>    <a  onClick="document.getElementById('tri_tab').value='special'  ;document.forms['frm_filtres_tab'].submit();" onMouseOver="javascript:showTooltip('Trier par "special", puis gain, puis inferno, puis timezone');" onMouseOut="javascript:hideTooltip();"><img src="images/city_default.png"></a>
			</th>
			<th>
					<a  onClick="document.getElementById('tri_tab').value='gain'  ;document.forms['frm_filtres_tab'].submit();" onMouseOver="javascript:showTooltip('Trier par gain, puis inferno, puis timezone');" onMouseOut="javascript:hideTooltip();"><img src="images/miniMoney.gif"></a> / 
					<a  onClick="document.getElementById('tri_tab').value='chrono';document.forms['frm_filtres_tab'].submit();" onMouseOver="javascript:showTooltip('Trier par timezone, puis gain, puis inferno');" onMouseOut="javascript:hideTooltip();"><img src="images/time.gif"></a>
			</th>
			<th>	<a  onClick="document.getElementById('tri_tab').value='inferno';document.forms['frm_filtres_tab'].submit();" onMouseOver="javascript:showTooltip('Trier par inferno, puis gain, puis timezone');" onMouseOut="javascript:hideTooltip();"><img src="images/finferno.png"></a>
			</th>
		
<?PHP
		$nbCasesParLigne = count($monsters)+4;

		foreach($monsters as $idx => $monstre)
		{
			$color="";
			if ( intval($monstre['fatigue']) > 0 )
					$color = "style=\"color:orange\"";
			
			$class="";		
			if( $idx == count($monsters)-1 )		
					$class = "class=\"last_col\"";

			$occupation = "";
			if( $monstre['contract']>0)
					$occupation = "contrat";
			else if( $monstre['escort']>0)
					$occupation = "escort";
			else if( $monstre['attack']>0)
					$occupation = "attack";
			else if( $monstre['racket']>0)
					$occupation = "racket";
			else if( $monstre['propaganda']>0)
					$occupation = "propa";
			else if( $monstre['match']>0)
					$occupation = "match";
					
			echo "\t\t<th $class $color $class><div onMouseOver=\""
				 ."javascript:showMonstreInfos(".$monstre['id'].", '".$monstre['name']."'"
				 .", ".$monstre['sadism']
				 .", ".$monstre['ugliness']
				 .", ".$monstre['power']
				 .", ".$monstre['greediness']
				 .", ".$monstre['control']
				 .", ".$monstre['fight']
				 .", ".$monstre['endurance']
				 .", ".$monstre['bounty']
				 .", ".$monstre['fatigue']
				 .", '".$occupation."');\""
				 ." onMouseOut=\"javascript:hideTooltip();\"><a  onClick=\"document.getElementById('tri_tab').value='mstr".$monstre['id']."';document.forms['frm_filtres_tab'].submit();\" onMouseOver=\"javascript:showTooltip('Trier par faisabilité (par <b>".$monstre['name']."</b>) puis gain, puis timezone, puis inferno');\" onMouseOut=\"javascript:hideTooltip();\">".$monstre['name']."</a></div></th>\n";
		}

?>
		</tr></thead>
		
		<tbody>

<?
    $chrono  = array();
    $gain    = array();
    $inferno = array();
    $kind		 = array();
    $monstro_tri=array();
    $tri_spec=array();
		foreach($contracts as $key => $data)
		{
					$chrono[$key]=$data['countdown'];
					$gain[$key]=$data['prize'];
					$inferno[$key]=$data['inferno'];
					$kind[$key]=$data['kind'];
		
					$tri_spec[$key]=$data['ico_val'];
		}

		if( substr($sens_tri, 0, 4) == "mstr")
		{
				$idMstr = intval(substr($sens_tri, 4));
				foreach($reussiteContrats as $cid => $resLine)
				{
						if( ($resLine[$idMstr]['gain'] >= $filtre_gain ) && ($resLine[$idMstr]['reussite'] >= $filtre_reussite) )
								$monstro_tri[$cid] = $resLine[$idMstr]['gain'];
						else if (in_array( $cid, $arContratsSpeciaux ) === true)
								$monstro_tri[$cid] = $resLine[$idMstr]['gain'];
						else
								$monstro_tri[$cid] = 0;
				}
		}

		if ( substr($sens_tri, 0, 4) == "mstr" )
				array_multisort($monstro_tri, SORT_DESC, $contracts);
		else if ( $sens_tri == "special" )
				array_multisort($tri_spec, SORT_DESC, $gain, SORT_DESC, $chrono, SORT_ASC, $inferno, SORT_DESC, $kind, SORT_DESC, $contracts);
		else if ( $sens_tri == "chrono" )
				array_multisort($chrono, SORT_ASC, $gain, SORT_DESC, $inferno, SORT_DESC, $kind, SORT_DESC, $contracts);
		else if ( $sens_tri == "gain" )
				array_multisort($gain, SORT_DESC, $chrono, SORT_ASC, $inferno, SORT_DESC, $kind, SORT_DESC, $contracts);
		else if ( $sens_tri == "inferno" )
				array_multisort($inferno, SORT_DESC, $gain, SORT_DESC, $chrono, SORT_ASC, $kind, SORT_DESC, $contracts);
		else if ( $sens_tri == "kind" )
				array_multisort($kind, SORT_DESC, $gain, SORT_DESC, $inferno, SORT_DESC, $chrono, SORT_ASC, $contracts);

		$previousCountdown = 0;
		
		foreach ( $contracts as $line )
		{
				$fuseau_rel = intval(date("H", mktime(0, 0, $line['countdown'])));
				if( (($fuseau_rel < $fuseau_min) || ($fuseau_rel > $fuseau_max )) && ($line['city'] != $_SESSION['ville_prout']) )
						continue;
				/*
				if( ($affich_ville == "non") && (in_array( utf8_encode($line['city']), $arVillesPoints) === true) && ($line['city'] != $_SESSION['ville_prout']) )
				{
					//echo "Ville pas affichée : ".$line['city']."<br/>\n";
								continue;
				}	
				*/	
				$icon_special = "<img src=\"images/city_default.png\">";
			  if( isset($line['ico']) )
			 	{
			 		 	$icon_special = "<img src=\"images/".$line['ico']."\">";
			 	}

				$cid = $line['id'];

				$ligne_class = "";
				
				if($line['accepted'] == 1 && $line['monster'] == 0)
				{
					$ligne_class = " monstre_retire";
				}
				
				if(( in_array( $line['city'], $interdit_ville ) === true ) && ($line['city'] != $_SESSION['ville_prout']) )
						continue;
						
				if( (in_array( $line['id'], $arContratsSpeciaux ) === false)
				  //&&($line['inferno'] < $filtre_inferno)
					&&(in_array($line['city'], $filtre_ville ) === false)
					&&($line['city'] != $_SESSION['ville_prout'])  )
				{
								
						if( max_reussite($reussiteContrats[$cid])< $filtre_reussite )
						{
								continue;
						}
						if( max_gain($reussiteContrats[$cid])< $filtre_gain )
						{
								continue;
						}
						
						if ( $enf == 0 && $line['kind'] == 0 ) continue;
						if ( $abo == 0 && $line['kind'] == 1 ) continue;
						if ( $nor == 0 && $line['kind'] == 2 ) continue;
						if ( $dif == 0 && $line['kind'] == 3 ) continue;
						if ( $mon == 0 && $line['kind'] == 4 ) continue;
						if ( $inf == 0 && $line['kind'] == 5 ) continue;
				}
				
				if ( $sens_tri == "chrono" )
				{
						// Point de rupture
						if($previousCountdown != $line['countdown'])
						{
								$previousCountdown = $line['countdown'];
								if($previousCountdown != 0)
										echo "<tr style=\"height:5px;background-color:#1C5059; border: 3px solid red;\"><td colspan=\"$nbCasesParLigne\"></tr>";
						}
				}		
				$th = " onMouseOver=\"javascript:showContractInfo('".addslashes($line['name'])."','".$line['city']."','".$line['country']."',".$line['sadism'].",".$line['ugliness'].",".$line['power'].",".$line['greediness'].");\""
				    ." onMouseOut=\"hideTooltip();\"";
						
				
				if( in_array( utf8_encode($line['city']), $arVillesPoints) === true)
					$ligne_class .= " ville_a_points";
				else
					$ligne_class .= " ville_sans_point";	

				$ligne_class = " class=\"$ligne_class\"";
				
				$tz = new DateTimeZone($timezone);
				$d = new DateTime("now", $tz);
		//echo "avant : ".$d->format('H:i')."&nbsp;";
		$h = date("G", mktime(0, 0, $line['countdown']));
		$m = date("i", mktime(0, 0, $line['countdown']));
		//echo "+".$h." hour +".($m+1)." minute &nbsp; = ";
				
				$d->modify('+'.($m+1).' min' );
		 		$d->modify('+'.$h.' hour' );
				 
				//echo "  après : ".$d->format('H:i')."<br/>\n";
				
				echo "\t<tr$ligne_class><th $th><a name=\"".$line['id']."\"></a>".$icon_special."<img src=\"images/contract".$line['kind'].".gif\"></th>"
				."<th $th>".$line['prize']."&nbsp;<img src=\"images/miniMoney.gif\"><br/><small>".date("H:i", mktime(0, 0, $line['countdown']))."(".$d->format('H:i').")</small></th>";
						
/*				echo "<th $th>".date("H:i:s", mktime(0, 0, $line['countdown']))."</th>\n";*/
				echo "<th $th>".$line['inferno']."</th>\n";
				
				$resLine = $reussiteContrats[$cid];
				
				$idx=0;
				foreach( $resLine as $mid => $case )
				{
					$attributs = " onMouseOver=\"javascript:showTooltip('Gain prime déduite : ".$case['gain'];

					$case['gain_eq'] = $case['gain'];
					if( isset($case['equip_a_installer']))
					{
							$attributs = $attributs."<BR/><b>Equipements à installer :</b>";
							foreach($case['equip_a_installer'] as $cle => $a_equiper)
							{
										$attributs = $attributs."<br/>".addslashes($liste_equipements[$cle]['name']);
										$case['gain_eq'] -= $liste_equipements[$cle]['prix'];
							}
					}
					$attributs = $attributs."<BR/><b>Gain prime et équipements d&eacute;duits : ".$case['gain_eq']."</b>');\" onMouseOut=\"hideTooltip();\"";			
					
					if( $case['gain_eq'] < 0 )
					$case['gain_eq'] = "<span style=\"color: red;\">".$case['gain_eq']."</span>";
					
					
						$lien = "http://www.croquemonster.com/contract/?cid=".$cid.";mid=".$mid;
						
						$infos = "javascript:set_infos_contrat('".$line['name']."','".$line['city']."','".$line['country']."', '".$line['kind']."', '".$line['difficulty']."','".$line['sadism']."','".$line['ugliness']."','".$line['power']."','".$line['greediness']."', ".$mid.", '".$monsters[$mid]['name']."', '".$monsters[$mid]['sadism']."','".$monsters[$mid]['ugliness']."','".$monsters[$mid]['power']."','".$monsters[$mid]['greediness']."','".$monsters[$mid]['control']."', ".$bonusMoon.");";
					
					
						$class="";
						if ( $case['occuped'] == 1 )
							$class="occupe ";		
						if (( $case['accepted'] == 1 ) && ($line['monster'] == ''))
							$class="retire ";
						else if ( $case['accepted'] == 1 )
					  	$class="accepte ";
					  if ( $case['affected'] == 1 )
					  	$class="affecte ";
					  if ( $case['fatigued'] == 1 )
					  	$class="fatigue ";
						
						$class .= $case['max'];
						
						if( $idx == count($resLine)-1 )		
							$class .= " last_col ";
				  	
				  	$idx++;
				  	
		  			if($ouvr_fenetre == 'popup')
							$target="";
						else if($ouvr_fenetre == 'cm')
							$target="target=\"cm\"";
						else if($ouvr_fenetre == '_blank')
							$target="target=\"_blank\"";
		  	
				  	
					  	
					  if ($class != "")
					  	$class=" class=\"".$class."\"";
					
					  // On affiche toujours le % de reussite des spéciaux !!	
					  if( ( in_array( $line['id'], $arContratsSpeciaux ) === true )
					   || (( $line['inferno'] >= $filtre_inferno ) && ($filtre_inferno != 0))
					   || (in_array($line['city'], $filtre_ville ) === true)
					   || ($line['city'] == $_SESSION['ville_prout']) )
					  {
								echo "\t\t<td$class onmouseover=\"$infos\"><a $target href=\"$lien\" onclick=\"return monPopNamaNi(this.href, 950, 600, '".$ouvr_fenetre."');\" $attributs>";
								if( $affich_habille == 'oui' )
								{
									
										if ($case['reussite'] == $case['reussite_habille'])
										{
												echo $case['reussite']."%";
												if( $affich_primes == 'oui' )
														echo "<br/><small>".$case['gain']."</small>";
												echo "</td>\n";
										}
										else
										{
												echo $case['reussite']."-".$case['reussite_habille']."%";
												if( $affich_primes == 'oui' )
														echo "<br/><small>".$case['gain']."<br/>".$case['gain_eq']."</small>";
												echo "</td>\n";
										}
								}
								else
								{
										echo $case['reussite']."%";
										if( $affich_primes == 'oui' )
												echo"<br/><small>".$case['gain']."</small>";
										echo"</td>\n";
								}

						}
						else if (( $affich_habille == 'oui' ) && ( $case['reussite_habille'] < $filtre_reussite ) )
					  {	
								echo "\t\t<td$class>-</td>\n";
						}
						else if (( $affich_habille == 'non' ) && ( $case['reussite'] < $filtre_reussite ) )
					  {	
								echo "\t\t<td$class>-</td>\n";
						}
						else if ($case['gain'] < $filtre_gain)
						{
								echo "\t\t<td$class>-</td>\n";
						}
						else
						{
								echo "\t\t<td$class onmouseover=\"$infos\"><a $target href=\"$lien\" onclick=\"return monPopNamaNi(this.href, 950, 600, '".$ouvr_fenetre."');\" $attributs>";
								if( $affich_habille == 'oui' )
								{
									
										if ($case['reussite'] == $case['reussite_habille'])
										{
												echo $case['reussite']."%";
												if( $affich_primes == 'oui' )
														echo "<br/><small>".$case['gain']."</small>";
												echo "</td>\n";
										}
										else
										{
												echo $case['reussite']."-".$case['reussite_habille']."%";
												if( $affich_primes == 'oui' )
														echo "<br/><small>".$case['gain']."<br/>".$case['gain_eq']."</small>";
												echo "</td>\n";
										}
								}
								else
								{
										echo $case['reussite']."%";
										if( $affich_primes == 'oui' )
												echo"<br/><small>".$case['gain']."</small>";
										echo"</td>\n";
								}
								
								//if ($debug) echo $case['reussite'] ."<==>". $case['reussite_habille']."<BR/>\n";
						}
				}
				echo "\t</tr>\n";
			
			
		}
?>
		</tbody>
		</table>
		
<script type="text/javascript">
$(document).ready(function() {
$('#aide_contrats').dataTable();
} );
</script>
	</div>
<?PHP
		disconnect();

		foreach($monsters as $monstre)
		{
			echo "\t\t<div id=\"monstre".$monstre['id']."\" class=\"hidden\"><script type=\"text/javascript\" src=\"".$monstre['swfjs']."\"></script></div>\n";	
		}
