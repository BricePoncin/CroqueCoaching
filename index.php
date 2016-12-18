<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<HEAD>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="shortcut icon"               href="favicon.ico" >
		<link rel="icon"       type="image/gif" href="animated_favicon1.gif" >
		<link rel="stylesheet" type="text/css"  href="class.css" />
		<link rel="stylesheet" type="text/css"  href="style/footer.css" />
		<link rel="stylesheet" type="text/css"  href="css/jquery-ui.css" />
		<script type="text/javascript" src="//code.jquery.com/jquery-1.11.2.js"></script>	
		<script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>	
		<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
		<script src="jquery-ui-1.12.1.custom/jquery-ui.js"></script>
		<link rel="stylesheet" type="text/css"  href="jquery-ui-1.12.1.custom/jquery-ui.css" />
		<style>
			.identification {border: 1px solid orange;}
		</style>
<?PHP
	if( isset( $_GET['debug']) )
			$debug=true;
	else
			$debug=false;

	require_once("session.php");
	require_once("depoil.inc.php");
	require_once("cm_api.inc.php");

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
		case 'concours'   : include_once ("concours.inc.php"); break;
		case 'contrats'   : include_once ("contrats.inc.php"); break;
		case 'contrats2'  : include_once ("contrats.inc.php"); break;
		case 'crea_monstr': break;
		case 'erreur'     : break;
		case 'fusion'     : include_once ("fusion.inc.php"); break;
		case 'lst_mbl'    : break;
		case 'mbl'        : include_once("parsembldata.inc.php");break;
		case 'minou'      : break;
		case 'monstres'   : break;
		case 'news'       : break;
		case 'portails'   : break;
		case 'portails2'  : break;
		case 'syndicat'   : break;
		case 'sw5'        : break;
		case 'tas'        : break;
		case 'warlog'     : break;
		case 'whoami'     : break;
		default           : include_once ("accueil.inc.php"); break;
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
?>

		
	
	
<?PHP
		$onload = "";
		switch($pg)
			{
				case 'accueil'    : break;
				case 'erreur'     : break;
				case 'monstres'   : break;
				case 'crea_monstr': break;
				case 'fusion'     : echo "<script type=\"text/javascript\" src=\"js/fusion.js\"></script>"; break;
				case 'contrats'   : echo "<script type=\"text/javascript\" src=\"js/contrats.js\"></script>"; break;
				case 'contrats2'  : echo "<script type=\"text/javascript\" src=\"js/contrats2.js\"></script>"; break;
				case 'portails'   : break;
				case 'portails2'  : break;
				case 'syndicat'   : echo "<script type=\"text/javascript\" src=\"js/syndicat.js\"></script>"; break;
				case 'lst_mbl'    : break;
				case 'mbl'        : break;
				case 'whoami'     : break;
				case 'news'       : break;
				case 'sw5'        : break;
				case 'tas'        : break;
				case 'minou'      : break;
				case 'warlog'     : echo "<script type=\"text/javascript\" src=\"js/warlog.js\"></script>"; break;
				case 'concours'   : echo "<script type=\"text/javascript\" src=\"js/concours.js\"></script>"; break;;
				default           : break;
			}	
?>
	
	
<script type="text/javascript">

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
		<script type="text/javascript" src="js/popup.js"></script>
		
		<script type="text/javascript">

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
				if( document.getElementById(lien) )
            		document.getElementById(lien).innerHTML = "(-)";
				if( document.getElementById(identifiant) )
            		document.getElementById(identifiant).className = "visible";
            }
            else
            {
				if( document.getElementById(lien) )
            		document.getElementById(lien).innerHTML = "(+)";
				if( document.getElementById(identifiant) )
            		document.getElementById(identifiant).className = "hidden";
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
			var el = document.getElementById(identifiant);
            
			if ( el == null )
				return;
			
            if ( el.className == "hidden")
				document.getElementById(identifiant).className = "visible";
            else
				document.getElementById(identifiant).className = "hidden";
       	}

    </script>
		
