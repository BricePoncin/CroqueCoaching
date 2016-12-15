<?php 

	require_once("cm_api.inc.php");
	require_once('sql.php');
	
error_reporting(E_ALL);

/*
This script uses the following table (example) for the mysql queries :

	CREATE TABLE cm_dumplog (
		id int(10) unsigned NOT NULL COMMENT 'Index of the action for this syndicate',
		syndicate_id smallint(5) unsigned NOT NULL COMMENT 'Id of the syndicate',
		`user` varchar(13) default NULL COMMENT 'Name of the user who made the action, can be GrosMiam for types 3, 4, 7 and 8',
		`date` datetime NOT NULL COMMENT 'Date of the action',
		`type` tinyint(3) unsigned NOT NULL COMMENT 'Type of action, 1=Paradox, 2=Recycling, 3=GrosMiam eating, 4=GrosMiam eating and leaving, 5=Burn, 6=Defense, 7=GrosMiam arrives, 8=GrosMiam beatten',
		quantity int(11) default NULL COMMENT 'Quantity of added dump,  only for type <= 5',
		co2 mediumint(11) unsigned default NULL COMMENT 'Quantity of produced co2, only for type 5',
		concentration smallint(6) default NULL COMMENT 'Quantity of concentration added to GrosMiam, only for type 6',
		PRIMARY KEY  (id,syndicate_id)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;
	
*/

print "###DUMPDATA###\n";


// Data types
$name_type = "/^#?[0-9a-zA-Z]+$/";						// User
$num_type = "/^\d+$/";									// Numbers
$nullable_num_type = "/^\d*$/";							// Nullable numbers
$nullable_signed_num_type = "/^(-?\d+)|$/";				// Nullable signed numbers
$date_type = "/^(\d{4}-\d\d-\d\d \d\d:\d\d:\d\d)?$/";	// Date

// Data format to be received
$types = Array($num_type, $name_type, $date_type, $num_type, $nullable_signed_num_type, $nullable_num_type, $nullable_signed_num_type);

// This function is used to parse the XML from CroqueMonster when checking the API key, it also populates the $syndicateId variable

function tagStart($parser, $tagName, array $attribs)
{
	global $error, $syndicateId;
	if ($tagName == 'LOCKED') $error = 'locked';
	if (($tagName == 'ERR') and (isset($attribs['ERROR']))) $error = $attribs['ERROR'];
	if (($tagName == 'AGENCY') and (isset($attribs['SYNDICATEID']))) {
		$syndicateId = $attribs['SYNDICATEID'];
	}
}

// Retrieve and check the parameters
$agencyId = (isset($_GET['id']))?$_GET['id']:'';
$agencyPass = (isset($_GET['pass']))?$_GET['pass']:'';
$action = (isset($_GET['action']))?$_GET['action']:'';
$syndicateId = '';

if (($agencyId == '') or ($agencyPass == '')) {
	print "error\nName or Pass is undefined.";
	exit();
}

// Check the agency Pass (API key), and retreive the SyndicateId

//$xml = retreiveXML( 'agency', "id=" . $agencyId . "&pass=" . $agencyPass);
$xml = readXML( "http://www.croquemonster.com/api/agency.xml?id=" . $agencyId . "&pass=" . $agencyPass );
if( substr($xml, 0, 6)== "Erreur" )
{
		echo $xml;
		exit;
}		
if( isset($xml['syndicateId']))
	$syndicateId = $xml['syndicateId'];
else
	$syndicateId = '';


if ($syndicateId == '') {
	echo "error\nSyndicate not found for this user...";
	exit();
}

// Do the actions
if ($action == 'get')
{
	// Must print the last id stored for this syndicate
	if (!connect())
	{
		print "error\n";
		print "Database connection problem (" . mysql_error() . ").\n";
		exit();
	}
	// Example of query, works with a table defined as the one in the beginning of this script
	$query = "SELECT COALESCE(MAX(id), 0) last_id FROM cm_dumplog WHERE syndicate_id=". $syndicateId;
	//$res = mysql_query($query);
	select($query, $rows);
	$last_id = '';
	//if ($res) $last_id = mysql_result($res,0); 
	$last_id = $rows[0]['last_id'];
	if ($last_id == '') $last_id = '0';
	print "ok\n" . $last_id;
	
	//database_close();
	disconnect();
}
else if ($action == 'add')
{
	// Retrieve the data
	if (!isset($_POST['data']))
	{
		print "error\ndata";
		exit();
	}
	$data = $_POST['data'];
	
	/*	
	if (!database_init())
	{
		print "error\n";
		print "Database connection problem (" . mysql_error() . ").\n";
		exit();
	}
	*/
	if (!connect())
	{
		print "error\n";
		print "Database connection problem (" . mysql_error() . ").\n";
		exit();
	}
	// Split the data into lines, each line is a dump entry
	$lignes = explode("\n", $data);
		
	if ((count($lignes) == 1) && ($lignes[0] == ''))
	{
		print "ok\nNo data.";
		exit();
	}
	
	
	if (!isset($_POST['score']))
	{
			$score       = $_POST['score'];
			$dumpSize    = $_POST['dumpSize'];
			$co2Estimate = $_POST['co2Estimate'];
			$dumpRecord  = $_POST['dumpRecord'];
			$co2Record   = $_POST['co2Record'];
	
			$query  = "INSERT INTO cm_dumpsize (syndic_id, date, score, dumpSize, co2Estimate, dumpRecord, co2Record) VALUES ";
			$query .= " ($syndicateId, NOW(), $score, $dumpSize, $co2Estimate, $dumpRecord, $co2Record)";
			
			insert($query);
	}
	
	
	// Build the query, this is an example, it works with a table defined as the one in the beginning of this script
	$query = 'INSERT INTO cm_dumplog (syndicate_id, id, user, date, type, quantity, co2, concentration) VALUES ';
	
	for ($i = 0; $i < count($lignes); $i++)
	{
		// For each line, split it into values
		$values = explode(",", $lignes[$i]);
		
		if (count($values) != count($types))
		{
			print "error\nIncorrect data (Number of expected values per line : " . count($types) . " ; Number found : " . count($values) . ")\n";
		}
		
		$query .= " ('$syndicateId'";
		
		foreach ($values as $j => $value)
		{
			// For each value, check if it corresponds to the correct type
			$value = preg_replace("#\\\\(.?)#", "\\1", $value);
			if (!preg_match($types[$j], $value))
			{
				print "error\n";
				print "Incorrect data ($j: '$value' doesn't match $types[$j])\n";
				exit();
			}
			// Insert the resulting data into the query
			if ($value == '')
			{
				$query .= ", NULL";
			}
			else
			{
				$query .= ", '$value'";
			}
		}
		$query .= "),";
	}
	// Remove the extra comma from the end of the query
	$query = preg_replace("#,$#","", $query);
	
	// Run the query
	//$result = @mysql_query($query);
	$result = insert($query);
	
	if ($result)
	{
		// Don't forget to write the status of the insertion (or data handling)
		print "ok\n";
	} 
	else 
	{
		// Or else, write an error status
		$error = mysql_error();
		if (ereg("^Duplicate entry ", $error)) 
		{
			print "error\n";
			print ("Duplicate entry\n");
		}
		else 
		{
			print "error\n";
			print ("Query error (".mysql_error().").\n");
		}
	}
	//database_close();
	disconnect();
}
else
{
	// The action is incorrect
	print "error\n";
	print "Incorrect action ($action)";
}

?> 