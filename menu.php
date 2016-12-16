<ul id="navigation">
	<li><a href="#">Menu</a>
		<ul>
			<li><a href="index.php?pg=accueil">Accueil</a></li>
<?PHP
			if ( isset($_SESSION['name']) )
			{
?>
				<li><a href="index.php?pg=monstres">Monstres</a></li>
				<li><a href="index.php?pg=contrats">Contrats</a></li>
				<li><a href="index.php?pg=contrats2">Contrats 2</a></li>
				<li><a href="index.php?pg=portails">Portails</a></li>
				<li><a href="index.php?pg=syndicat">Syndicat</a></li>

				<li><a href="index.php?pg=minou"><img src="images/cat.gif" height="22" alt="Minouuuuuuuu" /></a></li>
<?PHP
				if ( $_SESSION['name'] == 'BlackTom' )
				{
?>
					<li><a href="index.php?pg=news">News</a></li>		
<?PHP
				}
			}
?>
			<li><a href="index.php?pg=concours"><img src="images/fight.gif"/> Concours <img src="images/fight.gif"/></a></li>
			<li><a href="index.php?pg=fusion">Simulateur de fusion</a></li>
			<li><a href="index.php?pg=crea_monstr">CreaMonstre</a></li>
			<li><a href="index.php?pg=whoami">Qui suis-je ?</a></li>
			<li>
<?PHP
				if ( isset($_SESSION['name']) )
				{
?>
					<form name="frm_deco" action="index.php" method="POST">
						<input type="hidden" name="logoff" value="1">
					</form>
					<a href="#" onClick="document.forms['frm_deco'].submit()">D&eacute;connecter</a>
<?PHP
				}
				else
				{
?>
					<form name="frm_conn" action="index.php" method="POST">
						<input type="hidden" name="log" value="1">
						Name :<input type="text" name="name" /> Cl&eacute; API :<input type="password" name="cle_api"/>
					</form>
					<a href="#" onClick="document.forms['frm_conn'].submit()">Connecter</a>
<?PHP
				}
?>
			</li>
		</ul>
	</li>
</ul>