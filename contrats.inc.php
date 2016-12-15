<!--<script src="js/jquery-1.5.1.min.js" type="text/javascript"></script>-->
<script type="text/javascript">
<!--
function ouvre_ferme(lien,identifiant)
        {
            var v_class = document.getElementById(identifiant).className;
          
            if (v_class == "hidden")
            {
            		document.getElementById(lien).innerHTML = "(-)";
            		document.getElementById(identifiant).className = "visible";
            }
            else
            {
            		document.getElementById(identifiant).className = "hidden";
            		document.getElementById(lien).innerHTML = "(+)";
            }
       }

	function getElementsByClass(searchClass, node, tag)
	{
	  var classElements = new Array();
	  if(node == null) node = document;
	  if(tag == null) tag = '*';
	  
	  var els = node.getElementsByTagName(tag);
	  var elsLen = els.length;
	  var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
	  
	  for(i = 0, j = 0; i < elsLen; i++)
	  {
	    if(pattern.test(els[i].className) )
	      { 
	        classElements[j] = els[i];
	        j++;
	      }
	  }
	  
	  return classElements;
	}

	
	function refresh(elem)
	{
			var elments = getElementsByClass('ville_a_points', null, 'tr');
			
      for(var i = 0; i < elments.length; i++)
			{
				var cn = elments[i].className.replace(/^\s+/g,'').replace(/\s+$/g,'');
				
				if( cn == elem )
					elments[i].style.display = "table-row";
				else
					elments[i].style.display = "none";
    	}
	}

		function min(a, b)
		{
			if (a>b) return b;
			else return a;
		}
		function max(a, b)
		{
			if (a>b) return a;
			else return b;
		}
		
		function pct_reussite( stat_dem, stat_pres, diff, bonus_moon )
		{
				ret = 100 + 5 * (stat_pres - stat_dem) - diff + bonus_moon;
				
				ret = max (5, ret);
				ret = min (100, ret);
				
				return(ret);
		}

		function pct_manger( mtr_greediness, mtr_control, crt_diff, crt_kind, bonus_moon )
		{
				var coeff=0;
				
				switch(crt_kind)
				{
				case '1': coeff = 1; break;
				case '2': coeff = 1.5; break;
				case '3': coeff = 2; break;
				}
				
				ret = 100 + ( 5 * (mtr_control - (coeff * mtr_greediness)) ) + bonus_moon;
				ret = max (5, ret);
				ret = min (100, ret);
				
				return(ret);
		}

		function set_infos_contrat(crt_name, crt_city, crt_country, crt_kind, crt_diff, crt_sadism, crt_ugliness, crt_power, crt_greediness, mid, mtr_name, mtr_sadism, mtr_ugliness, mtr_power, mtr_greediness, mtr_control, bonus_moon )
		{
				var e = document.getElementById('infos_contrat');	
				
				e.innerHTML = crt_name+' habitant '+crt_city+' ('+crt_country+')';
				switch(crt_kind)
				{
				case 0: e.innerHTML+= 'Contrat enfantin'; break;
				case 1: e.innerHTML+= 'Contrat abordable'; break;
				case 2: e.innerHTML+= 'Contrat normal'; break;
				case 3: e.innerHTML+= 'Contrat difficile'; break;
				case 4: e.innerHTML+= 'Contrat monstrueux'; break;
				case 5: e.innerHTML+= 'Contrat infernal'; break;
				}
				e.innerHTML+= '<BR/>';
				e.innerHTML+= '<b>Pr&eacute;requis du contrat :</b><BR/>';
				e.innerHTML+= '<img src="images/sadism.gif"/>&nbsp;'+crt_sadism+'<BR/>';
				e.innerHTML+= '<img src="images/ugliness.gif"/>&nbsp;'+crt_ugliness+'<BR/>';
				e.innerHTML+= '<img src="images/power.gif"/>&nbsp;'+crt_power+'<BR/>';
				e.innerHTML+= '<img src="images/greediness.gif"/>&nbsp;'+crt_greediness+'<BR/>';
				
				if(mtr_name != '')
				{
						e.innerHTML += document.getElementById('monstre'+mid).innerHTML+'<BR/>';
						e.innerHTML+= 'Chances de <b>'+mtr_name+'</b><BR/>';
						text = '<table border="1">\n';
						if(crt_sadism>0)
						{
								pct = pct_reussite( crt_sadism, mtr_sadism, crt_diff, bonus_moon );
								text += '<tr><td><img src="images/sadism.gif"/></td>'
											+ '<td>'+mtr_sadism+'</td>'
											+ '<td><div class="xpMiniBar">'
											+ '			<div class="xpBorder">'
											+ '				<div class="xpBar" style="width: '+pct+'%;"> </div>'
											+ '				<div class="xpNb"> '+pct+'%</div>'
											+ '				</div>'
											+ '		</div>'
											+ '</td></tr>\n';
						}
						if(crt_ugliness>0)
						{
							pct = pct_reussite( crt_ugliness, mtr_ugliness, crt_diff, bonus_moon );
								text += '<tr><td><img src="images/ugliness.gif"/></td>'
											+ '<td>'+mtr_ugliness+'</td>'
											+ '<td><div class="xpMiniBar">'
											+ '			<div class="xpBorder">'
											+ '				<div class="xpBar" style="width: '+pct+'%;"> </div>'
											+ '				<div class="xpNb"> '+pct+'%</div>'
											+ '				</div>'
											+ '		</div>'
											+ '</td></tr>\n';
						}
						if(crt_power>0)
						{
								pct = pct_reussite( crt_power, mtr_power, crt_diff, bonus_moon );
								text += '<tr><td><img src="images/power.gif"/></td>'
											+ '<td>'+mtr_power+'</td>'
											+ '<td><div class="xpMiniBar">'
											+ '			<div class="xpBorder">'
											+ '				<div class="xpBar" style="width: '+pct+'%;"> </div>'
											+ '				<div class="xpNb"> '+pct+'%</div>'
											+ '				</div>'
											+ '		</div>'
											+ '</td></tr>\n';

								
						}
						if(crt_greediness>0)
						{
								pct = pct_reussite( crt_greediness, mtr_greediness, crt_diff, bonus_moon );
								text += '<tr><td><img src="images/greediness.gif"/></td>'
											+ '<td>'+mtr_greediness+'</td>'
											+ '<td><div class="xpMiniBar">'
											+ '			<div class="xpBorder">'
											+ '				<div class="xpBar" style="width: '+pct+'%;"> </div>'
											+ '				<div class="xpNb"> '+pct+'%</div>'
											+ '				</div>'
											+ '		</div>'
											+ '</td></tr>\n';

								
						}
						else
						{
								pct = pct_manger( mtr_greediness, mtr_control, crt_diff, crt_kind, bonus_moon );
								text += '<tr><td><img src="images/noburp.gif"/></td>'
											+ '<td>'+mtr_greediness+'</td>'
											+ '<td><div class="xpMiniBar">'
											+ '			<div class="xpBorder">'
											+ '				<div class="xpBar" style="width: '+pct+'%;"> </div>'
											+ '				<div class="xpNb"> '+pct+'%</div>'
											+ '				</div>'
											+ '		</div>'
											+ '</td></tr>\n';

								
						}
	
						text += '</table>';
						e.innerHTML=e.innerHTML + text;
				}
				
				e.className="visible";
				
				
		}

