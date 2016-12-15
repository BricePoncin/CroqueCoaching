<?PHP
		
		if( isset($_POST['hid_ordu']) )
		{
				$v_ordures=	$_POST['ordures'];
				$arLines = explode("\n", $v_ordures);
				
				$pattern1='#^([0-9]{2}[:][0-9]{2} le [0-9]{2}[/][0-9]{2}[/][0-9]{2})[ \t]*([a-zA-Z]*)[ \t]*ajoute 1 (.*) au tas .*[+]([0-9],[0-9]{3})#';
				
				$pattern2='#^([0-9]{2}[:][0-9]{2} le [0-9]{2}[/][0-9]{2}[/][0-9]{2})[ \t]*([a-zA-Z]*)[ \t]*envoie un monstre distraire GrosMiam.*[-]([0-9]*) concentration#';
				
				foreach($arLines as $line)
				{
						$ret = preg_match  ( $pattern1 , $line, $matches );
						if( isset($matches[1]))
							echo $matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4]."<BR>\n";
							
						$ret = preg_match  ( $pattern2 , $line, $matches );
						if( isset($matches[1]))
						    echo "CONTRATTAQUE => ".$matches[1]." ".$matches[2]." ".$matches[3]."<BR>\n";
						
				}
		}

?>
<form name="frm_ordu" action="#self" method="post">
	<input type="hidden" name="hid_ordu" value="1">
	<textarea name="ordures" rows="15" cols="30"><?PHP echo $v_ordures;?></textarea>
	<input type="submit" value="Envoyer">
</form>
