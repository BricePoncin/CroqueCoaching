<html>
  <body>
<?PHP

		function fusion($p_lvl_accre, $p_stat1, $p_stat2)
		{
			$v_stat_sup = max($p_stat1, $p_stat2);
			$v_stat_inf = min($p_stat1, $p_stat2);
			
//			echo "$p_stat1, $p_stat2 => Stat inf = ".$v_stat_inf." Stat sup = ".$v_stat_sup."<BR/>\n";
			
			if ( $v_stat_inf >= 0.6*$v_stat_sup )
			{
					$v_stat_res = min($v_stat_sup + 1, $p_lvl_accre+2);
//					echo "Cas 1 : $v_stat_sup + 1<BR/>\n";
			}
			else
			{
					$v_stat_res = min(ceil((($v_stat_inf + $v_stat_sup)/2)+1), $p_lvl_accre+2) ."~".min($v_stat_sup + 1, $p_lvl_accre+2);
//					echo "Cas 2 : entre ".ceil((($v_stat_inf + $v_stat_sup)/2)+1)." et $v_stat_sup + 1<BR/>\n";
			}		
			return $v_stat_res;	
		}
		
		$v_lvl_accre = "";

		$v_Sad_1 = "";
		$v_Lai_1 = "";
		$v_For_1 = "";
		$v_Gou_1 = "";
		$v_Con_1 = "";
		$v_Com_1 = "";
		$v_End_1 = "";
		$v_Pri_1 = "";

		$v_Sad_2 = "";
		$v_Lai_2 = "";
		$v_For_2 = "";
		$v_Gou_2 = "";
		$v_Con_2 = "";
		$v_Com_2 = "";
		$v_End_2 = "";
		$v_Pri_2 = "";

