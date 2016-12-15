<?PHP

	if( isset($_POST['sens_tri']) )
			$sens_tri = $_POST['sens_tri'];
	else
			$sens_tri = "ville";


	
	function parse_api_portails($sxml, $tag)
		{
			$arTable=array();
			
				foreach($sxml->children() as $element)
		  	{
		  		if ( $element->getName() == $tag )
		  		{
		  				$objet['id']	    = intval( $element['id']      );
		  				$objet['city']	  = utf8_decode(strval($element['city']   ));
							$objet['country'] = utf8_decode(strval($element['country']));
							$objet['level']   = intval( $element['level']     );
							$objet['timezone']= intval( $element['timezone']     );
							$objet['defense'] = intval( $element['defense']     );
							
							$arTable[ $objet['city'] ] = $objet;
		  		}
		      parse_api_portails($element, $tag);
		  	}
		  	
		  	return($arTable);
		 }
	
function parse_api_cities($sxml)
		{
		  //Acc&egrave;s aux enfants de l'&eacute;l&eacute;ment courant
		  foreach($sxml->children() as $element)
		  {
		  		if ( $element->getName() == "city" )
		  		{
		  				$city['id']=intval($element['id']);
							$city['name']=utf8_decode(strval($element['name']));
							$city['country']=utf8_decode(strval($element['country']));
							$city['reputation']=intval($element['reputation']);
							$city['percent']=intval($element['percent']);
							$city['infernos']=intval($element['infernos']);
							
							$arCities[ $city['name'] ] = $city;
		  		}
		      parse_api_cities($element);
		  }
			return ($arCities);
		}

		function parse_xml_cities($sxml, $tag)
		{
			$arTable=array();
			
				foreach($sxml->children() as $element)
		  	{
		  		if ( $element->getName() == $tag )
		  		{
		  				$objet['id']	    = intval  ( $element['id']      );
		  				$objet['name']	  = utf8_decode(strval  ( $element['name']   ));
							$objet['country'] = utf8_decode(strval  ( $element['country']));
							$objet['lat']     = floatval( $element['lat']     );
							$objet['lon']     = floatval( $element['lon']     );
							$objet['pop']     = intval  ( $element['pop']     );
							$objet['lvl']     = intval  ( $element['lvl']     );
							$objet['tz']      = intval  ( $element['tz']      );
							
							$arTable[ $objet['name'] ] = $objet;
		  		}
		      parse_xml_cities($element, $tag);
		  	}
		  	
		  	return($arTable);
		 }

		$url = "http://www.croquemonster.com/api/cities.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xmlCities = readXML( $url );
		if( substr($xmlCities, 0, 6)== "Erreur" )
		{
				echo $xmlCities;
				exit;
		}
		$arCities = parse_api_cities($xmlCities);
		
		$xml = readXML( "http://www.croquemonster.com/xml/cities.xml" );
		if( substr($xml, 0, 6)== "Erreur" )
		{
				echo $xml;
				exit;
		}
		$arVilles = parse_xml_cities($xml, 'city');

		$url = "http://www.croquemonster.com/api/portails.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
		$xmlPortails = readXML( $url );
		if( substr($xmlPortails, 0, 6)== "Erreur" )
		{
				echo $xmlPortails;
				exit;
		}
		$arPortails = parse_api_portails($xmlPortails, 'portail');
		

	
						
foreach( $arVilles as &$ville )
{
	
	if( array_key_exists ( $ville['name'] , $arCities ) === true )
{
				unset($arVilles['name']);
			 continue;
	}		
	
		$latOri = $ville['lat'];
		$lonOri = $ville['lon'];
		$nbLvl1 = 0;
		$nbLvl2 = 0;
		$nbLvl3 = 0;
		$nbLvl4 = 0;
		$nbLvl5 = 0;
		
		foreach( $arVilles as $villeTst )
		{
				if( array_key_exists ( $villeTst['name'] , $arCities ) === true )
					continue;		
				$dst = distance($latOri, $lonOri, $villeTst['lat'], $villeTst['lon']);
				if($dst <= 6)  $nbLvl5++;
				if($dst <= 5)  $nbLvl4++;
				if($dst <= 4)  $nbLvl3++;
				if($dst <= 3)  $nbLvl2++;
				if($dst <= 2)  $nbLvl1++;
		}

		$acces = '';
		$acc_lvl = 0;
		foreach($arPortails as $portail)
		{
				if($acces != "")
					break;
				for( $lvl_port = $portail['level']+1; $lvl_port <= 5; $lvl_port++ )
				{
					//echo "On teste si ".$ville['name']." est accessible via ".$portail['city']." au niveau ".$lvl_port."<br/>\n";
					//echo $latOri." ".$lonOri." ".$arVilles[$portail['city']]['lat']." ".$arVilles[$portail['city']]['lon']." ".$lvl_port."<BR/>\n";
						if( is_accessible($latOri, $lonOri, $arVilles[$portail['city']]['lat'], $arVilles[$portail['city']]['lon'], $lvl_port ) )
						{
								//echo $ville['name']." accessible via ".$portail['city']." au niveau ".$lvl_port."<br/>\n";
								$acces = $portail['city'];
								$acc_lvl = $lvl_port;
								$lvl_port=999;
						}
				}
		}
		
		$ville['dst1']    = $nbLvl1;
		$ville['dst2']    = $nbLvl2;
		$ville['dst3']    = $nbLvl3;
		$ville['dst4']    = $nbLvl4;
		$ville['dst5']    = $nbLvl5;
		$ville['acces']   = $acces;
		$ville['acc_lvl'] = $acc_lvl;
		
		
    $arVillesManquantes[$ville['name']] = $ville;
}



