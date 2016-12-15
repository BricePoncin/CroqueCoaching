        function load_ampe(numero)
				{
						var textBrut = document.getElementById('txt_monstre'+numero).value;
						var arText = textBrut.split(/\r\n|\r|\n/);
				
						var v_Sad=0;
						var v_Lai=0;
						var v_For=0;
						var v_Gou=0;
						var v_Con=0;
						var v_Com=0;
						var v_End=0;
						
						for(idx in arText)
						{
						   if (arText[idx].indexOf('Sadisme ', 0) == 0)
						   {
						   		v_Sad = arText[idx].substr( arText[idx].indexOf('Sadisme ', 0) + 'Sadisme '.length );
						   }
						   else if (arText[idx].indexOf('Laideur ', 0) == 0)
						   {
						   		v_Lai = arText[idx].substr( arText[idx].indexOf('Laideur ', 0) + 'Laideur '.length );
						   }
						   else if (arText[idx].indexOf('Force ', 0) == 0)
						   {
						   		v_For = arText[idx].substr( arText[idx].indexOf('Force ', 0) + 'Force '.length );
						   }
						   else if (arText[idx].indexOf('Gourmand ', 0) == 0)
						   {
						   		v_Gou = arText[idx].substr( arText[idx].indexOf('Gourmand ', 0) + 'Gourmand '.length );
						   }
						   else if (arText[idx].indexOf('Contr', 0) == 0)
						   {
						   		v_Con = arText[idx].substr( arText[idx].indexOf('Contr', 0) + 'Contrôle '.length );
						   }
						   else if (arText[idx].indexOf('Combat ', 0) == 0)
						   {
						   		v_Com = arText[idx].substr( arText[idx].indexOf('Combat ', 0) + 'Combat '.length );
						   }
						   else if (arText[idx].indexOf('Endurance ', 0) == 0)
						   {
						   		v_End = arText[idx].substr( arText[idx].indexOf('Endurance ', 0) + 'Endurance '.length );
						   }
						}
						
						document.getElementById('stats'+numero).innerHTML = '<td id="sad'+numero+'">'+v_Sad+'</td>'
              	                                               +'<td id="ugl'+numero+'">'+v_Lai+'</td>'
              	                                               +'<td id="pow'+numero+'">'+v_For+'</td>'
              	                                               +'<td id="gre'+numero+'">'+v_Gou+'</td>'
              	                                               +'<td id="con'+numero+'">'+v_Con+'</td>'
              	                                               +'<td id="fig'+numero+'">'+v_Com+'</td>'
              	                                               +'<td id="end'+numero+'">'+v_End+'</td>';	
						maj_fusion();
				}
				
				function maj_stats( mid,identifiant)
        {
    	
        		if( identifiant != 99 )
        		{
            		document.getElementById('stats'+mid).innerHTML = '<td id="sad'+mid+'">'+arrMonstres[identifiant][0]+'</td>'
                                                                +'<td id="ugl'+mid+'">'+arrMonstres[identifiant][1]+'</td>'
                                                                +'<td id="pow'+mid+'">'+arrMonstres[identifiant][2]+'</td>'
                                                                +'<td id="gre'+mid+'">'+arrMonstres[identifiant][3]+'</td>'
                                                                +'<td id="con'+mid+'">'+arrMonstres[identifiant][4]+'</td>'
                                                                +'<td id="fig'+mid+'">'+arrMonstres[identifiant][5]+'</td>'
                                                                +'<td id="end'+mid+'">'+arrMonstres[identifiant][6]+'</td>';
								
								document.getElementById('frm_result').src='./monstrinator.php5?sad='+arrMonstres[identifiant][0]+'&lai='+arrMonstres[identifiant][1]+'&for='+arrMonstres[identifiant][2]+'&gou='+arrMonstres[identifiant][3]+'&con='+arrMonstres[identifiant][4]+'&com='+arrMonstres[identifiant][5]+'&end='+arrMonstres[identifiant][6]+'&niv_fus=1';

								maj_fusion();
						}		
						else
						{
								document.getElementById('stats'+mid).innerHTML = '<td id="sad'+mid+'">0</td>'
              	                                                +'<td id="ugl'+mid+'">0</td>'
              	                                                +'<td id="pow'+mid+'">0</td>'
              	                                                +'<td id="gre'+mid+'">0</td>'
              	                                                +'<td id="con'+mid+'">0</td>'
              	                                                +'<td id="fig'+mid+'">0</td>'
              	                                                +'<td id="end'+mid+'">0</td>';
								document.getElementById('frm_result').src='./monstrinator.php5';	
						}
					
        }
        
        
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