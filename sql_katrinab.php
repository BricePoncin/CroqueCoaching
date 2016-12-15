<?PHP

$db_type = 'mysql';
$db_host = 'ftp.free.fr';
$db_name = 'katrinab';
$db_username = 'katrinab';
$db_password = 'dukuduku';
$db_link=FALSE;
$db_selected=FALSE;

function connect()
{
	global $db_host;
	global $db_name;
	global $db_username;
	global $db_password;
	global $db_link;
	global $db_selected;

	$db_link = @mysql_connect( $db_host, $db_username, $db_password );
	if ( !$db_link	)
	{
		return false;
		//die("Impossible de se connecter : " . mysql_error());
	}
	$db_selected = mysql_select_db($db_name, $db_link);
	if (!$db_selected) 
	{
	   //die ('Impossible de sélectionner la base de données : ' . mysql_error());
	   return false;
	}
	
	return true;
}

function disconnect()
{
	global $db_link;
	mysql_close($db_link);
}
	
function select($query, &$aResultats)
{
	global $db_link;
	
	$res = mysql_query  ( $query , $db_link );
	
	if (!$res) 
	{
	   echo "Impossible d'exécuter la requête ($query) dans la base : " . mysql_error();
	   exit;
	}
	if (mysql_num_rows($res) == 0)
	{
		return (0);
	}	
	$idx=0;
	while ($row = mysql_fetch_assoc($res))
	{
		$aResultats[$idx] = $row;
		$idx++;
	}
	mysql_free_result($res);
	
	return($idx);
}

function insert( $query )
{
	global $db_link;
	
	mysql_query  ( $query , $db_link );
	$ret = mysql_affected_rows  ($db_link);
	
	return($ret);
}

?>