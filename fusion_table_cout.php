<?PHP
		require_once("session.php");
		require_once("depoil.inc.php");
		require_once("cm_api.inc.php");

		function get_td_class ($p_lvl_accre, $p_stat1, $p_stat2)
		{
				$v_stat = fusion($p_lvl_accre, $p_stat1, $p_stat2);
				
				$class = ( $v_stat > $p_stat1 && $v_stat > $p_stat2)?'b':'';
				$class = ( $v_stat >= ($p_lvl_accre+1) )?'ab':$class;
				$class = ( $v_stat >= ($p_lvl_accre+2) )?'tb':$class;

				$class = ( $v_stat < $p_stat1 && $v_stat < $p_stat2)?'m':$class;
				$class = ( $v_stat <= ($p_lvl_accre-1) )?'am':$class;
				$class = ( $v_stat <= ($p_lvl_accre-2) )?'tm':$class;

				$pos = strpos($v_stat, "~");
				if( $pos !== false )
				{
						$min = intval( substr( $v_stat,0, $pos ) );
						$max = intval( substr( $v_stat, $pos + 1 ) );
						
						$class = ( $max >= $p_lvl_accre )?'r':$class;
						$class = ( $max < $p_stat1 && $max < $p_stat2)?'m':$class;
						$class = ( $max <= ($p_lvl_accre-1) )?'am':$class;
						$class = ( $max <= ($p_lvl_accre-2) )?'tm':$class;
				}
				return $class;
		}
		
		function fusion($p_lvl_accre, $p_stat1, $p_stat2)
    {
        $v_stat_res='';
        
        $v_stat_sup = max($p_stat1, $p_stat2);
        $v_stat_inf = min($p_stat1, $p_stat2);
        
        if ( $v_stat_inf >= 0.6 * $v_stat_sup )
        {
            $v_stat_res = min($v_stat_sup + 1, $p_lvl_accre+2);
        }
        else
        {	
        		$v_stat_res = min( ceil((( $v_stat_inf + $v_stat_sup)/2)+1), $p_lvl_accre+2)."~".min( $v_stat_sup + 1,  $p_lvl_accre+2);
        		//$v_stat_res = '###';
        }
        return $v_stat_res;  
    }

