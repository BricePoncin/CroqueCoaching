<?PHP

	include_once("sql.php");
	include_once("session.php");

	$arObjets[ '200']='Banane pr&eacute;-historique';
    $arObjets[ '300']='Bi&egrave;re Hk';
    $arObjets[ '400']='Sandwich happy-days BigMarx';
    $arObjets[ '500']='Clef &agrave; molette industrielle';
    $arObjets[ '600']='Gros cr&acirc;ne usag&eacute;';
    $arObjets[ '800']='Tableau du 16e';
    $arObjets[ '900']='&eacute;norme citrouille bien m&eacute;chante';
    $arObjets['1000']='Tuyau flexible';
	
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
	
	function concours_getList()
	{
		connect();
		$stmt  = "SELECT cnc_id, cnc_dt_debut, cnc_dt_fin, cnc_desc, cnc_type, cnc_obj, cnc_war";
		$stmt .= " FROM cm_concours";
		select($stmt, $arConcours);
		disconnect();
		
		return $arConcours;
	}
	
	function concours_ajouter($dateDeb, $dateFin, $desc, $type, $objet)
	{
		connect();
		
		$stmt = "INSERT INTO `cm_concours` (`cnc_dt_debut`, `cnc_dt_fin`, `cnc_desc`, `cnc_type`, `cnc_obj`) VALUES
			('$dateDeb', '$dateFin', '$desc', $type, $objet)";
		echo $stmt.'<BR/>';
		insert($stmt);
		disconnect();
		
		return;
	}
	
	function concours_result($cnc_id)
	{
		connect();
		
		$stmt  = "SELECT cnc_id, cnc_dt_debut, cnc_dt_fin, cnc_desc, cnc_type, cnc_obj, cnc_war";
		$stmt .= "  FROM cm_concours";
		$stmt .= " WHERE cnc_id=$cnc_id";
		
		select($stmt, $arRes);
		disconnect();
		
		if ( $arRes[0]['cnc_war'] != NULL )
			$szWar = sprintf("_w%03d\n", $arRes[0]['cnc_war']);
		else
			$szWar = '';

		switch($arRes[0]['cnc_type'])
		{
			case 1: $arResult=concours_inc_size_ag( $arRes[0]['cnc_dt_debut'], $arRes[0]['cnc_dt_fin'], $szWar ); break;
			case 2: $arResult=concours_inc_size_sy( $arRes[0]['cnc_dt_debut'], $arRes[0]['cnc_dt_fin'], $szWar ); break;
			case 3: $arResult=concours_paradox ( $arRes[0]['cnc_dt_debut'], $arRes[0]['cnc_dt_fin'], $szWar ); break;
			case 4: $arResult=concours_recycl  ( $arRes[0]['cnc_dt_debut'], $arRes[0]['cnc_dt_fin'], $szWar ); break;
			case 5: $arResult=concours_res_by_objects( $arRes[0]['cnc_obj'], $arRes[0]['cnc_dt_debut'], $arRes[0]['cnc_dt_fin'], $szWar ); break;
		}

		$arResult['cnc_desc'] = $arRes[0]['cnc_desc'];
		return $arResult;
	}

	function concours_inc_size_ag( $dt_deb, $dt_fin, $szWar )
	{
		connect();

		$stmt  = "SELECT user as User, sum(quantity) as Decompte, min(dl.date) as dt_first";
		$stmt .= "  FROM cm_dumplog".$szWar." dl ";
		$stmt .= " WHERE date between '$dt_deb' AND '$dt_fin'";  
		$stmt .= " GROUP BY user";
		$stmt .= " ORDER BY Decompte DESC, dt_first ASC";

		select($stmt, $arResults);
		disconnect();
		
		return $arResults;
		
	}

	function concours_inc_size_sy( $dt_deb, $dt_fin, $szWar )
	{
		connect();
		
		$stmt  = "SELECT syndicate_id as User, sum(quantity) as Decompte, min(dl.date) as dt_first";
		$stmt .= "  FROM cm_dumplog".$szWar." dl ";
		$stmt .= " WHERE date between '$dt_deb' AND '$dt_fin'";  
		$stmt .= " GROUP BY syndicate_id";
		$stmt .= " ORDER BY Decompte DESC, dt_first ASC";
		
		select($stmt, $arResults);
		disconnect();
		
		return $arResults;
		
	}
	
	function concours_paradox( $dt_deb, $dt_fin, $szWar )
	{
		connect();
		
		$stmt  = "SELECT user as User, count(1) as Decompte, min(dl.date) as dt_first";
		$stmt .= "  FROM cm_dumplog".$szWar." dl ";
		$stmt .= " WHERE type = 1";
		$stmt .= "   AND date between '$dt_deb' AND '$dt_fin'";  
		$stmt .= " GROUP BY user";
		$stmt .= " ORDER BY Decompte DESC, dt_first ASC";
		select($stmt, $arResults);
		disconnect();
		
		return $arResults;
		
	}
	
	function concours_recycl( $dt_deb, $dt_fin, $szWar )
	{
		connect();
		
		$stmt  = "SELECT user as User, sum(quantity) as Decompte, min(dl.date) as dt_first";
		$stmt .= "  FROM cm_dumplog".$szWar." dl ";
		$stmt .= " WHERE type = 2";
		$stmt .= "   AND date between '$dt_deb' AND '$dt_fin'";  
		$stmt .= " GROUP BY user";
		$stmt .= " ORDER BY Decompte DESC, dt_first ASC";
		select($stmt, $arResults);
		disconnect();
		
		return $arResults;
		
	}

	function concours_res_by_objects($type_obj, $dt_deb, $dt_fin, $szWar)
	{
		connect();

		$stmt  = "SELECT dl.user as User, sy.cm_syndic_name as Syndic, count(1) as Decompte, min(dl.date) as dt_first";
		$stmt .= "  FROM cm_dumplog".$szWar." dl ";
		$stmt .= "       INNER JOIN cm_syndicats sy on sy.cm_syndic_id = dl.syndicate_id ";
		$stmt .= " WHERE dl.type = 1";							 
		$stmt .= "   AND dl.date between '$dt_deb' AND '$dt_fin'";  
		$stmt .= "   AND dl.quantity = $type_obj"; 			 
		$stmt .= " GROUP BY dl.user";							 
		$stmt .= " ORDER BY Decompte DESC, dt_first ASC";

		select($stmt, $arResults);
		disconnect();
		
		return $arResults;
		
	}
	
	
?>