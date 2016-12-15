<?PHP
	if( isset( $_GET['debug']) )
			$debug=true;
	else
			$debug=false;

	require_once("session.php");
	require_once("depoil.inc.php");
	require_once("cm_api.inc.php");


$baseMemory = memory_get_usage();

	if(isset( $_GET['pg']) )
		$pg=$_GET['pg'];
	else
		$pg='accueil';

	if(isset( $_POST['logoff']) )
	{
		// D?uit toutes les variables de session
		$_SESSION = array();
		// Finalement, on d?uit la session.
		session_destroy();
	}
	if(isset( $_POST['log']) )
	{
		$url = "http://www.croquemonster.com/api/agency.xml?name=".$_POST['name'].";pass=".$_POST['cle_api'];
		$xml = readXML( $url );
		if( substr($xml, 0, 6)== "Erreur" )
		{
				echo "<p style=\"color: red; font-size: 2em; font-weight: bold; align:center;\">$xml</p>";
				// D?uit toutes les variables de session
				$_SESSION = array();
				// Finalement, on d?uit la session.
				session_destroy();
		}
		else
		{
				$_SESSION['name']       = strval($xml['name']);
				$_SESSION['id']       = intval($xml['id']);
				$_SESSION['cle_api']    = $_POST['cle_api'];
				$_SESSION['level']      =  0;
				$_SESSION['ville_prout']= '';
		}	
	}

	if (!isset($_SESSION['name']) && $pg != "fusion" && $pg != "whoami" )
			$pg = 'accueil';
	
	/* Ici on inclut les script pr?able ?out le reste */
	switch($pg)
	{
		case 'accueil'    : include_once ("accueil.inc.php"); break;
		case 'erreur'     : break;
		case 'monstres'   : break;
		case 'crea_monstr': break;
		case 'fusion'     : include_once ("fusion.inc.php"); break;
		case 'contrats'   : include_once ("contrats.inc.php"); break;
		case 'portails'   : break;
		case 'portails2'  : break;
		case 'syndicat'   : break;
		case 'lst_mbl'    : break;
		case 'mbl'        : include_once("parsembldata.inc.php");break;
		case 'whoami'     : break;
		case 'news'       : break;
		case 'sw5'        : break;
		case 'tas'        : break;
		case 'minou'      : break;
		case 'minou_1.7'  : break;
		case 'minou_2.1'  : break;
		case 'warlog'     : break;
		default 			    : include_once ("accueil.inc.php"); break;
	}


$timezones = DateTimeZone::listAbbreviations();

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
            
            $tz[$continent][$ville] = $zone['timezone_id'];
        }
    }
}

foreach( $tz as $zone => $ville )
		asort( $tz[$zone] );
		
//echo json_encode($tz);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<HEAD>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
		<link rel="shortcut icon" href="favicon.ico" >
		<link rel="icon" type="image/gif" href="animated_favicon1.gif" >
		<link rel="stylesheet" type="text/css" href="class.css" />
			
		<style>
			.identification {border: 1px solid orange;}
		</style>

  <link href="css/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="js/jquery-1.4.1.min.js"></script>
  <script src="js/jquery.ui-1.8.6.min.js"></script>
  <script src="js/jquery.idTabs.min.js"></script>
		
  <script>
 	/* French initialisation for the jQuery UI date picker plugin. */
