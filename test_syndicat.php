<?PHP
	echo "Ca va ??";
?>
<html>
	<head>
		<script language="javascript">
			<!--
				function load()
				{
					
					try {
						  netscape.security.PrivilegeManager.enablePrivilege('UniversalBrowserRead');
						}
						catch(e) {
						  alert('erreur');
						}
							// The web services request minus the domain name
							//var path = 'syndicate/8027/cityRanking?cid=264';
							
							// The full path to the PHP proxy
							//var url = 'http://bponcin.free.fr/cm/proxy.php?yws_path='+encodeURIComponent(path);
var url = 'http://bponcin.free.fr/cm/proxy.php?yws_path='+encodeURIComponent('syndicate/8027/cityRanking?cid=264');
							alert(url);							
							var xhr_object = null;
			
							if(window.XMLHttpRequest) // Firefox
								xhr_object = new XMLHttpRequest();
							else if(window.ActiveXObject) // Internet Explorer
								xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
							else { // XMLHttpRequest non supporté par le navigateur
								alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
								return;
							}
						
							xhr_object.open("GET", url, true);
							
							xhr_object.onreadystatechange = function anonymous() {
								if(xhr_object.readyState == 4) alert(xhr_object.responseText);
							}
							
							xhr_object.send(null);

					}
			-->
			</script>
	</head>
	<body>
			<form method="POST" action="proxy.php">
			<input name="yws_path" type="text" value="user/login">
			<input name="login" value="BlackTom">
			<input name="pass" value="pioupiou">
			<input name="submit" type="submit" value="Entrer">
			</form>
	</body>
	<a href="javascript:load();">Test</a>
</html>