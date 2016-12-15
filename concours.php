<?PHP
	



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

<?PHP
	
	$arConcours = concours_getList();
	
	echo "<div>";
	echo "	<table>";
	echo "		<thead>";
	echo "			<th>#</th>";
	echo "			<th>Date d&eacute;but</th>";
	echo "			<th>Date fin</th>";
	echo "			<th>Description</th>";
	echo "			<th>Type de concours</th>";
	echo "		</thead>";
	echo "		<tbody>";
	foreach($arConcours as $line)
	{
			$class=($idx%2==0)?"pair":"impair";
			$idx++;
			echo "<tr class=\"$class\">"
					."<td>".$line['cnc_id']."</td>"
					."<td>".$line['cnc_dt_debut']."</td>"
					."<td>".$line['cnc_dt_fin']."</td>"
					."<td>".$line['cnc_desc']."</td>"
					."<td>".$line['cnc_type']."</td>"
					."</tr>";
		
	}
	echo "		</tbody>";
	echo "	</table>";
	echo "</div>";

?>
	</tbody>
</table>