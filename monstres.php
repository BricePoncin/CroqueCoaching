<?PHP
		require_once("depoil.inc.php");

		$url = "http://www.croquemonster.com/api/monsters.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xml = readXML( $url );
		if( substr($xml, 0, 6)== "Erreur" )
		{
				echo $xml;
				exit;
		}


		function  note_pacifique( $level, $sadisme, $laideur, $force, $gourmandise )
		{
			$moy=( ($sadisme + $laideur + $force + $gourmandise) - min($sadisme, $laideur, $force, $gourmandise) ) / 3;
			$delta = intval($moy) - intval($level);
			
			if ($delta >= 2)
				$note = 10;
			else if ($delta >= 1.5)
				$note = 9;
			else if ($delta >= 1)
				$note = 8;
			else if ($delta >= 0.5)
				$note = 7;
			else if ($delta >= 0)
				$note = 6;
			else if ($delta >= -0.5)
				$note = 5;
			else if ($delta >= -1)
				$note = 4;
			else if ($delta >= -1.5)
				$note = 3;
			else if ($delta >= -2)
				$note = 2;
			else if ($delta >= -2.5)
				$note = 1;
			else 
				$note = 0;
			
			return $note;
		}

		function  note_mbl( $level, $combat, $endurance )
		{
			$moy=($combat + $endurance)/2;
			$delta = intval($moy) - intval($level);
			
			if ($delta >= 2)
				$note = 10;
			else if ($delta >= 1.5)
				$note = 9;
			else if ($delta >= 1)
				$note = 8;
			else if ($delta >= 0.5)
				$note = 7;
			else if ($delta >= 0)
				$note = 6;
			else if ($delta >= -0.5)
				$note = 5;
			else if ($delta >= -1)
				$note = 4;
			else if ($delta >= -1.5)
				$note = 3;
			else if ($delta >= -2)
				$note = 2;
			else if ($delta >= -2.5)
				$note = 1;
			else 
				$note = 0;
			
			$note = max(0, $note - 0.25*abs($combat-$endurance));
			
			return $note;	
		}

		function  note_guerre( $level, $combat, $controle )
		{
			$moy=($combat + $controle)/2;
			$delta = intval($moy) - intval($level);
			
			if ($delta >= 2)
				$note = 10;
			else if ($delta >= 1.5)
				$note = 9;
			else if ($delta >= 1)
				$note = 8;
			else if ($delta >= 0.5)
				$note = 7;
			else if ($delta >= 0)
				$note = 6;
			else if ($delta >= -0.5)
				$note = 5;
			else if ($delta >= -1)
				$note = 4;
			else if ($delta >= -1.5)
				$note = 3;
			else if ($delta >= -2)
				$note = 2;
			else if ($delta >= -2.5)
				$note = 1;
			else 
				$note = 0;
			
			
			return $note;	
		}




?>
<script language="javascript">
	<!--
	function getElementsByClass(searchClass, node, tag)
	{
	  var classElements = new Array();
	  if(node == null) node = document;
	  if(tag == null) tag = '*';
	  
	  var els = node.getElementsByTagName(tag);
	  var elsLen = els.length;
	  var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
	  
	  for(i = 0, j = 0; i < elsLen; i++)
	  {
	    if(pattern.test(els[i].className) )
	      { 
	        classElements[j] = els[i];
	        j++;
	      }
	  }
	  
	  return classElements;
	}

	
	function refresh(elem)
	{
			var elments = getElementsByClass('apoil', null, 'tr');
      for(var i = 0; i < elments.length; i++)
			{
				if( elments[i].className == elem )
					elments[i].style.display = "table-row";
				else
					elments[i].style.display = "none";
    	}
  		
			var elments = getElementsByClass('equip', null, 'tr');
      for(var i = 0; i < elments.length; i++)
			{
				if( elments[i].className == elem )
					elments[i].style.display = "table-row";
				else
					elments[i].style.display = "none";
    	}
        		
	}
	-->
</script>
<div style="margin-left: auto; margin-right: auto; width: 300px;" class="information">
	<form name="frm_affich" id="frm_affich" action="SELF" method="POST">
			<input type="radio" name="affich" value="apoil" OnClick="javascript:refresh('apoil');" checked="checked">Afficher les monstres à poil<BR>
			<input type="radio" name="affich" value="equip" OnClick="javascript:refresh('equip');">Afficher les monstres équipés<BR>
			
	</form>
