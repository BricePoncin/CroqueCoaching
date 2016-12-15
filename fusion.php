<?PHP
		require_once("depoil.inc.php");
?>

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
                echo "\t<option$selected value=\"$id\">".$monstre['name']."</option>\n";//."<BR/>\n";//="Nom du monstre"
              $id++;
        }
        ?> 
        		<option value="99">[ AMPE ]</option>     
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
                                <tr id="stats1"  ><td id="sad1">0</td>
                                									<td id="ugl1">0</td>
                                									<td id="pow1">0</td>
                                									<td id="gre1">0</td>
                                									<td id="con1">0</td>
                                									<td id="fig1">0</td>
                                									<td id="end1">0</td></tr>
                                <tr id="stats2"  ><td id="sad2">0</td>
                                									<td id="ugl2">0</td>
                                									<td id="pow2">0</td>
                                									<td id="gre2">0</td>
                                									<td id="con2">0</td>
                                									<td id="fig2">0</td>
                                									<td id="end2">0</td></tr>
                                <tr id="stats_fus"><td id="sad_fus">0</td>
                                                   <td id="ugl_fus">0</td>
                                                   <td id="pow_fus">0</td>
                                                   <td id="gre_fus">0</td>
                                                   <td id="con_fus">0</td>
                                                   <td id="fig_fus">0</td>
                                                   <td id="end_fus">0</td></tr>
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
                echo "\t<option$selected value=\"$id\">".$monstre['name']."</option>\n";//."<BR/>\n";//="Nom du monstre"
              $id++;
        }
        ?>    
        		<option value="99">[ AMPE ]</option>    
        </select>   </td></tr>
        <tr><td>
<?PHP
        $id=0;
        foreach($xml as $monstre) 
        {
            echo "\t\t<div id=\"monstre1$id\" class=\"monstre1\"><script type=\"text/javascript\" src=\"".$monstre['swfjs']."\"></script></div>\n";
            $id++;
        }
?>
 				<div id="monstre199" class="monstre1"><textarea id="txt_monstre1" rows="15" cols="30"><?PHP echo $v_monstre1;?></textarea>
 							<a href="javascript:load_ampe(1);">Rafraichir</a>
 				</div>
				</td><td style="valign:top;">
				<p>Si vous choisissez de simuler une fusion de l'un de vos monstres avec un monstre de l'AMPE, s&eacute;lectionnez [ AMPE ] dans l'une des liste d&eacute;roulante, et faites un copier/coller des statistiques du monstre choisi dans l'AMPE</p>
				
				<p>Voici l'apparence que <b>POURRAIT</b> avoir le monstre r&eacute;sultant de cette fusion. Si certaines caract&eacute;ristiques sont repr&eacute;sent&eacute;es par un intervalle (ex: 15~20), celle-ci sera tir&eacute;e au hasard. Cela peut modifier l'apparence de votre monstre. Cette fonctionnalit&eacute; est fournie &agrave; titre indicatif.</p>
				<IFRAME id="frm_result" name="frm_result" src="./monstrinator.php" width=170 height=250 scrolling=false frameborder=0> </IFRAME>
				</td><td>
<?PHP
                    $id=0;
        foreach($xml as $monstre) 
        {
            echo "\t\t<div id=\"monstre2$id\" class=\"monstre2\"><script type=\"text/javascript\" src=\"".$monstre['swfjs']."\"></script></div>\n";
            
            $id++;
        }
?>  
				<div id="monstre299" class="monstre2"><textarea id="txt_monstre2" rows="15" cols="30"><?PHP echo $v_monstre2;?></textarea>
							<a href="javascript:load_ampe(2);">Rafraichir</a>
				</div>
    			</td>
        </tr></table>