-->
</script>
<?PHP

		include_once('maj_stats_w5.inc.php');
		require_once("sql.php");

		connect();

		$stmt = "SELECT sens_tri, filtre_reussite, filtre_gain, affich_enf, affich_abo, affich_nor, affich_dif, affich_mon, affich_inf, fuseau_min, fuseau_max, affich_villes, affich_habille, affich_primes, filtre_inferno, ouvr_fenetre, timezone
							FROM cm_preferences
							WHERE login = '".$_SESSION['name']."'";
	
		select($stmt, $arPref);

		if( count($arPref) == 0 )
		{
				$stmt = "insert into cm_preferences (login) values ('".$_SESSION['name']."')"	;
				insert($stmt);
				
        $sens_tri        = "chrono";
        $enf             = 1;
        $abo             = 1;
        $nor             = 1;
        $dif             = 1;
        $mon             = 1;
        $inf             = 1;
        $filtre_reussite = 80;
        $filtre_gain     = 2000;
        $fuseau_min      = 0;
        $fuseau_max      = 24;
        $affich_ville	   = 'non';
        $affich_habille  = 'oui';
        $affich_primes   = 'non';
        $filtre_inferno  = 0;
        $ouvr_fenetre    = 'popup';
        $timezone				 = 'Europe/Paris';
        
        $sel_zone='Europe';
        $sel_ville='Paris';
		
				//echo "<script> $('#sel_zones').val('".$sel_zone."'); $('#sel_zones').change(); $('#sel_villes').val('".$sel_zone."/".$sel_ville."'); </script>";
		}
		else
		{
				$sens_tri        = ($arPref[0]['sens_tri']       != '')?$arPref[0]['sens_tri']       :"chrono";
				$enf             = ($arPref[0]['affich_enf']     != '')?$arPref[0]['affich_enf']     :1;
				$abo             = ($arPref[0]['affich_abo']     != '')?$arPref[0]['affich_abo']     :1;
				$nor             = ($arPref[0]['affich_nor']     != '')?$arPref[0]['affich_nor']     :1;
				$dif             = ($arPref[0]['affich_dif']     != '')?$arPref[0]['affich_dif']     :1;
				$mon             = ($arPref[0]['affich_mon']     != '')?$arPref[0]['affich_mon']     :1;
				$inf             = ($arPref[0]['affich_inf']     != '')?$arPref[0]['affich_inf']     :1;
				$filtre_reussite = ($arPref[0]['filtre_reussite']!= '')?$arPref[0]['filtre_reussite']:80;
				$filtre_gain     = ($arPref[0]['filtre_gain']    != '')?$arPref[0]['filtre_gain']    :2000;
				$fuseau_min      = ($arPref[0]['fuseau_min']     != '')?$arPref[0]['fuseau_min']     :0;
				$fuseau_max      = ($arPref[0]['fuseau_max']     != '')?$arPref[0]['fuseau_max']     :24;
				$affich_ville    = ($arPref[0]['affich_villes']  != '')?$arPref[0]['affich_villes']  :'non';
				$affich_habille  = ($arPref[0]['affich_habille'] != '')?$arPref[0]['affich_habille'] :'oui';
				$affich_primes   = ($arPref[0]['affich_primes']  != '')?$arPref[0]['affich_primes']  :'non';
				$filtre_inferno  = ($arPref[0]['filtre_inferno'] != '')?$arPref[0]['filtre_inferno'] :0;
				$ouvr_fenetre    = ($arPref[0]['ouvr_fenetre']   != '')?$arPref[0]['ouvr_fenetre']   :'popup';
				$timezone        = ($arPref[0]['timezone']       != '')?$arPref[0]['timezone']       :'Europe/Paris';
				
				list($sel_zone, $sel_ville) = explode('/', $timezone);
		
				//echo "<script> $('#sel_zones').val('".$sel_zone."'); $('#sel_zones').change(); $('#sel_villes').val('".$sel_zone."/".$sel_ville."'); </script>";
		}

		if(isset($_POST['maj'] ))
		{
				if(isset($_POST['timezone']))
					$timezone = $_POST['timezone'];
				else
					$timezone = 'Europe/Paris';

				list($sel_zone, $sel_ville) = explode('/', $timezone);
//echo "<script> alert('".$sel_zone."/".$sel_ville."'); </script>";
			
				if(isset($_POST['ouvr_fenetre']))
					$ouvr_fenetre = $_POST['ouvr_fenetre'];
				else
					$ouvr_fenetre = 'popup';
							
				if(isset($_POST['filtre_inferno']))
					$filtre_inferno=$_POST['filtre_inferno'];
				else
					$filtre_inferno=0;

				if(isset($_POST['affich_primes']))
					$affich_primes=$_POST['affich_primes'];
				else
					$affich_primes='non';

				if(isset($_POST['affich_habille']))
					$affich_habille=$_POST['affich_habille'];
				else
					$affich_habille='oui';
		
				if(isset($_POST['affich_villes']))
					$affich_ville=$_POST['affich_villes'];
				else
					$affich_ville='non';
		
				if (isset($_POST['fuseau_min'] ) )
					$fuseau_min=$_POST['fuseau_min'];
				else
					$fuseau_min=0;
				if (isset($_POST['fuseau_max'] ) )
					$fuseau_max=$_POST['fuseau_max'];
				else
					$fuseau_max=24;
			
				if (isset($_POST['tri'] ) )
						$sens_tri = $_POST['tri'];
				else
						$sens_tri = "chrono";
		
				$enf = 0;
				$abo = 0;
				$nor = 0;
				$dif = 0;
				$mon = 0;
				$inf = 0;
		
				if ( isset($_POST['filtre_diff']) )
				{
					foreach($_POST['filtre_diff'] as $val)
					{
						if ($val == 0)  $enf = 1;
						if ($val == 1)  $abo = 1;
						if ($val == 2)  $nor = 1;
						if ($val == 3)  $dif = 1;
						if ($val == 4)  $mon = 1;
						if ($val == 5)  $inf = 1;
					}
				}	
				else
				{
					$enf = 1;
					$abo = 1;
					$nor = 1;
					$dif = 1;
					$mon = 1;
					$inf = 1;
				}
		
				if ( isset($_POST['filtre_reussite']) )
						$filtre_reussite = $_POST['filtre_reussite'];
				else
						$filtre_reussite = 80;
						
				if ( isset($_POST['filtre_gain']) )
						$filtre_gain = $_POST['filtre_gain'];
				else
						$filtre_gain     = 1500;   
						
						
				$stmt = "update cm_preferences set "
							. "  sens_tri        = '".$sens_tri."'"
							. ", affich_enf      = ".$enf            
							. ", affich_abo      = ".$abo            
							. ", affich_nor      = ".$nor            
							. ", affich_dif      = ".$dif            
							. ", affich_mon      = ".$mon            
							. ", affich_inf      = ".$inf            
							. ", filtre_reussite = ".$filtre_reussite
							. ", filtre_gain     = ".$filtre_gain    
							. ", fuseau_min      = ".$fuseau_min
							. ", fuseau_max      = ".$fuseau_max
							. ", affich_villes   = '".$affich_ville."'"
							. ", affich_habille  = '".$affich_habille."'"
							. ", affich_primes   = '".$affich_primes."'"
							. ", filtre_inferno  = '".$filtre_inferno."'"
							. ", ouvr_fenetre    = '".$ouvr_fenetre."'"
							. ", timezone        = '".$timezone."'"
							. " where login = '".$_SESSION['name']."'";
				insert($stmt);
				
				$ret = insert("update  cm_updates set upd_preferences=NOW() where login='".$_SESSION['name']."'");
				if ( $ret	== 0 )
				{
						insert("insert into cm_updates (login, upd_preferences) values ('".$_SESSION['name']."', NOW())");
				}
		}
		else if(isset($_POST['maj_tab'] ))
		{
				if (isset($_POST['tri'] ) )
						$sens_tri = $_POST['tri'];
				else
						$sens_tri = "chrono";
						
						
				$stmt = "update cm_preferences set "
							. "  sens_tri        = '".$sens_tri."'"
							. " where login = '".$_SESSION['name']."'";
				insert($stmt);
				
				$ret = insert("update  cm_updates set upd_preferences=NOW() where login='".$_SESSION['name']."'");
				if ( $ret	== 0 )
				{
						insert("insert into cm_updates (login, upd_preferences) values ('".$_SESSION['name']."', NOW())");
				}
		}
		
		//if ($affich_ville == 'oui' )
		{
			$filtre_ville   = array();
			$fav_ville      = array();
			$interdit_ville = array();

			$stmt="select filtre_villes, fav_villes, interdit_villes from cm_preferences where login='".$_SESSION['name']."'";
			select( $stmt, $arVilles);
			
			$filtre_ville   = explode(";", $arVilles[0]['filtre_villes']) ; 
			$fav_ville      = explode(";", $arVilles[0]['fav_villes']) ; 
			$interdit_ville = explode(";", $arVilles[0]['interdit_villes']) ; 
		}
		
		if($debug) echo "Mémoire consommée (step 0.5) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";                     
		
		 function calcul_esperance_gain($contracts, $monsters)
		 {
		 		global $debug;
		 	
		 		$total = 0;
	 			foreach( $monsters as $monstre )
	 			{
	 					if( $monstre['contract'] > 0 )
	 					{
	 							if($debug)
	 								echo "Prize : ".$contracts[ $monstre['contract'] ]['prize']." Bounty ".$monstre['bounty']."<br/>";
	 							$total = $total + ($contracts[ $monstre['contract'] ]['prize'] - $monstre['bounty'] );
	 					}

	 			}
	 			
	 			return $total;
		 }
		
		function max_reussite($arLine)
		{
		    global $affich_habille;
		
				$max = 0;
				
				foreach( $arLine as $case )
				{
						if( $affich_habille == 'oui' )
							$reussite = $case['reussite_habille'];
						else
							$reussite = $case['reussite'];
						if($reussite > $max )
								$max = $reussite;
				}
				return $max;
		}

		function max_gain($arLine)
		{
				$max = 0;
				foreach( $arLine as $case )
				{
						if($case['gain'] > $max )
								$max = $case['gain'];
				}
				return $max;
		}

		function calcule_reussite( $contrat_power,
		                           $contrat_ugliness,
		                           $contrat_greediness,
		                           $contrat_sadism,
		                           $contrat_difficulty,
		                           $contrat_kind,
		                           $monstre_power,
		                           $monstre_ugliness,
		                           $monstre_greediness,
		                           $monstre_sadism,
		                           $monstre_control,
		                           &$reussite,
		                           &$pct_manger )
		{
				global $bonusMoon;
				$result=1;
				$ungre = 0;
								                                        
				if($monstre_sadism == 0)
					$sad = 5;
				else
					$sad = min(100, max(5, 100-($contrat_sadism - $monstre_sadism)*5 - $contrat_difficulty + $bonusMoon));
				
				if($monstre_ugliness == 0)
					$ugl = 5;
				else
					$ugl = min(100, max(5, 100-($contrat_ugliness - $monstre_ugliness)*5 - $contrat_difficulty + $bonusMoon));
				
				if($monstre_power == 0)
					$pow = 5;
				else
					$pow = min(100, max(5, 100-($contrat_power - $monstre_power)*5 - $contrat_difficulty + $bonusMoon));
			
				if( $contrat_greediness > 0 )
				{
						if($monstre_greediness == 0)
							$gre = 5;
						else
							$gre = min(100, max(5, 100-($contrat_greediness - $monstre_greediness)*5 - $contrat_difficulty + $bonusMoon));
							
						$result = $result*($gre/100);
				}	
				else
				{
						if ($monstre_greediness == 0)
								$ungre = 100;
						else
						{
								switch($contrat_kind )
								{
								case 1: $coeff=1;break;	
								case 2: $coeff=1.5;break;
								case 3: $coeff=2;break;
								}
				//echo "coeff : $coeff => ";				
								$calcul = floor( $monstre_control - ($coeff * $monstre_greediness) );
				//echo "$ungre => ";
							//	$calcul = $calcul - ($calcul % 5);
				//echo "$ungre => ";
								$ungre = 100 + ($calcul * 5)  - $bonusMoon;
				//echo "$ungre => ";
								$ungre = min( 100, max ( 0, $ungre ) );
				//echo "$ungre ";
								
								//return $ungre;
						}
						
						$result = $result*($ungre/100);
				}

				if ($contrat_sadism > 0)
						$result = $result*($sad/100);
				if ($contrat_ugliness > 0)
						$result = $result*($ugl/100);
				if ($contrat_power > 0)
						$result = $result*($pow/100);
			
				$result = floor($result*100);
			
				$reussite = $result;
				$pct_manger = $ungre;
				
				return $result;
		}