/* Written by Keith Wood (kbwood{at}iinet.com.au) and Stephane Nahmani (sholby@sholby.net). */
jQuery(function($){
	$.datepicker.regional['fr'] = {
		closeText: 'Fermer',
		prevText: '&#x3c;Prec',
		nextText: 'Suiv&#x3e;',
		currentText: 'Courant',
		monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
		'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
		monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
		'Jul','Aou','Sep','Oct','Nov','Dec'],
		dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
		dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
		dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['fr']);
});
  	
  $(document).ready(function() {
		  //alert('<?PHP echo $sel_zone."/".$sel_ville; ?>');
		    $(function() 
				{
						$("#sel_zones").change(function() 
						{
								var json=<?= json_encode($tz); ?>;

						    var options = '';
								$.each(json[ $("#sel_zones").val() ], function(key, value) {
								   options += '<option value="'+value+'">'+key+'</option>';
								});
		  					$("#sel_villes").html( options );
		  			});
		  			
		  			$("#sel_villes").change(function() 
						{
								$("#timezone").val($("#sel_villes").val());
						});
		  	});
		  	
		  	$('#sel_zones').val('<?PHP echo $sel_zone; ?>'); $('#sel_zones').change();
		  	$('#sel_villes').val('<?PHP echo $sel_zone."/".$sel_ville; ?>');
  	
    $("#datepicker").datepicker();
  });
  </script>

		<script type="text/javascript" src="js/infobulle.js"></script>
		<script type="text/javascript" src="js/fusion.js"></script>
		<script type="text/javascript" src="js/popup.js"></script>
		<script type="text/javascript" src="js/syndicat.js"></script>


		<script type="text/javascript">
    <!--

        arrMonstres = new Array();
    <?PHP
    		if ( isset($xml))
    		{
		        foreach($xml as $monstre) 
		        {
				        $equipements_portes = explode(",", $monstre['permanentItems']);
				        $eqPermanents  = array_compile  ( $equipements_portes  , $liste_equipements  );
				        
				        $equipements_tempo = explode(",", $monstre['contractItems']);
				        $eqTemporaires = array_compile  ( $equipements_tempo  , $liste_equipements  );
				        
								$stats_equipement = equipement_stat($eqPermanents, $eqTemporaires);
		
		        	
		            echo "arrMonstres.push( new Array(".($monstre['sadism']    - $stats_equipement["sad"]).","
		                                               .($monstre['ugliness']  - $stats_equipement["ugl"]).","
		                                               .($monstre['power']     - $stats_equipement["pow"]).","
		                                               .($monstre['greediness']- $stats_equipement["gre"]).","
		                                               .($monstre['control']   - $stats_equipement["con"]).","
		                                               .($monstre['fight']     - $stats_equipement["fig"]).","
		                                               .($monstre['endurance'] - $stats_equipement["end"]).","
		                                               .'"'.$monstre['swfjs'].'"'.") );\n";
		        }
		     }
    ?>
  
        function ouvre_ferme(lien,identifiant)
        {
            var v_class = document.getElementById(identifiant).className;
          
            if (v_class == "hidden")
            {
            		document.getElementById(lien).innerHTML = "(-)";
            		document.getElementById(identifiant).className = "visible";
            }
            else
            {
            		document.getElementById(identifiant).className = "hidden";
            		document.getElementById(lien).innerHTML = "(+)";
            }
       }
        
        function maj_fusion()
        {
        	<?PHP
        	if (isset($_SESSION[level]))
        	{
        		?>
            var_sad=fusion(<?PHP echo $_SESSION['level']; ?>, parseInt(document.getElementById('sad1').innerHTML), parseInt(document.getElementById('sad2').innerHTML));
            var_ugl=fusion(<?PHP echo $_SESSION['level']; ?>, parseInt(document.getElementById('ugl1').innerHTML), parseInt(document.getElementById('ugl2').innerHTML));
            var_pow=fusion(<?PHP echo $_SESSION['level']; ?>, parseInt(document.getElementById('pow1').innerHTML), parseInt(document.getElementById('pow2').innerHTML));
            var_gre=fusion(<?PHP echo $_SESSION['level']; ?>, parseInt(document.getElementById('gre1').innerHTML), parseInt(document.getElementById('gre2').innerHTML));
            var_con=fusion(<?PHP echo $_SESSION['level']; ?>, parseInt(document.getElementById('con1').innerHTML), parseInt(document.getElementById('con2').innerHTML));
            var_fig=fusion(<?PHP echo $_SESSION['level']; ?>, parseInt(document.getElementById('fig1').innerHTML), parseInt(document.getElementById('fig2').innerHTML));
            var_end=fusion(<?PHP echo $_SESSION['level']; ?>, parseInt(document.getElementById('end1').innerHTML), parseInt(document.getElementById('end2').innerHTML));  

						document.getElementById('sad_fus').innerHTML = var_sad;
						document.getElementById('ugl_fus').innerHTML = var_ugl;
						document.getElementById('pow_fus').innerHTML = var_pow;
						document.getElementById('gre_fus').innerHTML = var_gre;
						document.getElementById('con_fus').innerHTML = var_con;
						document.getElementById('fig_fus').innerHTML = var_fig;
						document.getElementById('end_fus').innerHTML = var_end;

						document.getElementById('frm_result').src='./monstrinator.php?sad='+var_sad+'&lai='+var_ugl+'&for='+var_pow+'&gou='+var_gre+'&con='+var_con+'&com='+var_fig+'&end='+var_end+'&niv_fus=1';
								
            <?PHP
          }
          ?>
        }
        
        function hide(mid,identifiant)
        {
            for(var idx=0; idx<11; idx++)
            {
            		if( document.getElementById('monstre'+mid+idx) )
                	document.getElementById('monstre'+mid+idx).className = "monstre"+mid+" hidden";
            }
            document.getElementById('monstre'+mid+'99').className = "monstre"+mid+" hidden";
            
            document.getElementById(identifiant).className = "monstre"+mid+" visible";
            
        }
        
        function cache(identifiant)
        {
            var v_class = document.getElementById(identifiant).className;
            
            if (v_class == "hidden")
            		document.getElementById(identifiant).className = "visible";
            else
            		document.getElementById(identifiant).className = "hidden";
       	}
        
        /*
        function GetTime( timezone ) 
				{
						var dt = new Date();
						var def = dt.getTimezoneOffset()/60;
						var gmt = (dt.getHours() + def);
						var tz = parseInt(timezone);
						var heure_gmt_temp = dt.getUTCHours();
						var heure_gmt = 24 - (heure_gmt_temp - def);
						
						// return (heure_gmt_temp+" "+tz+" "+def+" "+heure_gmt);
						
						var h = 24 - tz - gmt;

						var h=0;
					
								h = 24 - tz - (heure_gmt - heure_gmt_temp) + def;
					
					return ( h+ ' '+heure_gmt+' '+heure_gmt_temp+' '+def+' '+(heure_gmt - heure_gmt_temp - def) );

						var ho = check24(h);
						
						//return ('24 - '+tz+' -'+def + ' : ' +gmt+ ' => '+ IfZero(ho) + ending);
						//return ( IfZero(ho) + ending );
						
						return ( (ho)+'h'+' => '+def );
				}
				
				function IfZero(num) 
				{
						return ((num <= 9) ? ("0" + num) : num);
				}
				function check24(hour) 
				{
						return (hour >= 24) ? hour - 24 : hour;
				}
				*/

    -->
    </script>
		
