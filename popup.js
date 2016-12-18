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
function monPopNamaNi(url,width,height)
{
		if (!ie5&&!ns6)
		window.open(url,"","width=width,height=height,scrollbars=1")
		else
		{
				document.getElementById("popNamaNi").style.display=''
				document.getElementById("popNamaNi").style.width=initialwidth=width+"px"
				document.getElementById("popNamaNi").style.height=initialheight=height+"px"
				document.getElementById("popNamaNi").style.left="30px"
				document.getElementById("popNamaNi").style.top=ns6? window.pageYOffset*1+30+"px" : voirie().scrollTop*1+30+"px"
				document.getElementById("NaMaNiPoP").src=url
		}
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

