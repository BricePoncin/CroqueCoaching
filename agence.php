<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<HEAD>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
		<link rel="shortcut icon" href="favicon.ico" />
		<link rel="icon" type="image/gif" href="animated_favicon1.gif" />
		<link rel="stylesheet" type="text/css" href="class.css" />
</head>
<body>
<?PHP

		require_once("cm_api.inc.php");

		if ( isset($_GET['id']))
				$agence_id=$_GET['id'];
		else
				exit(-1);

		$url = "http://www.croquemonster.com/api/agency.xml?id=".$agence_id;
		$xml = readXML( $url );
		if( substr($xml, 0, 6)== "Erreur" )
		{
				echo $xml;
				exit;
		}
					
		$contrats_reussis = $xml['contractsA']+$xml['contractsB']+$xml['contractsC']+$xml['contractsD']+$xml['contractsE'];
		$contrats_echoues = $xml['failedA']+$xml['failedB']+$xml['failedC']+$xml['failedD']+$xml['failedE'];
		
		if($xml['level'] == 1)
				$contrats_restants = 3-$contrats_reussis;
		else if($xml['level'] == 2)
				$contrats_restants = 9-$contrats_reussis;
		else if($xml['level'] == 3)
				$contrats_restants = 16-$contrats_reussis;
		else
				$contrats_restants = (1.2 + ($xml['level']-4)*0.4 )*($xml['level']+1)*($xml['level']+1) - $contrats_reussis;

?>

	<div id="infos" style="width:45%;float: left;">
			<table>
					<tr><td width="50"><img src="images/stat_syndicat.gif"></td>
						<td colspan="2">Agence <span style="font-weigth: bold; color:orange;"><?PHP echo $xml['name'];?></span> (<?PHP echo $xml ['days'];?> jours)</td>
						<td rowspan="19">
							<?PHP 
								echo ( $xml[0]->description );
							?>
						</td>
					</tr>
		
<tr><td colspan="3"><div  style="height:2px; border:1px solid black; margin-right:15px"></div></td></tr>
		
				<tr><td rowspan="2"><img src="images/stat_lvl.gif"></td>
					<td colspan="2">Niveau <?PHP echo $xml['level']; ?></td>
				</tr>
				<tr><td colspan="2"><?PHP echo "prochain niveau dans ".$contrats_restants." contrats"; ?></td></tr>
<tr><td colspan="3"><div  style="height:2px; border:1px solid black; margin-right:15px"></div></td></tr>
				<tr><td rowspan="2"><img src="images/stat_city.gif"></td>
						<td colspan="2"><?PHP echo $xml['portails'];?> portails existants</td></tr>
				<tr><td colspan="2"><?PHP echo $xml['cities']; ?> villes g&eacute;r&eacute;es (<?PHP echo round($xml['cities']/426*100, 2); ?> %).</td></tr>
		
<tr><td colspan="3"><div  style="height:2px; border:1px solid black; margin-right:15px"></div></td></tr>
		
				<tr><td><img src="images/stat_monster.gif"></td><td colspan="2"><a>Monstres</a> : 	<?PHP echo $xml['monsters']." / ".$xml['maxMonsters'];?></td></tr>
		
<tr><td colspan="3"><div  style="height:2px; border:1px solid black; margin-right:15px"></div></td></tr>
		
  	  	<tr><td rowspan="2"><img src="images/stat_afraid.gif"></td>
    				<td  colspan="2">Chiards effray&eacute;s :<?PHP echo $xml['scared']." ( ".round($xml['scared']/($xml['scared']+$xml['devoured'])*100,1)." % )"; ?></td></tr>
	    	<tr><td colspan="2">Lardons boulott&eacute;s :<?PHP echo  $xml['devoured']." ( ".round($xml['devoured']/($xml['scared']+$xml['devoured'])*100,1)." % )"; ?></td></tr>

<tr><td colspan="3"><div  style="height:2px; border:1px solid black; margin-right:15px"></div></td></tr>

				<tr style="font-weight: bold""><td><img src="images/stat_contract.gif"></td><td><?PHP  echo $contrats_reussis." / ".($contrats_echoues+$contrats_reussis);?> contrats</td><td>R&eacute;ussite <?PHP if ($contrats_reussis > 0 ) echo round($contrats_reussis/($contrats_echoues+$contrats_reussis)*100, 2); else echo "0 "; ?> %</td></tr>
				<tr><td style="text-align: right;"><img src="images/stat_contract1.gif"></td><td><?PHP echo $xml['contractsA']." / ".($xml['contractsA']+$xml['failedA']);?> contrats</td><td>R&eacute;ussite <?PHP if ($xml['contractsA'] > 0 )       echo round($xml['contractsA']/($xml['contractsA']+$xml['failedA'])*100, 2); else echo "0 "; ?> %</td></tr>
				<tr><td style="text-align: right;"><img src="images/stat_contract2.gif"></td><td><?PHP echo $xml['contractsB']." / ".($xml['contractsB']+$xml['failedB']);?> contrats</td><td>R&eacute;ussite <?PHP if ($xml['contractsB'] > 0 )       echo round($xml['contractsB']/($xml['contractsB']+$xml['failedB'])*100, 2); else echo "0 "; ?> %</td></tr>
				<tr><td style="text-align: right;"><img src="images/stat_contract3.gif"></td><td><?PHP echo $xml['contractsC']." / ".($xml['contractsC']+$xml['failedC']);?> contrats</td><td>R&eacute;ussite <?PHP if ($xml['contractsC'] > 0 )       echo round($xml['contractsC']/($xml['contractsC']+$xml['failedC'])*100, 2); else echo "0 "; ?> %</td></tr>
				<tr><td style="text-align: right;"><img src="images/stat_contract4.gif"></td><td><?PHP echo $xml['contractsD']." / ".($xml['contractsD']+$xml['failedD']);?> contrats</td><td>R&eacute;ussite <?PHP if ($xml['contractsD'] > 0 )       echo round($xml['contractsD']/($xml['contractsD']+$xml['failedD'])*100, 2); else echo "0 "; ?> %</td></tr>
				<tr><td style="text-align: right;"><img src="images/stat_contract5.gif"></td><td><?PHP echo $xml['contractsE']." / ".($xml['contractsE']+$xml['failedE']);?> contrats</td><td>R&eacute;ussite <?PHP if ($xml['contractsE'] > 0 )       echo round($xml['contractsE']/($xml['contractsE']+$xml['failedE'])*100, 2); else echo "0 "; ?> %</td></tr>
		</table>
				
	</div>
</body>
</html>