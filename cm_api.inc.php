<?PHP

		function readXML( $url )
		{
				$homepage = file_get_contents($url);
				$xml = new SimpleXMLElement($homepage);
				
				if ( isset($xml['error']) &&  $xml['error']=="Unknown user" )
						return "Erreur : Utilisateur inconnu<br/>\n";
				else if ( isset($xml['error']) &&  $xml['error']=="Unknown syndicate" )
						return "Erreur : Ce syndicat n'existe pas (ou plus)<br/>\n";
				else if ( isset($xml['error']) &&  $xml['error']=="Bad password" )
						return "Erreur : Mauvais mot de passe API<br/>\n";
				else if ( strcmp($xml, '<locked/>') == 0 )
				    return "Erreur : Un fuseau est en cours d'ouverture ... patientez svp<BR/>\n";
				else if ( isset($xml['error']) &&  $xml['error']=="API access disabled" )
				    return "Erreur : L'accès aux API CroqueMonster est temporairement interrompu<BR/>\n";
				else if ( isset($xml['error']) &&  strlen($xml['error'])>0 )
				    return "Erreur : erreur inconnue \"".$xml['error']."\"<BR/>\n";
				else 
						return $xml;
				    
		}

		function direct_get( $p_url, $bLogin=false, $logLvl=1 )
		{
				$username ="Cradinc";  
				$password = "dukuduku"; 
				
				$fp = fopen("cookie.txt", "w");
				fclose($fp);
				$ch = curl_init();
				
				// Login
				curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
				curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.10) Gecko/20100914 Firefox/3.6.10");
				curl_setopt($ch, CURLOPT_TIMEOUT, 40);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				
				
				if($bLogin)
				{
						curl_setopt($ch, CURLOPT_URL, 'http://www.croquemonster.com/user/login');
						curl_setopt($ch, CURLOPT_REFERER, 'http://www.croquemonster.com/');
						curl_setopt($ch, CURLOPT_POST, TRUE);
						curl_setopt($ch, CURLOPT_POSTFIELDS, "login=$username&pass=$password&submit=Entrer");
						curl_exec($ch);
				}				
		
				curl_setopt($ch, CURLOPT_URL, $p_url);
				curl_setopt($ch, CURLOPT_POST, FALSE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "");
				$result = curl_exec ($ch);
		
				return $result;
		}
		
		function distance($p_Lat1, $p_Lon1, $p_Lat2, $p_Lon2 )
		{
				$a = pow(($p_Lat2 - $p_Lat1), 2);
				$b = pow(($p_Lon2 - $p_Lon1), 2);
				
				$distance = sqrt( $a + $b );
				
				return $distance;
		}
		
		function is_accessible($p_Lat1, $p_Lon1, $p_Lat2, $p_Lon2, $port_lvl )
		{
				$a = pow(($p_Lat2 - $p_Lat1), 2);
				$b = pow(($p_Lon2 - $p_Lon1), 2);
				
				$distance = sqrt( $a + $b );
				
				if($distance <= $port_lvl+1)
					return true;
				else
					return false;
		}
?> 