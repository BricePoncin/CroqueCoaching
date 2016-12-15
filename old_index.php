<?PHP
	require_once("session.php");
	require_once("depoil.inc.php");

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
		$_SESSION['name']    = $_POST['name'];
		$_SESSION['cle_api'] = $_POST['cle_api'];
		$_SESSION['level']   =  0;
	}

	if (!isset($_SESSION['name']) && $pg != "fusion" && $pg != "whoami" )
			$pg = 'accueil';
	
	/* Ici on inclut les script pr?able ?out le reste */
	switch($pg)
	{
		case 'accueil'    : include_once ("accueil.inc.php5"); break;
		case 'monstres'   : break;
		case 'crea_monstr': break;
		case 'fusion'     : include_once ("fusion.inc.php5"); break;
		case 'contrats'   : break;
		case 'portails'   : break;
		case 'syndicat'   : break;
		case 'mbl'        : break;
		case 'whoami'     : break;
		default 			    : include_once ("accueil.inc.php5"); break;
	}	
		

?>
<HTML>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
		<link rel="shortcut icon" href="favicon.ico" >
		<link rel="icon" type="image/gif" href="animated_favicon1.gif" >
		<link rel="stylesheet" type="text/css" href="class.css" />
		<style>
			.identification {border: 1px solid orange;}
		</style>
		<script type="text/javascript" language="javascript" src="js/infobulle.js"></script>
		<script type="text/javascript" language="javascript" src="js/fusion.js"></script>
		<script type="text/javascript" language="javascript" src="js/popup.js"></script>

		<script type="text/javascript" language="javascript" src="js/syndicat.js"></script>

		<script language="javascript">
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

						document.getElementById('frm_result').src='./monstrinator.php5?sad='+var_sad+'&lai='+var_ugl+'&for='+var_pow+'&gou='+var_gre+'&con='+var_con+'&com='+var_fig+'&end='+var_end+'&niv_fus=1';
								
            <?PHP
          }
          ?>
        }
        
        function hide(mid,identifiant)
        {
            for(var idx=0; idx<10; idx++)
            {
            		if( document.getElementById('monstre'+mid+idx) )
                	document.getElementById('monstre'+mid+idx).className = "monstre"+mid+" hidden";
            }
            document.getElementById('monstre'+mid+'99').className = "monstre"+mid+" hidden";
            
            document.getElementById(identifiant).className = "monstre"+mid+" visible";
            
        }
        
        function cache(identifiant)
        {
            var class = document.getElementById(identifiant).className;
            
            if (class == "hidden")
            		document.getElementById(identifiant).className = "visible";
            else
            		document.getElementById(identifiant).className = "hidden";
       	}
        
        function ouvre_ferme(lien,identifiant)
        {
            var class = document.getElementById(identifiant).className;
            
            if (class == "hidden")
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
        
        
    -->
    </script>
		
	</head>
	<?PHP
		$onload = "";
		switch($pg)
			{
				case 'accueil'    : break;
				case 'monstres'   : break;
				case 'crea_monstr': $onload="onload=\"javascript:carac('sad', 0);\"";
				case 'fusion'     : if (isset($_SESSION['name']) )
														  $onload = "onload=\"javascript:hide(1,'monstre10');hide(2,'monstre21');maj_stats(1,0);maj_stats(2,1);\""; 
													  else
													 	  $onload = "onload=\"javascript:hide(1,'monstre199');hide(2,'monstre299');load_ampe(1);load_ampe(2);\""; 
													  break;
				case 'contrats'   : $onload = "onload=\"javascript:cache('choix_difficulte');\""; break;
				case 'portails'   : break;
				case 'syndicat'   : break;
				case 'mbl'        : break;
				case 'whoami'     : break;
				default 			    : break;
			}	
	
	?>
	
	<body <?PHP echo $onload;?>>
		
		<div id="global">
		<?PHP	
			if ($pg=='accueil')
			{
		?>
				<div id="entete">
					<h1>
						<img alt="" src="template/logo.png" />
						<img alt="" src="template/typo_croquemonster.png" />
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
		case 'monstres'   : include ("monstres.php5"); break;
		case 'crea_monstr': include ("crea_monstre.php5"); break;  
		case 'fusion'     : include ("fusion.php5"); break;
		case 'contrats'   : include ("contrats.php5"); break;
		case 'portails'   : include ("portails.php5"); break;
		case 'syndicat'   : include ("syndicat.php5"); break;
		case 'mbl'        : include ("mbl.php5"); break;
		case 'whoami'     : include ("whoami.php5"); break;
		default           : include ("accueil.php"); break;
	}	
?>

</div><!-- #contenu -->

	<p id="copyright">
		Mise en page &copy; 2008
		<a href="http://www.elephorm.com">Elephorm</a> et
		<a href="http://www.alsacreations.com">Alsacr?ions</a>
	</p>

</div><!-- #global -->
<div id="bulle" class="info"></div>

<div id="popNamaNi" style="position:absolute;background-color:#EBEBEB;cursor:hand;left:0px;top:0px;display:none" onMousedown="onyVaty(event)" onMouseup="retiensMoi()" onSelectStart="return false">
<div align="right" style="background-color:black"><img src="images/close.gif" onClick="tchaoAdemain()" width="16" height="14"></div>
<div id="dansPopNamaNi" style="height:100%">
<iframe id="NaMaNiPoP" name="NaMaNiPoP"  src="" width=100% height=100%></iframe>
</div>
</div>

</body>
</html>
