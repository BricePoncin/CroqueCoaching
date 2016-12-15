<?PHP
	
	include_once("cm_api.inc.php");

	function maj_stats_membres()
	{
		
		  $syndic_id = $_SESSION['syndic_id'];	
					
					
			$stmt = " SELECT TIMESTAMPDIFF(MINUTE , upd_membre5, now( ) ) diff"
				. " FROM cm_syndic_updates"
        . " WHERE syndic = '".$_SESSION['syndic_id']."'";

			select($stmt, $arStmtPortails);
			$ret = 999;
			if( (count($arStmtPortails) == 1) && ($arStmtPortails[0]['diff']!= NULL) )
					$ret = $arStmtPortails[0]['diff'];
			unset($arStmtPortails);
			
			if( $ret >= 60 )	
			{
					$url = "http://www.croquemonster.com/syndicate/".$syndic_id."/membersStats";
					$xml = direct_get( $url, true, 3 );
					if( $xml == false )
					{
							return;
					}
					else
					{
							$pattern = '#[\t\n\r]*<tr class="\b(true|false)\b">';
							$pattern.= '[\t\n\r]*<td><a href="(.*)">(.*)<\/a><\/td>';
							$pattern.= '[\t\n\r]*<td>([-0-9]+\.?\d{0,2})<\/td>';
							$pattern.= '[\t\n\r]*<td>([-0-9]+\.?\d{0,2})<\/td>';
							$pattern.= '[\t\n\r]*<td>([-0-9]+\.?\d{0,2})<\/td>';
							$pattern.= '[\t\n\r]*<td>([-0-9]+\.?\d{0,2})<\/td>';
							$pattern.= '[\t\n\r]*<td>([-0-9]+\.?\d{0,2})<\/td>';
							$pattern.= '[\t\n\r]*<\/tr>#';
			
							$ret = preg_match_all  ( $pattern , $xml, $matches );
										
							//print_r($matches);
										
							for( $idxLig = 0; $idxLig < count($matches[0]); $idxLig++ )
							{
									$nbUpd++;
									
									$stmt = "UPDATE cm_stats_membre SET "
												. " date  = NOW()"
												. ",pdm = ".$matches[4][$idxLig]
												. ",roublardise = ".$matches[5][$idxLig]
												. " WHERE syndic_id = ".$syndic_id
												. "   AND user = '".utf8_encode($matches[3][$idxLig])."'"
												. "   AND jour = CURDATE() ";
		
									$ret_cod = insert( $stmt ); 
									//echo $stmt."<br/>\n";
									if( $ret_cod != 1 )
									{
											$stmt = "INSERT INTO cm_stats_membre (syndic_id, user, date, jour, pdm, roublardise ) "
												    . " VALUES (".$syndic_id.", '".utf8_encode($matches[3][$idxLig])."', NOW(), CURDATE(), ".$matches[4][$idxLig].", ".$matches[5][$idxLig]." )";
														
											insert( $stmt ); 
											//echo $stmt."<br/>\n";
									}
							}
					}
			
					$stmt = " UPDATE cm_syndic_updates SET upd_membre5=NOW() WHERE syndic = '".$_SESSION['syndic_id']."'";
					$ret_cod = insert(	$stmt );
					if( $ret_cod == 0 )
					{
							$stmt = "INSERT INTO cm_syndic_updates (syndic, upd_membre5 ) "
									  . " VALUES ( '".$_SESSION['syndic_id']."', NOW() )";
							insert( $stmt ); 
					}
			}
	}



	function maj_villes_a_point()
	{
			$nbUpd = 0;
		
			$stmt = " SELECT TIMESTAMPDIFF(MINUTE , upd_guerre5, now( ) ) diff"
				. " FROM cm_syndic_updates"
        . " WHERE syndic = '".$_SESSION['syndic_id']."'";

			select($stmt, $arStmtPortails);
			$ret = 999;
			if( (count($arStmtPortails) == 1) && ($arStmtPortails[0]['diff']!= NULL) )
					$ret = $arStmtPortails[0]['diff'];
			unset($arStmtPortails);
			
			if( $ret >= 60 )	
			{
					$pattern_lst = '#[\t\n\r]*<tr class="\b(true|false)\b">';
					$pattern_lst.= '[\t\n\r]*<td>([0-9]*)<\/td>';
					$pattern_lst.= '[\t\n\r]*<td>([-0-9]*)<\/td>';
					$pattern_lst.= '[\t\n\r]*<td><a href="(.*)">(.*)<\/a><\/td>';
					$pattern_lst.= '[\t\n\r]*<td>\b(.*)\b<\/td>';
					$pattern_lst.= '[\t\n\r]*<td>[\t\n\r]*<span.*>([-0-9]+(\.(\d{1,2}))?)(pts).*[\t\n\r]*</td>';
					$pattern_lst.= '[\t\n\r]*<td>([-0-9]+(\.(\d{1,2}))?)<\/td>';
					$pattern_lst.= '[\t\n\r]*<td>([-0-9]+(\.(\d{1,2}))?)<\/td>';
					$pattern_lst.= '[\t\n\r]*<\/tr>#';

					$url = "http://www.croquemonster.com/syndicate/".$_SESSION['syndic_id']."/cities?;page=1;sort=pos";
					$xml = direct_get( $url, true, 3 );
					if( $xml == false )
					{
							return;
					}
					else
					{
							$nRet = preg_match_all  ( $pattern_lst , $xml, $matches );
							
							for( $idxLig = 0; $idxLig < count($matches[0]); $idxLig++ )
							{
									
									$nbUpd++;
												
									$stmt = "UPDATE cm_villes_syndic SET "
												. " contrat  = ".$matches[7][$idxLig]
												. ",roublard = ".$matches[11][$idxLig]
												. ",position = ".$matches[2][$idxLig]
												. " WHERE syndic_id = ".$_SESSION['syndic_id']
												. "   AND ville = '".utf8_encode($matches[5][$idxLig])."'"
												. "   AND pays = '".utf8_encode($matches[6][$idxLig])."'";
									$ret_cod = insert( $stmt ); 
									if( $ret_cod != 1 )
									{
											$stmt = "INSERT INTO cm_villes_syndic (syndic_id, ville, pays, fuseau, position, contrat, roublard ) "
														 ." VALUES (".$_SESSION['syndic_id'].", '".utf8_encode($matches[5][$idxLig])."', '".utf8_encode($matches[6][$idxLig])."', ".$matches[3][$idxLig].", ".$matches[2][$idxLig].", ".$matches[7][$idxLig].", ".$matches[11][$idxLig].")";
														
											insert( $stmt ); 
									}
							}
							
							
							$pattern_pag = '#[\t\n\r]*<div class="\bpaginate\b">.*Page 1[/]([0-9]+)<#';
							$nRet = preg_match ( $pattern_pag , $xml, $match_pagi );
							
							$maxPages = $match_pagi[1];
							
							for($pag=2; $pag<=$maxPages; $pag++)
							{
									$url = "http://www.croquemonster.com/syndicate/".$_SESSION['syndic_id']."/cities?;page=".$pag.";sort=pos";
									$xml = direct_get( $url, true, 3 );
									
									if( $xml == false )
									{
											return;
									}
									else
									{
											$nRet = preg_match_all  ( $pattern_lst , $xml, $matches );
			
											for( $idxLig = 0; $idxLig < count($matches[0]); $idxLig++ )
											{
																$nbUpd++;
																
																									$stmt = "UPDATE cm_villes_syndic SET "
																. " contrat  = ".$matches[7][$idxLig]
																. ",roublard = ".$matches[11][$idxLig]
																. ",position = ".$matches[2][$idxLig]
																. " WHERE syndic_id = ".$_SESSION['syndic_id']
																. "   AND ville = '".utf8_encode($matches[5][$idxLig])."'"
																. "   AND pays = '".utf8_encode($matches[6][$idxLig])."'";
		
													$ret_cod = insert( $stmt ); 
													
													if( $ret_cod != 1 )
													{
															$stmt = "INSERT INTO cm_villes_syndic (syndic_id, ville, pays, fuseau, position, contrat, roublard ) "
																    . " VALUES (".$_SESSION['syndic_id'].", '".utf8_encode($matches[5][$idxLig])."', '".utf8_encode($matches[6][$idxLig])."', ".$matches[3][$idxLig].", ".$matches[2][$idxLig].", ".$matches[7][$idxLig].", ".$matches[11][$idxLig].")";
																		
															insert( $stmt ); 
													}
												
											}
	
									}
							}
					}
					
					$stmt = " UPDATE cm_syndic_updates SET upd_guerre5=NOW() WHERE syndic = '".$_SESSION['syndic_id']."'";
					$ret_cod = insert(	$stmt );
					if( $ret_cod == 0 )
					{
							$stmt = "INSERT INTO cm_syndic_updates (syndic, upd_guerre5 ) "
									  . " VALUES ( '".$_SESSION['syndic_id']."', NOW() )";
							insert( $stmt ); 
					}
			}
}

?>