$v_monstre1="";
$v_monstre2="";

	if (isset($_POST['fusion']) )
	{
		$v_lvl_accre = $_POST['accre'];
		/*		
		$v_Sad_1 = $_POST['Sad_1'];
		$v_Lai_1 = $_POST['Lai_1'];
		$v_For_1 = $_POST['For_1'];
		$v_Gou_1 = $_POST['Gou_1'];
		$v_Con_1 = $_POST['Con_1'];
		$v_Com_1 = $_POST['Com_1'];
		$v_End_1 = $_POST['End_1'];
		$v_Pri_1 = $_POST['Pri_1'];

		$v_Sad_2 = $_POST['Sad_2'];
		$v_Lai_2 = $_POST['Lai_2'];
		$v_For_2 = $_POST['For_2'];
		$v_Gou_2 = $_POST['Gou_2'];
		$v_Con_2 = $_POST['Con_2'];
		$v_Com_2 = $_POST['Com_2'];
		$v_End_2 = $_POST['End_2'];
		$v_Pri_2 = $_POST['Pri_2'];
		*/
		
		$v_monstre1=$_POST['monstre1'];
		$v_monstre2=$_POST['monstre2'];
		
		$arStats1 = explode("\n", $v_monstre1);
		$arStats2 = explode("\n", $v_monstre2);
		
		foreach( $arStats1 as $line )
		{
			  if ( strstr($line, "Sadisme ") )	
			  		$v_Sad_1 = substr(strstr($line, "Sadisme "),strlen("Sadisme "));
			  if ( strstr($line, "Laideur ") )	
			  		$v_Lai_1 = substr(strstr($line, "Laideur "),strlen("Laideur "));
			  if ( strstr($line, "Force ") )	
			  		$v_For_1 = substr(strstr($line, "Force "),strlen("Force "));
			  if ( strstr($line, "Gourmand ") )	
			  		$v_Gou_1 = substr(strstr($line, "Gourmand "),strlen("Gourmand "));
			  if ( strstr($line, "Contrôle ") )	
			  		$v_Con_1 = substr(strstr($line, "Contrôle "),strlen("Contrôle "));
			  if ( strstr($line, "Combat ") )	
			  		$v_Com_1 = substr(strstr($line, "Combat "),strlen("Combat "));
			  if ( strstr($line, "Endurance ") )	
			  		$v_End_1 = substr(strstr($line, "Endurance "),strlen("Endurance "));
			  if ( strstr($line, "Prime") )	
			  		$v_Pri_1 = substr(strstr($line, "Prime"),strlen("Prime"));
		}
		
		foreach( $arStats2 as $line )
		{
			  if ( strstr($line, "Sadisme ") )	
			  		$v_Sad_2 = substr(strstr($line, "Sadisme "),strlen("Sadisme "));
			  if ( strstr($line, "Laideur ") )	
			  		$v_Lai_2 = substr(strstr($line, "Laideur "),strlen("Laideur "));
			  if ( strstr($line, "Force ") )	
			  		$v_For_2 = substr(strstr($line, "Force "),strlen("Force "));
			  if ( strstr($line, "Gourmand ") )	
			  		$v_Gou_2 = substr(strstr($line, "Gourmand "),strlen("Gourmand "));
			  if ( strstr($line, "Contrôle ") )	
			  		$v_Con_2 = substr(strstr($line, "Contrôle "),strlen("Contrôle "));
			  if ( strstr($line, "Combat ") )	
			  		$v_Com_2 = substr(strstr($line, "Combat "),strlen("Combat "));
			  if ( strstr($line, "Endurance ") )	
			  		$v_End_2 = substr(strstr($line, "Endurance "),strlen("Endurance "));
			  if ( strstr($line, "Prime") )	
			  		$v_Pri_2 = substr(strstr($line, "Prime"),strlen("Prime"));
		}
		
		$v_Sad_res = fusion(intval($_POST['accre']), intval($v_Sad_1), intval($v_Sad_2));
		$v_Lai_res = fusion(intval($_POST['accre']), intval($v_Lai_1), intval($v_Lai_2));
		$v_For_res = fusion(intval($_POST['accre']), intval($v_For_1), intval($v_For_2));
		$v_Gou_res = fusion(intval($_POST['accre']), intval($v_Gou_1), intval($v_Gou_2));
		$v_Con_res = fusion(intval($_POST['accre']), intval($v_Con_1), intval($v_Con_2));
		$v_Com_res = fusion(intval($_POST['accre']), intval($v_Com_1), intval($v_Com_2));
		$v_End_res = fusion(intval($_POST['accre']), intval($v_End_1), intval($v_End_2));
		$v_Pri_res = "???";

		
		?>
		<h1>Résultat</h1>
		<table>
				<tr><th>Stat      </th><th>Monstre 1</th>																														<th>Monstre Résultat</th>                                                                      <th>Monstre 2</th></tr>                                                           
				<tr><th>Sadisme   </th><td><input size="3" type="text" name="Sad_1" value="<?PHP echo $v_Sad_1; ?>"><td><input size="5" type="text" name="Sad_res" readonly value="<?PHP echo $v_Sad_res; ?>"></td><td><input size="3" type="text" name="Sad_2" value="<?PHP echo $v_Sad_2; ?>"></td></tr>
		    <tr><th>Laideur   </th><td><input size="3" type="text" name="Lai_1" value="<?PHP echo $v_Lai_1; ?>"><td><input size="5" type="text" name="Lai_res" readonly value="<?PHP echo $v_Lai_res; ?>"></td><td><input size="3" type="text" name="Lai_2" value="<?PHP echo $v_Lai_2; ?>"></td></tr>
		    <tr><th>Force     </th><td><input size="3" type="text" name="For_1" value="<?PHP echo $v_For_1; ?>"><td><input size="5" type="text" name="For_res" readonly value="<?PHP echo $v_For_res; ?>"></td><td><input size="3" type="text" name="For_2" value="<?PHP echo $v_For_2; ?>"></td></tr>
		    <tr><th>Gourmand  </th><td><input size="3" type="text" name="Gou_1" value="<?PHP echo $v_Gou_1; ?>"><td><input size="5" type="text" name="Gou_res" readonly value="<?PHP echo $v_Gou_res; ?>"></td><td><input size="3" type="text" name="Gou_2" value="<?PHP echo $v_Gou_2; ?>"></td></tr>
				<tr><th>Contrôle  </th><td><input size="3" type="text" name="Con_1" value="<?PHP echo $v_Con_1; ?>"><td><input size="5" type="text" name="Con_res" readonly value="<?PHP echo $v_Con_res; ?>"></td><td><input size="3" type="text" name="Con_2" value="<?PHP echo $v_Con_2; ?>"></td></tr>
		    <tr><th>Combat    </th><td><input size="3" type="text" name="Com_1" value="<?PHP echo $v_Com_1; ?>"><td><input size="5" type="text" name="Com_res" readonly value="<?PHP echo $v_Com_res; ?>"></td><td><input size="3" type="text" name="Com_2" value="<?PHP echo $v_Com_2; ?>"></td></tr>
		    <tr><th>Endurance </th><td><input size="3" type="text" name="End_1" value="<?PHP echo $v_End_1; ?>"><td><input size="5" type="text" name="End_res" readonly value="<?PHP echo $v_End_res; ?>"></td><td><input size="3" type="text" name="End_2" value="<?PHP echo $v_End_2; ?>"></td></tr>
		    <tr><th>Prime     </th><td><input size="3" type="text" name="Pri_1" value="<?PHP echo $v_Pri_1; ?>"><td><input size="5" type="text" name="Pri_res" readonly value="<?PHP echo $v_Pri_res; ?>"></td><td><input size="3" type="text" name="Pri_2" value="<?PHP echo $v_Pri_2; ?>"></td></tr>
		</table>
		
		
		
		
		
		
		
		
		
		<?PHP
		}