$arVille = array();
$arPays  = array();
$arTime  = array();
$arDiff  = array();
$arL1    = array();
$arL2    = array();
$arL3    = array();
$arL4    = array();
$arL5    = array();
$arAcces = array();

foreach( $arVillesManquantes as $key => $data )
{
		$arVille[$key] = $data['name'];
		$arPays [$key] = $data['country'];
		$arTime [$key] = $data['tz'];
		$arDiff [$key] = $data['lvl'];
		$arL1   [$key] = $data['dst1'];
		$arL2   [$key] = $data['dst2'];
		$arL3   [$key] = $data['dst3'];
		$arL4   [$key] = $data['dst4'];
		$arL5   [$key] = $data['dst5'];
		$arAcces[$key] = $data['acces'];
}                          

switch($sens_tri)
{
case 'ville': array_multisort($arVille, SORT_ASC , $arPays , SORT_ASC, $arVillesManquantes ); break;
case 'pays' : array_multisort($arPays , SORT_ASC , $arVille, SORT_ASC, $arVillesManquantes ); break;
case 'time' : array_multisort($arTime , SORT_DESC, $arVille, SORT_ASC, $arPays , SORT_ASC, $arVillesManquantes ); break;
case 'diff' : array_multisort($arDiff , SORT_DESC, $arVille, SORT_ASC, $arPays , SORT_ASC, $arVillesManquantes ); break;
case 'L1'   : array_multisort($arL1   , SORT_DESC, $arVille, SORT_ASC, $arPays , SORT_ASC, $arVillesManquantes ); break;
case 'L2'   : array_multisort($arL2   , SORT_DESC, $arVille, SORT_ASC, $arPays , SORT_ASC, $arVillesManquantes ); break;
case 'L3'   : array_multisort($arL3   , SORT_DESC, $arVille, SORT_ASC, $arPays , SORT_ASC, $arVillesManquantes ); break;
case 'L4'   : array_multisort($arL4   , SORT_DESC, $arVille, SORT_ASC, $arPays , SORT_ASC, $arVillesManquantes ); break;
case 'L5'   : array_multisort($arL5   , SORT_DESC, $arVille, SORT_ASC, $arPays , SORT_ASC, $arVillesManquantes ); break;
case 'acces': array_multisort($arAcces, SORT_DESC, $arVille, SORT_ASC, $arPays , SORT_ASC, $arVillesManquantes ); break;
}
?>
<h2>Portails Manquants</h2>
<a href="index.php?pg=portails">vers les villes contrôlées</a>
<form action="#self" method="POST" name="frm_villes">
<input type="hidden" id="maj" name="maj" value="1">
<input type="hidden" id="sens_tri" name="sens_tri" value="<?PHP echo $sens_tri; ?>">
<table id="portails" class="head_fixe">
<thead>
<tr>
<th colspan=\"4\">&nbsp;</th>
<th colspan=\"5\">Nombre de villes accessible selon le niveau du portail</th>
<th>&nbsp;</th>
</tr>
<tr>
<th><a onclick="document.getElementById('sens_tri').value='ville'; document.forms['frm_villes'].submit();">Ville</a></th>
<th><a onclick="document.getElementById('sens_tri').value='pays' ; document.forms['frm_villes'].submit();">Pays</a></th>
<th><a onclick="document.getElementById('sens_tri').value='time' ; document.forms['frm_villes'].submit();"><img src="images/time.gif"></a></th>
<th><a onclick="document.getElementById('sens_tri').value='diff' ; document.forms['frm_villes'].submit();"><img src="images/lvl.gif"></a></th>
<th><a onclick="document.getElementById('sens_tri').value='L1'   ; document.forms['frm_villes'].submit();">L1</a></th>
<th><a onclick="document.getElementById('sens_tri').value='L2'   ; document.forms['frm_villes'].submit();">L2</a></th>
<th><a onclick="document.getElementById('sens_tri').value='L3'   ; document.forms['frm_villes'].submit();">L3</a></th>
<th><a onclick="document.getElementById('sens_tri').value='L4'   ; document.forms['frm_villes'].submit();">L4</a></th>
<th><a onclick="document.getElementById('sens_tri').value='L5'   ; document.forms['frm_villes'].submit();">L5</a></th>
<th><a onclick="document.getElementById('sens_tri').value='acces'; document.forms['frm_villes'].submit();">Accessible via</a></th>
</tr>
</thead>

<?PHP
$idx=0;
foreach( $arVillesManquantes as $ville )
{
	/*
	if( array_key_exists ( $ville['name'] , $arCities ) === true )
			continue;
	*/
	if($idx%2 == 0)
			$class=" class=\"pair\"";
	else
			$class=" class=\"impair\"";
	if($ville['acces'] != "")
			$acces = $ville['acces']." (niveau ".$ville['acc_lvl'].")";
	else
			$acces = '';
		echo "<tr$class>"
		."<td>".$ville['name']."</td>"
		."<td>".$ville['country']."</td>"
		."<td>".$ville['tz']."</td>"
		."<td>".$ville['lvl']."</td>"
		."<td>".$ville['dst1']."</td>"
		."<td>".$ville['dst2']."</td>"
		."<td>".$ville['dst3']."</td>"
		."<td>".$ville['dst4']."</td>"
		."<td>".$ville['dst5']."</td>"
		."<td class=\"last_col\">".$acces."</td>"
		."</tr>";
		
		$idx++;
}
?> 
</table>
</form>

