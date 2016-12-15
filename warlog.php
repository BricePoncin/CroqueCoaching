<?PHP

		function warlog($sxml)
		{
		  //Acc&egrave;s aux enfants de l'&eacute;l&eacute;ment courant
		  foreach($sxml->children() as $element)
		  {
		  		if ( $element->getName() == "log" )
		  		{
		  				$agency['stolen']=strval($element['stolen']);
							$agency['points']=strval($element['points']);
							$agency['kind']=intval($element['kind']);
							$agency['defSyndId']=intval($element['defSyndId']);
							$agency['defUserId']=intval($element['defUserId']);
							$agency['attSyndId']=intval($element['attSyndId']);
							$agency['attUserId']=intval($element['attUserId']);
							$agency['cityId']=intval($element['cityId']);
							$agency['date']=strval($element['date']);
							
							$arAgencies[] = $agency;
		  		}
		      warlog($element);
		  }
			return ($arAgencies);
		}

		function parseCities($sxml, $tag)
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
							
							$arTable[ $objet['id'] ] = $objet;
		  		}
		      parseCities($element, $tag);
		  	}
		  	
		  	return($arTable);
		}


		if( isset($_SESSION['syndicat']) )
		{
			
				$xml = readXML( "http://www.croquemonster.com/xml/cities.xml" );
				if( substr($xml, 0, 6)== "Erreur" )
				{
						echo $xml;
						exit;
				}
				$arVilles = parseCities($xml, 'city');
		
				$warlogUrl = "http://www.croquemonster.com/api/warlog.xml?defSyndId=".$_SESSION['syndic_id'];
				
				$xml = readXML( $warlogUrl );
				if( substr($xml, 0, 6)== "Erreur" )
				{
						echo $xml;
						exit;
				}
				
				$arWarlogs = array();
							
				$arWarlogs = warlog($xml);
				
				//print_r($arWarlogs);
				
				echo "<table border=\"1\">";
				echo "<tr><th>Date</th><th>Syndic Attaquant</th><th>Attaquant</th><th>Syndic Defenseur</th><th>Defenseur</th><th>Type d'attaque</th><th>Points perdus (def)</th><th>Points volés (att)</th><th>Ville ciblée</th></tr>";
				$totalPerte =0;
				$totalVol =0;
				foreach($arWarlogs as $warlog)
				{
						if( ( !isset($arSyndic[$warlog['attSyndId']]) ) && ( $warlog['attSyndId']>0 ) )
						{
								$url = "http://www.croquemonster.com/api/syndicate.xml?id=".$warlog['attSyndId'];
								$xml = readXML( $url );
								if( substr($xml, 0, 6)== "Erreur" )
										$arSyndic[$warlog['attSyndId']] = "Syndic disparu";
								else
										$arSyndic[$warlog['attSyndId']] = utf8_decode($xml['name']);
						}

						if( ( !isset($arSyndic[$warlog['defSyndId']]) ) && ( $warlog['defSyndId']>0 ) )
						{
								$url = "http://www.croquemonster.com/api/syndicate.xml?id=".$warlog['defSyndId'];
								$xml = readXML( $url );
								if( substr($xml, 0, 6)== "Erreur" )
										$arSyndic[$warlog['defSyndId']] = "Syndic disparu";
								else
										$arSyndic[$warlog['defSyndId']] = utf8_decode($xml['name']);

						}
						if( ( !isset($arUser[$warlog['attUserId']]) ) && ( $warlog['attUserId']>0 ) )
						{
								$url = "http://www.croquemonster.com/api/agency.xml?id=".$warlog['attUserId'];
								$xml = readXML( $url );
								if( substr($xml, 0, 6)== "Erreur" )
										$arUser[$warlog['attUserId']] = "Agence disparue";
								else
										$arUser[$warlog['attUserId']] = utf8_decode($xml['name']);
						}
						
						if( ( !isset($arUser[$warlog['defUserId']]) ) && ( $warlog['defUserId']>0 ) )
						{
								$url = "http://www.croquemonster.com/api/agency.xml?id=".$warlog['defUserId'];
								$xml = readXML( $url );
								if( substr($xml, 0, 6)== "Erreur" )
										$arUser[$warlog['defUserId']] = "Agence disparue";
								else
										$arUser[$warlog['defUserId']] = utf8_decode($xml['name']);
						}
						
						switch ($warlog['kind'])
						{
						case 0: $strKind="racket";      break;
						case 1: $strKind="sabotage";    break;
						case 2: $strKind="diminution";  break;
						case 3: $strKind="vol";         break;
						case 4: $strKind="propagande";  break;
						case 5: $strKind="racket raté";	break;
						}
						
						
						echo "<tr><td>".$warlog['date']."</td><td>".$arSyndic[$warlog['attSyndId']]."</td><td>".$arUser[$warlog['attUserId']]."</td><td>".$arSyndic[$warlog['defSyndId']]."</td><td>".$arUser[$warlog['defUserId']]."</td><td>".$strKind."</td><td>".$warlog['points']."</td><td>".$warlog['stolen']."</td><td>".$arVilles[ $warlog['cityId'] ]['name']." ( ".$arVilles[ $warlog['cityId'] ]['country']." ) "."</td></tr>";
				
						$totalPerte += $warlog['points'];
						$totalVol += $warlog['stolen'];
				}
						echo "<tr><th colspan=\"6\"</th><th>".$totalPerte."</th><th>".$totalVol."</th><th>&nbsp;</th></tr>";
			//	echo "</table>";
				
				
				$warlogUrl = "http://www.croquemonster.com/api/warlog.xml?attSyndId=".$_SESSION['syndic_id'];
				
				$xml = readXML( $warlogUrl );
				if( substr($xml, 0, 6)== "Erreur" )
				{
						echo $xml;
						exit;
				}
				
				$arWarlogs = array();
							
				$arWarlogs = warlog($xml);
				
				//print_r($arWarlogs);
				
				echo "<tr><td colspan=\"9\">&nbsp;</td></tr>";
				echo "<tr><td colspan=\"9\">&nbsp;</td></tr>";
				//echo "<table border=\"1\">";
				echo "<tr><th>Date</th><th>Syndic Attaquant</th><th>Attaquant</th><th>Syndic Defenseur</th><th>Defenseur</th><th>Type d'attaque</th><th>Points perdus (def)</th><th>Points volés (att)</th><th>Ville ciblée</th></tr>";
				$totalPerte =0;
				$totalVol =0;
				foreach($arWarlogs as $warlog)
				{
						if( ( !isset($arSyndic[$warlog['attSyndId']]) ) && ( $warlog['attSyndId']>0 ) )
						{
								$url = "http://www.croquemonster.com/api/syndicate.xml?id=".$warlog['attSyndId'];
								$xml = readXML( $url );
								if( substr($xml, 0, 6)== "Erreur" )
										$arSyndic[$warlog['attSyndId']] = "Syndic disparu";
								else
										$arSyndic[$warlog['attSyndId']] = utf8_decode($xml['name']);

						}

						if( ( !isset($arSyndic[$warlog['defSyndId']]) ) && ( $warlog['defSyndId']>0 ) )
						{
								$url = "http://www.croquemonster.com/api/syndicate.xml?id=".$warlog['defSyndId'];
								$xml = readXML( $url );
								if( substr($xml, 0, 6)== "Erreur" )
									$arSyndic[$warlog['defSyndId']] = "Syndic disparu";
								else
									$arSyndic[$warlog['defSyndId']] = utf8_decode($xml['name']);
						}
						if( ( !isset($arUser[$warlog['attUserId']]) ) && ( $warlog['attUserId']>0 ) )
						{
								$url = "http://www.croquemonster.com/api/agency.xml?id=".$warlog['attUserId'];
								$xml = readXML( $url );
								if( substr($xml, 0, 6)== "Erreur" )
										$arUser[$warlog['attUserId']] = "Agence disparue";
								else
										$arUser[$warlog['attUserId']] = utf8_decode($xml['name']);
						}
						
						if( ( !isset($arUser[$warlog['defUserId']]) ) && ( $warlog['defUserId']>0 ) )
						{
								$url = "http://www.croquemonster.com/api/agency.xml?id=".$warlog['defUserId'];
								$xml = readXML( $url );
								if( substr($xml, 0, 6)== "Erreur" )
										$arUser[$warlog['defUserId']] = "Agence disparue";
								else
										$arUser[$warlog['defUserId']] = utf8_decode($xml['name']);
						}
						
						switch ($warlog['kind'])
						{
						case 0: $strKind="racket";      break;
						case 1: $strKind="sabotage";    break;
						case 2: $strKind="diminution";  break;
						case 3: $strKind="vol";         break;
						case 4: $strKind="propagande";  break;
						case 5: $strKind="racket raté";	break;
						}
						
						
						echo "<tr><td>".$warlog['date']."</td><td>".$arSyndic[$warlog['attSyndId']]."</td><td>".$arUser[$warlog['attUserId']]."</td><td>".$arSyndic[$warlog['defSyndId']]."</td><td>".$arUser[$warlog['defUserId']]."</td><td>".$strKind."</td><td>".$warlog['points']."</td><td>".$warlog['stolen']."</td><td>".$arVilles[ $warlog['cityId'] ]['name']." ( ".$arVilles[ $warlog['cityId'] ]['country']." ) "."</td></tr>";
				
						$totalPerte += $warlog['points'];
						$totalVol += $warlog['stolen'];
				}
				echo "<tr><th colspan=\"6\"</th><th>".$totalPerte."</th><th>".$totalVol."</th><th>&nbsp;</th></tr>";
				echo "</table>";
				
	
		}	


?>