function villes_a_point($sxml)
	{
		  //Acc&egrave;s aux enfants de l'&eacute;l&eacute;ment courant
		  foreach($sxml->children() as $element)
		  {
		  		if ( $element->getName() == "c" )
		  		{
		  				$city['id']=intval($element['id']);
							
							$arCities[ intval($element['id']) ] = $city;
		  		}
		      villes_a_point($element);
		  }
			return ($arCities);
	}

function cities($sxml)
		{
		  //Acc&egrave;s aux enfants de l'&eacute;l&eacute;ment courant
		  foreach($sxml->children() as $element)
		  {
		  		if ( $element->getName() == "city" )
		  		{
		  				$city['id']=intval($element['id']);
							$city['name']=utf8_decode(strval($element['name']));
							$city['country']=utf8_decode(strval($element['country']));
							$city['reputation']=intval($element['reputation']);
							$city['percent']=intval($element['percent']);
							$city['infernos']=intval($element['infernos']);
							
							$cle = utf8_decode(strval($element['name']))." - ".utf8_decode(strval($element['country']));
							$arCities[$cle] = $city;
		  		}
		      cities($element);
		  }
			return ($arCities);
		}


function process_contrats( $xmlContrats, $xmlMonstres, $xmlCities, &$contracts, &$monsters, &$gain_max )
{
		$contracts = array();
		$monsters  = array();
		$cities  = array();
		$tableau_reussites = array();
		
		$cities =  cities($xmlCities);
		unset($xmlCities);
		
		$idx=0;
		foreach($xmlContrats as $line)
		{
				if( isset($line['name']) )
				{
						$contrat['id']         = intval($line['id']);
						$contrat['countdown']  = intval($line['countdown']);
						$contrat['city']       = utf8_decode(strval($line['city']));
						$contrat['country']    = utf8_decode(strval($line['country']));
						$contrat['timezone']   = intval($line['timezone']);
						$contrat['name']       = utf8_decode(strval($line['name']));
						//$contrat['age']        = intval($line['age']);
						//$contrat['sex']        = intval($line['sex']);
						$contrat['kind']       = intval($line['kind']);
						$contrat['difficulty'] = intval($line['difficulty']);
						$contrat['accepted']   = strval($line['accepted']);
						$contrat['monster']    = intval($line['monster']);
						$contrat['prize']      = intval($line['prize']);
						
						$contrat['power'] 		 = intval($line['power']);
						$contrat['ugliness'] 	 = intval($line['ugliness']);
						$contrat['greediness'] = intval($line['greediness']);
						$contrat['sadism'] 		 = intval($line['sadism']);
						
						$inferno=0;
						
						//print_r($cities);
						$cle=utf8_decode(strval($line['city'])).' - '.utf8_decode(strval($line['country']));
						$contrat['inferno']	= $cities[ $cle ]['percent'];
						
						
						/*
						foreach($cities as $city)
						{
								if( ($city['name'] == $contrat['city']) && ($city['country'] == $contrat['country']) )	
								{
										$contrat['inferno']	= $city['percent'];
								}
						}
						*/
						$contracts[intval($line['id'])] = $contrat;
						$idx++;
						
						
				}
		}

		if($debug) echo "Mémoire consommée (step 1.1) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";                     
		unset($xmlContrats );

		$idx=0;
		foreach($xmlMonstres as $line)
		{
			if( isset($line['isMblMonster']) )
				continue;
			
			$monstre['id'] 						= intval($line['id'] 						);
			$monstre['name']					= utf8_decode(strval($line['name']));
			//$monstre['firePrize']			= intval($line['firePrize']			);
			//$monstre['fusions']				= intval($line['fusions']				);
			$monstre['permanentItems']=        $line['permanentItems'] ;
			$monstre['contractItems']	=        $line['contractItems']	 ;
			$monstre['bounty']				= intval($line['bounty']				);
			$monstre['fatigue']				= intval($line['fatigue']				);
			$monstre['contract']			= intval($line['contract']			);
			//$monstre['failures']			= intval($line['failures']			);
			//$monstre['successes']			= intval($line['successes']			);
			//$monstre['devoured']			= intval($line['devoured']			);
			$monstre['swfjs']					=        $line['swfjs']					;
			
			$monstre['power']					= intval($line['power']					);
			$monstre['ugliness']			= intval($line['ugliness']			);
			$monstre['greediness']		= intval($line['greediness']		);
			$monstre['sadism']				= intval($line['sadism']				);

			$monstre['control']				= intval($line['control']				);
			$monstre['fight']					= intval($line['fight']					);
			$monstre['endurance']			= intval($line['endurance']			);
			      
			$monstre['escort']        = intval($line['escort']        );
			$monstre['attack']        = intval($line['attack']        );
			$monstre['racket']        = intval($line['racket']        );
			$monstre['propaganda']    = intval($line['propaganda']    );
			$monstre['match']         = intval($line['match']         );
			
			$monstre['watchSpas']     = intval($line['watchSpas']     );
			      
			$monsters[intval($line['id'])] = $monstre;
			$idx++;
		}
		
		if($debug) echo "Mémoire consommée (step 1.2) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";                     		
		unset($xmlMonstres);
		
		foreach( $contracts as $contrat )
		{
				if (isset($contrat['name']) )
				{
						foreach($monsters as $monstre)	
						{
								if ( $gain_max[ $monstre['id'] ] < ($contrat['prize']-$monstre['bounty']) )
										$gain_max[ $monstre['id'] ] = ($contrat['prize']-$monstre['bounty']);
								
								$reussite = calcule_reussite( $contrat['power'],
																			$contrat['ugliness'],
																			$contrat['greediness'],
																			$contrat['sadism'],
																			$contrat['difficulty'],
																			$contrat['kind'],
																			$monstre['power'],
																			$monstre['ugliness'],
																			$monstre['greediness'],
																			$monstre['sadism'],
																			$monstre['control'],
																			$reussite,
																			$pct_manger
																			 );	

								$monstr_habille = monstro_habillage ( $monstre, $contrat );

								$reussite_habille = calcule_reussite( $contrat['power'],
																			$contrat['ugliness'],
																			$contrat['greediness'],
																			$contrat['sadism'],
																			$contrat['difficulty'],
																			$contrat['kind'],
																			$monstr_habille['power'],
																			$monstr_habille['ugliness'],
																			$monstr_habille['greediness'],
																			$monstr_habille['sadism'],
																			$monstr_habille['control'],
																			$reussite_habille,
																			$pct_manger_babille
																			 );	


								$accepted = ($contrat['accepted']!="false")?1:0;
								$affected = ($monstre['id']==$contrat['monster'])?1:0;
								$occuped  = ($monstre['contract']   != 0 
								          || $monstre['escort']     != 0
								          || $monstre['attack']     != 0
								          || $monstre['racket']     != 0
								          || $monstre['propaganda'] != 0
								          || $monstre['match']      != 0)?1:0;


								$tableau_reussites[$contrat['id']][$monstre['id']] = array('reussite' => $reussite
																													                ,'reussite_habille' => $reussite_habille
																													 								,'equip_a_installer' => $monstr_habille['equip']
																													 								, 'peut_manger'=> ($contrat['greediness']>0)?1:0
																																					, 'pct_manger' => $pct_manger
																																					, 'gain'=> ($contrat['prize']-$monstre['bounty'])
																																					, 'countdown' => $contrat['countdown']
																																					, 'accepted' => $accepted 
																																					, 'affected' => $affected
																																					, 'occuped'  => $occuped
																																					, 'fatigued' => ( ($monstre['fatigue']*3600) > $contrat['countdown'])?1:0
																																					);
						}
				}
		}
		
		if($debug) echo "Mémoire consommée (step 1.3) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";                     
		return $tableau_reussites;

}    

