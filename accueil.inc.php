<?PHP

				$arConvCO2['Chaussette']          = 0.01;
				$arConvCO2['Petite voiture']      = 0.005;
				$arConvCO2['Livre de conte']      = 0.005;
				$arConvCO2['Ours en peluche']     = 0.01;
				$arConvCO2['Soldat en plastique'] = 0.01;
				$arConvCO2['Thermomètre']         = 0.015;
				$arConvCO2['Chat']                = 0.018;
				$arConvCO2['Console DM']          = 0.018;
				$arConvCO2['Talkie Walkie']       = 0.021;
				$arConvCO2['Poisson rouge']       = 0.025;

				$arConvMoney['Chaussette']          = 80;
				$arConvMoney['Petite voiture']      = 40;
				$arConvMoney['Livre de conte']      = 40;
				$arConvMoney['Ours en peluche']     = 80;
				$arConvMoney['Soldat en plastique'] = 80;
				$arConvMoney['Thermomètre']         = 120;
				$arConvMoney['Chat']                = 160;
				$arConvMoney['Console DM']          = 160;
				$arConvMoney['Talkie Walkie']       = 200;
				$arConvMoney['Poisson rouge']       = 240;

				function parseXML($sxml, $tag)
				{
					$arInventaire=array();
					
						foreach($sxml->children() as $element)
				  	{
				  		if ( $element->getName() == $tag )
				  		{
				  				$objet['id']=intval($element['id']);
				  				if( substr($element['name'], 0, 7) == "Thermom" )
				  						$objet['name']="Thermomètre";
				  				else
											$objet['name']=utf8_decode(strval($element['name']));
									$objet['qty']=intval($element['qty']);
									
									$arInventaire[] = $objet;
				  		}
				      parseXML($element, $tag);
				  	}
				  	
				  	return($arInventaire);
				 }
				 
				if( isset($_SESSION['name']) && $_SESSION['name'] != "" )
				{
						$url = "http://www.croquemonster.com/api/agency.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
						$xml = readXML( $url );
						if( substr($xml, 0, 6)== "Erreur" )
						{
								echo $xml;
								exit;
						}
						
						$url = "http://www.croquemonster.com/api/inventory.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
						$xmlInventaire = readXML( $url );
						if( substr($xmlInventaire, 0, 6)== "Erreur" )
						{
								echo $xmlInventaire;
								exit;
						}
		
						$url = "http://www.croquemonster.com/api/portails.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
						$xmlPortails = readXML( $url );
						if( substr($xmlPortails, 0, 6)== "Erreur" )
						{
								echo $xmlPortails;
								exit;
						}

						$url = "http://www.croquemonster.com/api/monsters.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
						$xmlMonstres = readXML( $url );
						if( substr($xmlMonstres, 0, 6)== "Erreur" )
						{
								echo $xmlMonstres;
								exit;
						}
		
						/*********************************/
						/*      Process de l'agence      */
						/*********************************/
						$_SESSION['level'] = intval($xml['level']);
						
						if ( isset($xml['syndicate']) )
						{
								$_SESSION['syndicat'] = utf8_decode(strval($xml['syndicate']));
								$_SESSION['syndic_id'] = intval($xml['syndicateId']);
						}
						if ( isset($xml['fartBox']) )
						{
								$_SESSION['ville_prout'] = utf8_decode(strval($xml['fartBox']));
						}
									
						$contrats_reussis = $xml['contractsA']+$xml['contractsB']+$xml['contractsC']+$xml['contractsD']+$xml['contractsE'];
						$contrats_echoues = $xml['failedA']+$xml['failedB']+$xml['failedC']+$xml['failedD']+$xml['failedE'];
						
						if($xml['level'] == 1)
								$contrats_restants = 3-$contrats_reussis;
						else if($xml['level'] == 2)
								$contrats_restants = 9-$contrats_reussis;
						else if($xml['level'] == 3)
								$contrats_restants = 16-$contrats_reussis;
						else
								$contrats_restants = round((1.2 + ($xml['level']-4)*0.4 )*($xml['level']+1)*($xml['level']+1) - $contrats_reussis, 0);
		
		
		
						/*********************************/
						/*    Process de l'inventaire    */
						/*********************************/
						$arInventaire = parseXML($xmlInventaire, "resource");
						$arEquipements= parseXML($xmlInventaire, "item");
						
						foreach( $arInventaire as $objet)
						{
								$nbObjTot += $objet['qty'];
								$totCO2   += $arConvCO2[ $objet['name'] ] * $objet['qty'];
								$totMoney += $arConvMoney[ $objet['name'] ] * $objet['qty'];
						}
						
						foreach( $arEquipements as $objet)
						{
								$nbObjTotEq += $objet['qty'];
								$totMoneyEq += $liste_equipements[ $objet['id'] ]['prix'] * $objet['qty'];
						}
						
						
						/*********************************/
						/*     Process des portails      */
						/*********************************/
						$idx=0;
						$prixOptions = 0;
						foreach ( $xmlPortails as $xmlPort)
						{
								switch (intval($xmlPort['defense']))
								{
									case 0:	$prixDefense =    0; break;
									case 1:	$prixDefense = 1500; break;
									case 2:	$prixDefense = 4500; break;
									case 3:	$prixDefense = 9500; break;
								}
								switch (intval($xmlPort['level']))
								{
									case 1:	$prixNiveau =     0; break;
									case 2:	$prixNiveau =  3000; break;
									case 3:	$prixNiveau =  8000; break;
									case 4:	$prixNiveau = 16000; break;
									case 5:	$prixNiveau = 28000; break;
								}
								$prixOptions = $prixOptions + $prixDefense + $prixNiveau;
						}
		
						$portails = intval($xml['portails']);
						$prixBase = 5000*$portails;
						$nbSup = max(0, $portails - 5);
						$prixPortails = ($nbSup+1)*$nbSup*150 + $prixBase + $prixOptions;
						$prixProchain = (max(0, $portails - 4))*300 + 5000;
		
						/*********************************/
						/*      Process des monstres     */
						/*********************************/
		
						$mntMonstres = 0;
						$nbMonstres = 0;
						foreach($xmlMonstres as $monstre)
						{
								$mntMonstres += intval($monstre['firePrize']);
								$nbMonstres++;
						}
										
										
						$valTotale = $mntMonstres + $prixPortails + $totMoneyEq + $totMoney + intval($xml['gold']);
							
				}
?>