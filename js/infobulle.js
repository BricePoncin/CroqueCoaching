var xOffset=6
var yOffset=5

var affiche = false; // La variable i nous dit si le bloc est visible ou non
var w3c=document.getElementById && !document.all;
var ie=document.all;

if (ie||w3c) {
var laBulle
}

function ietruebody(){ // retourne le bon corps...
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function deplacer(e) {
if(affiche){
var curX = (w3c) ? e.pageX : event.x + ietruebody().scrollLeft;
var curY = (w3c) ? e.pageY : event.y + ietruebody().scrollTop;

var winwidth = ie && !window.opera ? ietruebody().clientWidth : window.innerWidth - 20;
var winheight = ie && !window.opera ? ietruebody().clientHeight : window.innerHeight - 20;

var rightedge = ie && !window.opera ? winwidth - event.clientX - xOffset : winwidth - e.clientX - xOffset;
var bottomedge = ie && !window.opera ? winheight - event.clientY - yOffset : winheight - e.clientY - yOffset;

var leftedge = (xOffset < 0) ? xOffset*(-1) : -1000

// modifier la largeur de l'objet s'il est trop grand...
if(laBulle.offsetWidth > winwidth / 3){
laBulle.style.width = winwidth / 3
}

// si la largeur horizontale n'est pas assez grande pour l'info bulle
if(rightedge < laBulle.offsetWidth){
// bouge la position horizontale de sa largeur à gauche
laBulle.style.left = curX - laBulle.offsetWidth + "px"
} else {
if(curX < leftedge){
laBulle.style.left = "5px"
} else{
// la position horizontale de la souris
laBulle.style.left = curX + xOffset + "px"
}
}

// même chose avec la verticale
if(bottomedge < laBulle.offsetHeight){
laBulle.style.top = curY - laBulle.offsetHeight - yOffset + "px"
} else {
laBulle.style.top = curY + yOffset + "px"
}
}
}

function showMonstre(mid, name, fatigue )
{
		text = '<table>'
					+'<tr><td>'+document.getElementById('monstre'+mid).innerHTML+'</td></tr>'
					+'<tr><td><b>'+name+'</b></td></tr></table>';	
					
			if (fatigue > 0)
				text = text + '<span style="color:red;">Encore '+fatigue+' heures de repos</span><br/>';		
			else		
				text = text + '<span style="color:green;">Monstre repos&eacute</span><br/>';
		
		showTooltip(text);
}

function showMonstreInfos(mid, name, sadism, ugliness, power, greediness, control, fight, endurance, bounty, fatigue, occupation)
{
		text = '<table>'
					+'<tr><td rowspan="6">'+document.getElementById('monstre'+mid).innerHTML+'</td></tr>'
					+'<tr><td colspan="2"><b>'+name+'</b></td></tr>'
					+'<tr><td><img src="images/sadism.gif"/>&nbsp;'    +sadism    +'</td><td><img src="images/control.gif"/>&nbsp;'  +control  +'</td></tr>'
					+'<tr><td><img src="images/ugliness.gif"/>&nbsp;'  +ugliness  +'</td><td><img src="images/fight.gif"/>&nbsp;'    +fight    +'</td></tr>'
					+'<tr><td><img src="images/power.gif"/>&nbsp;'     +power     +'</td><td><img src="images/endurance.gif"/>&nbsp;'+endurance+'</td></tr>'
					+'<tr><td><img src="images/greediness.gif"/>&nbsp;'+greediness+'</td><td><img src="images/miniMoney.gif"/>&nbsp;'+bounty   +'</td></tr></table>';	

			if (fatigue > 0)
				text = text + '<span style="color:red;">Encore '+fatigue+' heures de repos</span><br/>';		
			else		
				text = text + '<span style="color:green;">Monstre repos&eacute</span><br/>';
		
			switch(occupation)
			{
			case 'contrat': text = text + '<p>Ce monstre est affect&eacute; &agrave; un contrat.</p>'; break;
			case 'escort' : text = text + '<p>Ce monstre est parti escorter un co&eacute;quipier.</p>'; break;
			case 'attack' : text = text + '<p>Ce monstre est parti attaquer un adversaire.</p>'; break;
			case 'racket' : text = text + '<p>Ce monstre va racketter un petit copain &agrave; la sortie de l\'&eacute;cole.</p>'; break;
			case 'propa'  : text = text + '<p>Ce monstre va distribuer des tracts et faire un beau discours de <b>propagande</b>.</p>'; break;
			case 'match'  : text = text + '<p>Ce monstre va tenter de briller en MBL.</p>'; break;
			default : text = text + '<p>Ce monstre est libre, Max.</p>'
			}
		
		
		showTooltip(text);
}


function showContractInfo(name, city, country,sadism, ugliness, power, greediness)
{
		text ='<strong>'+name+'</strong><BR/>' 
		      +'<b>'+city+'</b>&nbsp('+country+')<BR/>'
					+'<img src="images/sadism.gif"/>     '+sadism+'<BR/>'
					+'<img src="images/ugliness.gif"/>	 '+ugliness+'<BR/>'
					+'<img src="images/power.gif"/>  		 '+power+'<BR/>'
					+'<img src="images/greediness.gif"/> '+greediness;	
		showTooltip(text);
}

function showTooltip(text) {
if (w3c||ie){
laBulle = document.all ? document.all["bulle"] : document.getElementById ? document.getElementById("bulle") : ""
laBulle.innerHTML = text; // fixe le texte dans l'infobulle
laBulle.style.visibility = "visible"; // Si il est cachee (la verif n'est qu'une securite) on le rend visible.
affiche = true;
}
}
function hideTooltip() {
if (w3c||ie){
affiche = false
laBulle.style.visibility="hidden" // avoid the IE6 cache optimisation with hidden blocks
laBulle.style.top = '-100000px'
laBulle.style.backgroundColor = ''
laBulle.style.width = ''
}
}


document.onmousemove = deplacer; // des que la souris bouge, on appelle la fonction move pour mettre a jour la position de la bulle.
 