function agences($sxml)
{
  //Acc&egrave;s aux enfants de l'&eacute;l&eacute;ment courant
  foreach($sxml->children() as $element)
  {
  		if ( $element->getName() == "agency" )
  		{
  				//$agency['id']=intval($element['id']);
					$agency['name']=strval($element['name']);
					//$agency['score']=intval($element['score']);
					//$agency['reputation']=intval($element['reputation']);
					//$agency['level']=intval($element['level']);
					$agency['fartBox']=strval($element['fartBox']);
					
					if($agency['name']==$_SESSION['name'])
							$_SESSION['ville_prout'] = $agency['fartBox'];
					
					$arAgencies[] = $agency;
  		}
      agences($element);
  }
	return ($arAgencies);
}

function list_villes( $arContracts )
{
		$arVilles=array();
		$arCities=array();
		$arCountries=array();
		foreach($arContracts AS $contrat)	
		{
				$arVilles[] = array('ville'=> $contrat['city'], 'pays' => $contrat['country']);
				$arCities[]=$contrat['city'];
				$arCountries[]=$contrat['country'];
		}
		
		
		array_multisort($arCities, SORT_ASC, $arCountries, SORT_ASC, $arVilles);
		
		return $arVilles;
}

function list_contrats_speciaux( &$arContracts )
{
		global $affich_ville;
		global $filtre_ville;
		global $fav_ville;
		global $arAgences;
		
		$listRes=array();
		
		foreach( $arContracts as &$contrat )
		{
				if (in_array($contrat['city'], $arAgences ) === true)
				{
						$contrat['ico_val'] = '4';
						$contrat['ico'] = 'ville_prout.png';
				}
				
				if (in_array($contrat['city'], $filtre_ville ) === true)
				{
						$contrat['ico_val'] = '5';
						$contrat['ico'] = 'city_selected.png';
				}
				if (in_array($contrat['city'], $fav_ville ) === true)
				{
						$contrat['ico_val'] = '8';
						$contrat['ico'] = 'city_favorite.png';
						$listRes[]=$contrat['id'];
				}	
				
				if( $contrat['name'] == "David et David" ) 
				{
					$listRes[]=$contrat['id'];
					$contrat['ico'] = 'control.gif';
					$contrat['ico_val'] = '6';
				}
				
				if( $contrat['name'] == "Camp de boyscouts" ) 
				{
					$listRes[]=$contrat['id'];
					$contrat['ico'] = 'scout.jpg';
					$contrat['ico_val'] = '4';
				}
				if(( $contrat['name'] == "Un lutin de noël" ) 
				 ||( $contrat['name'] == "Le Gros Truc Rouge" ))
				{
					$listRes[]=$contrat['id'];
					$contrat['ico'] = 'pere_noel.jpg';
					$contrat['ico_val'] = '4';
				}

				 		// Spécial Halloween
				if(( $contrat['name'] == "A la casserole !" ) 
				 ||( $contrat['name'] == "Houlala qu'il m'énerve" ) 
				 ||( $contrat['name'] == "Encore un mioche déguisé" ) 
				 ||( $contrat['name'] == "M. farce et attrape")
				 ||( $contrat['name'] == "Mioche qui s'y croit")
				 ||( $contrat['name'] == "Le roi de la sonnette")
				 ||( $contrat['name'] == "Plein de sucre")
				 ||( $contrat['name'] == "Même pas peur"))
				 {
					$listRes[]=$contrat['id'];
					$contrat['ico'] = 'halloween.jpg';
					$contrat['ico_val'] = '4';
				 }		
				 		// Contrats sur les 3 nouvelles villes
				if( $contrat['city'] == "Squiiick" ) 
				{
					$listRes[]=$contrat['id'];
					$contrat['ico'] = 'squiiick.gif';
					$contrat['ico_val'] = '3';
				 }
				if( $contrat['city'] == "Engloutown" ) 
				{
					$listRes[]=$contrat['id'];
					$contrat['ico'] = 'engloutown.gif';
					$contrat['ico_val'] = '3';
				 }
				if( $contrat['city'] == "Hororgoréale" )
				 {
					$listRes[]=$contrat['id'];
					$contrat['ico'] = 'hororgoreale.gif';
					$contrat['ico_val'] = '3';
				 }
				 
				 		// boite à prout
				 if( utf8_encode($contrat['city']) == $_SESSION['ville_prout'] )
				 {
					$listRes[]=$contrat['id'];
					$contrat['ico'] = 'fartbox.gif';
					$contrat['ico_val'] = '9';
				 }
				    // Contrats infernaux
				 if( $contrat['kind'] == 5 )
				 {
					$listRes[]=$contrat['id'];
					$contrat['ico'] = 'finferno.png';
					$contrat['ico_val'] = '10';
				 }
		}
	
		$listRes = array_unique ( $listRes );
		return $listRes;
}


