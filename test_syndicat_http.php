<?php

function Get($url, $sid = '')
{
   $url = parse_url($url);
   
   print_r($url );
   
    if(isset($url['port'])){
      $port = $url['port'];
    }else{
      $port = 80;
    }
    
    if (!$fp = fsockopen ($url['host'], $port, $errno, $errstr)){ 
        $out = false; 
    }else{ 
    		if ( isset($url['query']) )
    				$url['query'] = "?".$url['query'];
    	  
    	  $addr = $url['scheme']."://".$url['host'].$url['path'].$url['query'];
    	        
        $request = "GET ".$addr." HTTP/1.1\n"; 
        $request .= "Host: ".$url['host']."\n"; 
        $request .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.9.2.8) Gecko/20100722 AskTbFXTV5/3.8.0.12304 Firefox/3.6.8 (.NET CLR 2.0.50727; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C; .NET4.0E)"."\n";
        $request .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\n";
        $request .= "Accept-Language: fr\n";
        $request .= "Accept-Encoding: gzip,deflate\n";
        $request .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\n";
        $request .= "Keep-Alive: 115\n";
        $request .= "Proxy-Connection: keep-alive\n";
        if( $sid != '')
        {
        		//$request .= "Cookie: sid=".$sid.";\n"; 
        		$request .= "Cookie: __utma=192537720.655568036.1242287564.1284445620.1284453228.611; __utmz=192537720.1283763121.584.42.utmccn=(referral)|utmcsr=bponcin.free.fr|utmcct=/cm/index.php|utmcmd=referral; sid=".$sid."; __utmc=192537720; __utmb=192537720\n";
        }
        $request .= "\r\n"; 
        $fput = fputs($fp, $request); 
print_r($request);
        
        $contents = "";
				while (!feof($fp)) 
				{
  					$contents .= fread($fp, 8192);
				}
				
        fclose ($fp); 
    }
    echo "<br><br>HEADER<br>\n\n".substr( $contents, 0, strpos($contents, "\r\n\r\n"));
    
    echo "<br><br>BODY<br>\n\n".substr( utf8_decode($contents), strpos($contents, "\r\n\r\n"));
    
     
    if($sid == '')
    {
		    $sid=substr($contents, strpos($contents , "Set-Cookie: sid=") );
				$sid=substr($sid, strpos($sid , "=")+1 );
				$sid=substr($sid, 0,  strpos($sid , ";")-1 );
				
				return $sid;
		}    
   	return $contents ;
}
function Post($url,$array, &$sid)
{
		$contents = '';
   	$args = http_build_query($array); 
   	$url = parse_url($url);
    if(isset($url['port']))
    {
    		$port = $url['port'];
    }
    else
    {
      	$port = 80;
    }
    
    if (!$fp = fsockopen ($url['host'], $port, $errno, $errstr))
    { 
        $out = false; 
    }
    else
    { 
    	$addr = $url['scheme']."://".$url['host'].$url['path'];

    		$args = urlencode($args);
        $size = strlen($args); 
        $request = "POST ".$addr." HTTP/1.1\n"; 
        $request .= "Host: ".$url['host']."\n"; 

        $request .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.9.2.9) Gecko/20100824 AskTbFXTV5/3.8.0.12304 Firefox/3.6.9 (.NET CLR 2.0.50727; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C; .NET4.0E)\n";
        $request .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\n";
        $request .= "Accept-Language: fr\n";
        $request .= "Accept-Encoding: gzip,deflate\n";
        $request .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\n";
        $request .= "Keep-Alive: 115\n";
        $request .= "Proxy-Connection: keep-alive\n";
        $request .= "Referer: ".$url['scheme']."://".$url['host']."/\n";

        //$request .= "Cookie: sid=".$sid.";\n"; 
$request .= "Cookie: __utma=192537720.655568036.1242287564.1284445620.1284453228.611; __utmz=192537720.1283763121.584.42.utmccn=(referral)|utmcsr=bponcin.free.fr|utmcct=/cm/index.php|utmcmd=referral; sid=".$sid."; __utmc=192537720; __utmb=192537720\n";
//Cookie: __utma=192537720.655568036.1242287564.1284445620.1284453228.611; __utmz=192537720.1283763121.584.42.utmccn=(referral)|utmcsr=bponcin.free.fr|utmcct=/cm/index.php|utmcmd=referral; sid=n69kPiACSGyhrDHM98Lbof3a1TiElrMA; __utmc=192537720; __utmb=192537720



  //      $request .= "Connection: Close\r\n"; 
        $request .= "Content-Type: application/x-www-form-urlencoded\n"; 
        $request .= "Content-length: ".$size."\r\n\r\n"; 
        $request .= $args."\n"; 
print_r($request);

        $fput = fputs($fp, $request); 

        $contents = '';
				while (!feof($fp)) 
				{
  					$contents .= fread($fp, 8192);
				}
				
        fclose ($fp); 
        
      
    		{
				    $sid_recu=substr($contents, strpos($contents , "Set-Cookie: sid=") );
						$sid_recu=substr($sid_recu, strpos($sid_recu , "=")+1 );
						$sid_recu=substr($sid_recu, 0,  strpos($sid_recu , ";")-1 );
						
				} 
				if( $sid != $sid_recu)
				{
					echo "Problème ! Le SID reçu ne correspond pas : $sid_recu<BR>\n"	;
					//exit;
				}
				
				$sid=$sid_recu;
    } 
    
    echo "<br><br>HEADER<br>\n\n".substr( $contents, 0, strpos($contents, "\r\n\r\n"));
    echo "<br><br>BODY<br>\n\n".substr( utf8_decode($contents), strpos($contents, "\r\n\r\n"));
    
   return $contents;
}



// exemple d'utilisation

$data = array("login" => "BlackTom","pass"=>"pioupiou", "submit"=>"Entrer");

$sid = Get("http://www.croquemonster.com/" );

$resp = Post("http://www.croquemonster.com/user/login",$data, $sid);

echo "BOBY<br>\n\n".substr( $contents, strpos($contents, "\r\n\r\n")+4)."<BR>\n";
    
//var_dump($resp);
$resp = Get("http://www.croquemonster.com/syndicate/8027/cityRanking?cid=264", $sid );
var_dump($resp);
?>