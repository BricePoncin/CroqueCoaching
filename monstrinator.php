<html>
	<head>
	</head>
	<body>
		<?PHP

		function parse_fourchette($v_fourchette)
		{
				if( strpos($v_fourchette, '~') !== false )
				{
						$min = substr($v_fourchette, 0, strpos($v_fourchette, '~'));
						$max = substr($v_fourchette, strpos($v_fourchette, '~')+1);
						$v_ret=rand(intval($min), intval($max));
					
						return($v_ret);
				}
				else
					return($v_fourchette);
		}

		if( isset($_GET['sad']) )
		{	
			$v_mod = 6;//rand(5, 6);
			$v_skin = ( isset($_GET['skin']) )?parse_fourchette($_GET['skin']):rand(1, 10000);
			$v_sad = parse_fourchette($_GET['sad']);
			$v_lai = parse_fourchette($_GET['lai']);
			$v_for = parse_fourchette($_GET['for']);
			$v_gou = parse_fourchette($_GET['gou']);
			$v_con = parse_fourchette($_GET['con']);
			$v_com = parse_fourchette($_GET['com']);
			$v_end = parse_fourchette($_GET['end']);
			$v_niv_fus = $_GET['niv_fus'];

			$v_flashVars = "$v_mod:$v_skin:$v_sad:$v_lai:$v_for:$v_gou:$v_com:$v_end:$v_con:$v_niv_fus";
			//$v_flashVars = "6:46942:12:8:12:16:8:9:16";
		?>
			<div id="generatorxhr">
				<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" height="170" width="150">
				    <param name="FlashVars" value="mface=<?PHP echo $v_flashVars;?>&amp;size=1">
				    <param name="wmode" value="transparent">
				    <param name="movie" value="http://data.croquemonster.com/swf/generator.swf?v=17">
				    <param name="menu" value="false">
				    <param name="quality" value="high">
	    		<embed quality="high" menu="false" type="application/x-shockwave-flash" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" src="http://data.croquemonster.com/swf/generator.swf?v=17" flashvars="mface=<?PHP echo $v_flashVars;?>&amp;size=1" height="170" width="150"></object>
			</div>
			
<?PHP

		}
?>
	</body>
</html>
