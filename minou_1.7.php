<?php
require_once("session.php");
require_once dirname(__FILE__)."/minou_1.7/src/phpfreechat.class.php";
$params = array();
$params["title"] = "Minou de CroqueCoaching";
//$params["nick"] = "guest".rand(1,1000);  // setup the intitial nickname
$params["nick"] = $_SESSION['name'];
$params['nickmeta']['id'] = $_SESSION['id'];

$link = "agence.php?id=".$_SESSION['id'];

$params['nickmeta']['profil'] = '<a href="'.$link.'" target="_blank">open profil</a>';

$file = dirname(__FILE__)."/avatars/".strtolower ($params["nick"]).".jpg";
if( file_exists($file) )
		$strAvatar="./avatars/".strtolower ($params["nick"]).".jpg";
else
		$strAvatar="./avatars/avatar".rand(1,9).".jpg";
$params['nickmeta']['avatar'] = '<a href="'.$link.'" target="_blank"><img src="'.$strAvatar.'" alt=""/></a>';
$params['nickmeta_key_to_hide'] = array('profil','avatar');

$params["frozen_nick"] = true;
$params['firstisadmin'] = false;
$params['admins'] = array( "BlackTom");
if(( $_SESSION['syndicat'] == "DELIRIUM" || $params["nick"] == "Badsam" || $params["nick"] == "BlackTom" ) && ($params["nick"] != "Cathrollette ") )
	$params['nickmeta']['isadmin']="O";
//$params["isadmin"] = true; // makes everybody admin: do not use it on production servers ;)
$params["serverid"] = md5(__FILE__); // calculate a unique id for this chat
$params["debug"] = false;
$params["channels"] = array( $_SESSION['syndicat'], "CroqueCoaching" );
$params["frozen_channels"] = array( $_SESSION['syndicat'], "CroqueCoaching" );
$params["max_channels"] = 2;
$params["timeout"] = 3000000;
$params["displaytabclosebutton"] = false;
$params["display_pfc_logo"] = false;
//$params["theme"] = "msn";
$params["theme"] = "blune";
$params["language"] = "fr_FR";

//print_r($params);

if( $params["nick"] == "momoo38" )
{
	?>
	 <p style="color:red;font-weight:bold;">Momo, ton comportement m'a été raporté et j'ai décidé de te bannir de ce tchat à vie.</p>
	<?PHP
	exit;
}
$chat = new phpFreeChat( $params );

//$chat->printJavascript(); 
$chat->printStyle(); 


  		$chat->printChat(); 

	if (isset($params["isadmin"]) && $params["isadmin"]) 
	{ 
		?>
  	  <p style="color:red;font-weight:bold;">Warning: because of "isadmin" parameter, everybody is admin. Please modify this script before using it on production servers !</p>
  <?php 
  } 
  ?>