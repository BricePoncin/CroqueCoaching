<style>
.onoffswitch {
    position: relative; width: 170px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}
.onoffswitch-checkbox {
    display: none;
}
.onoffswitch-label {
    display: block; overflow: hidden; cursor: pointer;
    border: 2px solid #999999; border-radius: 0px;
}
.onoffswitch-inner {
    display: block; width: 200%; margin-left: -100%;
    transition: margin 0.3s ease-in 0s;
}
.onoffswitch-inner:before, .onoffswitch-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 26px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    box-sizing: border-box;
    border: 2px solid transparent;
    background-clip: padding-box;
}
.onoffswitch-inner:before {
    content: attr(data-on) " ";
    padding-left: 10px;
    background-color: #2E8DEF; color: #FFFFFF;
}
.onoffswitch-inner:after {
    content: attr(data-off) " ";
    padding-right: 10px;
    background-color: #CCCCCC; color: #333333;
    text-align: right;
}
.onoffswitch-switch {
    display: block; width: 25px; margin: 0px;
    background: #000000;
    position: absolute; top: 0; bottom: 0;
    right: 145px;
    transition: all 0.3s ease-in 0s; 
}
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
    margin-left: 0;
}
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
    right: 0px; 
}
</style>
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
// on sait que le 4 janvier est tout le temps en premi&egrave;re semaine
// cf. fr.wikipedia.org/wiki/ISO...
// donc on part du 4 janvier et on avance de ($semaine-1) semaines
// et on teste si on est un lundi. Si ce n'est pas le cas on recule
// d'un jour jusqu'&agrave; trouver un lundi.
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
    $arObjets[ '600']='Gros cr&acirc;ne usag&eacute;';
    $arObjets[ '800']='Tableau du 16e';
    $arObjets[ '900']='&eacute;norme citrouille bien m&eacute;chante';
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

				/*************************/
				/* Passages de Gros Miam */
				/*************************/
				$stmt  = "SELECT MAX(date) date";
				$stmt .= " FROM cm_dumplog cd";
				$stmt .= " WHERE TYPE = 5";
				$stmt .= " AND syndicate_id = ".$syndic_id; 
				select($stmt, $arLastBrulage);
				$vLastBrulage = $arLastBrulage[0]['date'];

				$arPassagesGM=array();
				$stmt = "SELECT `id`, `syndicate_id`, `user`, date, `date` dt_deb, NULL dt_fin, `concentration` FROM `cm_dumplog`"
				       ." WHERE `syndicate_id` = ".$syndic_id
                       ."  and `type` = 7"
					   ."   AND `date` > '".$vLastBrulage."'"
                       ." ORDER BY date  DESC";
				select($stmt, $arPassagesGM);
				
				$index = count($arPassagesGM) - 1;
				while($index >= 0)
				{
					$arTmp=array();
					$stmt = "SELECT MIN(date) dt_fin"
						   ."  FROM `cm_dumplog`"
						   ." WHERE `syndicate_id` = ".$syndic_id
						   ."   AND `type` = 8"
						   ."   AND date > '".$arPassagesGM[$index]['dt_deb']."'";
					select($stmt, $arTmp);
//echo "<!--".$stmt."-->\n";
					$arPassagesGM[$index]['dt_fin'] = $arTmp[0]['dt_fin'];
					
					$arCombats=array();
					$stmt = "SELECT count(*) as nbdef, sum(`concentration`) as concentration FROM `cm_dumplog`"
				       ." WHERE `syndicate_id` = ".$syndic_id
  				       ."   AND `type` = 6"
				       ."   AND date between '".$arPassagesGM[$index]['dt_deb']."' and '".$arPassagesGM[$index]['dt_fin']."'";
					select($stmt, $arCombats);
echo "<!--".$stmt."-->\n";
					$arPassagesGM[$index]['nbdef'] = $arCombats[0]['nbdef'];
					$arPassagesGM[$index]['concentration'] = $arCombats[0]['concentration'];
					
					$index--;
				}
				
				disconnect();

/*
*
*		Section affichage !
*
*/

