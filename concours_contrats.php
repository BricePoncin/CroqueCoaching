<?PHP

	include_once("sql.php");
	include_once("cm_api.inc.php");
	
	function agences($sxml)
	{
		//Acc&egrave;s aux enfants de l'&eacute;l&eacute;ment courant
		foreach($sxml->children() as $element)
		{
			if ( $element->getName() == "agency" )
			{
				$agency['id']=intval($element['id']);
				$agency['name']=strval($element['name']);
				$agency['score']=intval($element['score']);
				$agency['reputation']=intval($element['reputation']);
				$agency['level']=intval($element['level']);
				$agency['fartBox']=utf8_decode(strval($element['fartBox']));

				$arAgencies[] = $agency;
			}
			agences($element);
		}
		return ($arAgencies);
	}
/*	
	connect();
	$stmt  = "SELECT ccc_id, ccc_syndicat";
	$stmt .= "  FROM cm_cnc_contrats";
	$stmt .= " WHERE ccc_id = $conc_id";
	select($stmt, $arSyndicats);
	disconnect();
*/
	$arSyndicats[0]['ccc_syndicat'] = 'Dioxydedebob';
	$arSyndicats[1]['ccc_syndicat'] = 'DELIRIUM';
	$arSyndicats[2]['ccc_syndicat'] = 'BornToWin';
	
	foreach($arSyndicats as $idx => $syndic)
	{
		$url = "http://www.croquemonster.com/api/syndicate.xml?name=".$syndic['ccc_syndicat'];
		$xml = readXML( $url );
		if( substr($xml, 0, 6)== "Erreur" )
		{
				echo $xml;
				exit;
		}
		
		$arAgences = agences($xml);
		foreach($arAgences as $idx => $agency)
		{
			$url = "http://www.croquemonster.com/api/agency.xml?name=".$agency['name'];
			$xml = readXML( $url );
			if( substr($xml, 0, 6)== "Erreur" )
			{
					echo $xml;
					exit;
			}
			$points = $xml['contractsA']*0.5+$xml['contractsB']*1+$xml['contractsC']*2+$xml['contractsD']*3+$xml['contractsE']*10;
			
			echo $agency['name'].' => '.$points.' points<BR/>';
		}
	}
	

?>