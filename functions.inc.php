<?PHP
			function retreiveXML( $api, $params)
			{
					$url = "http://www.croquemonster.com/api/$api.xml?$params";
					$homepage = file_get_contents($url);
					$xml = new SimpleXMLElement($homepage);
			
					return $xml;
			}

?>