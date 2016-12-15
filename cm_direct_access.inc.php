<?PHP
		function direct_get( $p_url )
		{
				/*
				$username ="CrokCoaching";  
				$password = "dukuduku"; 
				*/
				$fp = fopen("cookie.txt", "w");
				fclose($fp);
				$ch = curl_init();
				
				// Login
				curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
				curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.10) Gecko/20100914 Firefox/3.6.10");
				curl_setopt($ch, CURLOPT_TIMEOUT, 40);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				
				/*
				curl_setopt($ch, CURLOPT_URL, 'http://www.croquemonster.com/user/login');
				curl_setopt($ch, CURLOPT_REFERER, 'http://www.croquemonster.com/');
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "login=$username&pass=$password&submit=Entrer");
				curl_exec($ch);
				*/
		
				curl_setopt($ch, CURLOPT_URL, $p_url);
				curl_setopt($ch, CURLOPT_POST, FALSE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "");
				$result = curl_exec ($ch);
		
				return $result;
		}
		
?>