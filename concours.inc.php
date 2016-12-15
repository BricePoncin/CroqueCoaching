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
		$stmt  = "SELECT cnc_id, cnc_dt_debut, cnc_dt_fin, cnc_desc, cnc_type, cnc_obj";
		$stmt .= " FROM cm_concours";
		select($stmt, $arConcours);
		disconnect();
		
		return $arConcours;
	}
	
	function concours_result($cnc_id)
	{
		connect();
		$stmt  = "SELECT cnc_id, cnc_dt_debut, cnc_dt_fin, cnc_desc, cnc_type, cnc_obj";
		$stmt .= "  FROM cm_concours";
		$stmt .= " WHERE cnc_id=$cnc_id";
		select($stmt, $arRes);
		disconnect();
		
		switch($arRes[0]['cnc_type'])
		{
			case 1: $arResult=concours_inc_size_ag( $arRes[0]['cnc_dt_debut'], $arRes[0]['cnc_dt_fin'] ); break;
			case 2: $arResult=concours_inc_size_sy( $arRes[0]['cnc_dt_debut'], $arRes[0]['cnc_dt_fin'] ); break;
			case 3: $arResult=concours_paradox ( $arRes[0]['cnc_dt_debut'], $arRes[0]['cnc_dt_fin'] ); break;
			case 4: $arResult=concours_recycl  ( $arRes[0]['cnc_dt_debut'], $arRes[0]['cnc_dt_fin'] ); break;
			case 5: $arResult=concours_res_by_objects( $arRes[0]['cnc_obj'], $arRes[0]['cnc_dt_debut'], $arRes[0]['cnc_dt_fin'] ); break;
		}

		$arResult['cnc_desc'] = $arRes[0]['cnc_desc'];
		return $arResult;
	}

	function concours_inc_size_ag( $dt_deb, $dt_fin )
	{
		connect();

		$stmt  = "SELECT user as User, sum(quantity) as Decompte";
		$stmt .= "  FROM cm_dumplog ";
		$stmt .= " WHERE date between '$dt_deb' AND '$dt_fin'";  
		$stmt .= " GROUP BY user";
		$stmt .= " ORDER BY sum(quantity)  DESC, user ASC";

		select($stmt, $arResults);
		disconnect();
		
		return $arResults;
		
	}

	function concours_inc_size_sy( $dt_deb, $dt_fin )
	{
		connect();
		
		$stmt  = "SELECT syndicate_id as User, sum(quantity) as Decompte";
		$stmt .= "  FROM cm_dumplog ";
		$stmt .= " WHERE date between '$dt_deb' AND '$dt_fin'";  
		$stmt .= " GROUP BY syndicate_id";
		$stmt .= " ORDER BY sum(quantity)  DESC, syndicate_id ASC";
		
		select($stmt, $arResults);
		disconnect();
		
		return $arResults;
		
	}
	
	function concours_paradox( $dt_deb, $dt_fin )
	{
		connect();
		
		$stmt  = "SELECT user as User, count(1) as Decompte";
		$stmt .= "  FROM cm_dumplog ";
		$stmt .= " WHERE type = 1";
		$stmt .= "   AND date between '$dt_deb' AND '$dt_fin'";  
		$stmt .= " GROUP BY user";
		$stmt .= " ORDER BY count(1)  DESC, user ASC";
		select($stmt, $arResults);
		disconnect();
		
		return $arResults;
		
	}
	
	function concours_recycl( $dt_deb, $dt_fin )
	{
		connect();
		
		$stmt  = "SELECT user as User, count(1) as Decompte";
		$stmt .= "  FROM cm_dumplog ";
		$stmt .= " WHERE type = 2";
		$stmt .= "   AND date between '$dt_deb' AND '$dt_fin'";  
		$stmt .= " GROUP BY user";
		$stmt .= " ORDER BY count(1)  DESC, user ASC";
		select($stmt, $arResults);
		disconnect();
		
		return $arResults;
		
	}

	function concours_res_by_objects($type_obj, $dt_deb, $dt_fin)
	{
		connect();
		
		$stmt  = "SELECT user as User, count(1) as Decompte";
		$stmt .= "  FROM cm_dumplog ";						 
		$stmt .= " WHERE type = 1";							 
		$stmt .= "   AND date between '$dt_deb' AND '$dt_fin'";  
		$stmt .= "   AND quantity = $type_obj"; 			 
		$stmt .= " GROUP BY user";							 
		$stmt .= " ORDER BY count(1)  DESC, user ASC";		 
		
		select($stmt, $arResults);
		disconnect();
		
		return $arResults;
		
	}
	
	
?>