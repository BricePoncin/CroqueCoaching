<?PHP

include_once("sql.php");
include_once("session.php");

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

	function time_ago($date1, $date2) 
	{
		  $days = abs(floor(($date1-$date2)/86400));
		    if ($days > 1)  $timepast = $days."&nbsp;days";
		    if ($days == 1)  $timepast = $days."&nbsp;day";
		  
		  $reste =  (($date1-$date2)/86400) -  floor(($date1-$date2)/86400);
		    
		  $hours = abs(floor($reste/3600));
		    if ($hours > 1) $timepast .= "&nbsp;".$hours."&nbsp;hours";
		    if ($hours == 1) $timepast .= "&nbsp;".$hours."&nbsp;hour";
		  $minutes = abs(ceil($reste/60))-($hours*60);
		    if ($minutes > 1) $timepast .= "&nbsp;".$minutes."&nbsp;minutes";
		    if ($minutes == 1) $timepast .= "&nbsp;".$minutes."&nbsp;minute";
		  return $timepast;
	}

//########## get_semaine() ###########
// Fonction retournant les dates de la
// semaine en cours :
// tableau-de-retour[0] : date du lundi
// tableau-de-retour[1] : date du mardi
// ...
// Les dates sont au format
// AAAA-MM-JJ

function get_semaine($semaine,$annee)
{
// on sait que le 4 janvier est tout le temps en première semaine
// cf. fr.wikipedia.org/wiki/ISO...
// donc on part du 4 janvier et on avance de ($semaine-1) semaines
// et on teste si on est un lundi. Si ce n'est pas le cas on recule
// d'un jour jusqu'à trouver un lundi.
$date_depart = 4 ;
while (date("w",mktime(0,0,0,01,($date_depart+($semaine-1)*7),$annee)) != 1)
$date_depart-- ;

for ($a=0;$a<7;$a++)
$dateSemaine[$a] = date("Y-m-d",mktime(0,0,0,01,($date_depart+$a+($semaine-1)*7),$annee));

return $dateSemaine;
}

 $n_semaine = date('W');
 $n_annee = date('Y');
  
 $tmp = get_semaine($n_semaine,$n_annee);
 $lundi_1 = $tmp[0];

	$tmp = get_semaine($n_semaine-1,$n_annee);
 	$lundi_2 = $tmp[0];

	$tmp = get_semaine($n_semaine-2,$n_annee);
 	$lundi_3 = $tmp[0];
 	
 	$tmp = get_semaine($n_semaine-3,$n_annee);
 	$lundi_4 = $tmp[0];
	

		$arObjets[ '200']='Banane pr&eacute;-historique';
    $arObjets[ '300']='Bi&egrave;re Hk';
    $arObjets[ '400']='Sandwich happy-days BigMarx';
    $arObjets[ '500']='Clef &agrave; molette industrielle';
    $arObjets[ '600']='Gros crâne usagé';
    $arObjets[ '800']='Tableau du 16e';
    $arObjets[ '900']='Énorme citrouille bien méchante';
    $arObjets['1000']='Tuyau flexible';


connect();

$syndic_id = $_SESSION['syndic_id'];
//$syndic_id = 5441;

		$url = "http://www.croquemonster.com/api/syndicate.xml?id=".$_SESSION['syndic_id'].";user=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xml = readXML( $url );
		if( substr($xml, 0, 6)== "Erreur" )
		{
				echo $xml;
				exit;
		}
		
		$arAgencies = array();
					
		$arAgencies = agences($xml);   

		$lstAgences = "";
		foreach ( $arAgencies as $agences )
		{
			if ($lstAgences == "")
					$lstAgences .= "'".$agences['name']."'";
			else
					$lstAgences .= ", '".$agences['name']."'";
		}
$stmt  = "SELECT user, date, quantity, co2";
$stmt .= " FROM cm_dumplog cd";
$stmt .= " WHERE TYPE = 5";
$stmt .= " AND syndicate_id = ".$syndic_id; 
select($stmt, $arBrulages);

//print_r($arBrulages);

