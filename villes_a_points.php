<?PHP
		require_once("session.php");
		include_once("cm_api.inc.php");
 		include_once('simple_html_dom.php');  


function parseLigne( $shtml )
{

		$cases = $shtml->find("td");
		
		$arLine = array();
		
		$arLine['pos']      = intval     ($cases[0]->innertext);
		$arLine['fuseau']   = floatval   ($cases[1]->innertext);
		$arLine['ville']    = utf8_decode($cases[2]->innertext);
		$arLine['pays']     = utf8_decode($cases[3]->innertext);
		
		$arLine['contrat']  = floatval($cases[4]->find("span", 0)->innertext);
		
		
		$arLine['roublard'] = floatval   ($cases[5]->innertext);
		
		return $arLine;
}

			$url = "http://www.croquemonster.com/syndicate/".$_SESSION['syndic_id']."/cities?;page=1;sort=pos";

			$xml = direct_get( $url, true );

			if( $xml == false )
			{
					echo "Désolé, ce service n'est pas disponible du lundi au vendredi de 8h30 à 16h45<BR/>\n";
					exit;
			}
			
			if( strpos($xml, "<err") !== FALSE )
				echo "<h1>Erreur $xml</h1>";
			else
			{
				$html = new simple_html_dom();
				$html->load($xml); 
echo $xml;
				$element = $html->find("table");
				// Table des matches : 2e table
				echo "<table border=\"1\">";
				echo "<tr><td>Ville</td><td>Pays</td><td>Position</td><td>Fuseau</td><td>Contrats</td><td>Roublardise</td><td>Total</td></tr>";
				foreach($element[1]->find("tr") as $key => $line)
				{
					
					if( $key > 0 )
					{
						$arLine = parseLigne( $line );
						echo "<tr>"
									."<td>".$arLine['ville']."</td>"
									."<td>".$arLine['pays']."</td>"
									."<td>".$arLine['pos']."</td>"
									."<td>".$arLine['fuseau']."</td>"
									."<td>".$arLine['contrat']."</td>"
									."<td>".$arLine['roublard']."</td>"
									."<td>".($arLine['contrat']
												 +$arLine['roublard'])."</td>"
								."</tr>\n";
					}
				}
				$tmpPage = $html->find("div[class=currentpage]", 0)->plaintext;
				list($currpage, $maxPages) = explode("/", $tmpPage);
				echo "Nbr pages :  $maxPages<br/>\n";
				
				for($pag=2; $pag<=$maxPages; $pag++)
				{
						$url = "http://www.croquemonster.com/syndicate/8027/cities?;page=".$pag.";sort=pos";
						$xml = direct_get( $url, true );
						
						if( strpos($xml, "<err") !== FALSE )
							echo "<h1>Erreur $xml</h1>";
						else
						{
							$html = new simple_html_dom();
							$html->load($xml); 
			
							$element = $html->find("table");
							// Table des matches : 2e table
							foreach($element[1]->find("tr") as $key => $line)
							{
								
								if( $key > 0 )
								{
										$arLine = parseLigne( $line );
										echo "<tr>"
													."<td>".$arLine['ville']."</td>"
													."<td>".$arLine['pays']."</td>"
													."<td>".$arLine['pos']."</td>"
													."<td>".$arLine['fuseau']."</td>"
													."<td>".$arLine['contrat']."</td>"
													."<td>".$arLine['roublard']."</td>"
													."<td>".($arLine['contrat']
																 +$arLine['roublard'])."</td>"
												."</tr>\n";
								}
							}
						}
					}
	
	
				echo "</table>";
			}