<?PHP
//	require_once("contrats.inc.php");

/*************************************************/
/*                                               */
/*                                               */
/*                                               */
/*************************************************/

		function fast_in_array($elem, $array)
		{
		   $top = sizeof($array) -1;
		   $bot = 0;
		
		   while($top >= $bot)
		   {
		      $p = floor(($top + $bot) / 2);
		      if ($array[$p] < $elem) $bot = $p + 1;
		      elseif ($array[$p] > $elem) $top = $p - 1;
		      else return TRUE;
		   }
		    
		   return FALSE;
		} 

if($debug) echo "Mémoire consommée (step 0.8) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";                     

		maj_villes_a_point();
		maj_stats_membres();

if($debug) echo "Mémoire consommée (step 0.9.1) : ".(memory_get_usage() - $baseMemory)."<BR/>\n"; 		

		$url = "http://www.croquemonster.com/api/contracts.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xmlContracts = readXML( $url );
		if( substr($xmlContracts, 0, 6)== "Erreur" )
		{
				echo $xmlContracts;
				exit;
		}
if($debug) echo "Mémoire consommée (step 0.9.2) : ".(memory_get_usage() - $baseMemory)."<BR/>\n"; 		
		$url = "http://www.croquemonster.com/api/monsters.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xmlMonsters = readXML( $url );
		if( substr($xmlMonsters, 0, 6)== "Erreur" )
		{
				echo $xmlMonsters;
				exit;
		}

