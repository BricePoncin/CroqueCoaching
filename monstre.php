<?PHP
        $url = "http://www.croquemonster.com/api/monsters.xml?name=BlackTom;pass=dxUS3CPj0Lzkr8ZhQPJo";
				$xml = readXML( $url );
				if( substr($xml, 0, 6)== "Erreur" )
				{
						echo $xml;
						exit;
				}
?>
<html>
    <head>
    <style>
		.stats td{width: 50px;}
        .monstre1{border: 1px solid blue;width:200px;}
    	.monstre2{border: 1px solid red;width:200px;}
    	.hidden {display: none;}
		.visible {display: block;}
		#stats1 td {border: 1px solid blue;}
		#stats2 td {border: 1px solid red;}
		#stats_fus td {border: 1px solid purple;}
</style>
    
    <script language="javascript">
    <!--
        arrMonstres = new Array();
    <?PHP
        foreach($xml as $monstre) 
        {
            echo "arrMonstres.push( new Array(".$monstre['sadism'].","
                                               .$monstre['ugliness'].","
                                               .$monstre['power'].","
                                               .$monstre['greediness'].","
                                               .$monstre['control'].","
                                               .$monstre['fight'].","
                                               .$monstre['endurance'].","
                                               .'"'.$monstre['swfjs'].'"'.") );\n";
        }
    ?>
        function fusion(p_lvl_accre, p_stat1, p_stat2)
        {
            var v_stat_res='';
            
            var v_stat_sup = Math.max(p_stat1, p_stat2);
            var v_stat_inf = Math.min(p_stat1, p_stat2);
            
            if ( v_stat_inf >= 0.6 * v_stat_sup )
            {
                    v_stat_res = Math.min(v_stat_sup + 1, p_lvl_accre+2);
            }
            else
            {
            
                    v_stat_res = Math.min( Math.ceil((( v_stat_inf + v_stat_sup)/2)+1), p_lvl_accre+2)+'~'+Math.min( v_stat_sup + 1,  p_lvl_accre+2);
            }
            return v_stat_res;  
        }
        
        function maj_fusion()
        {
            document.getElementById('sad_fus').innerHTML=fusion(16, parseInt(document.getElementById('sad1').innerHTML), parseInt(document.getElementById('sad2').innerHTML));
            document.getElementById('ugl_fus').innerHTML=fusion(16, parseInt(document.getElementById('ugl1').innerHTML), parseInt(document.getElementById('ugl2').innerHTML));
            document.getElementById('pow_fus').innerHTML=fusion(16, parseInt(document.getElementById('pow1').innerHTML), parseInt(document.getElementById('pow2').innerHTML));
            document.getElementById('gre_fus').innerHTML=fusion(16, parseInt(document.getElementById('gre1').innerHTML), parseInt(document.getElementById('gre2').innerHTML));
            document.getElementById('con_fus').innerHTML=fusion(16, parseInt(document.getElementById('con1').innerHTML), parseInt(document.getElementById('con2').innerHTML));
            document.getElementById('fig_fus').innerHTML=fusion(16, parseInt(document.getElementById('fig1').innerHTML), parseInt(document.getElementById('fig2').innerHTML));
            document.getElementById('end_fus').innerHTML=fusion(16, parseInt(document.getElementById('end1').innerHTML), parseInt(document.getElementById('end2').innerHTML));  
        }
        function maj_stats( mid,identifiant)
        {
                document.getElementById('stats'+mid).innerHTML = '<td id="sad'+mid+'">'+arrMonstres[identifiant][0]+'</td>'
                                                                +'<td id="ugl'+mid+'">'+arrMonstres[identifiant][1]+'</td>'
                                                                +'<td id="pow'+mid+'">'+arrMonstres[identifiant][2]+'</td>'
                                                                +'<td id="gre'+mid+'">'+arrMonstres[identifiant][3]+'</td>'
                                                                +'<td id="con'+mid+'">'+arrMonstres[identifiant][4]+'</td>'
                                                                +'<td id="fig'+mid+'">'+arrMonstres[identifiant][5]+'</td>'
                                                                +'<td id="end'+mid+'">'+arrMonstres[identifiant][6]+'</td>';
                maj_fusion();
        }
        
        function hide(mid,identifiant)
        {
            for(var idx=0; idx<10; idx++)
            {
                document.getElementById('monstre'+mid+idx).className = "monstre"+mid+" hidden";
            }
            
            document.getElementById(identifiant).className = "monstre"+mid+" visible";
            
        }
        
        function change_image(oDivName, idx)
        {
            alert('<script type="text/javascript" src="'+ arrMonstres[idx][7] +'"></script>');
            document.getElementById(oDivName).innerHTML = 'Hey, ça marche ?'+'<script type="text/javascript" src="'+ arrMonstres[idx][7] +'"></script> ';
        }
    -->
    </script>
    </head>
    <body onload="javascript:hide(1,'monstre10');hide(2,'monstre21');maj_stats(1,0);maj_stats(2,1);">
    <form action="#self" method="POST">
        <table><tr><td>
        <select name="monstre1" onchange="javascript:hide(1,'monstre1'+this.value);maj_stats(1,this.value);" >
    <?PHP
        $id=0;
        foreach($xml as $monstre) 
        {
            $selected="";
            if ($id==0)
                $selected=" selected=\"selected\"";
                echo "<option$selected value=\"$id\">".$monstre['name']."</option>";//."<BR/>\n";//="Nom du monstre"
              $id++;
        }
        ?>      
        </select>   </td>
        <td>
                <table class="stats">
                                <tr><td><img src="images/sadism.gif"/></td>
                                    <td><img src="images/ugliness.gif"/></td>
                                    <td><img src="images/power.gif"/></td>
                                    <td><img src="images/greediness.gif"/></td>
                                    <td><img src="images/control.gif"/></td>
                                    <td><img src="images/fight.gif"/></td>
                                    <td><img src="images/endurance.gif"/></td></tr>
                                <tr id="stats1" ><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                <tr id="stats2" ><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                <tr id="stats_fus"><td id="sad_fus">&nbsp;</td>
                                                   <td id="ugl_fus">&nbsp;</td>
                                                   <td id="pow_fus">&nbsp;</td>
                                                   <td id="gre_fus">&nbsp;</td>
                                                   <td id="con_fus">&nbsp;</td>
                                                   <td id="fig_fus">&nbsp;</td>
                                                   <td id="end_fus">&nbsp;</td></tr>
                </table>
        </td>
        
        
        <td>    

    <select name="monstre2" onchange="javascript:hide(2,'monstre2'+this.value);maj_stats(2,this.value);" >
    <?PHP
        $id=0;
        foreach($xml as $monstre) 
        {
            $selected="";
            if ($id==1)
                $selected=" selected=\"selected\"";
                echo "<option$selected value=\"$id\">".$monstre['name']."</option>";//."<BR/>\n";//="Nom du monstre"
              $id++;
        }
        ?>      
        </select>   </td></tr>
        <tr><td>
<?PHP
        $id=0;
        foreach($xml as $monstre) 
        {
            echo "<div id=\"monstre1$id\" class=\"monstre1\"><script type=\"text/javascript\" src=\"".$monstre['swfjs']."\"></script></div>";
            $id++;
        }
?></td><td>&nbsp;</td><td><?PHP
                    $id=0;
        foreach($xml as $monstre) 
        {
            echo "<div id=\"monstre2$id\" class=\"monstre2\"><script type=\"text/javascript\" src=\"".$monstre['swfjs']."\"></script></div>";
            
            $id++;
        }

?>      
        </tr></table>
        
        
    </body>
</html>