?>     
	<h1>Simulateur de fusion</h1>      
		<form action="#self" method="post">
			Niveau d'acréditation : <input type="text" name="accre" value="<?PHP echo $v_lvl_accre;?>">
			
			<input type="hidden" name="fusion">
			<!--
				<table>
					<tr><th>Stat      </th><th>Monstre 1</th>																														     <th>Monstre 2</th></tr>
					<tr><th>Sadisme   </th><td><input size="3" type="text" name="Sad_1" value="<?PHP echo $v_Sad_1; ?>"></td><td><input size="3" type="text" name="Sad_2" value="<?PHP echo $v_Sad_2; ?>"></td></tr>
			    <tr><th>Laideur   </th><td><input size="3" type="text" name="Lai_1" value="<?PHP echo $v_Lai_1; ?>"></td><td><input size="3" type="text" name="Lai_2" value="<?PHP echo $v_Lai_2; ?>"></td></tr>
			    <tr><th>Force     </th><td><input size="3" type="text" name="For_1" value="<?PHP echo $v_For_1; ?>"></td><td><input size="3" type="text" name="For_2" value="<?PHP echo $v_For_2; ?>"></td></tr>
			    <tr><th>Gourmand  </th><td><input size="3" type="text" name="Gou_1" value="<?PHP echo $v_Gou_1; ?>"></td><td><input size="3" type="text" name="Gou_2" value="<?PHP echo $v_Gou_2; ?>"></td></tr>
					<tr><th>Contrôle  </th><td><input size="3" type="text" name="Con_1" value="<?PHP echo $v_Con_1; ?>"></td><td><input size="3" type="text" name="Con_2" value="<?PHP echo $v_Con_2; ?>"></td></tr>
			    <tr><th>Combat    </th><td><input size="3" type="text" name="Com_1" value="<?PHP echo $v_Com_1; ?>"></td><td><input size="3" type="text" name="Com_2" value="<?PHP echo $v_Com_2; ?>"></td></tr>
			    <tr><th>Endurance </th><td><input size="3" type="text" name="End_1" value="<?PHP echo $v_End_1; ?>"></td><td><input size="3" type="text" name="End_2" value="<?PHP echo $v_End_2; ?>"></td></tr>
			    <tr><th>Prime     </th><td><input size="3" type="text" name="Pri_1" value="<?PHP echo $v_Pri_1; ?>"></td><td><input size="3" type="text" name="Pri_2" value="<?PHP echo $v_Pri_2; ?>"></td></tr>
					<tr><th>&nbsp;</th><td></td><td><input type="submit"></td></tr>
				</table>
				-->
				<table>
						<tr><th>Monstre 1</th><th>Monstre 2</th></tr>
						<tr><td><textarea name="monstre1" rows="15" cols="30"><?PHP echo $v_monstre1;?></textarea></td>
								<td><textarea name="monstre2" rows="15" cols="30"><?PHP echo $v_monstre2;?></textarea></td></tr>
						<tr><td>&nbsp;</td><td><input type="submit"></td></tr>
				</table>
		</form>
  </body>
</html>