<?PHP
		include ("google_analytics.php");

		switch($pg)
			{
				case 'accueil'    : break;
				case 'erreur'     : break;
				case 'monstres'   : break;
				case 'crea_monstr': break;
				case 'fusion'     : break;
				case 'contrats'   : break;
				case 'portails'   : break;
				case 'portails2'  : break;
				case 'syndicat'   : break;
				case 'lst_mbl'    : break;
				case 'mbl'        : break;
				case 'whoami'     : break;
				case 'news'       : break;
				case 'sw5'        : break;
				case 'tas'        : break;
				case 'minou'      : break;
				case 'minou_1.7'  : break;
				case 'minou_2.1'  : echo "<script src=\"/minou_2.1/client/lib/jquery-1.8.2.min.js\" type=\"text/javascript\"></script>";
									echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/minou_2.1/client/themes/default/jquery.phpfreechat.min.css\" />";
                                    echo "<script src=\"/minou_2.1/client/jquery.phpfreechat.min.js\" type=\"text/javascript\"></script>";
									echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/minou_2.1/client/themes/carbon/jquery.phpfreechat.min.css\" />";
								    break;
				case 'warlog'     : break;
				default 		  : break;
			}	
	
?>
<TITLE>CroqueCoaching : Site d'aide au jeu Croque Monster</TITLE>
	</HEAD><?PHP
		$onload = "";
		switch($pg)
			{
				case 'accueil'    : break;
				case 'erreur'     : break;
				case 'monstres'   : break;
				case 'crea_monstr': $onload="onload=\"javascript:carac('sad', 0);\"";
				case 'fusion'     : if (isset($_SESSION['name']) )
														  $onload = "onload=\"javascript:hide(1,'monstre10');hide(2,'monstre21');maj_stats(1,0);maj_stats(2,1);\""; 
													  else
													 	  $onload = "onload=\"javascript:hide(1,'monstre199');hide(2,'monstre299');load_ampe(1);load_ampe(2);\""; 
													  break;
				case 'contrats'   : $onload = "onload=\"javascript:cache('choix_difficulte');\""; break;
				case 'portails'   : break;
				case 'portails2'  : break;
				case 'syndicat'   : break;
				case 'lst_mbl'    : break;
				case 'mbl'        : break;
				case 'whoami'     : break;
				case 'news'       : break;
				case 'sw5'        : break;
				case 'tas'        : break;
				case 'minou'      : break;
				case 'minou_1.7'  : break;
				case 'minou_2.1'  : break;
				case 'warlog'     : break;
				default           : break;
			}	
	
	?><body <?PHP echo $onload;?>>
		
		<div id="global">
		<?PHP	
			if ($pg=='accueil')
			{
		?>
				<div id="entete">
					<h1>
						<!--<img alt="" src="template/logo.png" />
						<img alt="" src="template/typo_croquemonster.png" />-->
						<?PHP include_once("nb_bandals.inc.php"); ?>
						<img alt="Bandal" src="template/<?PHP printf("bandal%02d.jpg", rand(1, $nb_bandals) );?>" />
					
					</h1>
					<p class="sous-titre">
						<strong>Croque-coaching</strong>

						Site d'aide au jeu <a href="http://www.croquemonster.com/">CroqueMonster</a>
						d&eacute;velopp&eacute; par le directeur de l'agence <a href="http://www.croquemonster.com?ref=BlackTom">BlackTom</a>.
					</p>
				</div><!-- #entete -->
		<?PHP
			}
		?>
