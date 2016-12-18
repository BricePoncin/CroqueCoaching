<?PHP

    if(isset( $_POST['cnc_id']) )
	{
		$cnc_id = $_POST['cnc_id'];
		$arResult = concours_result($cnc_id);
	}
	else
		$arResult=array();

	if( isset($_POST['send']))
	{
		$arDateDeb = explode ( '/', $_POST['dt_debut'] );
		$dateDeb = $arDateDeb[2].'-'.$arDateDeb[1].'-'.$arDateDeb[0].' 00:00:00';
		$arDateFin = explode ( '/', $_POST['dt_fin'] );
		$dateFin = $arDateFin[2].'-'.$arDateFin[1].'-'.$arDateFin[0].' 23:59:59';
		$desc = $_POST['desc'];
		$type = $_POST['type'];
		$objet= $_POST['objet'];
		
		concours_ajouter($dateDeb, $dateFin, $desc, $type, $objet);
	}
	
	if ( $_SESSION['name'] == 'BlackTom' )
	{
?>

	<h2>Organiser un concours <a id="montrer_cacher" onclick="javascript:afficher_cacher('formulaire');">+</a></h2>
	<div id="formulaire" style="display: none">
		<FORM name="frm_ajout_cnc" action="index.php?pg=concours" method="POST">
			<input type="hidden" name="send" id="send" value="1" />
			
			<div id="intitule">Date de début</div>
			<div>
				<input type="text" id="datepicker1" name="dt_debut" />
				<div id="datepicker1"></div>
			</div>

			<div id="intitule">Date de fin</div>
			<div>
				<input type="text" id="datepicker2" name="dt_fin" />
				<div id="datepicker2"></div>
			</div>

			<div id="intitule">Description</div>
			<div>
				<input type="text" id="desc" name="desc" maxlength="255" size="75" />
			</div>

			<div id="intitule">Type de concours</div>
			<div>
				<select id="type" name="type">
					<option value="0"><===================================></option>
					<option value="1">Plus grosse augmentation de tas par syndic</option>
					<option value="2">Plus grosse augmentation de tas par agence</option>
					<option value="3">Ca, c'est paradoxal !</option>
					<option value="4">Ecolo du mois</option>
					<option value="5">Liste de courses ...</option>
				</select>
			</div>		
			
			<div id="intitule">Objet (pour type "Liste de courses")</div>
			<div>
				<select id="objet" name="objet">
					<option value=   "0"><===================================></option>
					<option value= "200">Banane pr&eacute;-historique</option>
					<option value= "300">Bi&egrave;re Hk</option>
					<option value= "400">Sandwich happy-days BigMarx</option>
					<option value= "500">Clef &agrave; molette industrielle</option>
					<option value= "600">Gros cr&acirc;ne usag&eacute;</option>
					<option value= "800">Tableau du 16e</option>
					<option value= "900">&eacute;norme citrouille bien m&eacute;chante</option>
					<option value="1000">Tuyau flexible</option>
				</select>
			</div>		
					  
			
			<input type="submit" value="Valider" />
		</FORM>
	</div>
<script>
$("#datepicker1").datepicker({ changeMonth: true, changeYear: true, minDate: new Date(1920, 1 - 1, 1)});
</script>
 
<script>
$("#datepicker2").datepicker({ changeMonth: true, changeYear: true, minDate: new Date(1920, 1 - 1, 1)});
</script>
	
	
<?PHP		
	}
?>
<h2>R&eacute;sultats des concours</h2>
<FORM name="frm_resultats" action="index.php?pg=concours" method="POST">
	<input type="hidden" name="cnc_id" id="cnc_id" value="<?PHP echo $cnc_id;?>" />
</FORM>
<?PHP
	
	$arConcours = concours_getList();
	
	echo "<div>";
	echo "	<table>";
	echo "		<thead>";
	echo "			<th>#</th>";
	echo "			<th>Date d&eacute;but</th>";
	echo "			<th>Date fin</th>";
	echo "			<th>Description</th>";
	echo "			<th>Guerre</th>";
	echo "			<th>Type de concours</th>";
	echo "			<th>&nbsp;</th>";
	echo "		</thead>";
	echo "		<tbody>";
	foreach($arConcours as $idx => $line)
	{
			$class=($idx%2==0)?"pair":"impair";
						
			switch($line['cnc_type'])
			{
				case 1: $v_type="Plus grosse augmentation de tas par syndic"; break;
				case 2: $v_type="Plus grosse augmentation de tas par agence"; break;
				case 3: $v_type="Ca, c'est paradoxal !"; break;
				case 4: $v_type="Ecolo du mois"; break;
				case 5: $v_type="Liste de courses ..."; break;
				
			}
			
			echo "<tr class=\"$class\">"
					."<td>".$line['cnc_id']."</td>"
					."<td>".$line['cnc_dt_debut']."</td>"
					."<td>".$line['cnc_dt_fin']."</td>"
					."<td>".$line['cnc_desc']."</td>"
					."<td>".$line['cnc_war']."</td>"
					."<td>".$v_type."</td>"
					."<td><a href=\"#\" onClick=\"affiche_result(".$line['cnc_id'].")\">=></a></td>"
					."</tr>";
		
	}
	echo "		</tbody>";
	echo "	</table>";
	echo "</div>";

	if ( empty($arResult) != true )
	{
		echo "<div><h3>".$arResult['cnc_desc']."</h3>";
		echo "	<table>";
		echo "		<thead>";
		echo "			<th>#</th>";
		echo "			<th>User</th>";
		echo "			<th>Syndic</th>";
		echo "			<th>Décompte</th>";
		echo "			<th>Date retenue</th>";
		echo "		</thead>";
		echo "		<tbody>";
		foreach($arResult as $idx => $line)
		{
			if( "$idx" == "cnc_desc")
				continue;
			
			$class=($idx%2==0)?"pair":"impair";
			
			if ($idx > 9 )
				continue;
			
			echo "<tr class=\"$class\">"
					."<td>".($idx+1)."</td>"
					."<td>".$line['User']."</td>"
					."<td>".$line['Syndic']."</td>"
					."<td>".$line['Decompte']."</td>"
					."<td>".$line['dt_first']."</td>"
					."</tr>";
		}
		echo "		</tbody>";
		echo "	</table>";
		echo "</div>";
	}	
?>
	</tbody>
</table>