if($debug) echo "Mémoire consommée (step 0.9.3) : ".(memory_get_usage() - $baseMemory)."<BR/>\n"; 		
		$url = "http://www.croquemonster.com/api/cities.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xmlCities = readXML( $url );
		if( substr($xmlCities, 0, 6)== "Erreur" )
		{
				echo $xmlCities;
				exit;
		}

		if($debug) echo "Mémoire consommée (step 0.9) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";                     
		$arVillesPoints=array();
		if( isset($_SESSION['syndic_id']) )
		{
			/*
				$temp=array();
				select("SELECT distinct ville FROM cm_villes_syndic WHERE syndic_id = ".$_SESSION['syndic_id'], $temp);
				foreach($temp as $row)
					$arVillesPoints[]=$row['ville'];
				unset($temp);
			*/
			
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
		echo count($lst_villes)." villes à points<br>\n";
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
	echo count($arVillesPoints)." villes pas affichées<br>\n";	
		
if($debug) echo "Mémoire consommée (step 0.9.4) : ".(memory_get_usage() - $baseMemory)."<BR/>\n"; 		
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
		
if($debug) echo "Mémoire consommée (step 0.9.5) : ".(memory_get_usage() - $baseMemory)."<BR/>\n"; 				
		if( isset($_SESSION['syndic_id']) )
		{
				$url = "http://www.croquemonster.com/api/syndicate.xml?id=".$_SESSION['syndic_id'].";user=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
				$xmlSyndic = readXML( $url );
				if( substr($xmlSyndic, 0, 6)== "Erreur" )
				{
					/*
						echo $xmlSyndic;
						exit;
					*/
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

if($debug) echo "Mémoire consommée (step 1) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";                     
		$reussiteContrats = process_contrats($xmlContracts, $xmlMonsters, $xmlCities, $contracts, $monsters, $gain_max_par_monstre);
		
if($debug) echo "Mémoire consommée (step 2) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";
		meilleur_contrat_par_monstre( &$reussiteContrats, $filtre_gain, $filtre_reussite );

if($debug) echo "Mémoire consommée (step 3) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";
		$arContratsSpeciaux = list_contrats_speciaux( $contracts );

if($debug) echo "Mémoire consommée (step 4) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";
		$espeGains = calcul_esperance_gain($contracts, $monsters);

if($debug) echo "Mémoire consommée (step 5) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";
		$arVilles = list_villes( $contracts );
		
if($debug) echo "Mémoire consommée (step 6) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";
?>
<div style="display: block;float: left; margin-left: 20%; width:80%;">
		<form name="frm_filtres" action="index.php?pg=contrats" method="POST">
				<div>
					Pr&eacute;f&eacute;rences : <a href="#" id="ouv_ferm_pref" onclick="javascript:ouvre_ferme('ouv_ferm_pref','preferences');">(+)</a>
								&nbsp; Contrats &agrave; r&eacute;aliser entre :&nbsp;<select name="fuseau_min"><?PHP for($i=0; $i<=24; $i++) {  $selected=($fuseau_min==$i)?" selected=\"selected\"":""; echo "<option value=\"$i\"$selected>H+$i</option>"; } ?></select>
											et  <select name="fuseau_max"><?PHP for($i=0; $i<=24; $i++) {  $selected=($fuseau_max==$i)?" selected=\"selected\"":""; echo "<option value=\"$i\"$selected>H+$i</option>"; } ?></select> <span style="margin: 10px 0; border:1px;">
															<a href="#" onClick="document.forms['frm_filtres'].submit()"
																		style="border: 1px solid black; background-color: #EEEEEE; font-weight: bold; padding: 5px; margin: 10px 0;">Valider</a>
									</span>
										<br/><form name="frm_affich" id="frm_affich" action="SELF" method="POST">
												<input type="radio" name="affich" value="ville_a_points"    OnClick="javascript:refresh('ville_a_points');" checked="checked">Afficher les villes intoxiquées<BR>
												<input type="radio" name="affich" value="ville_sans_point"  OnClick="javascript:refresh('ville_sans_point');">Masquer les villes intoxiquées<BR>
												
										</form>
										
													
					<div id="preferences" class="hidden"  style="border:1px; float: top;">
							<input type="hidden" name="maj" value="1">		
							<div style="border:1px;">
									<table class="preferences">
											<tr><th>Probabilit&eacute; de r&eacute;ussite (%) minimum :&nbsp;</th><td><input size="6" type="text" name="filtre_reussite" value="<?PHP echo $filtre_reussite; ?>"></td></tr>
											<tr><th>Gains minimum (prime comprise) :&nbsp;</th><td><input size="6"  type="text" name="filtre_gain" value="<?PHP echo $filtre_gain; ?>"></td></tr>
											<tr><th>Forcer l'affichage des contrats dont l'inferno est d'au moins :&nbsp;</th><td><input size="3"  type="text" name="filtre_inferno" value="<?PHP echo $filtre_inferno; ?>"> (0, pas d'affichage forcé)</td></tr>
											
											<tr><th><div class="ouvre_ferme"  style="border:1px;">
															Niveau de difficult&eacute; &agrave; afficher&nbsp;<a href="#" id="ouv_ferm1" onclick="javascript:ouvre_ferme('ouv_ferm1','choix_difficulte');">(+)</a> :&nbsp;</th>
													<td><div id="choix_difficulte" class="visible" style="border:1px;">
																				<table>
																						<tr><th><input type="checkbox" name="filtre_diff[]" value="0" <?PHP if($enf == 1) echo "checked";?>></th><td>enfantin</td></tr>
																						<tr><th><input type="checkbox" name="filtre_diff[]" value="1" <?PHP if($abo == 1) echo "checked";?>></th><td>abordables</td></tr>
																						<tr><th><input type="checkbox" name="filtre_diff[]" value="2" <?PHP if($nor == 1) echo "checked";?>></th><td>normaux</td></tr>
																						<tr><th><input type="checkbox" name="filtre_diff[]" value="3" <?PHP if($dif == 1) echo "checked";?>></th><td>difficiles</td></tr>
																						<tr><th><input type="checkbox" name="filtre_diff[]" value="4" <?PHP if($mon == 1) echo "checked";?>></th><td>monstrueux</td></tr>
																						<tr><th><input type="checkbox" name="filtre_diff[]" value="5" <?PHP if($inf == 1) echo "checked";?>></th><td>infernaux</td></tr>
																				</table>
																		</div>
															</div>
													</td></tr>
											<!--
											<tr><th>Afficher les contrats des villes "intoxiquées" :&nbsp;</th><td>
													<input type="radio" name="affich_villes" value="oui" <?PHP if ($affich_ville=="oui") echo "checked"; ?>>Oui<br/>
													<input type="radio" name="affich_villes" value="non" <?PHP if ($affich_ville=="non") echo "checked"; ?>>Non</td></tr>
											-->									
											<tr><th>Afficher le pourcentage de réussite du monstre avec les équipements supplémentaires requis :&nbsp;</th><td>
													<input type="radio" name="affich_habille" value="oui" <?PHP if ($affich_habille=="oui") echo "checked"; ?>>Oui<br/>
													<input type="radio" name="affich_habille" value="non" <?PHP if ($affich_habille=="non") echo "checked"; ?>>Non</td></tr>
											
											<tr><th>Afficher la prime du contrat dans chaque case :&nbsp;</th><td>
													<input type="radio" name="affich_primes" value="oui" <?PHP if ($affich_primes=="oui") echo "checked"; ?>>Oui<br/>
													<input type="radio" name="affich_primes" value="non" <?PHP if ($affich_primes=="non") echo "checked"; ?>>Non</td></tr>
											
											<tr><th>Crit&egrave;re de tri :&nbsp;</th><td>
													<input type="radio" name="tri" value="gain"   <?PHP if ($sens_tri=="gain")   echo "checked"; ?>>&nbsp;Gain / Fuseau / Inferno<br/>
													<input type="radio" name="tri" value="chrono" <?PHP if ($sens_tri=="chrono") echo "checked"; ?>>&nbsp;Fuseau / Gain / Inferno <br/>
													<input type="radio" name="tri" value="inferno" <?PHP if ($sens_tri=="inferno") echo "checked"; ?>>&nbsp;Inferno / Fuseau / Gain
													</td></tr>
											<tr><th>Préférence d'ouverture des fenêtres CM</th><td><select name="ouvr_fenetre">
														<option value='popup' <?PHP if ($ouvr_fenetre=='popup') echo "selected=\"selected\""; ?>>popup</option>
														<option value='cm' <?PHP if ($ouvr_fenetre=='cm') echo "selected=\"selected\""; ?>>onglet ou fenêtre unique</option>
														<option value='_blank' <?PHP if ($ouvr_fenetre=='_blank') echo "selected=\"selected\""; ?>>nouvel onglet ou fenêtre</option>
														</select>
														</td></tr>
											<tr><th>Fuseau horaire</th><td><select id="sel_zones" name="sel_zones">
																												<option>America</option>
																												<option>Antartica</option>
																												<option>Arctic</option>
																												<option>Asia</option>
																												<option>Atlantic</option>
																												<option>Europe</option>
																												<option>Indian</option>
																												<option>Pacific</option>
																										</select>
																										<select id="sel_villes" name="sel_villes"></select>
														<input type="hidden" name="timezone" id="timezone" value="<?PHP echo $timezone; ?>" />
														</td></tr>
											
											<tr><th>&nbsp;</th><td>
													<div style="margin: 10px 0; border:1px;">
															<a href="#" onClick="document.forms['frm_filtres'].submit()"
																		style="border: 1px solid black; background-color: #EEEEEE; font-weight: bold; padding: 5px; margin: 10px 0;">Valider</a>
													</div>
													</td></tr>

											
									</table>
							</div>
					</div>
				</div>
		</form>
</div>


		<!--
		//
		// Menu gauche
		//
		-->
		<div style="float: left; width: 19%;">
				<div style="border: 2px outset #c0c0c0; color: black; padding: 5px;">
						Espérance de gain pour les prochaines 24 heures :<br/><strong><?PHP echo number_format($espeGains, 0, ".", " "); ?></strong>&nbsp;<img src="images/miniMoney.gif">
				</div>
				<div id="contrats_spe" style="border-bottom: 5px double black; padding-bottom: 10px;">
						<h3>Contrats sp&eacute;ciaux :</h3>
						<?PHP
						if( count($arContratsSpeciaux) == 0)
								echo "<i>Aucun contrat sp&eacute;cial pour l'instant.</i><br />";
						else
						{
								$chrono_tri=array();
								foreach($arContratsSpeciaux as $key => $data)
								{
											$chrono_tri[$key]=$contracts[ $data ]['countdown'];
								}
								array_multisort($chrono_tri, SORT_ASC, $arContratsSpeciaux);
								echo "<table style=\"width: 200px\">\n";
								foreach($arContratsSpeciaux as $cs)
								{
										echo "\t<tr><td style=\"text-align:left;width:150px\"><a href=\"#".$cs."\"><img src=\"images/".$contracts[ $cs ]['ico']."\">&nbsp;".$contracts[ $cs ]['city']."</a></td><td><small>".date("H:i:s", mktime(0, 0, $contracts[ $cs ]['countdown']))."</small></td></tr>\n";
								}
								echo "</table>\n";
						}
						?>
				</div>
				<div id="infos_contrat" class="hidden" style="padding-top: 10px;"></div>
		</div>
		
<?PHP
		//
		// Affichage de l'entête de la table
		//
		echo "<table id=\"aide_contrats\" class=\"head_fixe\" style=\"width:80%\">\n";
		echo "<thead>\n";
		echo "<form name=\"frm_filtres_tab\" action=\"index.php?pg=contrats\" method=\"POST\">";
		echo "<input type=\"hidden\" name=\"maj_tab\" value=\"1\">";
		echo "<input type=\"hidden\" id=\"tri_tab\" name=\"tri\" value=\"\">";
		
		echo "\t<tr><td>   <a  onClick=\"document.getElementById('tri_tab').value='special'  ;document.forms['frm_filtres_tab'].submit();\" onMouseOver=\"javascript:showTooltip('Trier par \"special\", puis gain, puis inferno, puis timezone');\" onMouseOut=\"javascript:hideTooltip();\"><img src=\"images/city_default.png\"></a></td><td>";
		echo "   <a  onClick=\"document.getElementById('tri_tab').value='gain'  ;document.forms['frm_filtres_tab'].submit();\" onMouseOver=\"javascript:showTooltip('Trier par gain, puis inferno, puis timezone');\" onMouseOut=\"javascript:hideTooltip();\"><img src=\"images/miniMoney.gif\"></a> / ";
		echo "   <a  onClick=\"document.getElementById('tri_tab').value='chrono';document.forms['frm_filtres_tab'].submit();\" onMouseOver=\"javascript:showTooltip('Trier par timezone, puis gain, puis inferno');\" onMouseOut=\"javascript:hideTooltip();\"><img src=\"images/time.gif\"></a>";
		echo "</td>";
		echo "<td><a  onClick=\"document.getElementById('tri_tab').value='inferno';document.forms['frm_filtres_tab'].submit();\" onMouseOver=\"javascript:showTooltip('Trier par inferno, puis gain, puis timezone');\" onMouseOut=\"javascript:hideTooltip();\"><img src=\"images/finferno.png\"></a></td>";
		echo "\n";
		echo "</form>\n";
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
		echo "\t</tr>\n</thead>\n";		
		
		echo "<tbody>";


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
		echo "</tbody>\n</table>\n</div>";
		disconnect();

		foreach($monsters as $monstre)
		{
			echo "\t\t<div id=\"monstre".$monstre['id']."\" class=\"hidden\"><script type=\"text/javascript\" src=\"".$monstre['swfjs']."\"></script></div>\n";	
		}
