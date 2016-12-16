var tireNamaNi=false
var revenirHN=0
var initialwidth,initialheight
var ie5=document.all&&document.getElementById
var ns6=document.getElementById&&!document.all
function voirie()
{
		return (document.compatMode!="reviensNamaNi")? document.documentElement : document.body
}
function tirePousse(e)
{
		if (ie5&&tireNamaNi&&event.button==1)
		{
				document.getElementById("popNamaNi").style.left=tempx+event.clientX-offsetx+"px"
				document.getElementById("popNamaNi").style.top=tempy+event.clientY-offsety+"px"
		}// http://www.namani.net 
		else if (ns6&&tireNamaNi)
		{
				document.getElementById("popNamaNi").style.left=tempx+e.clientX-offsetx+"px"
				document.getElementById("popNamaNi").style.top=tempy+e.clientY-offsety+"px"
		}
}
function onyVaty(e)
{
		offsetx=ie5? event.clientX : e.clientX
		offsety=ie5? event.clientY : e.clientY
		document.getElementById("dansPopNamaNi").style.display="none" //extra
		tempx=parseInt(document.getElementById("popNamaNi").style.left)
		tempy=parseInt(document.getElementById("popNamaNi").style.top)
		tireNamaNi=true
		document.getElementById("popNamaNi").onmousemove=tirePousse
}

function fctPopNamaNi(e) 
{
		var target;
		if (!e) var e = window.event;
		if (e.target) target = e.target; // Mozilla
		else if (e.srcElement) target = e.srcElement; // IE
		if (targ.nodeType == 3) target = target.parentNode; // Safari
		
		var id = target.id;
		var url = target.href;
		var width = 600;
		var height = 900;

		fctPopNamaNi(url,width,height);
		
		if(!event) event = window.event;
		event.preventDefault(); 
		
		return false;
}


function monPopNamaNi(e, url,target)
{
	var width  = 300;
	var height = 155;
	
	if (target==undefined) 
		target = 'popup';
	
	if(target != 'popup')
		return true;
	
	if (!ie5&&!ns6)
		window.open(url,"","width=width,height=height,scrollbars=1");
	else
	{
		var AObject = e
		var posX = 0;
		var posY = 0;		
		
		do
		{
			posX += AObject.offsetLeft;
			posY += AObject.offsetTop;
			AObject = AObject.offsetParent;
		}
		while( AObject != null );

		document.getElementById("popNamaNi").style.display='';
		document.getElementById("popNamaNi").style.width=initialwidth=width+"px";
		document.getElementById("popNamaNi").style.height=initialheight=height+"px";
		document.getElementById("popNamaNi").style.left=posX;
		document.getElementById("popNamaNi").style.top=posY - height;
		document.getElementById("NaMaNiPoP").src=url;
	}
	
	return false;
}
function agggrandis()
{
		if (revenirHN==0)
		{
				revenirHN=1 //restaurer la fenêtre
				document.getElementById("hihiMax").setAttribute("src","restore.gif")
				document.getElementById("popNamaNi").style.width=ns6? window.innerWidth-20+"px" : voirie().clientWidth+"px"
				document.getElementById("popNamaNi").style.height=ns6? window.innerHeight-20+"px" : voirie().clientHeight+"px"
		}
		else
		{
				revenirHN=0 //aggrandir la fenêtre
				document.getElementById("hihiMax").setAttribute("src","max.gif")
				document.getElementById("popNamaNi").style.width=initialwidth
				document.getElementById("popNamaNi").style.height=initialheight
		}
		document.getElementById("popNamaNi").style.left=ns6? window.pageXOffset+"px" : voirie().scrollLeft+"px"
		document.getElementById("popNamaNi").style.top=ns6? window.pageYOffset+"px" : voirie().scrollTop+"px"
}
function tchaoAdemain()
{
		document.getElementById("popNamaNi").style.display="none"
}// http://www.namani.net 
function retiensMoi()
{
		tireNamaNi=false;
		document.getElementById("popNamaNi").onmousemove=null;
		document.getElementById("dansPopNamaNi").style.display="" //extra
}

