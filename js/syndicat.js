function onProgress(e) {
  var percentComplete = (e.position / e.totalSize)*100;

	alert(percentComplete);
}

function onError(e) {
  alert("Une erreur " + e.target.status + " s'est produite au cours de la réception du document.");
}


function test_download()
{
 if(window.XMLHttpRequest) 
		   	xhr = new XMLHttpRequest(); 
		else if(window.ActiveXObject)
		  	 xhr = new ActiveXObject("Microsoft.XMLHTTP");  
 xhr.open("GET", "http://www.google.com/firefox/");  
 xhr.send(null);  
		
		
 if(xhr.status == 200)  
       dump(xhr.responseText);  
      else  
       dump("Error loading page\n"); 
		
/*		
		var xhr_object = null; 
		var args='login=BlackTom&pass=pioupiou';
		
		if(window.XMLHttpRequest) 
		   	xhr_object = new XMLHttpRequest(); 
		else if(window.ActiveXObject)
		  	 xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
		else
		{ 
	  		alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest..."); 
	    	return; 
		} 

		var url='http://www.croquemonster.com/user/login';

		xhr_object.open("POST", url, true);
		xhr_object.onprogress = onProgress;
//		xhr_object.onload = onLoad;
		xhr_object.onerror = onError;

		
		xhr_object.onreadystatechange  = function() 
		{ 
	  		if(xhr_object.readyState == 4) 
	  		{
						alert(xhr_object.responseText); // DEBUG MODE
						//document.write(xhr_object.responseText);
						eval(xhr_object.responseText);
		 		}
				return xhr_object.readyState;
		} 
		xhr_object.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	
		//  Envoi de la requête
		xhr_object.send(args);

alert(xhr_object.responseText); // DEBUG MODE
*/

}