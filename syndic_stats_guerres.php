<HTML>
<head>
		<link rel="shortcut icon" href="favicon.ico" >
		<link rel="icon" type="image/gif" href="animated_favicon1.gif" >
		<link rel="stylesheet" type="text/css" href="class.css" />
</head>
<body>
<?PHP

		require_once("cm_api.inc.php");

		if ( isset($_GET['id']))
				$syndic_id=$_GET['id'];
		else
				exit(-1);

		if ( isset($_GET['war']))
				$idGuerre=$_GET['war'];
		else
				exit(-1);

			echo "<div>";
			$url = "http://www.croquemonster.com/syndicate/war".$idGuerre."/members.xml?id=".$syndic_id;
			$xml = direct_get( $url, true );
			
			if( strpos($xml, "<err") !== FALSE )
				echo "<h1>Ce syndicat n'a probablement pas participé à cette guerre là.</h1>";
			else
				echo $xml;
			echo "</div>";
?> 