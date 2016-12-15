<?php                           
                                
require "pclzip.lib.php";       
                                
$bleh = new PclZip( $_GET['file'] );
$content = $bleh->listContent();
$bleh->extract();               
                                
print_r($content);              
                                
?>                              