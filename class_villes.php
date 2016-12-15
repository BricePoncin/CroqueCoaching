<?PHP
	
	include_once("cm_api.inc.php");
	include_once('simple_html_dom.php');  

		function parse_xml_cities($sxml, $tag)
		{
			$arTable=array();
			
				foreach($sxml->children() as $element)
		  	{
		  		if ( $element->getName() == $tag )
		  		{
		  				$objet['id']	    = intval  ( $element['id']      );
		  				$objet['name']	  = utf8_decode(strval  ( $element['name']   ));
							$objet['country'] = utf8_decode(strval  ( $element['country']));
							$objet['lat']     = floatval( $element['lat']     );
							$objet['lon']     = floatval( $element['lon']     );
							$objet['pop']     = intval  ( $element['pop']     );
							$objet['lvl']     = intval  ( $element['lvl']     );
							$objet['tz']      = intval  ( $element['tz']      );
							
							$arTable[ $objet['name'].';'.$objet['country'] ] = $objet;
		  		}
		      parse_xml_cities($element, $tag);
		  	}
		  	
		  	return($arTable);
		 }

	
	function parseLigne( $shtml )
	{
	
			$cases = $shtml->find("td");
			
			$arLine = array();
			/*
			
			<tr class="true">
			<td>1</td>
			<td><a href="/syndicate/5441">WoNdErLaNd</a></td>
			<td>
				<span onmouseover="Tip.show('Détail','&lt;span class=\'ck1\'&gt;1&lt;/span&gt;&lt;span class=\'ck2\'&gt;0&lt;/span&gt;&lt;span class=\'ck3\'&gt;0&lt;/span&gt;&lt;span class=\'ck4\'&gt;3&lt;/span&gt;&lt;span class=\'ck5\'&gt;0&lt;/span&gt; = 8.5&lt;br/&gt;&lt;strong&gt;Pertes: &lt;/strong&gt; 3',null,event);" onmouseout="Tip.hide();">24pts</span>

			</td>
			<td>15.5</td>
		</tr>
			
			*/
			$arLine['pos']      = intval     ($cases[0]->innertext);
			$arLine['syndic']   = utf8_decode($cases[1]->find("a", 0)->innertext);
			$arLine['points']   = floatval   ($cases[2]->innertext);
			$arLine['roublard'] = floatval   ($cases[3]->innertext);
			
			return $arLine;
	}

	function aff_villes()
	{
			$nbUpd = 0;
		
		/*
		  $stmt = " SELECT TIMESTAMPDIFF(MINUTE , upd_guerre5, now( ) ) diff"
				. " FROM cm_syndic_updates"
        . " WHERE syndic = '".$_SESSION['syndic_id']."'";

			select($stmt, $arStmtPortails);
			$ret = 999;
			if( (count($arStmtPortails) == 1) && ($arStmtPortails[0]['diff']!= NULL) )
					$ret = $arStmtPortails[0]['diff'];
*/
			$cities = readXML( "http://www.croquemonster.com/xml/cities.xml" );
			if( substr($cities, 0, 6)== "Erreur" )
			{
					echo $cities;
					exit;
			}
			$arVilles = parse_xml_cities($cities, 'city');
			
			echo "<table border=\"1\">";
			
			$nbvilles = 0;
			
			foreach($arVilles as $ville)
			{
				if($nbvilles > 5) exit;
				$nbvilles++;
				
				echo "<tr><td>".$ville['name']."</td><td>".$ville['country']."</td>";
				
					$url = "http://www.croquemonster.com/syndicate/7627/cityRanking?cid=".$ville['id'];
//echo "<td>".$url."</td>";

					$xml = direct_get( $url, true, 3 );
					if( $xml == false )
					{
							return;
					}
					else
					{
						
						$pattern = '#[\t\n\r]*<tr class="\b(true|false)\b">';
						$pattern.= '[\t\n\r]*<td>([0-9]*)<\/td>';
						$pattern.= '[\t\n\r]*<td><a href="(.*)">(.*)<\/a><\/td>';
						$pattern.= '[\t\n\r]*<td>[\t\n\r]*<span.*>([-0-9]+(\.(\d{1,2}))?)(pts).*[\t\n\r]*</td>';
						$pattern.= '[\t\n\r]*<td>([-0-9]+(\.(\d{1,2}))?)<\/td>';
						$pattern.= '[\t\n\r]*<\/tr>#';
						
						$ret = preg_match_all  ( $pattern , $xml, $matches );
						
						//Print_r( $matches );
						
						for($key=0; $key < 4; $key++ )
						{
							echo "<td>".$matches[4][$key]."</td>";
						}
						
					}
					
					echo "</tr>";
			
			}
			
			echo "</table>";
	}
	


aff_villes();

?>