for ( $idx = 0; $idx < count($arBrulages); $idx++ )
{

		$stmt  = "SELECT user, sum(COALESCE((quantity/1000), 0)) qty, sum( if(COALESCE(concentration, 0)=0,0,1) ) def ";
		$stmt .= " FROM cm_dumplog cd";
		$stmt .= " WHERE TYPE in (1,2,3,4, 6)";
		$stmt .= " AND syndicate_id = ".$syndic_id; 
		$stmt .= " AND user in (".$lstAgences.")";
		if ( $idx == 0 )
				$stmt .= " AND `date` <= '".$arBrulages[$idx]['date']."'";
		else
				$stmt .= " AND `date` > '".$arBrulages[$idx-1]['date']."' AND date <= '".$arBrulages[$idx]['date']."'";
		$stmt .= " GROUP BY cd.user ";

		select($stmt, $brulage[$idx]);

// echo "$stmt<br/>\n";
}
	
		$stmt  = "SELECT user, sum(COALESCE((quantity/1000), 0)) qty, sum( if(COALESCE(concentration, 0)=0,0,1) ) def ";
		$stmt .= " FROM cm_dumplog cd";
		$stmt .= " WHERE TYPE in (1,2,3,4, 6)";
		$stmt .= " AND syndicate_id = ".$syndic_id; 
		$stmt .= " AND date > '".$arBrulages[$idx-1]['date']."'";
		$stmt .= " AND user in (".$lstAgences.")";
		$stmt .= " GROUP BY cd.user ";
	
		select($stmt, $brulage[$idx]);
// echo "$stmt<br/>\n";

$arGlobBrulages = array();
for( $i = 0; $i < count($brulage); $i++)
{
		for( $j = 0; $j < count($brulage[$i]); $j++ )
				$arGlobBrulages[ $brulage[$i][$j]['user'] ][$i] = array( 'qty' => $brulage[$i][$j]['qty'], 'def' => $brulage[$i][$j]['def'] );
}
//print_r($arGlobBrulages);
//print_r($brulage);




				$arProgression = array();
				$stmt = " SELECT t.user, sum(t.qty4) qty4, sum(t.def4) def4, sum(t.qty3) qty3, sum(t.def3) def3, sum(t.qty2) qty2, sum(t.def2) def2, sum(t.qty1) qty1, sum(t.def1) def1"
							 ." FROM ("
							 ." SELECT user, sum(COALESCE((quantity/1000), 0)) qty4, 0 qty3, 0 qty2, 0 qty1, sum( if(COALESCE(concentration, 0)=0,0,1) ) def4, 0 def3, 0 def2, 0 def1 "
							 ." FROM cm_dumplog cd"
							 ." WHERE TYPE in (1,2,3,4, 6)"
							 ." AND syndicate_id = ".$syndic_id 
							 ." AND date <= '".$lundi_4."'"
							 ." GROUP BY cd.user "
				." UNION " 
				."SELECT user, 0, sum(COALESCE((quantity/1000), 0)), 0, 0, 0, sum( if(COALESCE(concentration, 0)=0,0,1) ), 0, 0"
							 ." FROM cm_dumplog cd"
							 ." WHERE TYPE in (1,2,3,4, 6)"
							 ." AND syndicate_id = ".$syndic_id 
							 ." AND date <= '".$lundi_3."'"
							 ." GROUP BY cd.user "
				." UNION " 
				."SELECT user, 0, 0, sum(COALESCE((quantity/1000), 0)), 0, 0, 0, sum( if(COALESCE(concentration, 0)=0,0,1) ), 0"
							 ." FROM cm_dumplog cd"
							 ." WHERE TYPE in (1,2,3,4, 6)"
							 ." AND syndicate_id = ".$syndic_id 
							 ." AND date <= '".$lundi_2."'"
							 ." GROUP BY cd.user "
				." UNION " 
				."SELECT user, 0, 0, 0, sum(COALESCE((quantity/1000), 0)),0, 0, 0, sum( if(COALESCE(concentration, 0)=0,0,1) ) "
							 ." FROM cm_dumplog cd"
							 ." WHERE TYPE in (1,2,3,4, 6)"
							 ." AND syndicate_id = ".$syndic_id 
							 ." AND date <= '".$lundi_1."'"
							 ." GROUP BY cd.user "
				." ) t"
				." WHERE user in (".$lstAgences.")"
				." GROUP BY t.user"			 
				." ORDER BY 1 ASC";
				select($stmt, $arProgression);

				$stmt = "SELECT quantity, count( quantity ) nbr_obj"
							 ." FROM cm_dumplog"
							 ." WHERE TYPE =1"
							 ." AND syndicate_id =".$syndic_id
							 ." GROUP BY quantity";
				select($stmt, $arObjetsParadox);
				$stmt = "SELECT user, count( user ) nbr"
							 ." FROM cm_dumplog"
							 ." WHERE TYPE =1"
							 ." AND syndicate_id =".$syndic_id
							 ." AND user in (".$lstAgences.")"
							 ." GROUP BY user"
							 ." ORDER BY 2 DESC";
				select($stmt, $arTopParadox);
				$stmt = "SELECT user, (sum( quantity )/1000) qty"
							 ." FROM cm_dumplog"
							 ." WHERE TYPE =1"
							 ." AND syndicate_id =".$syndic_id
							 ." AND user in (".$lstAgences.")"
							 ." GROUP BY user"
							 ." ORDER BY 2 DESC";
				select($stmt, $arTopParadoxVol);
				$stmt = "SELECT user, (sum( quantity )/1000) qty"
							 ." FROM cm_dumplog"
							 ." WHERE TYPE =2"
							 ." AND syndicate_id =".$syndic_id
							 ." AND user in (".$lstAgences.")"
							 ." GROUP BY user"
							 ." ORDER BY 2 DESC";
				select($stmt, $arTopRecycl);
				$arTailleTas=array();
				$stmt = "SELECT sum( quantity ) qty"
							 ." FROM cm_dumplog"
							 ." WHERE TYPE in (1,2,3,4)"
							 ." AND syndicate_id =".$syndic_id
							 ." AND id > (SELECT COALESCE(max(id), 0) FROM cm_dumplog WHERE TYPE=5 AND syndicate_id =".$syndic_id.")";
				select($stmt, $arTailleTas);

				$arTopOrdurier = array();
				$stmt = "SELECT user, (sum( quantity )/1000) qty"
							 ." FROM cm_dumplog"
							 ." WHERE TYPE in (1,2,3,4)"
							 ." AND syndicate_id =".$syndic_id
							 ." AND user in (".$lstAgences.")"
							 ." GROUP BY user"
							 ." ORDER BY 2 DESC";
				select($stmt, $arTopOrdurier);
				
				$arTopDefense=array();
				$stmt = "SELECT user, sum( concentration ) concentration, count(concentration) mstr"
							 ." FROM cm_dumplog"
							 ." WHERE TYPE = 6"
							 ." AND syndicate_id =".$syndic_id
							 ." AND user in (".$lstAgences.")"
							 ." GROUP BY user"
							 ." ORDER BY 2 ASC";
				select($stmt, $arTopDefense);

				$stmt  = "SELECT MAX(date) date";
				$stmt .= " FROM cm_dumplog cd";
				$stmt .= " WHERE TYPE = 5";
				$stmt .= " AND syndicate_id = ".$syndic_id; 
				select($stmt, $arLastBrulage);
				$vLastBrulage = $arLastBrulage[0]['date'];

				$arPassagesGM=array();
				/*
				$stmt = "SELECT min(`date`) date, count(*) nbdef, sum(concentration) concentration, cd2.id id"
							 ." FROM cm_dumplog cd, (SELECT id FROM cm_dumplog WHERE syndicate_id=".$syndic_id." AND type=7) cd2"
							 ." WHERE syndicate_id=".$syndic_id
							 ."   AND type=6"
							 ."   AND cd.id BETWEEN cd2.id AND (SELECT min(cd3.id) FROM cm_dumplog cd3 WHERE syndicate_id=".$syndic_id." AND type=8 AND cd3.id>cd2.id)"
							 ."   AND `date` > '".$vLastBrulage."'"
							 ." GROUP BY cd2.id"
							 ." ORDER BY cd2.id asc";
				select($stmt, $arPassagesGM);
				*/