function meilleur_contrat_par_monstre( &$reussiteContrats, $filtre_gain, $filtre_reussite )
{
		global $affich_habille;
		
		$maxGain=array();
		foreach( $reussiteContrats as $ligContrat )
		{
				foreach( $ligContrat as $mid => $caseMonstre )
				{
						if ($affich_habille == 'oui')
								$reussite = $caseMonstre['reussite_habille'];
						else
								$reussite = $caseMonstre['reussite'];
								
						if( ( $reussite >= $filtre_reussite ) 
						 && ( $caseMonstre['gain'] >= $filtre_gain ) 
						 && ( $caseMonstre['gain'] > $maxGain[$mid] )
						 && ( $caseMonstre['fatigued'] == 0 ) )
						{
								$maxGain[$mid] = $caseMonstre['gain'];
						}
				}
		}
		
		foreach( $reussiteContrats as $cid=> $ligContrat )
		{
				foreach( $ligContrat as $mid => $caseMonstre )
				{
						if( $caseMonstre['gain'] == $maxGain[$mid] )
						{
								$reussiteContrats[$cid][$mid]['max'] = 'gain_max';
						}
				}
		}
}

if($debug) echo "Mémoire consommée (step 0.6) : ".(memory_get_usage() - $baseMemory)."<BR/>\n";                     
