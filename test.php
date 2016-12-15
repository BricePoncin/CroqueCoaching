<?php

$timezones = DateTimeZone::listAbbreviations();

$cities = array();
$tz = array();
foreach( $timezones as $key => $zones )
{
    foreach( $zones as $id => $zone )
    {
        /**
         * Only get timezones explicitely not part of "Others".
         * @see http://www.php.net/manual/en/timezones.others.php
         */
        if ( preg_match( '/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//', $zone['timezone_id'] ) )
        {
        		list($continent, $ville) = explode('/', $zone['timezone_id']);
            $cities[$zone['timezone_id']][] = $key;
            
            $tz[$continent][$ville] = $zone['timezone_id'];
        }
    }
}

foreach( $tz as $zone => $ville )
		asort( $tz[$zone] );
		
//echo json_encode($tz);
?>

<script src="js/jquery-1.5.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
	// <![CDATA[	
		
		$(document).ready(function() 
		{
				$(function() 
				{
					alert("Ici ?");
						$("#sel_zones").change(function() 
						{
								var json=<?= json_encode($tz); ?>;

						    var options = '';
								$.each(json[ $("#sel_zones").val() ], function(key, value) {
								   options += '<option value="'+value+'">'+key+'</option>';
								});
				  			//alert( options );
				  			
		  					$("#sel_villes").html( options );
		  					//$("#sel_villes").html( "<option value='1'>Some oranges</option><option value='2'>More Oranges</option><option value='3'>Even more oranges</option>");
		  			});
		  	});
		});
</script>	

?>
	<form>
			Zone géographique : 
				<select id="sel_zones">
						<option>America</option>
						<option>Antartica</option>
						<option>Arctic</option>
						<option>Asia</option>
						<option>Atlantic</option>
						<option>Europe</option>
						<option>Indian</option>
						<option>Pacific</option>
				</select>
			Ville de référence : 
				<select id="sel_villes">
						<?php /*
						foreach( $tz as $zone => $v1 )
								foreach( $tz[$zone] as $ville => $value )
										echo "\t\t<option class=\"$zone\" value=\"$value\">$ville</option>\n";
						*/?>
				</select>
	</form>
<?PHP


$tz = new DateTimeZone('Europe/Paris');

$d = new DateTime("now", $tz);
echo "avant : ".$d->format('H:i')."&nbsp;";
$h = 1;
$m = 35;
echo "+".$h." hour +".($m+1)." minute &nbsp; = ";
		
		 $d->modify('+'.($m+1).' min' );
		 $d->modify('+'.$h.' hour' );
		 
		echo "  après : ".$d->format('H:i')."<br/>\n";

$d = new DateTime("now");
echo "avant : ".$d->format('H:i')."&nbsp;";
$h = 2;
$m = 35;
echo "+".$h." hour +".($m+1)." minute &nbsp; = ";
		
		 $d->modify('+'.($m+1).' min' );
		 $d->modify('+'.$h.' hour' );
		 
		echo "  après : ".$d->format('H:i')."<br/>\n";






?>