disconnect();

/*
*
*		Section affichage !
*
*/

?>
</div>	
		<div id="navigation" style="color: #FF6533;">
				<ul class="idTabs"> 
				  <li><a class="selected" href="#evol">Évolution globale</a></li> 
				  <li><a href="#objets">Objets</a></li> 
				  <li><a href="#participation">Top participation</a></li> 
				  <li><a href="#top_paradox_1">Top paradoxeur</a></li> 
				  <!--<li><a href="#top_paradox_2">Top Paradoxeur 2</a></li> -->
				  <li><a href="#top_recyc">Top recycleur</a></li> 
				  <li><a href="#top_def">Top défenseur</a></li> 
				  <li><a href="#tas">Évolution par tas</a></li> 
				  <li><a href="#gm">Passages de GrosMiam</a></li> 
				  
				</ul>
		</div>	
	<div id="contenu" style="height:800px">
<?
					echo "<div id=\"evol\">";
					echo "<table  class=\"records\">";
					echo "<tr><th rowspan=\"2\">Agence</th><th colspan=\"2\">".date("d M Y", strtotime($lundi_4))."</th><th colspan=\"3\">".date("d M Y", strtotime($lundi_3))."</th><th colspan=\"3\">".date("d M Y", strtotime($lundi_2))."</th><th colspan=\"3\">".date("d M Y", strtotime($lundi_1))."</th></tr>";
					echo "<tr><th>Contrib.(m3)</th><th>Défense</th><th>Contrib.(m3)</th><th>Prog.(m3)</th><th>Défense</th><th>Contrib.(m3)</th><th>Prog.(m3)</th><th>Défense</th><th>Contrib.(m3)</th><th>Prog.(m3)</th><th>Défense</th></tr>";
					
					$idx=0;
					foreach($arProgression as $line)
					{
							$class=($idx%2==0)?"pair":"impair";
							$idx++;
							echo "<tr class=\"$class\"><th>".$line['user']."</th>"
									."<td>".$line['qty4']."</td><td>".$line['def4']."</td>"
									."<td>".$line['qty3']."</td><td>".($line['qty3']-$line['qty4'])."</td><td>".$line['def3']."</td>"
									."<td>".$line['qty2']."</td><td>".($line['qty2']-$line['qty3'])."</td><td>".$line['def2']."</td>"
									."<td>".$line['qty1']."</td><td>".($line['qty1']-$line['qty2'])."</td><td>".$line['def1']."</td>"
									."</tr>";
						
					}
					echo "</table>";
					echo "</div>";
										
					echo "<div id=\"objets\">";
					echo "<table  class=\"records\">\n"; 	
					echo "<tr><th colspan=2>Objets ramen&eacute;s de paradoxes</th></tr>\n";
							echo "<tr><th>Type d'objet</th><th class=\"th75\">Objets (nbr)</td></tr>\n";
					$idx=0;		
					foreach( $arObjetsParadox as $line)
					{
							$class=($idx%2==0)?"pair":"impair";
							echo "<tr class=\"$class\"><td>".$arObjets[ $line['quantity'] ]."</td><td>".number_format($line['nbr_obj'], 0, ".", " ")."</td></tr>\n";
							$idx++;
					}
					echo "</table>\n"; 	
					echo "</div>";
					
					echo "<div id=\"participation\">";
					echo "<table class=\"records\">\n"; 	
					echo "<tr><th colspan=2>Participation au tas d'ordures</th></tr>\n";
					echo "<tr><th>Agence</th><th class=\"th75\">Ordures (m&sup3;)</th></tr>\n";
					$idx=0;
					foreach( $arTopOrdurier as $line)
					{
						$class=($idx%2==0)?"pair":"impair";
						echo "<tr class=\"$class\"><td>".$line['user']."</td><td>".number_format($line['qty'], 3, ".", " ")."</td></tr>\n";
						$idx++;	
					}
					echo "</table>\n"; 	
					echo "</div>";
					
					echo "<div id=\"top_paradox_1\">";
					echo "<table class=\"records\">\n"; 	
					echo "<tr><th colspan=2>Top 10 paradoxeurs</th></tr>\n";
					echo "<tr><th>Agence</th><th class=\"th75\">Objets (nbr)</th></tr>\n";
					$idx=0;
					foreach( $arTopParadox as $line)
					{
						$class=($idx%2==0)?"pair":"impair";
						if ( $idx<10 )
							echo "<tr class=\"$class\"><td>".$line['user']."</td><td>".number_format($line['nbr'], 0, ".", " ")."</td></tr>\n";
						$idx++;	
					}
					echo "</table>\n"; 	
					/*echo "</div>";
					
					echo "<div id=\"top_paradox_2\">";*/
					echo "<table class=\"records\">\n"; 	
					echo "<tr><th colspan=2>Top 10 paradoxeurs (m&sup3;)</th></tr>\n";
					echo "<tr><th>Agence</th><th class=\"th75\">Ordures (m&sup3;)</th></tr>\n";
					$idx=0;
					foreach( $arTopParadoxVol as $line)
					{
						$class=($idx%2==0)?"pair":"impair";
						if ( $idx<10 )
							echo "<tr class=\"$class\"><td>".$line['user']."</td><td>".number_format($line['qty'], 3, ".", " ")."</td></tr>\n";
						$idx++;	
					}
					echo "</table>\n"; 	
					echo "</div>";
		
					
					echo "<div id=\"top_recyc\">";
					echo "<table class=\"records\">\n"; 	
					echo "<tr><th colspan=2>TOP 10 recycleurs</th></tr>\n";
					echo "<tr><th>Agence</th><th class=\"th75\">Ordures (m&sup3;)</th></tr>\n";
					$idx=0;
					foreach( $arTopRecycl as $line)
					{
							$class=($idx%2==0)?"pair":"impair";
							if ( $idx<10 )
									echo "<tr class=\"$class\"><td>".$line['user']."</td><td>".number_format($line['qty'], 3, ".", " ")."</td></tr>\n";
							$idx++;	
					}
					echo "</table>\n"; 	
					echo "</div>";
		
					
					echo "<div id=\"top_def\">";
					echo "<table class=\"records\">\n"; 	
					echo "<tr><th colspan=3>Top 10 défenseurs du tas</th></tr>\n";
					echo "<tr><th>Agence</th><th class=\"th75\">Concentration</th><th class=\"th75\">Monstres intervenus</th></tr>\n";
					$idx=0;
					foreach( $arTopDefense as $line)
					{
						$class=($idx%2==0)?"pair":"impair";
						if ( $idx<10 )
							echo "<tr class=\"$class\"><td>".$line['user']."</td><td>".number_format($line['concentration'], 0, ".", " ")."</td><td>".number_format($line['mstr'], 0, ".", " ")."</td></tr>\n";
						$idx++;	
					}
					echo "</table>\n"; 	
					echo "</div>";
					
					
					echo "<div id=\"tas\">";
					echo "<table  class=\"records\">";
					echo "<tr><th rowspan=\"2\">Agence</th>";
					if ( count($arBrulages) > 0 )
					{
						foreach( $arBrulages as $brul )
						{
							echo "<th colspan=\"2\">Tas brûlé le ".date("d M Y", strtotime($brul['date']))."</th>";
						}
					}
					echo "<th colspan=\"2\">Tas courant</th>";
					echo "</tr>";
					echo "<tr>";
					if ( count($arBrulages) > 0 )
					{
						foreach( $arBrulages as $brul )
						{
							echo "<th>Contrib.(m3)</th><th>Défense</th>";
						}
					}
					echo "<th>Contrib.(m3)</th><th>Défense</th></tr>";
					
					$idx=0;
					foreach($arGlobBrulages as $user => $line)
					{
							$class=($idx%2==0)?"pair":"impair";
							$idx++;
							
							echo "<tr class=\"$class\"><th>".$user."</th>";
							//foreach( $line as $tas )
							for($j = 0; $j<= count($arBrulages); $j++)
							{
								if( isset( $arGlobBrulages[$user][$j]) )
									echo "<td>".$arGlobBrulages[$user][$j]['qty']."</td><td>".$arGlobBrulages[$user][$j]['def']."</td>";
								else	
									echo "<td>&nbsp;</td><td>&nbsp;</td>";
							}
							echo "</tr>";
					}
					echo "</table>";
					echo "</div>";
					
		
					echo "<div id=\"gm\">";
					echo "<table  class=\"records\">";
					echo "<tr><th colspan=\"4\">Passages de Gros Miam depuis le dernier brûlage du tas</th>";
					echo "<tr><th class=\"th75\">Date</th><th class=\"th75\">Nbr&nbsp;Déf.</th><th class=\"th75\">Concentration</th><th class=\"th75\">Intervalle&nbsp;de&nbsp;passages</th></tr>\n";
					$idx=0;
					
					$prec_date = 0;
					$date = 0;
					echo "<h2>Cette donnée est trop consommatrice pour les serveurs de mon hébergeur, je suis donc contraint de ne plus la fournir. Désolé !</h2>"; 
					/*
					foreach($arPassagesGM as &$line)
					{
							$date = strtotime ( $line['date'] );
							$diff_date=0;
							if($prec_date != 0)
							{	$diff_date = $date - $prec_date;
								//$diff_date = time_ago($date, $prec_date);
							}
							$prec_date=$date;
							
							$line['diff'] = $diff_date;
					}
					$arPassagesGM = array_reverse($arPassagesGM);
					
					foreach($arPassagesGM as $line)
					{
						$diff_date = $line['diff'];
						$strDiff = "";
						if($diff_date > 0)
						{
							$d = floor($diff_date/86400);
							$h = floor( ( $diff_date - $d*86400 ) / 3600 );
							$m = floor( ( $diff_date - $d*86400 - $h*3600 ) /60 );
							
							$strDiff = $d."&nbsp;jour(s)&nbsp;".$h."&nbsp;heure(s)&nbsp;".$m."&nbsp;minute(s)";
						}
							$class=($idx%2==0)?"pair":"impair";
							$idx++;
							
							echo "<tr class=\"$class\"><td>".str_replace(" ", "&nbsp;",$line['date'])."</td>";
							
							echo "<td>".$line['nbdef']."</td><td>".$line['concentration']."</td><td>".$strDiff ."</td>";
							
							
							echo "</tr>";
					}
					*/
					echo "</table>";
					echo "</div>";
?>
	<script type="text/javascript"> 
  $("#contenu ul").idTabs(); 
</script>
	