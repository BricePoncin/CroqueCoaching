<?PHP
		
		include_once("cm_api.inc.php");
		include_once("sql.php");

connect();
			$nbUpd = 0;
			
			//$syndic_id = $_SESSION['syndic_id'];	
			$syndic_id = 5583;	
						
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
								
					print_r($matches);
								
					for( $idxLig = 0; $idxLig < count($matches[0]); $idxLig++ )
					{
							$nbUpd++;
							
							$stmt = "UPDATE cm_stats_membre SET "
										. " date  = NOW()"
										. ",pdm = ".$matches[4][$idxLig]
										. ",roublardise = ".$matches[5][$idxLig]
										. " WHERE syndic_id = ".$syndic_id
										. "   AND user = '".utf8_encode($matches[3][$idxLig])."'"
										. "   AND jour = NOW()";

							$ret_cod = insert( $stmt ); 
							echo $stmt."<br/>\n";
							if( $ret_cod != 1 )
							{
									$stmt = "INSERT INTO cm_stats_membre (syndic_id, user, date, jour, pdm, roublardise ) "
										    . " VALUES (".$syndic_id.", '".utf8_encode($matches[3][$idxLig])."', NOW(), NOW(), ".$matches[4][$idxLig].", ".$matches[5][$idxLig]." )";
												
									insert( $stmt ); 
									echo $stmt."<br/>\n";
							}
												

							
							
//							echo utf8_encode($matches[5][$idxLig])."&nbsp;".utf8_encode($matches[6][$idxLig])."<br/>\n";
							
					}
				
			}
disconnect();
?>
