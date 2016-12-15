<script language="javascript">
<!--
		function carac(zone, incr)
		{
			var val=parseInt(document.getElementById(zone).value);	
			val=val+incr;
			if ( val < 0 )
					val = 0;
			document.getElementById(zone).value = val;
			var_skin   = document.getElementById('skin').value;
			var_fusion = document.getElementById('fusion').value;
			var_sad    = document.getElementById('sad').value;
			var_ugl    = document.getElementById('ugl').value;
			var_pow    = document.getElementById('pow').value;
			var_gre    = document.getElementById('gre').value;
			var_con    = document.getElementById('con').value;
			var_fig    = document.getElementById('fig').value;
			var_end    = document.getElementById('end').value;
			
			document.getElementById('frm_result').src='./monstrinator.php?skin='+var_skin+'&sad='+var_sad+'&lai='+var_ugl+'&for='+var_pow+'&gou='+var_gre+'&con='+var_con+'&com='+var_fig+'&end='+var_end+'&niv_fus='+var_fusion;

		}		
-->
</script>
<?PHP
	if(isset($_POST['skin']))
		$v_skin = $_POST['skin'];
	else
		$v_skin = 0;

	if(isset($_POST['fusion']))
		$v_fusion = $_POST['fusion'];
	else
		$v_fusion = 0;
		
	if(isset($_POST['sad']))
		$v_sadism = $_POST['sad'];
	else
		$v_sadism = 0;

	if(isset($_POST['ugl']))
		$v_ugliness = $_POST['ugl'];
	else
		$v_ugliness = 0;

	if(isset($_POST['pow']))
		$v_power = $_POST['pow'];
	else
		$v_power = 0;

	if(isset($_POST['gre']))
		$v_greediness = $_POST['gre'];
	else
		$v_greediness = 0;

	if(isset($_POST['con']))
		$v_control = $_POST['con'];
	else
		$v_control = 0;

	if(isset($_POST['fig']))
		$v_fight = $_POST['fig'];
	else
		$v_fight = 0;

	if(isset($_POST['end']))
		$v_endurance = $_POST['end'];
	else
		$v_endurance = 0;
		
?>

<form name="frm_monstro" id="frm_monstro">
Skin    &nbsp;<a href="javascript:carac('skin', -1);"><</a><input id="skin" type="text" size="6" value="<?PHP echo $v_skin;    ?>"><a href="javascript:carac('skin', 1);">></a>&nbsp;
Fusion  &nbsp;<a href="javascript:carac('fusion', -1);"><</a><input id="fusion" type="text" size="3" value="<?PHP echo $v_fusion;    ?>"><a href="javascript:carac('fusion', 1);">></a><br/>
<img src="images/sadism.gif">    &nbsp;<a href="javascript:carac('sad', -1);"><</a><input id="sad" type="text" size="3" value="<?PHP echo $v_sadism;    ?>"><a href="javascript:carac('sad', 1);">></a>&nbsp;
<img src="images/ugliness.gif">  &nbsp;<a href="javascript:carac('ugl', -1);"><</a><input id="ugl" type="text" size="3" value="<?PHP echo $v_ugliness;  ?>"><a href="javascript:carac('ugl', 1);">></a>&nbsp;
<img src="images/power.gif">     &nbsp;<a href="javascript:carac('pow', -1);"><</a><input id="pow" type="text" size="3" value="<?PHP echo $v_power;     ?>"><a href="javascript:carac('pow', 1);">></a>&nbsp;
<img src="images/greediness.gif">&nbsp;<a href="javascript:carac('gre', -1);"><</a><input id="gre" type="text" size="3" value="<?PHP echo $v_greediness;?>"><a href="javascript:carac('gre', 1);">></a><br/>
<img src="images/control.gif">   &nbsp;<a href="javascript:carac('con', -1);"><</a><input id="con" type="text" size="3" value="<?PHP echo $v_control;   ?>"><a href="javascript:carac('con', 1);">></a>&nbsp;
<img src="images/fight.gif">     &nbsp;<a href="javascript:carac('fig', -1);"><</a><input id="fig" type="text" size="3" value="<?PHP echo $v_fight;     ?>"><a href="javascript:carac('fig', 1);">></a>&nbsp;
<img src="images/endurance.gif"> &nbsp;<a href="javascript:carac('end', -1);"><</a><input id="end" type="text" size="3" value="<?PHP echo $v_endurance; ?>"><a href="javascript:carac('end', 1);">></a><br/> 

</form>

<IFRAME id="frm_result" name="frm_result" src="./monstrinator.php" width=170 height=250 scrolling=false frameborder=1> </IFRAME>