?>
<HTML>
	<head>
		<style type="text/css">
				table { border: solid 1px black;}
			  td { text-align: center; padding:0;}
			  
			  td .b { background-color: #23AD20 }
			  td .ab { background-color: #18D815 }
			  td .tb { background-color: #06FC02 }
			  
			  td .r { background-color: orange }
			  
			  td .m { background-color: #F9AEAE }
			  td .am { background-color: #F95E5E }
			  td .tm { background-color: #FF0000; color: white; }
			  
			  
	  </style>

	</head>	
	<body>
<?PHP
		if( isset($_SESSION['name']))
		{
		    $url = "http://www.croquemonster.com/api/monsters.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
				$monstr_H = readXML( $url );
				if( substr($monstr_H, 0, 6)== "Erreur" )
				{
						echo $monstr_H;
						exit;
				}
				$monstr_V = readXML( $url );
				if( substr($monstr_V, 0, 6)== "Erreur" )
				{
						echo $monstr_V;
						exit;
				}
		}
		else
		{
				$monstr_H = array();
				$monstr_V = array();
		}

print_r($monstr_H);


		echo "<table border=\"1\">\n";
		echo "<tr><th>&nbsp;</th>\n";
		foreach($monstr_H as $monstre) 
    {
				$equipements_portes = explode(",", $monstre['permanentItems'].",".$monstre['contractItems']);
				$equipements  = array_compile  ( $equipements_portes  , $liste_equipements  );
				$stats_equipement = equipement_stat($equipements);
    	
    		echo "<th>".$monstre['name']."<br/>";
				echo "<table style=\"font-size: 0.8em; width: 100%\">";
    		echo "<tr>";
    		echo "<td><img src=\"images/sadism.gif\"/>"    ;
				echo "<td><img src=\"images/ugliness.gif\"/>"  ;
				echo "<td><img src=\"images/power.gif\"/>"     ;
				echo "<td><img src=\"images/greediness.gif\"/>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>".($monstre['sadism']    - $stats_equipement["sad"])."</td>";
				echo "<td>".($monstre['ugliness']  - $stats_equipement["ugl"])."</td>";
				echo "<td>".($monstre['power']     - $stats_equipement["pow"])."</td>";
				echo "<td>".($monstre['greediness']- $stats_equipement["gre"])."</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td><img src=\"images/control.gif\"/>  </td>";
				echo "<td><img src=\"images/fight.gif\"/>    </td>";
				echo "<td><img src=\"images/endurance.gif\"/></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>".($monstre['control']   - $stats_equipement["con"])."</td>";
				echo "<td>".($monstre['fight']     - $stats_equipement["fig"])."</td>";
				echo "<td>".($monstre['endurance'] - $stats_equipement["end"])."</td>";
				echo "</tr>";
				echo "</table>";
				
    		echo "</th>";
    }
    
    echo "<tr>\n";
    
		$i = 0;
		foreach($monstr_V as $mV) 
    {
    		$equipements_portes_V = explode(",", $mV['permanentItems'].",".$mV['contractItems']);
				$equipements_V  = array_compile  ( $equipements_portes_V  , $liste_equipements  );
				$stats_equipement_V = equipement_stat($equipements_V);
    	
    		echo "<th>".$mV['name']."<br/>";
				echo "<table style=\"font-size: 0.8em; width: 100%\">";
    		echo "<tr>";
    		echo "<td><img src=\"images/sadism.gif\"/>"    ;
				echo "<td><img src=\"images/ugliness.gif\"/>"  ;
				echo "<td><img src=\"images/power.gif\"/>"     ;
				echo "<td><img src=\"images/greediness.gif\"/>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>".($mV['sadism']    - $stats_equipement_V["sad"])."</td>";
				echo "<td>".($mV['ugliness']  - $stats_equipement_V["ugl"])."</td>";
				echo "<td>".($mV['power']     - $stats_equipement_V["pow"])."</td>";
				echo "<td>".($mV['greediness']- $stats_equipement_V["gre"])."</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td><img src=\"images/control.gif\"/>  </td>";
				echo "<td><img src=\"images/fight.gif\"/>    </td>";
				echo "<td><img src=\"images/endurance.gif\"/></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>".($mV['control']   - $stats_equipement_V["con"])."</td>";
				echo "<td>".($mV['fight']     - $stats_equipement_V["fig"])."</td>";
				echo "<td>".($mV['endurance'] - $stats_equipement_V["end"])."</td>";
				echo "</tr>";
				echo "</table>";
    		echo "</th>";

				$j = 0;
				foreach($monstr_H as $mH) 
		    {
		    		$equipements_portes_H = explode(",", $mH['permanentItems'].",".$mH['contractItems']);
						$equipements_H  = array_compile  ( $equipements_portes_H  , $liste_equipements  );
						$stats_equipement_H = equipement_stat($equipements_H);
		    	
    				//if(( strval($mV['name']) != strval($mH['name']) ) and ($j > $i) )
    				if(( strval($mV['name']) != strval($mH['name']) ))
		    		{
		    				echo "<td>";
								echo "<table style=\"font-size: 0.8em; width: 100%\">";
				    		/*
				    		echo "<tr>";
				    		echo "<td><img src=\"images/sadism.gif\"/>"    ;
								echo "<td><img src=\"images/ugliness.gif\"/>"  ;
								echo "<td><img src=\"images/power.gif\"/>"     ;
								echo "<td><img src=\"images/greediness.gif\"/>";
								echo "</tr>";
								*/
								echo "<tr>";
								
								$v_sad = fusion($_SESSION['level'], ($mV['sadism']    - $stats_equipement_V["sad"]), ($mH['sadism']    - $stats_equipement_H["sad"]) );
								$cl_sad = get_td_class($_SESSION['level'], ($mV['sadism']    - $stats_equipement_V["sad"]), ($mH['sadism']    - $stats_equipement_H["sad"]) );
								$cl_sad = ( $v_sad == ($_SESSION['level']+1) )?'ab':$cl_sad;
								$cl_sad = ( $v_sad == ($_SESSION['level']+2) )?'tb':$cl_sad;
								$v_ugl = fusion($_SESSION['level'], ($mV['ugliness']  - $stats_equipement_V["ugl"]), ($mH['ugliness']  - $stats_equipement_H["ugl"]) );
								$cl_ugl = get_td_class($_SESSION['level'], ($mV['ugliness']  - $stats_equipement_V["ugl"]), ($mH['ugliness']  - $stats_equipement_H["ugl"]) );
								$v_pow = fusion($_SESSION['level'], ($mV['power']     - $stats_equipement_V["pow"]), ($mH['power']     - $stats_equipement_H["pow"]) );
								$cl_pow = get_td_class($_SESSION['level'], ($mV['power']     - $stats_equipement_V["pow"]), ($mH['power']     - $stats_equipement_H["pow"]) );
								$v_gre = fusion($_SESSION['level'], ($mV['greediness']- $stats_equipement_V["gre"]), ($mH['greediness']- $stats_equipement_H["gre"]) );
								$cl_gre = get_td_class($_SESSION['level'], ($mV['greediness']- $stats_equipement_V["gre"]), ($mH['greediness']- $stats_equipement_H["gre"]) );
								$v_con  = fusion($_SESSION['level'], ($mV['control']   - $stats_equipement_V["con"]), ($mH['control']   - $stats_equipement_H["con"]) );
								$cl_con = get_td_class($_SESSION['level'], ($mV['control']   - $stats_equipement_V["con"]), ($mH['control']   - $stats_equipement_H["con"]) );
								$v_fig  = fusion($_SESSION['level'], ($mV['fight']     - $stats_equipement_V["fig"]), ($mH['fight']     - $stats_equipement_H["fig"]) );
								$cl_fig = get_td_class($_SESSION['level'], ($mV['fight']     - $stats_equipement_V["fig"]), ($mH['fight']     - $stats_equipement_H["fig"]) );
								$v_end  = fusion($_SESSION['level'], ($mV['endurance'] - $stats_equipement_V["end"]), ($mH['endurance'] - $stats_equipement_H["end"]) );
								$cl_end = get_td_class($_SESSION['level'], ($mV['endurance'] - $stats_equipement_V["end"]), ($mH['endurance'] - $stats_equipement_H["end"]) );

								echo "<td class=\"".$cl_sad."\">".$v_sad."</td>";
								echo "<td class=\"".$cl_ugl."\">".$v_ugl."</td>";
								echo "<td class=\"".$cl_pow."\">".$v_pow."</td>";
								echo "<td class=\"".$cl_gre."\">".$v_gre."</td>";
								echo "</tr>";
								/*
								echo "<tr>";
								echo "<td><img src=\"images/control.gif\"/>  </td>";
								echo "<td><img src=\"images/fight.gif\"/>    </td>";
								echo "<td><img src=\"images/endurance.gif\"/></td>";
								echo "</tr>";
								*/
								echo "<tr>";
								echo "<td class=\"".$cl_con."\">".$v_con."</td>";
								echo "<td class=\"".$cl_fig."\">".$v_fig."</td>";
								echo "<td class=\"".$cl_end."\">".$v_end."</td>";
								echo "</tr>";
								echo "</table>";
				    		echo "</td>";
		    		}
		    		else
		    				echo "<th>&nbsp;</th>";
		    		$j++;
		    }
		    echo "<tr>\n";
		    $i++;
    }

		echo "</table>\n";
?>
	</body>
</HTML> 