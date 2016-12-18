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

	function show_hide_contract(src, tgt)
	{
		var elment = document.getElementById(tgt);
	
		if( src.checked == true )
			elment.style.display = "table-row";
		else
			elment.style.display = "none";

		return(true);
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