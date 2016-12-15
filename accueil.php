<?PHP

require_once('sql.php');

connect();
$arNews=array();
select('select * from cm_news order by nw_date desc', $arNews);
disconnect();

		if (!isset($_SESSION['name']))
		{
?>
		<div id="infos" style="width:45%;float: left;">
			<p>Si vous ne savez pas comment vous connecter, vous avez besoin de votre cl&eacute; API.</p>
			<p>Pour r&eacute;cup&eacute;rer votre cl&eacute; API :</p>
			<ul>
					<li>Cliquez sur "Compte", tout en haut de la page Croque-Monster</li>
					<li>Une fois l&agrave;, choisissez "Acc&egrave;s API"</li>
					<li>Ensuite : "Activer l'acc&eacute;s ext&eacute;rieur et cr&eacute;er un mot de passe API"</li>
			</ul>
			<p>Votre cl&eacute; API apparait en vert</p>
			<p><strong>ENJOY</strong></p>
		</div>
<?PHP
		}
		else
		{
		?>
	<div id="infos" style="width:45%;float: left;">
			<table width="500" class="tab_accueil">
					<tr><td><img src="images/stat_syndicat.gif"></td>
							<td colspan="2"><span style="font-size: 1.2em;">Agence <strong><?PHP echo $xml['name'];?></strong> (<?PHP echo $xml ['days'];?> jours)</span></td></tr>

<tr><td colspan="3"><div  style="height:2px; border:1px solid black; margin-right:15px"></div></td></tr>
		
				<tr><td rowspan="2"><img src="images/stat_lvl.gif"></td><td colspan="2">Niveau <?PHP echo $xml['level']."</td></tr>";
			echo "<tr><td colspan=\"3\"> prochain niveau dans ".$contrats_restants." contrats</td></tr>";
		?>
<tr><td colspan="3"><div  style="height:2px; border:1px solid black; margin-right:15px"></div></td></tr>
				<tr><td rowspan="2"><img src="images/stat_city.gif"></td>
						<td colspan="2"><?PHP echo $xml['portails'];?> portails existants<BR/><small>Prix du prochain portail : <?PHP echo number_format( $prixProchain, 0, ".", " ");?>&nbsp;<img src="images/miniMoney.gif"></small></td></tr>
				<tr><td colspan="3"><?PHP echo $xml['cities'];?> villes g&eacute;r&eacute;es (<?PHP echo round($xml['cities']/429*100, 2)?> %).</td></tr>
		
<tr><td colspan="3"><div  style="height:2px; border:1px solid black; margin-right:15px"></div></td></tr>
		
				<tr><td><img src="images/stat_monster.gif"></td><td colspan="2"><a href="index.php?pg=monstres" style="font-size: 1em; text-decoration: underline;">Vos monstres</a> : 	<?PHP echo $xml['monsters']." / ".$xml['maxMonsters'];?></td></tr>
		
<tr><td colspan="3"><div  style="height:2px; border:1px solid black; margin-right:15px"></div></td></tr>
		
  	  	<tr><td rowspan="2"><img src="images/stat_afraid.gif"></td>
    				<td  colspan="2">Chiards effray&eacute;s :<?PHP if ( $xml['scared'] > 0 ) echo $xml['scared']." ( ".round($xml['scared']/($xml['scared']+$xml['devoured'])*100,1)." % )"; else echo "0 ( 0% )" ;?></td></tr>
	    	<tr><td colspan="2">Lardons boulott&eacute;s :<?PHP if ( $xml['devoured'] > 0 ) echo  $xml['devoured']." ( ".round($xml['devoured']/($xml['scared']+$xml['devoured'])*100,1)." % )"; else echo "0 ( 0% )"; ?></td></tr>

<tr><td colspan="3"><div  style="height:2px; border:1px solid black; margin-right:15px"></div></td></tr>

				<tr style="font-weight: bold""><td><img src="images/stat_contract.gif"></td><td><?PHP  echo $contrats_reussis." / ".($contrats_echoues+$contrats_reussis);?> contrats</td><td>R&eacute;ussite <?PHP if ($contrats_reussis > 0 ) echo round($contrats_reussis/($contrats_echoues+$contrats_reussis)*100, 2); else echo "0 "; ?> %</td></tr>
				<tr><td style="text-align: right;"><img src="images/stat_contract1.gif"></td><td><?PHP echo $xml['contractsA']." / ".($xml['contractsA']+$xml['failedA']);?> contrats</td><td>R&eacute;ussite <?PHP if ($xml['contractsA'] > 0 )       echo round($xml['contractsA']/($xml['contractsA']+$xml['failedA'])*100, 2); else echo "0 "; ?> %</td></tr>
				<tr><td style="text-align: right;"><img src="images/stat_contract2.gif"></td><td><?PHP echo $xml['contractsB']." / ".($xml['contractsB']+$xml['failedB']);?> contrats</td><td>R&eacute;ussite <?PHP if ($xml['contractsB'] > 0 )       echo round($xml['contractsB']/($xml['contractsB']+$xml['failedB'])*100, 2); else echo "0 "; ?> %</td></tr>
				<tr><td style="text-align: right;"><img src="images/stat_contract3.gif"></td><td><?PHP echo $xml['contractsC']." / ".($xml['contractsC']+$xml['failedC']);?> contrats</td><td>R&eacute;ussite <?PHP if ($xml['contractsC'] > 0 )       echo round($xml['contractsC']/($xml['contractsC']+$xml['failedC'])*100, 2); else echo "0 "; ?> %</td></tr>
				<tr><td style="text-align: right;"><img src="images/stat_contract4.gif"></td><td><?PHP echo $xml['contractsD']." / ".($xml['contractsD']+$xml['failedD']);?> contrats</td><td>R&eacute;ussite <?PHP if ($xml['contractsD'] > 0 )       echo round($xml['contractsD']/($xml['contractsD']+$xml['failedD'])*100, 2); else echo "0 "; ?> %</td></tr>
				<tr><td style="text-align: right;"><img src="images/stat_contract5.gif"></td><td><?PHP echo $xml['contractsE']." / ".($xml['contractsE']+$xml['failedE']);?> contrats</td><td>R&eacute;ussite <?PHP if ($xml['contractsE'] > 0 )       echo round($xml['contractsE']/($xml['contractsE']+$xml['failedE'])*100, 2); else echo "0 "; ?> %</td></tr>

<tr><td colspan="3"><div  style="height:2px; border:1px solid black; margin-right:15px"></div></td></tr>
				<tr><td rowspan="7"><img src="images/money.png"></td>
					  <td align="left">Cash</td><td align="right"><?PHP echo number_format( intval($xml['gold']), 0, ".", " "); ?>&nbsp;<img src="images/miniMoney.gif"></td></tr>
						<td align="left"><?PHP  echo $nbObjTot;?> objets humains</td><td align="right"><?PHP echo number_format( $totMoney, 0, ".", " ");?>&nbsp;<img src="images/miniMoney.gif"></td></tr>
						<td align="left">&nbsp;</td><td align="right"><small><?PHP echo " ou ".number_format( $totCO2, 3, ".", " ")." m&sup3; de CO2";?></small></td></tr>
            <td align="left"><?PHP  echo "$nbObjTotEq &eacute;quipements ";?></td><td align="right"><?PHP echo number_format( $totMoneyEq, 0, ".", " ");?>&nbsp;<img src="images/miniMoney.gif"></td></tr>
           	<td align="left"><?PHP  echo $portails;?> portails</td><td align="right"><?PHP echo number_format( $prixPortails, 0, ".", " ");?>&nbsp;<img src="images/miniMoney.gif"></td></tr>
            <td align="left"><?PHP  echo $nbMonstres;?> monstres</td><td align="right"><?PHP echo number_format( $mntMonstres, 0, ".", " ");?>&nbsp;<img src="images/miniMoney.gif"></td></tr>
            <td align="left">Total</td><td align="right"><?PHP echo number_format( $valTotale, 0, ".", " ");?>&nbsp;<img src="images/miniMoney.gif"></td></tr>
		</table>
				
	</div>
	
		<?PHP
		}
?>

<table class="head_fixe" style="height: 400px; padding-left:15px; margin-left: 50%; border-left: 1px solid navy; width: 50%">
<thead><tr><td>NEWS !!</td></tr></thead>
<tbody><tr><td>
<div id="news" style="padding-right:15px;margin-right:15px;">
<?PHP

		$nom_jour_fr = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
		$mois_fr = Array("", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", 
		        "septembre", "octobre", "novembre", "décembre");
		// on extrait la date du jour
		
		foreach($arNews as $news)
		{
			$date = $news['nw_date'];
			$arDate=explode('-', $date);
			list($nom_jour, $jour, $mois, $annee) = explode('/', date("w/d/n/Y", mktime(0, 0, 0, $arDate[1], $arDate[2], $arDate[0])));
			
			echo '<h3>'.$nom_jour_fr[$nom_jour].' '.$jour.' '.$mois_fr[$mois].' '.$annee.'</h3>'; 	
			echo utf8_decode($news['nw_text']).'<BR/>';
			
			
		}
?>
	</div>
		 
</td></tr><tbody>
</table>