</div>
      <table id="tab_monstres">
      <thead>
      		<th class="entete_lig">&nbsp;</th>
      		<th onmouseover="javascript:showTooltip('Sadisme');" onmouseout="javascript:hideTooltip();"><img src="images/sadism.gif"/></th>
		      <th onmouseover="javascript:showTooltip('Laideur');" onmouseout="javascript:hideTooltip();"><img src="images/ugliness.gif"/></th>
		      <th onmouseover="javascript:showTooltip('Force');" onmouseout="javascript:hideTooltip();"><img src="images/power.gif"/></th>
		      <th onmouseover="javascript:showTooltip('Gourmandise');" onmouseout="javascript:hideTooltip();"><img src="images/greediness.gif"/></th>
		      <th style="width:5px; background-color: navy;"></th>
		      <th onmouseover="javascript:showTooltip('Contr&ocirc;le');" onmouseout="javascript:hideTooltip();"><img src="images/control.gif"/></th>
		      <th onmouseover="javascript:showTooltip('Combat');" onmouseout="javascript:hideTooltip();"><img src="images/fight.gif"/></th>
		      <th onmouseover="javascript:showTooltip('Endurance');" onmouseout="javascript:hideTooltip();"><img src="images/endurance.gif"/></th>
					<th style="width:5px; background-color: navy;"></th>
					<th onmouseover="javascript:showTooltip('Note pacifique');" onmouseout="javascript:hideTooltip();">Note pacifique</th>
					<th onmouseover="javascript:showTooltip('Note MBL');" onmouseout="javascript:hideTooltip();">Note MBL</th>
					<th onmouseover="javascript:showTooltip('Note guerrier');" onmouseout="javascript:hideTooltip();">Note guerrier</th>
		</thead>
      
        <?PHP
        $id=0;
		$nb_mons=0;
		$min_sad=100;$max_sad=0;$moy_sad=0;
		$min_lai=100;$max_lai=0;$moy_lai=0;
		$min_for=100;$max_for=0;$moy_for=0;
		$min_gou=100;$max_gou=0;$moy_gou=0;
		$min_con=100;$max_con=0;$moy_con=0;
		$min_com=100;$max_com=0;$moy_com=0;
		$min_end=100;$max_end=0;$moy_end=0;
		         
        foreach($xml as $monstre) 
        {
			$nb_mons=0;

			$equipements_portes = explode(",", $monstre['permanentItems'].",".$monstre['contractItems']);
			$equipements  = array_compile  ( $equipements_portes  , $liste_equipements  );
			$stats_equipement = equipement_stat($equipements);

			$cur_sad=(intval($monstre['sadism']    )- intval($stats_equipement["sad"]));
			$cur_lai=(intval($monstre['ugliness']  )- intval($stats_equipement["ugl"]));
			$cur_for=(intval($monstre['power']     )- intval($stats_equipement["pow"]));
			$cur_gou=(intval($monstre['greediness'])- intval($stats_equipement["gre"]));
			$cur_con=(intval($monstre['control']   )- intval($stats_equipement["con"]));
			$cur_com=(intval($monstre['fight']     )- intval($stats_equipement["fig"]));
			$cur_end=(intval($monstre['endurance'] )- intval($stats_equipement["end"]));
			
			$min_sad=min($min_sad, $cur_sad); $max_sad=max($max_sad, $cur_sad); $moy_sad+=$cur_sad;
			$min_lai=min($min_lai, $cur_lai); $max_lai=max($max_lai, $cur_lai); $moy_lai+=$cur_lai;
			$min_for=min($min_for, $cur_for); $max_for=max($max_for, $cur_for); $moy_for+=$cur_for;
			$min_gou=min($min_gou, $cur_gou); $max_gou=max($max_gou, $cur_gou); $moy_gou+=$cur_gou;
			$min_con=min($min_con, $cur_con); $max_con=max($max_con, $cur_con); $moy_con+=$cur_con;
			$min_com=min($min_com, $cur_com); $max_com=max($max_com, $cur_com); $moy_com+=$cur_com;
			$min_end=min($min_end, $cur_end); $max_end=max($max_end, $cur_end); $moy_end+=$cur_end;
			
			$img_mbl="";
			if( isset($monstre['isMblMonster']) && ($monstre['isMblMonster']=='true'))
				$img_mbl="<img src=\"images/mbl.gif\">";
		


			$note_pacifique = note_pacifique( $_SESSION['level'], $cur_sad, $cur_lai, $cur_for, $cur_gou );

			$note_mbl = note_mbl( $_SESSION['level'], $cur_com, $cur_end );

			$note_guerre = note_guerre( $_SESSION['level'], $cur_com, $cur_con );

			echo "<tr class=\"apoil\"><th  class=\"entete_lig\" onMouseOver=\""
				 ."javascript:showMonstre(".$monstre['id'].", '".$monstre['name']."'"
				 .", ".$monstre['fatigue'].");\""
				 ." onMouseOut=\"javascript:hideTooltip();\">";
            echo "<div id=\"m$id\" class=\"hidden\"><script type=\"text/javascript\" src=\"".$monstre['swfjs']."\"></script></div>";
			echo $img_mbl.$monstre['name'].$img_mbl."</th>";
          
			echo "\t\t<td class=\"stat_nu\">".$cur_sad."</td>\n"
				."\t\t<td class=\"stat_nu\">".$cur_lai."</td>\n"
				."\t\t<td class=\"stat_nu\">".$cur_for."</td>\n"
				."\t\t<td class=\"stat_nu\">".$cur_gou."</td>\n"
				."\t\t<td style=\"width:5px; background-color: navy;\"></td>\n"
				."\t\t<td class=\"stat_nu\">".$cur_con."</td>\n"
				."\t\t<td class=\"stat_nu\">".$cur_com."</td>\n"
				."\t\t<td class=\"stat_nu\">".$cur_end."</td>\n"
				."\t\t<td style=\"width:5px; background-color: navy;\"></td>\n"
				."\t\t<td class=\"stat_nu\">".$note_pacifique." / 10</td>\n"
				."\t\t<td class=\"stat_nu\">".$note_mbl." / 10</td>\n"
				."\t\t<td class=\"stat_nu\">".$note_guerre." / 10</td>\n"
				."\t</tr>";
                 
			echo "<tr class=\"equip\"><th  class=\"entete_lig\" onMouseOver=\""
				 ."javascript:showMonstre(".$monstre['id'].", '".$monstre['name']."'"
				 .", ".$monstre['fatigue'].");\""
				 ." onMouseOut=\"javascript:hideTooltip();\">";
            echo "<div id=\"m$id\" class=\"hidden\"><script type=\"text/javascript\" src=\"".$monstre['swfjs']."\"></script></div>";
			echo $img_mbl.$monstre['name'].$img_mbl."</th>";
          
            echo "\t\t<td class=\"stat_eq\">".$monstre['sadism']    ."</td>\n"
				."\t\t<td class=\"stat_eq\">".$monstre['ugliness']  ."</td>\n"
				."\t\t<td class=\"stat_eq\">".$monstre['power']     ."</td>\n"
				."\t\t<td class=\"stat_eq\">".$monstre['greediness']."</td>\n"
				."\t\t<td style=\"width:5px; background-color: navy;\"></td>\n"
				."\t\t<td class=\"stat_eq\">".$monstre['control']   ."</td>\n"
				."\t\t<td class=\"stat_eq\">".$monstre['fight']     ."</td>\n"
				."\t\t<td class=\"stat_eq\">".$monstre['endurance'] ."</td>\n"
				."\t\t<td style=\"width:5px; background-color: navy;\"></td>\n"
				."\t\t<td class=\"stat_eq\">"."</td>\n"
				."\t\t<td class=\"stat_eq\">"."</td>\n"
				."\t\t<td class=\"stat_eq\">"."</td>\n"
				."\t</tr>";
        
        	$id++;
			$nb_mons++;
        }

    
        ?>
		<tfoot>
			<th>min<br/>max<br/>moy</th>
			<th><?PHP echo $min_sad; ?><br/><?PHP echo $max_sad; ?><br/><?PHP echo ($moy_sad/$nb_mons); ?></th>
			<th><?PHP echo $min_lai; ?><br/><?PHP echo $max_lai; ?><br/><?PHP echo ($moy_lai/$nb_mons); ?></th>
			<th><?PHP echo $min_for; ?><br/><?PHP echo $max_for; ?><br/><?PHP echo ($moy_for/$nb_mons); ?></th>
			<th><?PHP echo $min_gou; ?><br/><?PHP echo $max_gou; ?><br/><?PHP echo ($moy_gou/$nb_mons); ?></th>
			<th style="width:5px; background-color: navy;"></th>
			<th><?PHP echo $min_con; ?><br/><?PHP echo $max_con; ?><br/><?PHP echo ($moy_con/$nb_mons); ?></th>
			<th><?PHP echo $min_com; ?><br/><?PHP echo $max_com; ?><br/><?PHP echo ($moy_com/$nb_mons); ?></th>
			<th><?PHP echo $min_end; ?><br/><?PHP echo $max_end; ?><br/><?PHP echo ($moy_end/$nb_mons); ?></th>
			<th style="width:5px; background-color: navy;"></th>
			
		</tfoot>
    </table>
<?PHP
		foreach($xml as $monstre) 
		{
			echo "\t\t<div id=\"monstre".$monstre['id']."\" class=\"hidden\"><script type=\"text/javascript\" src=\"".$monstre['swfjs']."\"></script></div>\n";	
		}
?>