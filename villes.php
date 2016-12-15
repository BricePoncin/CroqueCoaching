<?PHP
	
	error_reporting(E_ALL);
	require_once("cm_api.inc.php");


function distance4($p_Lat1, $p_Lon1, $p_Lat2, $p_Lon2 )
{
		$a = pow(($p_Lat2 - $p_Lat1), 2);
		$b = pow(($p_Lon2 - $p_Lon1), 2);
		
		$distance = sqrt( $a + $b );
		
		return $distance;
}

function distances($p_Lat1, $p_Lon1, $p_Lat2, $p_Lon2, $distance )
{
		if ( distance4($p_Lat1, $p_Lon1, $p_Lat2, $p_Lon2 ) <= $distance+1 )
		return 1;
		else
		return 0;
			
}

function parseXML($sxml, $tag)
				{
					$arTable=array();
					
						foreach($sxml->children() as $element)
				  	{
				  		if ( $element->getName() == $tag )
				  		{
				  				$objet['id']=intval($element['id']);
				  				$objet['name']=strval($element['name']);
									$objet['country']=strval($element['country']);
									$objet['lat']=floatval($element['lat']);
									$objet['lon']=floatval($element['lon']);
									
									$arTable[] = $objet;
				  		}
				      parseXML($element, $tag);
				  	}
				  	
				  	return($arTable);
				 }

$url = "http://www.croquemonster.com/xml/cities.xml";
$xmlVilles = readXML( $url );
if( substr($xmlVilles, 0, 6)== "Erreur" )
{
		echo $xmlVilles;
		exit;
}

$arVilles = parseXML($xmlVilles, 'city');
	
echo "<table>";						
//foreach( $xmlVilles as $villeOri )
for( $i = 0 ; $i < count($arVilles) ; $i++ )
{
	$villeOri = $arVilles[$i];
	
		$latOri = $villeOri['lat'];
		$lonOri = $villeOri['lon'];
		$nbLvl1 = 0;
		$nbLvl2 = 0;
		$nbLvl3 = 0;
		$nbLvl4 = 0;
		$nbLvl5 = 0;
		
		foreach( $xmlVilles as $villeTst )
		{	
				$nbLvl1 += distances($latOri, $lonOri, floatval($villeTst['lat']), floatval($villeTst['lon']), 1 );
				$nbLvl2 += distances($latOri, $lonOri, floatval($villeTst['lat']), floatval($villeTst['lon']), 2 );
				$nbLvl3 += distances($latOri, $lonOri, floatval($villeTst['lat']), floatval($villeTst['lon']), 3 );
				$nbLvl4 += distances($latOri, $lonOri, floatval($villeTst['lat']), floatval($villeTst['lon']), 4 );
				$nbLvl5 += distances($latOri, $lonOri, floatval($villeTst['lat']), floatval($villeTst['lon']), 5 );
		}
		
		echo "<tr><td>".$villeOri['id']."</td><td>".$villeOri['name']."</td><td>".$villeOri['country']."</td><td>".$nbLvl1."</td><td>".$nbLvl2."</td><td>".$nbLvl3."</td><td>".$nbLvl4."</td><td>".$nbLvl5."</td></tr>";

}
echo "</table>";


?> 