?>

		<div style="width:200px; display: inline-block;">
			<div class="onoffswitch">
				<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="swtch_evol" checked onclick="javascript:ouvre_ferme('','evol');">
				<label class="onoffswitch-label" for="swtch_evol">
					<span class="onoffswitch-inner" data-on="&Eacute;volution Globale" data-off="&Eacute;volution Globale"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
		</div>
		<div style="width:200px; display: inline-block;">
			<div class="onoffswitch">
				<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="swtch_obj" onclick="javascript:ouvre_ferme('','objets');">
				<label class="onoffswitch-label" for="swtch_obj">
					<span class="onoffswitch-inner" data-on="Objets" data-off="Objets"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
		</div>
		<div style="width:200px; display: inline-block;">
			<div class="onoffswitch">
				<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="swtch_partic" onclick="javascript:ouvre_ferme('','participation');">
				<label class="onoffswitch-label" for="swtch_partic">
					<span class="onoffswitch-inner" data-on="Top participation" data-off="Top participation"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
		</div>
		<div style="width:200px; display: inline-block;">
			<div class="onoffswitch">
				<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="swtch_top_para" onclick="javascript:ouvre_ferme('','top_paradox_1');">
				<label class="onoffswitch-label" for="swtch_top_para">
					<span class="onoffswitch-inner" data-on="Top paradoxeur" data-off="Top paradoxeur"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
		</div>
		<div style="width:200px; display: inline-block;">
			<div class="onoffswitch">
				<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="swtch_top_recyc" onclick="javascript:ouvre_ferme('','top_recyc');">
				<label class="onoffswitch-label" for="swtch_top_recyc">
					<span class="onoffswitch-inner" data-on="Top recycleur" data-off="Top recycleur"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
		</div>
		<div style="width:200px; display: inline-block;">
			<div class="onoffswitch">
				<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="swtch_top_def" onclick="javascript:ouvre_ferme('','top_def');">
				<label class="onoffswitch-label" for="swtch_top_def">
					<span class="onoffswitch-inner" data-on="Top d&eacute;fenseur" data-off="Top d&eacute;fenseur"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
		</div>
		<div style="width:200px; display: inline-block;">
			<div class="onoffswitch">
				<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="swtch_tas" onclick="javascript:ouvre_ferme('','tas');">
				<label class="onoffswitch-label" for="swtch_tas">
					<span class="onoffswitch-inner" data-on="&Eacute;volution par tas" data-off="&Eacute;volution par tas"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
		</div>
		<div style="width:200px; display: inline-block;">
			<div class="onoffswitch">
				<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="swtch_gm" onclick="javascript:ouvre_ferme('','gm');">
				<label class="onoffswitch-label" for="swtch_gm">
					<span class="onoffswitch-inner" data-on="Passages de GM" data-off="Passages de GM"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
		</div>
		
		<div id="contenu_in" style="height:800px">

					<div id="evol" class="visible">
						<table  class="records">
							<tr><th rowspan="2">Agence</th><th colspan="2"><?PHP echo date("d M Y", strtotime($lundi_4)); ?></th><th colspan="3"><?PHP echo date("d M Y", strtotime($lundi_3)); ?></th><th colspan="3"><?PHP echo date("d M Y", strtotime($lundi_2)); ?></th><th colspan="3"><?PHP echo date("d M Y", strtotime($lundi_1)); ?></th></tr>
							<tr><th>Contrib.(m3)</th><th>D&eacute;fense</th><th>Contrib.(m3)</th><th>Prog.(m3)</th><th>D&eacute;fense</th><th>Contrib.(m3)</th><th>Prog.(m3)</th><th>D&eacute;fense</th><th>Contrib.(m3)</th><th>Prog.(m3)</th><th>D&eacute;fense</th></tr>
<?PHP					
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
?>
						</table>
					</div>
										
					<div id="objets" class="hidden">
						<table  class="records">
							<tr><th colspan=2>Objets ramen&eacute;s de paradoxes</th></tr>
							<tr><th>Type d'objet</th><th class="th75">Objets (nbr)</td></tr>
<?PHP
								$idx=0;		
								foreach( $arObjetsParadox as $line)
								{
										$class=($idx%2==0)?"pair":"impair";
										echo "<tr class=\"$class\"><td>".$arObjets[ $line['quantity'] ]."</td><td>".number_format($line['nbr_obj'], 0, ".", " ")."</td></tr>\n";
										$idx++;
								}
?>
						</table>
					</div>
					
					<div id="participation" class="hidden">
					<table class="records">
					<tr><th colspan=2>Participation au tas d'ordures</th></tr>
					<tr><th>Agence</th><th class="th75">Ordures (m&sup3;)</th></tr>
<?PHP
					$idx=0;
					foreach( $arTopOrdurier as $line)
					{
						$class=($idx%2==0)?"pair":"impair";
						echo "<tr class=\"$class\"><td>".$line['user']."</td><td>".number_format($line['qty'], 3, ".", " ")."</td></tr>\n";
						$idx++;	
					}
