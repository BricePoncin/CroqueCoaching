<?PHP
		include_once("cm_api.inc.php");
					
					$nbUpd = 0;
			
					
					//$syndic_id = $_SESSION['syndic_id'];	
					$syndic_id = 5583;	
					
					$pattern_lst = '#[\t\n\r]*<tr class="\b(true|false)\b">';
					$pattern_lst.= '[\t\n\r]*<td>([0-9]*)<\/td>';
					$pattern_lst.= '[\t\n\r]*<td>([-0-9]*)<\/td>';
					$pattern_lst.= '[\t\n\r]*<td><a href="(.*)">(.*)<\/a><\/td>';
					$pattern_lst.= '[\t\n\r]*<td>\b(.*)\b<\/td>';
					$pattern_lst.= '[\t\n\r]*<td>[\t\n\r]*<span.*>([-0-9]+(\.(\d{1,2}))?)(pts).*[\t\n\r]*</td>';
					$pattern_lst.= '[\t\n\r]*<td>([-0-9]+(\.(\d{1,2}))?)<\/td>';
					$pattern_lst.= '[\t\n\r]*<td>([-0-9]+(\.(\d{1,2}))?)<\/td>';
					$pattern_lst.= '[\t\n\r]*<\/tr>#';

					$url = "http://www.croquemonster.com/syndicate/".$syndic_id."/cities?;page=1;sort=pos";
					$xml = direct_get( $url, true, 3 );
					if( $xml == false )
					{
							return;
					}
					else
					{
							$nRet = preg_match_all  ( $pattern_lst , $xml, $matches );
							print_r($matches);
							
							for( $idxLig = 0; $idxLig < count($matches[0]); $idxLig++ )
							{
									
									$nbUpd++;
												
									$stmt = "UPDATE cm_villes_syndic SET "
												. " contrat  = ".$matches[7][$idxLig]
												. ",roublard = ".$matches[11][$idxLig]
												. ",position = ".$matches[2][$idxLig]
												. " WHERE syndic_id = ".$syndic_id
												. "   AND ville = '".utf8_encode($matches[5][$idxLig])."'"
												. "   AND pays = '".utf8_encode($matches[6][$idxLig])."'";
									//$ret_cod = insert( $stmt ); 
									if( $ret_cod != 1 )
									{
											$stmt = "INSERT INTO cm_villes_syndic (syndic_id, ville, pays, fuseau, position, contrat, roublard ) "
														 ." VALUES (".$syndic_id.", '".utf8_encode($matches[5][$idxLig])."', '".utf8_encode($matches[6][$idxLig])."', ".$matches[3][$idxLig].", ".$matches[2][$idxLig].", ".$matches[7][$idxLig].", ".$matches[11][$idxLig].")";
														
											//insert( $stmt ); 
									}
							}
							
							
							$pattern_pag = '#[\t\n\r]*<div class="\bpaginate\b">.*Page 1[/]([0-9]+)<#';
							$nRet = preg_match ( $pattern_pag , $xml, $match_pagi );
							print_r($matches);
							
							$maxPages = $match_pagi[1];
							
							for($pag=2; $pag<=$maxPages; $pag++)
							{
									$url = "http://www.croquemonster.com/syndicate/".$syndic_id."/cities?;page=".$pag.";sort=pos";
									$xml = direct_get( $url, true, 3 );
									
									if( $xml == false )
									{
											return;
									}
									else
									{
											$nRet = preg_match_all  ( $pattern_lst , $xml, $matches );
											print_r($matches);
							
											for( $idxLig = 0; $idxLig < count($matches[0]); $idxLig++ )
											{
																$nbUpd++;
																
																$stmt = "UPDATE cm_villes_syndic SET "
																. " contrat  = ".$matches[7][$idxLig]
																. ",roublard = ".$matches[11][$idxLig]
																. ",position = ".$matches[2][$idxLig]
																. " WHERE syndic_id = ".$syndic_id
																. "   AND ville = '".utf8_encode($matches[5][$idxLig])."'"
																. "   AND pays = '".utf8_encode($matches[6][$idxLig])."'";
		
													//$ret_cod = insert( $stmt ); 
													
													if( $ret_cod != 1 )
													{
															$stmt = "INSERT INTO cm_villes_syndic (syndic_id, ville, pays, fuseau, position, contrat, roublard ) "
																    . " VALUES (".$syndic_id.", '".utf8_encode($matches[5][$idxLig])."', '".utf8_encode($matches[6][$idxLig])."', ".$matches[3][$idxLig].", ".$matches[2][$idxLig].", ".$matches[7][$idxLig].", ".$matches[11][$idxLig].")";
																		
															//insert( $stmt ); 
													}
												
											}
	
									}
							}
					}

		echo $nbUpd." Lignes mises à jour<br/>\n";
?>