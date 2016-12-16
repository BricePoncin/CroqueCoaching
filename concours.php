<?PHP
    if(isset( $_POST['cnc_id']) )
	{
		$cnc_id = $_POST['cnc_id'];
		$arResult = concours_result($cnc_id);
	}
	else
		$arResult=array();


	if ( $_SESSION['name'] == 'BlackTom' )
	{
?>
	<h2>Organiser un concours</h2>
	<form>
	
	</form>
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