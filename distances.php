<?PHP

function distances($p_Lat1, $p_Lon1, $p_Lat2, $p_Lon2 )
{
		$R = 6371;
		$dLat = deg2rad($p_Lat2 - $p_Lat1);
		$dLon = deg2rad($p_Lon2 - $p_Lon1);
		$a = pow(sin( ($dLat/2) ),2) + cos( deg2rad($p_Lat1) )*cos( deg2rad($p_Lat2) )*pow(sin( ($dLon/2) ), 2);
		$c = 2*atan2(sqrt($a), sqrt(1-$a));
		$distance = round( $R*$c , 2);
		
		return $distance;
}

function dec2min( $p_deg )
{
		$deg=floor($p_deg);
		$rest = $p_deg - $deg;	
		$min=floor($rest*60);
		
		$result = $deg*60 + $min;
		
		return $result;
}

function distances2($p_Lat1, $p_Lon1, $p_Lat2, $p_Lon2 )
{
	
	$distance = sqrt( pow( (dec2min($p_Lat2)-dec2min($p_Lat1)), 2) + pow( (dec2min($p_Lon2)-dec2min($p_Lon1)), 2) ) ;
	$distance = $distance * 1.852;
	return round($distance, 2);
}


$lat_vref=-6.71;
$lon_vref=108.56;

$lat_v1=-6.86;
$lon_v1=109.13;

$lat_v2=-6.97;
$lon_v2=110.42;

$lat_v3=-6.91;
$lon_v3=107.6;

$lat_v4=-6.18;
$lon_v4=106.83;

$distance = distances2($lat_vref, $lon_vref, $lat_v1, $lon_v1 );
echo "Tegal ".$distance."<BR/>";
$distance = distances2($lat_vref, $lon_vref, $lat_v2, $lon_v2 );
echo "Semarang ".$distance."<BR/>";
$distance = distances2($lat_vref, $lon_vref, $lat_v3, $lon_v3 );
echo "Bandung ".$distance."<BR/>";
$distance = distances2($lat_vref, $lon_vref, $lat_v4, $lon_v4 );
echo "Djakarta ".$distance."<BR/>";

$distance = distances2($lat_vref, $lon_vref, -7.57, 110.82 );
echo "Surakarta ".$distance."<BR/>";
$distance = distances2($lat_vref, $lon_vref, -5.45, 105.16 );
echo "Bandar Lampung ".$distance."<BR/>";
$distance = distances2($lat_vref, $lon_vref, -7.78, 110.37 );
echo "Yogyakarta ".$distance."<BR/>";
$distance = distances2($lat_vref, $lon_vref, -2.99, 104.75 );
echo "Palembang ".$distance."<BR/>";
$distance = distances2($lat_vref, $lon_vref, -7.24, 112.74 );
echo "Surabaya ".$distance."<BR/>";




?>