?>
						</table>
					</div>
					
					<div id="top_paradox_1" class="hidden">
					<table class="records"> 	
					<tr><th colspan=2>Top 10 paradoxeurs</th></tr>
					<tr><th>Agence</th><th class="th75">Objets (nbr)</th></tr>
<?PHP
					$idx=0;
					foreach( $arTopParadox as $line)
					{
						$class=($idx%2==0)?"pair":"impair";
						if ( $idx<10 )
							echo "<tr class=\"$class\"><td>".$line['user']."</td><td>".number_format($line['nbr'], 0, ".", " ")."</td></tr>\n";
						$idx++;	
					}
?>
					</table> 	
					<table class="records"> 	
					<tr><th colspan=2>Top 10 paradoxeurs (m&sup3;)</th></tr>
					<tr><th>Agence</th><th class="th75">Ordures (m&sup3;)</th></tr>
<?PHP
					$idx=0;
					foreach( $arTopParadoxVol as $line)
					{
						$class=($idx%2==0)?"pair":"impair";
						if ( $idx<10 )
							echo "<tr class=\"$class\"><td>".$line['user']."</td><td>".number_format($line['qty'], 3, ".", " ")."</td></tr>\n";
						$idx++;	
					}
?>
						</table>
					</div>

					<div id="top_recyc" class="hidden">
					<table class="records"> 	
					<tr><th colspan=2>TOP 10 recycleurs</th></tr>
					<tr><th>Agence</th><th class="th75">Ordures (m&sup3;)</th></tr>
<?PHP
					$idx=0;
					foreach( $arTopRecycl as $line)
					{
							$class=($idx%2==0)?"pair":"impair";
							if ( $idx<10 )
									echo "<tr class=\"$class\"><td>".$line['user']."</td><td>".number_format($line['qty'], 3, ".", " ")."</td></tr>\n";
							$idx++;	
					}
?>
						</table>
					</div>
		
					
					<div id="top_def" class="hidden">
					<table class="records"> 	
					<tr><th colspan=3>Top 10 d&eacute;fenseurs du tas</th></tr>
					<tr><th>Agence</th><th class="th75">Concentration</th><th class="th75">Monstres intervenus</th></tr>
<?PHP
					$idx=0;
					foreach( $arTopDefense as $line)
					{
						$class=($idx%2==0)?"pair":"impair";
						if ( $idx<10 )
							echo "<tr class=\"$class\"><td>".$line['user']."</td><td>".number_format($line['concentration'], 0, ".", " ")."</td><td>".number_format($line['mstr'], 0, ".", " ")."</td></tr>\n";
						$idx++;	
					}
?>
						</table>
					</div>
					
					
					<div id="tas" class="hidden">
					<table  class="records">
					<tr><th rowspan="2">Agence</th>
<?
					if ( count($arBrulages) > 0 )
					{
						foreach( $arBrulages as $brul )
						{
							echo "<th colspan=\"2\">Tas br&ucirc;l&eacute; le ".date("d M Y", strtotime($brul['date']))."</th>";
						}
					}
					echo "<th colspan=\"2\">Tas courant</th>";
					echo "</tr>";
					echo "<tr>";
					if ( count($arBrulages) > 0 )
					{
						foreach( $arBrulages as $brul )
						{
							echo "<th>Contrib.(m3)</th><th>D&eacute;fense</th>";
						}
					}
					echo "<th>Contrib.(m3)</th><th>D&eacute;fense</th></tr>";

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
?>
						</table>
					</div>
					
		
					<div id="gm" class="hidden">
					<table  class="records">
					<tr><th colspan="4">Passages de Gros Miam depuis le dernier br&ucirc;lage du tas</th>
					<tr><th class="th75">Date</th><th class="th75">Nbr&nbsp;D&eacute;f.</th><th class="th75">Concentration</th><th class="th75">Intervalle&nbsp;de&nbsp;passages</th></tr>
<?
					$idx=0;
					
					$prec_date = 0;
					$date = 0;

					$index = count($arPassagesGM) - 1;
					while($index >= 0)
					{
						$date = strtotime ( $arPassagesGM[$index]['date'] );
						$diff_date=0;
						if($prec_date != 0)
						{	
							$diff_date = $date - $prec_date;
						}
						$prec_date=$date;
						
						$arPassagesGM[$index]['diff'] = $diff_date;
						
						$index--;
					}

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
?>
						</table>
					</div>
		</div>
	<script type="text/javascript"> 
  $("#contenu ul").idTabs(); 
</script>
	