<div id="navigation">
		
<?PHP
		include ("menu.php");
?>
</div><!-- #navigation -->

	<div id="contenu">

<?PHP 

	switch($pg)
	{
		case 'accueil'    : include ("accueil.php"); break;
		case 'erreur'     : include ("erreur.php"); break;
		case 'monstres'   : include ("monstres.php"); break;
		case 'crea_monstr': include ("crea_monstre.php"); break;  
		case 'fusion'     : include ("fusion.php"); break;
		case 'contrats'   : include ("contrats.php"); break;
		case 'portails'   : include ("portails.php"); break;
		case 'portails2'  : include ("portails2.php"); break;
		case 'syndicat'   : include ("syndicat.php"); break;
		case 'lst_mbl'    : include ("liste_mbl.php"); break;
		case 'mbl'        : include ("parsembldata.php"); break;
		case 'whoami'     : include ("whoami.php"); break;
		case 'news'       : include ("gst_news.php");break;
		case 'sw5'        : include ("syndic_stats_guerre_5.php");break;
		case 'tas'        : include ("stats_tas.php");break;
		case 'minou'      : include ("minou.php");break;
		case 'minou_1.7'  : include ("minou_1.7.php");break;
		case 'minou_2.1'  : include ("minou_2.1.php");break;
		case 'warlog'     : include ("warlog.php");break;
    default           : include ("accueil.php"); break;
	}	
?>

</div><!-- #contenu -->


</div><!-- #global -->

<div id="bulle" class="info"></div>

<div id="popNamaNi" style="position:absolute;background-color:#EBEBEB;cursor:hand;left:0px;top:0px;display:none" onMousedown="onyVaty(event)" onMouseup="retiensMoi()">
<div><img alt="Close" src="images/close.gif" onClick="tchaoAdemain()" width="16" height="14"></div>
<div id="dansPopNamaNi" style="height:100%">
<iframe id="NaMaNiPoP" name="NaMaNiPoP"  src="" width=100% height=100%></iframe>
</div>
</div>

</body>
</html>
