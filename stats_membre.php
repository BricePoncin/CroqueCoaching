<?PHP

include_once("sql.php");
include_once("session.php");

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
	


connect();

$syndic_id = $_SESSION['syndic_id'];
//$syndic_id = 8027;

				$arProgression = array();
				$stmt = " SELECT t.user, sum(t.pdm4) pdm4, sum(t.roub4) roub4, sum(t.pdm3) pdm3, sum(t.roub3) roub3, sum(t.pdm2) pdm2, sum(t.roub2) roub2, sum(t.pdm1) pdm1, sum(t.roub1) roub1, sum(t.pdm0) pdm0, sum(t.roub0) roub0"
							 ." FROM ("
							 ." SELECT user, max(pdm) pdm4, 0 pdm3, 0 pdm2, 0 pdm1, 0 pdm0, max(roublardise) roub4, 0 roub3, 0 roub2, 0 roub1, 0 roub0 "
							 ." FROM cm_stats_membre cd"
							 ." WHERE syndic_id = ".$syndic_id 
							 ." AND date <= '".$lundi_4."'"
							 ." GROUP BY cd.user "
				." UNION " 
				."SELECT user, 0, max(pdm), 0, 0, 0, 0, max(roublardise), 0, 0, 0"
							 ." FROM cm_stats_membre cd"
							 ." WHERE syndic_id = ".$syndic_id 
							 ." AND date <= '".$lundi_3."'"
							 ." GROUP BY cd.user "
				." UNION " 
				."SELECT user, 0, 0, max(pdm), 0, 0, 0, 0, max(roublardise), 0, 0"
							 ." FROM cm_stats_membre cd"
							 ." WHERE syndic_id = ".$syndic_id 
							 ." AND date <= '".$lundi_2."'"
							 ." GROUP BY cd.user "
				." UNION " 
				."SELECT user, 0, 0, 0, max(pdm), 0,0, 0, 0, max(roublardise), 0 "
							 ." FROM cm_stats_membre cd"
							 ." WHERE syndic_id = ".$syndic_id 
							 ." AND date <= '".$lundi_1."'"
							 ." GROUP BY cd.user "
				." UNION " 
				."SELECT user, 0, 0, 0, 0, max(pdm),0, 0, 0, 0, max(roublardise) "
							 ." FROM cm_stats_membre cd"
							 ." WHERE syndic_id = ".$syndic_id 
							 ." GROUP BY cd.user "
				." ) t"
				." GROUP BY t.user"			 
				." ORDER BY 1 ASC";
				select($stmt, $arProgression);

				
disconnect();

/*
*
*		Section affichage !
*
*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/> 
		<link rel="shortcut icon" href="favicon.ico" >
		<link rel="icon" type="image/gif" href="animated_favicon1.gif" >
		<link rel="stylesheet" type="text/css" href="class.css" />
		<style>
				
				a {
				  display:block;
				  padding:4px 0;
				  height:24px;
				  width:140px;
				  text-decoration:none!important;
				  margin:0px;
				  margin-left:0;
				  font:12pt Calibri;
				  color:#FFF;
				  background:#AAA;
				}
				a:hover {
				  height:28px;
				  padding:4px 0 6px;
				  background:#0D0D0D;
				  border-bottom:1px solid #181818;
				}
				a.selected {
				  height:30px;
				  padding:4px 0 8px;
				  background:snow;
				  color:#222;
				  font-weight:bold;
				  border-bottom:none;
				}
				
				ul {
						  float: right;
						  margin-top: 16px;
						  margin-right: 16px;
						  margin-bottom: 0px;
						  margin-left: 0px;
						  padding: 0px;
						  list-style: none;
						}
				li {
				  float: left;
				  margin-left: 3px;
				}
				
				.records{
						margin-left:auto;
						margin-right:auto;
						width:100%;
						}
				
		</style>
	</head>
	<body>
		<div id="contenu" style="height:550px">

<?
					echo "<div id=\"evol\">";
					echo "<table  class=\"records\">";
					echo "<tr><th rowspan=\"2\">Agence</th><th colspan=\"2\">".date("d M Y", strtotime($lundi_4))."</th><th colspan=\"2\">".date("d M Y", strtotime($lundi_3))."</th><th colspan=\"2\">".date("d M Y", strtotime($lundi_2))."</th><th colspan=\"2\">".date("d M Y", strtotime($lundi_1))."</th><th colspan=\"2\">Aujourd'hui</th></tr>";
					echo "<tr><th>Pdm</th><th>Roublardise</th><th>Pdm</th><th>Roublardise</th><th>Pdm</th><th>Roublardise</th><th>Pdm</th><th>Roublardise</th><th>Pdm</th><th>Roublardise</th></tr>";
					
					$idx=0;
					foreach($arProgression as $line)
					{
							$class=($idx%2==0)?"pair":"impair";
							$idx++;
							echo "<tr class=\"$class\"><th>".$line['user']."</th>"
									."<td>".$line['pdm4']."</td><td>".$line['roub4']."</td>"
									."<td>".$line['pdm3']."</td><td>".$line['roub3']."</td>"
									."<td>".$line['pdm2']."</td><td>".$line['roub2']."</td>"
									."<td>".$line['pdm1']."</td><td>".$line['roub1']."</td>"
									."<td>".$line['pdm0']."</td><td>".$line['roub0']."</td>"
									."</tr>";
						
					}
					echo "</table>";
					echo "</div>";

?>
	</div>
	</body>
</html>