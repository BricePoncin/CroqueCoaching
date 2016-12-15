<?PHP
	require_once("sql.php");

function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

		function agences($sxml)
		{
		  //Acc&egrave;s aux enfants de l'&eacute;l&eacute;ment courant
		  foreach($sxml->children() as $element)
		  {
		  		if ( $element->getName() == "agency" )
		  		{
		  				$agency['id']=intval($element['id']);
							$agency['name']=strval($element['name']);
							$agency['score']=intval($element['score']);
							$agency['reputation']=intval($element['reputation']);
							$agency['level']=intval($element['level']);
							$agency['fartBox']=utf8_decode(strval($element['fartBox']));
							
							$arAgencies[] = $agency;
		  		}
		      agences($element);
		  }
			return ($arAgencies);
		}

		/* Coefficients des formules d'estimation de CO2 */
    $a = 0.0119996    ;
    $b = 0.000241019  ;
    $c = 0.176883     ;
    $d = -0.006148    ;
    $e = 33.5189      ;
    $f = -15370.9     ;






		connect();

		if( isset($_SESSION['syndicat']) )
		{
				$url = "http://www.croquemonster.com/api/syndicate.xml?id=".$_SESSION['syndic_id'].";user=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
				$xml = readXML( $url );
				if( substr($xml, 0, 6)== "Erreur" )
				{
						echo $xml;
						exit;
				}
				
				$arAgencies = array();
							
				$arAgencies = agences($xml);    
				
				// Obtient une liste de colonnes
				$level=array();
				foreach ($arAgencies as $key => $row) 
				{
				    $level[$key]  = $row['level'];
				    $reputation[$key] = $row['reputation'];
				}
				
				array_multisort($level, SORT_DESC, $reputation, SORT_DESC, $arAgencies);				
		
				$arTailleTas=array();
				$stmt = "SELECT sum( quantity ) qty"
							 ." FROM cm_dumplog"
							 ." WHERE TYPE in (1,2,3,4)"
							 ." AND syndicate_id =".$_SESSION['syndic_id']
							 ." AND id > (SELECT COALESCE(max(id), 0) FROM cm_dumplog WHERE TYPE=5 AND syndicate_id =".$_SESSION['syndic_id'].")";
				select($stmt, $arTailleTas);
				
				if( isset($arTailleTas[0]['qty']) && $arTailleTas[0]['qty']>=0 )
				{
					
					insert("INSERT INTO cm_dumpsize (syndic_id, date, dumpSize, co2Estimate ) "
					      ."VALUES ('".$_SESSION['syndic_id']."', NOW(), ".$xml['dumpSize'].", ".$xml['dumpCo2'].") "); 
					
						$tas=true;
				}
				else
				{
						$tas=false;	
				}
		
		   disconnect();
		
		
		/*************************/	
		/*   Partie affichage    */
		/*************************/	
		echo "<a href=\"syndic_stats_guerres.php?id=".$_SESSION['syndic_id']."&war=1\"  onclick=\"return monPopNamaNi('syndic_stats_guerres.php?id=".$_SESSION['syndic_id']."&war=1',600,450)\" class=\"lien_bouton\">Stats 1<sup>ière</sup> Guerre Syndicale</a>&nbsp;\n";
		echo "<a href=\"syndic_stats_guerres.php?id=".$_SESSION['syndic_id']."&war=2\"  onclick=\"return monPopNamaNi('syndic_stats_guerres.php?id=".$_SESSION['syndic_id']."&war=2',600,450)\" class=\"lien_bouton\">Stats 2<sup>ième</sup> Guerre Syndicale</a>&nbsp;\n";
		echo "<a href=\"syndic_stats_guerres.php?id=".$_SESSION['syndic_id']."&war=3\"  onclick=\"return monPopNamaNi('syndic_stats_guerres.php?id=".$_SESSION['syndic_id']."&war=3',600,450)\" class=\"lien_bouton\">Stats 3<sup>ième</sup> Guerre Syndicale</a>&nbsp;\n";
		echo "<a href=\"syndic_stats_guerres.php?id=".$_SESSION['syndic_id']."&war=4\"  onclick=\"return monPopNamaNi('syndic_stats_guerres.php?id=".$_SESSION['syndic_id']."&war=4',600,450)\" class=\"lien_bouton\">Stats 4<sup>ième</sup> Guerre Syndicale</a>&nbsp;\n";
		echo "<a href=\"index.php?pg=sw5\" class=\"lien_bouton\">Stats 5<sup>ième</sup> Guerre Syndicale</a>&nbsp;\n";
		
		echo "<br/>\n";
		echo "<div style=\"height: 480px;\">\n"; 
			echo "<div style=\"float: left; width: 420px;\">\n";
				echo "<h1>".utf8_decode($xml['name'])."</h1>\n";

				echo "Existe depuis <strong>".$xml['days']."</strong> jours<br/>";
				echo "SCORE total&nbsp;:&nbsp;".$xml['score']."<br/>";
				echo "PdM du syndicat&nbsp;:&nbsp;".$xml['points']."<br/>";
				
				echo "<h3>La MBL</h3>\n";
				switch($xml['mblLeague'])
				{
				case 0: $league="Marasme";break;
				case 1: $league="Ligue 1";break;
				case 2: $league="Ligue 2";break;
				case 3: $league="Ligue 3";break;
				case 4: $league="Ligue 4";break;
				}
				echo "Votre syndicat est actuellement classé en ".$xml['mblPos']."<sup>e</sup> position de ".$league."<br/><div style=\"margin-top:10px;\"><a class=\"lien_bouton\" href=\"index.php?pg=lst_mbl\">Liste des matches</a></div>";
				
				echo "<h3>Le tas d'ordures</h3>\n";
				if( isset($xml['grosmiam']))
						echo "<strong style=\"color: red;\">GrosMiam ATTAAAAAQUE</strong><br/>";
				else
				   echo "<div style=\"color: green;\">le tas d'ordure est tranquille</div>";
				echo "Score total points de CO&sup2;&nbsp;:&nbsp;".$xml['co2']."<br/>";
				echo "Score rapport&eacute; par le plus gros tas br&ucirc;l&eacute;&nbsp;:&nbsp;".$xml['co2max']."<br/>";
				echo "Score cumul de CO&sup2; produit&nbsp;:&nbsp;".$xml['co2bonus']."<br/>";
				echo "Taille actuelle du tas&nbsp;:&nbsp;".number_format( intval($xml['dumpSize']), 3, ".", " ")."<br/>";
				echo "Estimation du CO&sup2; d&eacute;gag&eacute;&nbsp;:&nbsp;".number_format( intval($xml['dumpCo2']), 0, ".", " ")."<br/>";
				   
				echo "<h3>Les attaques subies</h3>\n";
				echo "Nombre de propagandes subies en ce moment&nbsp;:&nbsp;".$xml['propagandas']."<br/>";
				echo "Nombre d'attaques de t&eacute;l&eacute;-portail subies actuellement&nbsp;:&nbsp;".$xml['portailAttacks']."<br/>";
			echo "</div>\n"; 
		
			echo "<div style=\"margin-left: 450px; margin-top: 15px;\">\n";
				echo "<table>\n";
				echo "<tr><th>Agence</th><th><img src=\"images/score.gif\" /></th><th><img src=\"images/reputation.gif\" /></th><th><img src=\"images/lvl.gif\" /></th><th width=\"150\"><img src=\"images/fartbox.gif\" /></th></tr>";
				$i=0;
				foreach($arAgencies as $agency)
				{
					$lien = "agence.php?id=".$agency['id'];
					//$lien = "javascript:monPopNamaNi('agence?id=".$agency['id']."',600,450);";
					
					$class=($i%2==0)?"pair":"impair";
						echo "\t<tr class=\"$class\">\n";
						echo "\t\t<td><a href=\"$lien\"  onclick=\"return monPopNamaNi(this.href,600,450)\">".$agency['name']."</a></td>\n";
						echo "\t\t<td>".$agency['score']."</td>\n";
						echo "\t\t<td>".$agency['reputation']."</td>\n";
						echo "\t\t<td>".$agency['level']."</td>\n";
						echo "\t\t<td>".$agency['fartBox']."</td>\n";
						echo "\t</tr>\n";
						$i++;
				}
				
				echo "</table>\n";
			echo "</div>\n"; 
		echo "</div>\n"; 	
		  
		  if( $tas )
		  {
		  	?>
				<div style="width: 100%; text-align: center;"><a  class="lien_bouton" href="index.php?pg=tas">Statistiques du tas</a></div>
				<?PHP  	
			}
//			else
			{
				?>
				<div class="information">
						Si vous voulez accéder aux statistiques du tas de votre syndicat, vous devez installer un script <a href="https://addons.mozilla.org/fr/firefox/addon/greasemonkey/">GreaseMonkey</a>.<BR/>
						Ce script a été créé par <a href="http://www.boumbh.com/cm/">Boumbh</a>.<br/>
						Pour l'installer, cliquez <a href="boumbh/cmmonstroapi.user.js">ICI</a>. Une fois le script installé, rendez-vous sur la page d'accueil de <a href="http://www.croquemonster.com/news">Croque Monster</a> où vous verrez une nouvelle icône bleue en haut à droite.<br/>
						En cliquant sur cette icône, une fenêtre s'ouvrira qui vous invitera à mettre à jour le tas d'ordure.<br/>
						La première exécution risque d'être assez longue. Si le script semble bloqué, rechargez la page d'accueil.<br/>
						Si vous avez un problème, n'hésitez pas à me poser vos questions directement par <a href="http://www.croquemonster.com/mail/create?to=BlackTom">MP</a>.<br/>
				</div>	
				<?PHP
			}			
			echo "</div>\n"; 				
		}
		else
		{
			?>
				<h2>Syndicat</h2>
				<p>Vous n'&ecirc;tes actuellement rattach&eacute; &agrave; aucun syndicat... je n'ai donc aucune info &agrave; vous fournir.</p>
			<?PHP
		}
		

?>