<?PHP
		require_once("session.php");
 		include('simple_html_dom.php');  


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


function direct_get( $p_url )
	{
			
			$username ="BlackTom";  
			$password = "pioupiou"; 
			
			$fp = fopen("cookie.txt", "w");
			fclose($fp);
			$ch = curl_init();
			
			// Login
			curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
			curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.10) Gecko/20100914 Firefox/3.6.10");
			curl_setopt($ch, CURLOPT_TIMEOUT, 40);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			
			curl_setopt($ch, CURLOPT_URL, 'http://www.croquemonster.com/user/login');
			curl_setopt($ch, CURLOPT_REFERER, 'http://www.croquemonster.com/');
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "login=$username&pass=$password&submit=Entrer");
			curl_exec($ch);
	
			curl_setopt($ch, CURLOPT_URL, $p_url);
			curl_setopt($ch, CURLOPT_POST, FALSE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "");
			$result = curl_exec ($ch);
	
			return $result;
	}


			$url = "http://www.croquemonster.com/syndicate/8027/cities?;page=1;sort=pos";
			$xml = direct_get( $url, true );
			
			if( strpos($xml, "<err") !== FALSE )
				echo "<h1>Erreur $xml</h1>";
			else
			{
				$html = new simple_html_dom();
				$html->load($xml); 

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
			
?>