<?PHP
		include ("google_analytics.php");
?>
<TITLE>CroqueCoaching : Site d'aide au jeu Croque Monster</TITLE>
<?PHP
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
				case 'contrats2'  : $onload = "onload=\"javascript:cache('choix_difficulte');\""; break;
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
				case 'warlog'     : break;
				default           : break;
			}	
	
?>
	<STYLE type="text/css">
		#masquage {
			position: absolute;
			width: 100%;
			height: 100%;
			background-color: orange;
			opacity: 0.2;
			z-index: 10;
		}
		#div_cnx {
			border: 1px solid black;
			background-color: white;
			position:absolute;
			left: 50%;
			top: 50%;
			width: 300px;
			height: 150px;
			margin-left: -150px; /* Cette valeur doit être la moitié négative de la valeur du width */
			margin-top: -75px; /* Cette valeur doit être la moitié négative de la valeur du height */
			z-index: 100;
		}
		#div_cnx_cnt{
			position:absolute;
			left: 50%;
			top: 50%;
			width: 180px;
			height: 100px;
			margin-left: -90px;
			margin-top: -50px;
		}
	</STYLE>

	
</HEAD>
<BODY <?PHP echo $onload;?>>

<div id="masquage"></div>
<div id="div_cnx">
	<div id="div_cnx_cnt">
		<form name="frm_conn" action="index.php" method="POST">
			<input type="hidden" name="log" value="1">
			Name :<input type="text" name="name" /><BR/>
			Cl&eacute; API :<input type="password" name="cle_api"/><BR/>
		</form>
		<a href="#" onClick="document.forms['frm_conn'].submit()">Connecter</a>
	</div>
</div>
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
			
/* #navigation */			
		include ("menu.php");
?>

	<div id="contenu" style="height: 600px; overflow-y: auto;">

<?PHP 

	switch($pg)
	{
		case 'accueil'    : include ("accueil.php"); break;
		case 'erreur'     : include ("erreur.php"); break;
		case 'monstres'   : include ("monstres.php"); break;
		case 'crea_monstr': include ("crea_monstre.php"); break;  
		case 'fusion'     : include ("fusion.php"); break;
		case 'contrats'   : include ("contrats.php"); break;
		case 'contrats2'  : include ("contrats2.php"); break;
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
		case 'warlog'     : include ("warlog.php");break;
		case 'concours'   : include ("concours.php");break;
		default           : include ("accueil.php"); break;
	}	
?>

</div><!-- #contenu -->

<div class="footer"><a class="leetchi-widget-btn" href="https://www.leetchi.com/c/projets-de-croque-coaching"><img src="https://asset.leetchi.com/Content/Quenette/img/culture/fr/view/wizard/embed-btn.png" alt="leetchi"></a></div>
</div><!-- #global -->

<div id="bulle" class="info"></div>

<div id="popNamaNi" style="position:absolute;background-color:#EBEBEB;cursor:hand;left:0px;top:0px;display:none" 
	onMousedown="onyVaty(event)" onMouseup="retiensMoi()">
	<div>
		<img alt="Close" src="images/close.gif" onClick="tchaoAdemain()" width="16" height="14">
	</div>
	<div id="dansPopNamaNi" style="height:155px" class="outdiv">
		<iframe id="NaMaNiPoP" name="NaMaNiPoP"  src="_blank" width="310" height="155" class="iniframe"></iframe>
	</div>
</div>

	
	<script>
	window.onload = function () {
<?PHP
		if (empty($_SESSION))
			echo "var logged=false;";
		else
			echo "var logged=true;";
?>

		if ( logged )
		{
		    document.getElementById('masquage').style.display = "none";
            document.getElementById('div_cnx').style.display = "none";
		}
		else
		{
		    document.getElementById('masquage').style.display = "block";
            document.getElementById('div_cnx').style.display = "block";
		}		
	}
	</script>
</body>
</html>
