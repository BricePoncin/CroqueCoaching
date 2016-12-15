<?php 
error_reporting(E_ALL);

	require_once("parsembldata.inc.php");
//	require_once("cm_api.inc.php");


if(isset($_GET['debug']))
	$debug = true;
else
	$debug = false;

if( isset($_GET['mid']) )
		$mid = $_GET['mid'];
else
{
	print "error<br/>Pas d'identifiant de match...";
	exit();
}

if (!is_numeric($mid)) 
{
	print "error<br/>Mauvais identifiant de match ($mid)...";
	exit();
}


$arDirs['Left']="la gauche";
$arDirs['Right']="la droite";
$arDirs['Up']="le haut";
$arDirs['Down']="le bas";


		// Retrieve the MBL Watcher XML
		$mbl_watcher_xml_object = simplexml_load_file("http://www.croquemonster.com/mbl/$mid/watcher.xml?h=0&x=wy11:mbl.Commandy7:Refresh:0");
		$references = array();
		$data = (string)$mbl_watcher_xml_object[0];
		$mbl_watcher_object = parseData();
//		print_r($mbl_watcher_object);
		
		// Retrieve the MBL Data XML
		$mbl_data_xml_object = simplexml_load_file("http://www.croquemonster.com/mbl/$mid/data.xml");
		$references = array();
		$data = (string)$mbl_data_xml_object[0];
		$mbl_data_object = parseData();
		
		$score[ $mbl_data_object['team1']['id'] ] = 0;
		$score[ $mbl_data_object['team2']['id'] ] = 0;
		$arActions=array();
		$arDeplacement=array();
		
		foreach ($mbl_watcher_object as $line)
		{
			/*if( $line['t'] == 'ActBoxAppear' )
					$arActions[] = "<tr><td>Une nouvelle boite apparait en ".$line['p'][0]."x".$line['p'][1]."</td></tr>\n";
			*/
			if( $line['t'] == 'ActEnters' )
			{
					if($line['p'][0]['teamId'] ==	$mbl_data_object['team1']['id'])
							$class="team_rouge";
					else
							$class="team_bleue";
							
					$arActions[] = "<span class=\"$class\">".$line['p'][0]['owner']." fait rentrer ".urldecode($line['p'][0]['name'])." en jeu</span>";
					
					$v_teamname		="";

					$arMonstres[$line['p'][0]['id']] = array( 'name'=> urldecode($line['p'][0]['name']), 'owner' => "<span class=\"$class\">".$line['p'][0]['owner']."</span>", 'teamId' => $line['p'][0]['teamId'], 'team' => $v_teamname );
			}		
			if( $line['t'] == 'ActDistributeActions2' )
					$arActions[] = "<span style=\"font-weight:bold; color: black;\">TEMPS MORT, tout le monde r&eacute;cup&egrave;re 2 pa</span>";
					
			if( $line['t'] == 'ActMove' )
			{
					
					if( (isset($arDeplacement['id']) && $arDeplacement['id'] != $line['p'][0]) || (isset($arDeplacement['dir']) && $arDeplacement['dir'] != $line['p'][1]['t']) )
					{
							$arActions[] =  $arMonstres[ $arDeplacement['id'] ]['owner']." se d&eacute;place vers ".$arDirs[ $arDeplacement['dir'] ]." x".$arDeplacement['nbr'];
							$arDeplacement=array();
							
							$arDeplacement['id'] = $line['p'][0];
							$arDeplacement['dir'] = $line['p'][1]['t'];
							$arDeplacement['nbr'] = 1;
					}
					else if ( count($arDeplacement) == 0 )
					{
							$arDeplacement['id'] = $line['p'][0];
							$arDeplacement['dir'] = $line['p'][1]['t'];
							$arDeplacement['nbr'] = 1;
					}
					else if( $arDeplacement['id'] == $line['p'][0] && $arDeplacement['dir'] == $line['p'][1]['t'] )
						$arDeplacement['nbr']++;
				
			}
			
			if ( $line['t'] != 'ActMove' && count($arDeplacement) != 0 )
			{
					$arActions[] =  $arMonstres[ $arDeplacement['id'] ]['owner']." se d&eacute;place vers ".$arDirs[ $arDeplacement['dir'] ]." x".$arDeplacement['nbr'];
					$arDeplacement=array();
			}
					
			if( $line['t'] == 'ActBoxOpened')
			{
					$arActions[] =  $arMonstres[ $line['p'][0] ]['owner']." ouvre la boite (".$line['p'][1]."x".$line['p'][2].")";
					if( is_array($line['p'][3]) )
							$arActions[] =  "Le petit ".$line['p'][3]['name']." &eacute;tait dedans.";
					else
							$arActions[] =  "La boite &eacute;tait vide.";
			}
			if( $line['t'] == 'ActFightBall' )
			{
				if ( $line['p'][2] == 1 )
						$arActions[] = $arMonstres[ $line['p'][0] ]['owner']." ramasse le mioche.";
				else
						$arActions[] = $arMonstres[ $line['p'][0] ]['owner']." &eacute;chappe le môme.";
			}
			if( $line['t'] == 'ActGetBall' )
					$arActions[] = $arMonstres[ $line['p'][0] ]['owner']." ramasse le mioche qui trainait."; 
		
			if( $line['t'] == 'ActGoal' )
			{
					$score[ $arMonstres[ $line['p'][0] ]['teamId'] ]++;
					
					$arActions[] = "<span style=\"font-size:1.2em; font-weight: bold;\">".$arMonstres[ $line['p'][0] ]['owner']." marque un but.<br/>"
												."<span style=\"color:red;\">".urldecode($mbl_data_object['team1']['name'])." ".$score[ $mbl_data_object['team1']['id'] ]."</span> - "."<span style=\"color:blue;\">".$score[ $mbl_data_object['team2']['id'] ]." ".urldecode($mbl_data_object['team2']['name'])."</span></span>\n";
					$id = $line['p'][0];
					$arMonsters[ $id ]['goals']= (isset($arMonsters[ $id ]['goals']) ) ? $arMonsters[ $id ]['goals']++ : 1;
					
			}
			if( $line['t'] == 'ActPass' )
			 		$arActions[] = $arMonstres[ $line['p'][0] ]['owner']." fait une passe de ".$line['p'][2]." vers ".$arDirs[ $line['p'][1]['t'] ];
		
			if( $line['t'] == 'ActFight' )
			{
					$arActions[] = $arMonstres[ $line['p'][0] ]['owner']." (-".$line['p'][3].") fight ".$arMonstres[ $line['p'][1] ]['owner']." (-".$line['p'][4].")";
					$fight = $line['p'][0];
					$fight2 = $line['p'][1];
					
					$arMonsters[ $fight ]['fights'] = (isset($arMonsters[ $fight ]['fights']))?$arMonsters[ $fight ]['fights']+1:1;
					$arMonsters[ $fight ]['hurts' ] = (isset($arMonsters[ $fight ]['hurts'] ))?$arMonsters[ $fight ]['hurts']+$line['p'][4]:$line['p'][4];

					$arMonsters[ $fight2]['fights'] = (isset($arMonsters[ $fight2]['fights']))?$arMonsters[ $fight2]['fights']+1:1;
					$arMonsters[ $fight2]['hurts' ] = (isset($arMonsters[ $fight2]['hurts'] ))?$arMonsters[ $fight2]['hurts']+$line['p'][3]:$line['p'][3];
			}
		
			if( $line['t'] == 'ActDropBall' )
			 		$arActions[] = $arMonstres[ $line['p'][0] ]['owner']." lâche le mioche.";
		
		
			if( $line['t'] == 'ActProjected' )
					$arActions[] = $arMonstres[ $line['p'][0] ]['owner']." est projet&eacute; vers ".$arDirs[ $line['p'][1]['t'] ].".";
					
		
			if( $line['t'] == 'ActMonsterDie' )
			{
				 	$arActions[] = $arMonstres[ $line['p'][0] ]['owner']." est KO.";
					
					if($fight == $line['p'][0])
						$id = $fight2;
					else
						$id = $fight;
					$arMonsters[ $id ]['kills']= (isset($arMonsters[ $id ]['kills']) ) ? $arMonsters[ $id ]['kills']+1 : 1;
			}
			 		
			if( $line['t'] == 'ActEnd' )
			 		$arActions[] = "LE MATCH EST TERMINEEEEEEE !!!";
			
		}

	
	
	if($debug)
	print_r($arMonsters);
	/*
	print '<a href="http://www.croquemonster.com/mbl/' . $mid . '">Match #' . $mid . '</a><br/>' . "\n";
	print 'Type de match : ' . $mbl_data_object['kind']['t'] . '<br/>' . "\n";
	print 'Equipe rouge : <a href="http://www.croquemonster.com/syndicate/view?id=' . $mbl_data_object['team1']['id'] . ';">' . urldecode($mbl_data_object['team1']['name']) . '</a><br/>' . "\n";
	print 'Equipe bleue : <a href="http://www.croquemonster.com/syndicate/view?id=' . $mbl_data_object['team2']['id'] . ';">' . urldecode($mbl_data_object['team2']['name']) . '</a><br/>' . "\n";
	print 'Score : <span style="color: red">' . $mbl_data_object['team1']['score'] . '</span>-<span style="color: blue">' . $mbl_data_object['team2']['score'] . '</span><br/>' . "\n";
	print 'Date de d&eacute;but : ' . $mbl_data_object['sdate'] . '<br/>' . "\n";
	print 'Date de fin : ' . $mbl_data_object['edate'] . '<br/>' . "\n";
	print 'Statut : ' . $mbl_data_object['status']['t'] . '<br/>' . "\n";
	*/
	echo "<a href=\"http://croquecoaching.kappatau.eu/index.php?pg=lst_mbl\">Retour à la liste des matchs</a><br/>\n";
	
	print 'Du ' . $mbl_data_object['sdate'].' au ' . $mbl_data_object['edate'] . '<br/>' . "\n";
	
	
	echo '<table border="0" width="100%"><tr>';
		echo '<td style="width:35%;vertical-align:top;">';
			//echo "<div style=\"display:block; height: 600px; overflow: auto; \">";
				echo "<table border=\"0\" style=\"width: 100%;\">";
				echo '<thead style=\"height:75px\">';
				echo "<tr><th colspan=\"8\"><h3>".urldecode($mbl_data_object['team1']['name'])." : ".$mbl_data_object['team1']['score']."</h3></th></tr>";
				echo "<tr><th>Agence</th>"
							  ."<th><img src=\"images/action.jpg\" alt=\"Point d'Action\" /></th>"
							  ."<th><img src=\"images/life.jpg\" alt=\"Point de vie\" /></th>"
							  ."<th><img src=\"images/fight.gif\" alt=\"Fight\" /></th>"
							  ."<th><img src=\"images/endurance.gif\" alt=\"Endurance\" /></th>"
							  ."<th><img src=\"images/kills.jpg\" alt=\"Kills\" /></th>"
							  ."<th><img src=\"images/goal.jpg\" alt=\"Goals\" /></th>"
							  ."<th><img src=\"images/attack.jpg\" alt=\"Attacks\" /></th>\n";
				echo '</thead>';
				echo '<tbody style=\"height:500px; overflow-y: visible; overflow-x:hidden;\">';
				$totalPV = 0;
				$totalPA = 0;
				$trStyle="";
				foreach ( $mbl_data_object['monsters'] as $monstre )
				{
					$trStyle="";
						if ($monstre['life'] == 0)
								$trStyle='style="text-decoration:line-through; background-color: grey;"';
					
						if ( $monstre['teamId'] == $mbl_data_object['team1']['id'] )	
						{		
							echo "<tr $trStyle><td>".urldecode($monstre['owner'])."</td>"
								        ."<td>".(($monstre['life']>0)?$monstre['actions']:"")."</td>"
								        ."<td>".(($monstre['life']>0)?$monstre['life']."/".$monstre['lifeMax']:"mort")."</td>"
								        ."<td>".$monstre['caracs']['fight']."</td>"
								        ."<td>".$monstre['caracs']['endurance']."</td>"
								        ."<td>".(isset($arMonsters[$monstre['id']]['kills'])?$arMonsters[$monstre['id']]['kills']:0)."</td>"
								        ."<td>".(isset($arMonsters[$monstre['id']]['goals'])?$arMonsters[$monstre['id']]['goals']:0)."</td>"
								        ."<td>".(isset($arMonsters[$monstre['id']]['hurts'])?$arMonsters[$monstre['id']]['hurts']." en ".$arMonsters[$monstre['id']]['fights']." fois":0)."</td>"
								    ."</tr>\n";
							
							if($monstre['life']>0)
							{
								$totalPV += $monstre['life'];
								$totalPA += $monstre['actions'];
							}
						}	    
								    
				}
				echo '</tbody>';
				
				echo '<thead style=\"height:25px\">';
				echo "<tr $trStyle><th>Total</th>"
								        ."<th>".$totalPA."</th>"
								        ."<th>".$totalPV."</th>"
								    ."</tr>\n";
				echo '</thead>';
				
				echo "</table>";
			//echo "</div>";
		echo "</td><td style=\"width:30%;vertical-align:top;\">";

			
				echo "<table  border=\"0\" class=\"head_fixe\" style=\"width: 100%; height: 100%;\">";
				echo "<thead style=\"height:75px\">"
						."<tr><th>Match entre les <a href=\"http://www.croquemonster.com/syndicate/view?id=" . $mbl_data_object['team1']['id'] . ";\">" . urldecode($mbl_data_object['team1']['name']) . "</a> et les <a href=\"http://www.croquemonster.com/syndicate/view?id=" . $mbl_data_object['team2']['id'] . ";\">" . urldecode($mbl_data_object['team2']['name']) . "</a>.</th></tr>"
						."</thead>";
				echo "<tbody style=\"font-size: 0.7em;  height: 525px;\">";
				for( $i = count($arActions)-1; $i>=0; $i-- )
				{
					$line = "<tr><td>".$arActions[$i]."</td></tr>";
					echo $line ;
				}
				echo "</tbody></table>";

		echo "</td><td style=\"width:35%;vertical-align:top;\">";
			//echo "<div style=\"display:block; height: 600px; overflow: auto; \">";
				echo "<table border=\"0\" style=\"width: 100%; \">";
				echo '<thead style=\"height:75px\">';
				echo "<tr><th colspan=\"8\"><h3>".urldecode($mbl_data_object['team2']['name'])." : ".$mbl_data_object['team2']['score']."</h3></th></tr>";
				echo "<tr><th>Agence</th>"
							  ."<th><img src=\"images/action.jpg\" alt=\"Point d'Action\" /></th>"
							  ."<th><img src=\"images/life.jpg\" alt=\"Point de vie\" /></th>"
							  ."<th><img src=\"images/fight.gif\" alt=\"Fight\" /></th>"
							  ."<th><img src=\"images/endurance.gif\" alt=\"Endurance\" /></th>"
							  ."<th><img src=\"images/kills.jpg\" alt=\"Kills\" /></th>"
							  ."<th><img src=\"images/goal.jpg\" alt=\"Goals\" /></th>"
							  ."<th><img src=\"images/attack.jpg\" alt=\"Attacks\" /></th>\n";
				echo '</thead>';
				echo '<tbody style=\"overflow-y: visible; overflow-x:hidden;\">';
				$totalPV = 0;
				$totalPA = 0;
				$trStyle="";
				foreach ( $mbl_data_object['monsters'] as $monstre )
				{
						$trStyle="";
						if ($monstre['life'] == 0)
								$trStyle='style="text-decoration:line-through; background-color: grey;"';

						if ( $monstre['teamId'] == $mbl_data_object['team2']['id'] )	
						{
								/*echo "<tr><td>".urldecode($monstre['name'])." (".urldecode($monstre['owner']).")</td>"*/
							  echo "<tr $trStyle><td>".urldecode($monstre['owner'])."</td>"
								        ."<td>".(($monstre['life']>0)?$monstre['actions']:"")."</td>"
								        ."<td>".(($monstre['life']>0)?$monstre['life']."/".$monstre['lifeMax']:"mort")."</td>"
								        ."<td>".$monstre['caracs']['fight']."</td>"
								        ."<td>".$monstre['caracs']['endurance']."</td>"
								        ."<td>".(isset($arMonsters[$monstre['id']]['kills'])?$arMonsters[$monstre['id']]['kills']:0)."</td>"
								        ."<td>".(isset($arMonsters[$monstre['id']]['goals'])?$arMonsters[$monstre['id']]['goals']:0)."</td>"
								        ."<td>".(isset($arMonsters[$monstre['id']]['hurts'])?$arMonsters[$monstre['id']]['hurts']." en ".$arMonsters[$monstre['id']]['fights']." fois":0)."</td>"
								    ."</tr>\n";
								if($monstre['life']>0)
								{
									$totalPV += $monstre['life'];
									$totalPA += $monstre['actions'];
								}
						}
				}
				echo '</tbody>';

				echo '<thead style=\"height:25px\">';
				echo "<tr $trStyle><th>Total</th>"
				        ."<th>".$totalPA."</th>"
				        ."<th>".$totalPV."</th>"
				    ."</tr>\n";
				echo '</thead>';
				echo "</table>";
			//echo "</div>";
		echo "</td>";
	echo '</tr></table>';

	?>
	
			<div style="position: relative; background-image: url(images/field.jpg); width: 803px; height: 435px;" >
			<div id="Nom_team_1" style="position: absolute; left: 99px; top: 9px; height: 18px; width: 229px; color: white; font-weight: bold; border: 0px solid black;">Team 1</div>
			<div id="Nom_team_2" style="position: absolute; left: 473px; top: 9px; height: 18px; width: 229px; color: white; font-weight: bold; border: 0px solid black;">Team 2</div>
			<div id="Score_team_1" style="position: absolute; left:  13px; top: 4px; height: 25px; width: 25px; color: white; font-weight: bold; border: 0px solid black;">0</div>
			<div id="Score_team_2" style="position: absolute; left: 756px; top: 4px; height: 25px; width: 25px; color: white; font-weight: bold; border: 0px solid black;">0</div>
			<?PHP
				for ( $y = 0; $y < 17; $y++ )
				{
						for ( $x = 0; $x < 35; $x++ )
						{
								$coord_x1 = 18+(22*$x);
								$coord_x2 = 38+(22*($x+1));
							  $coord_y1 = 49+(22*$y);
							  $coord_y2 = 71+(22*($y+1));
								echo "<div id=\"".$x."x".$y."\" style=\"position: absolute; left: ".$coord_x1."px; top: ".$coord_y1."px; height: 22px; width: 22px; color: white; font-weight: bold; border: 1px solid black;\"></div>\n";
						}
				}
			?>
			</div>	
			
			<script language="javascript">
			<!--
			<?PHP
			foreach ( $mbl_data_object['monsters'] as $monster )
			{
					if( $monster['life'] > 0 )	
					{
							$id=$monster['x']."x".$monster['y'];
							
							$infos =urldecode($monster['name'])." (".$monster['owner'].")<br/>";
							$infos.="PA : ".$monster['actions']."/24; PV : ".$monster['life']." / ".$monster['lifeMax'];
							
							if($monster['ballId']>0)
								$type='tadded';
							else
									$type='ingame';
							
							if( $monster['teamId'] == $mbl_data_object['team1']['id'] )
							{
									?>
									document.getElementById('<?PHP echo $id;?>').innerHTML = '<img src="images/monster_team1_<?PHP echo $type;?>.gif" style="width:22px; height:22px;" onMouseOver="showTooltip(\'<?PHP echo $infos;?>\');" onMouseOut="hideTooltip();">';
									<?PHP
							}
							else
							{
									?>
									document.getElementById('<?PHP echo $id;?>').innerHTML = '<img src="images/monster_team2_<?PHP echo $type;?>.gif" style="width:22px; height:22px;" onMouseOver="showTooltip(\'<?PHP echo $infos;?>\');" onMouseOut="hideTooltip();">';
									<?PHP
							}
					}
			}
			?>
			<?PHP
			foreach ( $mbl_data_object['boxes'] as $monster )
			{
					$id=$monster['x']."x".$monster['y'];
					?>
					document.getElementById('<?PHP echo $id;?>').innerHTML = "<img src=\"images/boxed_tad.gif\" style=\"width:22px; height:22px;\">";
					<?PHP
			}
					?>
			document.getElementById('Score_team_1').innerHTML = <?PHP echo $mbl_data_object['team1']['score']; ?>;
			document.getElementById('Score_team_2').innerHTML = <?PHP echo $mbl_data_object['team2']['score']; ?>;
			
			document.getElementById('Nom_team_1').innerHTML = '<?PHP echo urldecode($mbl_data_object["team1"]["name"]); ?>';
			document.getElementById('Nom_team_2').innerHTML = '<?PHP echo urldecode($mbl_data_object["team2"]["name"]); ?>';
			Nom_team_1
				
			-->
			</script>
			<img src="images/magneto_begin.gif">
			<img src="images/magneto_previous.gif">
			<img src="images/magneto_stop.gif">
			<img src="images/magneto_go.gif">
			<img src="images/magneto_next.gif">
			<img src="images/magneto_end.gif">


<?PHP 

if($debug)
	print_r($mbl_watcher_object); 
if($debug)
	print_r($mbl_data_object); 
?>