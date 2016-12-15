<?PHP

    if( isset($_SESSION['name']))
    {
		    $url = "http://www.croquemonster.com/api/monsters.xml?name=".$_SESSION['name'].";pass=".$_SESSION['cle_api'];
				$xml = readXML( $url );
				if( substr($xml, 0, 6)== "Erreur" )
				{
						echo $xml;
						exit;
				}
		 }
		 else
		 		$xml = array();
?>