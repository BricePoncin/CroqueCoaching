<?PHP
		require_once("session.php");
		require_once("cm_api.inc.php");
 		include('simple_html_dom.php');  


echo "<a href=\"http://croquecoaching.kappatau.eu/index.php?pg=syndicat\">Retour au syndicat</a><br/>\n";
	
			echo "<div>";
			$url = "http://www.croquemonster.com/mbl/team/".$_SESSION['syndic_id'];
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
				foreach($element[1]->find("tr") as $key => $line)
				{
					if( $key > 0 )
					{
						$cases = $line->find("td");
						
						$lien = $cases[5]->find("a", 0)->href;
						$tmp = explode("/", $lien);
						$lien = $tmp[2];
						
						$isOfficiel = utf8_decode($cases[0]->innertext);
						$equipe_et_score=$cases[1];
						$isEnCours = utf8_decode($cases[2]->innertext);
						$pct_avancement= utf8_decode($cases[3]->innertext);
						$date_fin = utf8_decode($cases[4]->innertext);
						$lien_visionnage=utf8_decode($cases[5]->innertext);
						
						$equipe1 = $equipe_et_score->find("a", 0);
						$equipe2 = $equipe_et_score->find("a", 1);
						$score = utf8_decode($equipe_et_score->find("strong", 0)->innertext);
						
						$equipe1->href="http://www.croquemonster.com".$equipe1->href;
						$equipe1->target="_blank";
						$equipe1->innertext=utf8_decode($equipe1->innertext);
						$equipe2->href="http://www.croquemonster.com".$equipe2->href;
						$equipe2->target="_blank";
						$equipe2->innertext=utf8_decode($equipe2->innertext);
						echo "<tr><td>".$equipe1."</td><td>VS</td><td>".$equipe2."</td><td>".$score."</td><td>".$isEnCours."</td><td>".$date_fin."</td><td><a href=\"index.php?pg=mbl&mid=".$lien."\">y aller</a></td></tr>";
						
						//echo $line;
					}
				}
				echo "</table>";
			}
			echo "</div>";
			
			
?>