function affiche_result(pi_cnc_id)
{
	document.getElementById('cnc_id').value = pi_cnc_id;
	document.forms['frm_resultats'].submit();
}

function afficher_cacher(piIdent)
{
	var el = document.getElementById(piIdent);
	
	if(el.style.display=="none")
    {
        el.style.display="block";
        document.getElementById('montrer_cacher').innerHTML='-';
    }
    else
    {
        el.style.display="none";
        document.getElementById('montrer_cacher').innerHTML='+';
    }
    return true;
	
}