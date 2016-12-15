<?php

require_once ("../session.php");

if( !isset($_SESSION['syndicat']) )
		header("Location: ../index.php");

require_once dirname(__FILE__)."/src/phpfreechat.class.php";
$params = array();
$params["title"] = "Minou de CroqueCoaching";
//$params["nick"] = "guest".rand(1,1000);  // setup the intitial nickname
$params["nick"] = $_SESSION['name'];
$params["frozen_nick"] = true;
$params['firstisadmin'] = false;
$params['admins'] = array( "BlackTom" );
//$params["isadmin"] = true; // makes everybody admin: do not use it on production servers ;)
$params["serverid"] = md5(__FILE__); // calculate a unique id for this chat
$params["debug"] = false;
$params["channels"] = array( $_SESSION['syndicat'], "CroqueCoaching" );
$params["frozen_channels"] = array( "CroqueCoaching", $_SESSION['syndicat'] );
$params["max_channels"] = 2;
$params["timeout"] = 300000;
$params["displaytabclosebutton"] = false;
$params["display_pfc_logo"] = false;
$params["theme"] = "msn";
$params["language"] = "fr_FR";

$chat = new phpFreeChat( $params );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>phpFreeChat- Sources Index</title>
  <!--
  <link rel="stylesheet" title="classic" type="text/css" href="style/generic.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/header.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/footer.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/menu.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/content.css" />  
  -->
  <link rel="stylesheet" title="classic" type="text/css" href="../class.css" />  
 </head>
 <body>


  <?php $chat->printChat(); ?>
  <?php if (isset($params["isadmin"]) && $params["isadmin"]) { ?>
    <p style="color:red;font-weight:bold;">Warning: because of "isadmin" parameter, everybody is admin. Please modify this script before using it on production servers !</p>
  <?php } ?>
    
</body></html>
