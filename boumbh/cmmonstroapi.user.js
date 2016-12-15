// ==UserScript==
// @name           cmMonstroAPI
// @namespace      cmMonstroAPI
// @include        http://www.croquemonster.com/news
// ==/UserScript==

var scriptData = {
	updatesCheck: 'none',
	refreshTime: new Date(),
	initiated: false,
	version: '0.04',
	name: 'CM Monstro API Dev',
	prefix: 'cmMonstroAPI',
	updateLog: null,
	timeouts: {},
	current: {
		running: false,
		type: '',
	},
	dumpDataInfos: ['score', 'dumpSize', 'co2Estimate', 'dumpRecord', 'co2Record'],
};
var scriptParameters = new Object();
var userParameters = new Object();

function replacer(key, value) {
	// alert(key + ' ' + typeof(value));
    if (typeof value == 'array') {
		var result = '';
		for (i in value) {
			if (result != '') result += ', ';
			result += '"' + i + '" : "' + value[i] + '"';
		}
        return '[' + result + ']';
    }
    return value;
}

/*********
 * Xpath *
 *********/
 // Taken from Mozilla developer center (https://developer.mozilla.org/fr/Introduction_%C3%A0_l%27utilisation_de_XPath_avec_JavaScript)
function nsResolver(prefix) {
  var ns = {
    'xhtml' : 'http://www.w3.org/1999/xhtml',
  };
  return ns[prefix] || null;
}

// Taken from CroqueMonster AMPE Counter script by Raphael Quinet 
function $xpath(expression, contextNode, type) {
    return (contextNode.nodeType == 9 ? contextNode : contextNode.ownerDocument).evaluate(expression, contextNode, nsResolver, type, null);
}

/*********************
 * Script Parameters *
 *********************/
function getScriptParameters() {
	var tempParameters = GM_getValue('params', 'null');
	if (tempParameters != 'null') scriptParameters = eval(tempParameters);
	if (typeof(scriptParameters.updatesEnabled) == 'undefined') scriptParameters.updatesEnabled = true;
}

function setScriptParameters() {
	GM_setValue('params', '(' + JSON.stringify(scriptParameters) + ')');
}

/***********
 * Updates *
 ***********/
// This function has been originally written by Kykyo
function checkForUpdates() {
	messageWrite(":charte: Vérification des mises à jour.");

	setNewTimeout('updates', function () {
		scriptData.updatesCheck = 'timeout';
		init();
	}, 10000);

	if (!timeoutEnded('updates')) GM_xmlhttpRequest({
		method: "GET",
		url: scriptData.url + ".version",
		headers: {
			"User-agent": "Mozilla/4.0 (compatible) Greasemonkey",
			"Content-Type": "text/plain; charset=utf-8",
			"Pragma": "no-cache",
			"Refresh": "1",
		},
		onload: function(responseDetails) {
			if (!endTimeout('updates')) return;
			if (!responseDetails.responseText || responseDetails.responseText == "" || responseDetails.responseText.substr(0, 9) == "<!DOCTYPE" ||
				responseDetails.responseText.substr(0, 5) == "<html") {
				scriptData.updatesCheck = 'error';
				init();
				return;
			}
			var response = responseDetails.responseText.split("\n");
			var updateVersion = response[0];
			var updateType = response[1];
			scriptData.updateLog = "[h1]Historique des mises à jour[/h1]";
			for (var i = 2; i < response.length; i++) scriptData.updateLog += "[br/]" + response[i];
			scriptData.updateLog += "[cite]Votre version : [b]" + scriptData.version + "[/b][br/]Dernière mise à jour : [b]" + updateVersion + "[/b][/cite]";
			if (parseFloat(scriptData.version) < parseFloat(updateVersion)) {
				document.getElementById(scriptData.prefix + 'Icon').src = scriptData.linkLocation + 'Images/iconUpdates.png';
				scriptData.updatesCheck = updateType + 'Updates';
			} else scriptData.updatesCheck = 'ok';
			init();
		},
		onerror: function(responseDetails) {
			if (!endTimeout('updates')) return;
			scriptData.updatesCheck = 'error';
			init();
		}
	});
	return false;
}
function handleUpdates() {
	if (scriptData.updatesCheck == 'ok') scriptData.updatesCheck = 'done';
	if (scriptData.updatesCheck == 'error') {
		messageWrite(':charte: [important]Erreur[/important]');
		scriptData.updatesCheck = 'done';
	}
	if (scriptData.updatesCheck == 'timeout') {
		messageWrite(':charte: [important]Time Out[/important]');
		scriptData.updatesCheck = 'done';
	}
	
	if (scriptData.updatesCheck.substr(scriptData.updatesCheck.length - 7) == 'Updates') {
		messageWrite("Une [b]nouvelle version[/b] du script [b]" + scriptData.name + "[/b] est disponible :youpi: !");
		messageWrite("Vous pouvez l'installer en cliquant sur [lien=" + scriptData.url + ".user.js]ce lien[/lien], ensuite il faudra [lien=javascript:history.go(0)]réactualiser la page[/lien]... Hoyoyo !");
		messageWrite("Si ça marche pas, vous pouvez aller chercher le script [lien=" + scriptData.linkLocation + "]ici[/lien] !");
		messageWrite("Si vous recevez ce message à chaque actualisation, [b]videz votre cache[/b].");
		if (scriptData.updatesCheck == "securityUpdates") {
			messageWrite("[important][tab/][tab/][tab/][tab/][tab/]Mise à jour importante, le script a été désactivé.[/important]");
			messageAddButton("InitScript", "Activer le script", function() { scriptData.updatesCheck = 'done'; init(); messageRemoveButton("InitScript"); }, "Activer le script malgré la mise à jour importante");
		} else {
			scriptData.updatesCheck = 'done';
		}
		openMessageDiv();
	}
	if (scriptData.updatesCheck == 'none') scriptData.updatesCheck = 'done';
	else messageAddButton("DiplayUpdatesLog", "Historique", displayUpdatesLog, "Afficher l'historique des mises à jour");
	if (scriptData.updatesCheck == 'done') return true; else return false;
}

function displayUpdatesLog() {
	messageWrite(scriptData.updateLog);
	messageRemoveButton("DiplayUpdatesLog");
	openMessageDiv();
}

/*************
 * Time Outs *
 *************/
function setNewTimeout(name, func, delay) {
	if (timeoutEnded(name)) {
		return false;
	} else {
		scriptData.timeouts[name] = new Object();
		scriptData.timeouts[name].ended = false;
		scriptData.timeouts[name].timer = setTimeout(function () { 
			if (requestTimeout(name)) func();
		}, delay);
		return true;
	}
}

function requestTimeout(name) {
	if (!scriptData.timeouts[name].ended) scriptData.timeouts[name].ended = true;
	return scriptData.timeouts[name].ended;
}

function timeoutEnded(name) {
	return ((typeof(scriptData.timeouts[name]) != 'undefined') && (scriptData.timeouts[name].ended));
}

function endTimeout(name) {
	if (timeoutEnded(name)) {
		scriptData.timeouts[name].ended = false;
		return false;
	} else {
		clearTimeout(scriptData.timeouts[name].timer);
		scriptData.timeouts[name].ended = false;
		return true;
	}
}

/************
 * Messages *
 ************/
function generateMessageDiv() {
	if (document.getElementById(scriptData.prefix + 'MessageDiv') != null) return;
	var agencyDiv = document.body;
	var messageDiv = document.createElement('div');
	messageDiv.setAttribute('id', scriptData.prefix + 'MessageDiv');
	messageDiv.setAttribute('class', 'message');
	messageDiv.style.position = "fixed";
	messageDiv.style.backgroundColor = "rgb(252, 254, 255)";
	messageDiv.style.border = "3px solid rgb(28, 80, 89)";
	messageDiv.style.overflow = "auto";
	messageDiv.style.width = "700px";
	messageDiv.style.height = "400px";
	messageDiv.style.padding = "10px";
	messageDiv.style.visibility = "hidden";
	
	var writableMessageDiv = document.createElement('div');
	writableMessageDiv.setAttribute('id', scriptData.prefix + 'WritableMessageDiv');
	writableMessageDiv.style.paddingBottom = "10px";
	
	messageDiv.appendChild(writableMessageDiv);
	agencyDiv.appendChild(messageDiv);
	messageDiv.style.left = (window.innerWidth - messageDiv.offsetWidth)/2 + "px";
	messageDiv.style.top = (window.innerHeight - messageDiv.offsetHeight)/2 + "px";
	messageDiv.style.display = "none";
	messageDiv.style.visibility = "visible";
	
	messageWrite("[b]" + scriptData.name + " v" + scriptData.version + "[/b]");
	messageAddButton("CloseMessages", "Fermer", function() { closeMessage(); }, "Fermer cette stupide boîte");
	
	var where = '';
	var divGMMenu = document.getElementById("cmMonstroMenu");
	if (divGMMenu == null) {
		var contentTop = document.getElementById("contentTop");
		if (contentTop) where = 'contentTop';
		else {
			where = 'labo';
			var div = document.getElementById('labo');
			if (!div) {
				where = 'forum';
				div = document.getElementById('forum');
			}
			if (div) {
				var h1s = document.getElementsByTagName("h1");
				for (var i = 0; i < h1s.length; i++) if ((h1s[i].getAttribute("class") == 'noMarg') && (contentTop == null)) contentTop = h1s[i];
			} else {
				contentTop = document.body;
				where = 'body';
			}
		}
		if (contentTop) {
			divGMMenu = document.createElement("div");
			divGMMenu.style.position = "absolute";
			divGMMenu.style.clear = "none";
			divGMMenu.id = "cmMonstroMenu";
			if (where == 'forum') divGMMenu.style.top = "-2px"; else divGMMenu.style.top = "4px";
			divGMMenu.style.right = "4px";
			contentTop.appendChild(divGMMenu);
		}
	}
	
	var mailButton = document.createElement("a");
	mailButton.title = "Messages du script " + scriptData.name;
	mailButton.addEventListener('click', function() { openMessageDiv(); }, false);
	var mailIcon = document.createElement("img");
	mailIcon.alt = scriptData.name;
	mailIcon.style.fontSize = '8px';
	mailIcon.style.cursor = 'pointer';
	mailIcon.src = scriptData.linkLocation + 'Images/iconNormal.png';
	mailIcon.id = scriptData.prefix + 'Icon';
	mailButton.appendChild(mailIcon);
	divGMMenu.appendChild(mailButton);
}

function openMessageDiv() {
	var messageDiv = document.getElementById(scriptData.prefix + 'MessageDiv');
	if (messageDiv == null) {
		generateMessageDiv();
		messageDiv = document.getElementById(scriptData.prefix + 'MessageDiv');
	}
	
	messageDiv.style.display = "block";
	messageDiv.scrollTop = messageDiv.scrollHeight;
}

function closeMessage() {
	document.getElementById(scriptData.prefix + 'MessageDiv').style.display = "none";
}

function messageWrite(message) {
	var messageDiv = document.getElementById(scriptData.prefix + 'MessageDiv');
	if (messageDiv == null) {
		generateMessageDiv();
		messageDiv = document.getElementById(scriptData.prefix + 'MessageDiv');
	}
	
	var messageWDiv = document.getElementById(scriptData.prefix + 'WritableMessageDiv');
	if (message != "") {
		var span = document.createElement('span');
		
		mess = convertBBString(message);

		var span2 = document.createElement('span');
		span2.innerHTML = mess;
		span.appendChild(span2);

		span.appendChild(document.createElement('br'));
		messageWDiv.appendChild(span);
	}
}

function messageAddButton(id, text, func, details) {
	var messageDiv = document.getElementById(scriptData.prefix + 'MessageDiv');
	if (messageDiv == null) {
		generateMessageDiv();
		messageDiv = document.getElementById(scriptData.prefix + 'MessageDiv');
	}
	messageRemoveButton(id);
	var messageDiv = document.getElementById(scriptData.prefix + 'MessageDiv');
	var a = document.createElement('a');
	a.setAttribute('class', 'okButton');
	a.setAttribute('id', scriptData.prefix + 'Message' + id + 'Button');
	a.title = details;
	var text = document.createTextNode(" " + text + " ");
	a.appendChild(text);
	a.addEventListener('click', func, false);
	messageDiv.appendChild(a);
}

function messageExistButton(id) {
	return (document.getElementById(scriptData.prefix + 'Message' + id + 'Button') != null);
}

function messageRemoveButton(id) {
	var button = document.getElementById(scriptData.prefix + 'Message' + id + 'Button');
	if (button != null) button.parentNode.removeChild(button);
}

function convertBBString(s) {
	var r = s;
	r = r.replace(/\[b\](.*?)\[\/b\]/g, "<b>$1<\/b>");
	r = r.replace(/\[i\](.*?)\[\/i\]/g, "<i>$1<\/i>");
	r = r.replace(/\[tab\/]/g, "&#160;&#160;&#160;");
	r = r.replace(/\[br\/]/g, "<br/>");
	r = r.replace(/\[lien=([^\]]*)\](.*?)\[\/lien\]/g, "<a href=\"$1\">$2<\/a>");
	r = r.replace(/\[check-([^\]\=]*)=([^\]-]*)\](.*?)\[\/check\]/g, "<input type=\"checkbox\" value=\"$2\" id=\"$1_$2\"/><label for=\"$1_$2\">$3</label>");
	r = r.replace(/\[linput-([^\]\=]*)=([^\]-]*)\/\]/g, "<input type=\"text\" value=\"$2\" id=\"$1\" size=\"8\"/>");
	r = r.replace(/\[tinput-([^\]\=]*)=([^\]-]*)\/\]/g, "<input type=\"text\" value=\"$2\" id=\"$1\" size=\"3\"/>");
	r = r.replace(/\[input-([^\]\=]*)=([^\]-]*)\/\]/g, "<input type=\"text\" value=\"$2\" id=\"$1\" size=\"40\"/>");
	r = r.replace(/\[button-([^\]\=]*)=([^\]-]*)\/\]/g, "<a id=\"$1\" class=\"okButton\">$2</a>");
	r = r.replace(/\[span-([^\]]*)\](.*?)\[\/span\]/g, "<span id=\"$1\">$2<\/span>");
	r = r.replace(/\[check+([^\]\=]*)=([^\]-]*)\](.*?)\[\/check\]/g, "<input type=\"checkbox\" value=\"$2\" id=\"$1_$2\" checked=\"checked\"/><label for=\"$1_$2\">$3</label>");
	r = r.replace(/\[h1\](.*?)\[\/h1\]/g, "<h1>$1<\/h1>");
	r = r.replace(/\[h2\](.*?)\[\/h2\]/g, "<h2>$1<\/h2>");
	r = r.replace(/\[cite\](.*?)\[\/cite\]/g, "<cite>$1<\/cite>");
	r = r.replace(/\[important\](.*?)\[\/important\]/g, "<span style=\"color: red; font-weight: bold\">$1<\/span>");
	r = r.replace(/\[spoiler\](.*?)\[\/spoiler\]/g, "<span style=\"border: 1px dotted rgb(242, 140, 17); background-color: rgb(255, 245, 214); padding: 4px; color: rgb(255, 245, 214);\">$1<\/span>");
	r = r.replace(/:youpi:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_yeah.gif\"/>");
	r = r.replace(/:dislike:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/dislike.gif\"/>");
	r = r.replace(/:noon:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_nooo.gif\"/>");
	r = r.replace(/;\)/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_wink.gif\"/>");
	r = r.replace(/8O/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_eek.gif\"/>");
	r = r.replace(/:innocent:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_rolleyes.gif\"/>");
	r = r.replace(/:charte:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_chart.gif\"/>");
	r = r.replace(/:idee:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_idea.gif\"/>");
	r = r.replace(/:D/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_biggrin.gif\"/>");
	r = r.replace(/:cool:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_cool.gif\"/>");
	r = r.replace(/:P/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_razz.gif\"/>");
	r = r.replace(/:!:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_exclaim.gif\"/>");
	r = r.replace(/:quoi:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_question.gif\"/>");
	r = r.replace(/:fleche:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_arrow.gif\"/>");
	r = r.replace(/:croix:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_cross.gif\"/>");
	r = r.replace(/:timide:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_redface.gif\"/>");
	r = r.replace(/:lol:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_lol.gif\"/>");
	r = r.replace(/:temps:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/time.gif\"/>");
	r = r.replace(/:sadisme:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/sadism.gif\"/>");
	r = r.replace(/:laideur:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/ugliness.gif\"/>");
	r = r.replace(/:force:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/power.gif\"/>");
	r = r.replace(/:gourmand:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/greediness.gif\"/>");
	r = r.replace(/:controle:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/control.gif\"/>");
	r = r.replace(/:combat:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/fight.gif\"/>");
	r = r.replace(/:endurance:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/endurance.gif\"/>");
	r = r.replace(/:monstercredit:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/miniMoney.gif\"/>");
	r = r.replace(/:niveau:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/lvl.gif\"/>");
	r = r.replace(/:reputation:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/reputation.gif\"/>");
	r = r.replace(/:dontcare:/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_dontcare.gif\"/>");
	r = r.replace(/:\?/g, "<img src=\"http://www.croquemonster.com/gfx/forum/icon_confused.gif\"/>");
	return r;
}

/*******************
 * User Parameters *
 *******************/
function getAgencyId() {
	var href = document.getElementById('level').getElementsByTagName('a')[0].href;
	userParameters.agencyId = href.substr(href.lastIndexOf('/') + 1);
	getUserParameters();
	return true;
}

function getAgencyName() {
	messageWrite(":charte: Récupération du nom de l'agence.");
	setNewTimeout('agencyName', function () { 
		messageWrite(":charte: [important]Timeout.[/important]");
	}, 10000);
	
	GM_xmlhttpRequest({
		method: "GET",
		url: "http://www.croquemonster.com/api/agency.xml?id=" + userParameters.agencyId,
		headers: {
			"User-agent": "Mozilla/4.0 (compatible) Greasemonkey",
			"Content-Type": "text/plain; charset=utf-8",
			"Pragma": "no-cache",
			"Refresh": "1",
		},
		onload: function(responseDetails) {
			if (!endTimeout('agencyName')) return;
			if (!responseDetails.responseText || responseDetails.responseText == "" || responseDetails.responseText.substr(0, 9) == "<!DOCTYPE" ||
				responseDetails.responseText.substr(0, 5) == "<html") {
				messageWrite(":charte: [important]Erreur lors de la récupération du nom de l'agence.[/important]");
				return;
			}
			if (!responseDetails.responseXML)
				responseDetails.responseXML = new DOMParser().parseFromString(responseDetails.responseText, "text/xml");
			var doc = responseDetails.responseXML.documentElement;
			var agencyName = '';
			var syndicateId = '';
			var syndicateName = '';
			if (doc.tagName == 'locked') {
				messageWrite(":charte: [important]Fuseau.[/important]");
				return;
			}
			if (doc.tagName == 'err') {
				messageWrite(":charte: [important]Erreur " + doc.getAttribute('error') + ".[/important]");
				return;
			}
			if (doc.tagName == 'agency') {
				agencyName = doc.getAttribute('name');
			}
			if (doc.tagName == 'agency') {
				syndicateId = doc.getAttribute('syndicateId');
			}
			if (doc.tagName == 'agency') {
				syndicateName = doc.getAttribute('syndicate');
			}
			userParameters.agencyName = agencyName;
			scriptData.syndicateId = syndicateId;
			scriptData.syndicateName = syndicateName;
			init();
		},
		onerror: function(responseDetails) {
			if (!endTimeout('agencyName')) return;
			messageWrite(":charte: [important]Erreur lors de la récupération du nom de l'agence.[/important]");
			return;
		}
	});
	
	return false;
}

function getAgencyPass() {
	messageWrite(":innocent: Veuillez saisir votre clé d'API ([b]" + userParameters.agencyName + "[/b]) : [input-" + scriptData.prefix + "AgencyPassInput=/] [button-" + scriptData.prefix + "AgencyPassButton=Ok/]");
	document.getElementById(scriptData.prefix + 'AgencyPassButton').addEventListener('click', setAgencyPass, false);
	openMessageDiv();
	return false;
}

function setAgencyPass(e) {
	var input = document.getElementById(scriptData.prefix + 'AgencyPassInput');
	var button = document.getElementById(scriptData.prefix + 'AgencyPassButton');
	if (input.value == '') return;
	userParameters.agencyPass = input.value;
	input.parentNode.removeChild(input);
	button.parentNode.removeChild(button);
	init();
}

function resetAgencyPass(e) {
	var button = document.getElementById(scriptData.prefix + 'AgencyPassResetButton');
	delete(userParameters.agencyPass);
	setUserParameters();
	button.parentNode.removeChild(button);
	init();
}

function getUserParameters() {
	var tempParameters = GM_getValue(userParameters.agencyId + '.params', 'null');
	if (tempParameters != 'null') userParameters = eval(tempParameters);
	
	if (typeof(userParameters.servers) == 'undefined') {
		userParameters.servers = new Array();
		var server = new Object();
		server.name = 'CroqueCoaching';
		server.url = 'http://croquecoaching.blacktom197.com/';
		userParameters.servers.push(server);
	}
	for (var i = 0; i < userParameters.servers.length; i++) {
		if (typeof(userParameters.servers[i].ext) == 'undefined') {
			userParameters.servers[i].ext = 'php';
		}
	}
}

function setUserParameters() {
	GM_setValue(userParameters.agencyId + '.params', '(' + JSON.stringify(userParameters) + ')');
}

function getData() {
	var garbageBody;
	var chaine = "";
	for (var i = 0; i < document.getElementById('garbage').getElementsByTagName('div').length; i++) 
	{
		if (document.getElementById('garbage').getElementsByTagName('div')[i].className == "garbageBody") garbageBody = document.getElementById('garbage').getElementsByTagName('div')[i];
	}
	var dumpInfos;
	var currentPage;
	for (var i = 0; i < garbageBody.getElementsByTagName('div').length; i++) {
		if (garbageBody.getElementsByTagName('div')[i].className == "currentpage") currentPage = garbageBody.getElementsByTagName('div')[i];
		if (garbageBody.getElementsByTagName('div')[i].className == "dumpInfos") dumpInfos = garbageBody.getElementsByTagName('div')[i];
	}
	
	dumpSize = parseFloat(dumpInfos.getElementsByTagName('li')[1].childNodes[1].textContent.replace(/^(\n|\r|\s|\+)+/, '').replace(/(\n|\r|\s|\+)+$/, '').replace(/\./g, '').replace(/,/, '.'));
	totalPages = parseInt(currentPage.textContent.substring(currentPage.textContent.indexOf('/') + 1));
messageWrite('Total Pages : '+ totalPages );
	var progress = 0;
	//alert(chaine);
	
	if (!retrievingEnded) {
		displayProgressBar();
		changes = [];
		requestData(1);
	} else {
		displayGraph();
	}
	
}


function parseData(htmlDocument) {
	var tables = htmlDocument.getElementsByTagName('table');
	var table;
	
	var knownTimestamp = 0;
	
	if (savedChanges.length > 0) {
		knownTimestamp = savedChanges[0][0];
	}
	
	for (var i = 0; i < tables.length; i++) {
		if (tables[i].id == 'dumpLogs') table = tables[i];
	}
	var trs = table.getElementsByTagName('tr');
	var gm = false;
	for (var i = 0; (i < trs.length) && (!retrievingEnded); i++) {
		var tds = trs[i].getElementsByTagName('td');
		if (tds.length == 4) {
			var quantity =  parseFloat(tds[3].textContent.replace(/^(\n|\r|\s|\+)+/, '').replace(/(\n|\r|\s|\+)+$/, '').replace(/\./g, '').replace(/,/g, '.'));
			if ((isNaN(quantity)) && (!gm)) gm = true;
			if ((quantity != 0) || (gm)) {
				var date = tds[0].textContent.replace(/^(\n|\r|\s)+/, '');
				var agence = tds[1].textContent;
				
				var d = new Date();
				d.setYear("20" + date.substring(15,17));
				d.setMonth(parseInt(date.substring(12,14)) - 1);
				d.setDate(date.substring(9,11));
				d.setHours(date.substring(0,2).replace(/^0/, ''));
				d.setMinutes(date.substring(3,5).replace(/^0/, ''));
				d.setSeconds(0,0);
				
				var timestamp = d.getTime() / 10000;
				
				if (timestamp < knownTimestamp) {
					retrievingEnded = true;
				} else {
					changes.push([timestamp, agence, quantity]);
				}
				
				gm = false;
			}
		}
	}
	
	if (retrievingEnded) {
		for (var i = 0; i < savedChanges.length; i++) {
			if (savedChanges[i][0] < knownTimestamp) {
				changes.push(savedChanges[i]);	
			}
		}
		if (changes.length == savedChanges.length) {
			dataSaved = true;
		}
	}
}

function displayProgressBar() {
	var messageDiv = document.getElementById(scriptData.prefix + 'MessageDiv');
		
	var progressBar = document.createElement("div");
	progressBar.id = "cmMonstroDumpGraphProgressBar";
	progressBar.style.width = "504px";
	progressBar.style.height = "18px";
	progressBar.style.position = "fixed";
	progressBar.style.top = (messageDiv.offsetTop + messageDiv.offsetHeight - 3) + 'px';
	progressBar.style.left = (window.innerWidth - 504)/2 + 'px';
	progressBar.style.display = "block";
	progressBar.style.color = "rgb(94, 131, 59)";
	progressBar.style.fontSize = "16px";
	progressBar.style.padding = "0px";
	progressBar.style.textAlign = "center";
	progressBar.style.lineHeight = "18px";
	progressBar.style.fontWeight = "bold";
	//progressBar.style.border = "1px rgb(94, 131, 59) solid";
	progressBar.style.border = "3px solid rgb(28, 80, 89)";
	progressBar.style.backgroundImage = "url(\"/gfx/gui/input_bg3.jpg\")";
	
	var progressSpan = document.createElement("span");
	progressSpan.id = "cmMonstroDumpGraphProgressSpan";
	progressSpan.appendChild(document.createTextNode("0%"));
	progressBar.appendChild(progressSpan);
	
	var cursorBar = document.createElement("div");
	cursorBar.id = "cmMonstroDumpGraphCursorBar";
	cursorBar.style.width = "0px";
	cursorBar.style.height = "14px";
	cursorBar.style.position = "absolute";
	cursorBar.style.top = "1px";
	cursorBar.style.left = "1px";
	cursorBar.style.display = "block";
	cursorBar.style.border = "1px rgb(47, 65, 30) solid";
	cursorBar.style.opacity = "0.5";
	cursorBar.style.backgroundColor = "rgb(94, 131, 59)";

	progressBar.appendChild(cursorBar);
	messageDiv.appendChild(progressBar);
}

function destroyProgressBar() {
	var progressBar = document.getElementById("cmMonstroDumpGraphProgressBar");
	progressBar.parentNode.removeChild(progressBar);
}

function progressSet(pourcent) {
	var progressSpan = document.getElementById("cmMonstroDumpGraphProgressSpan");
	var cursorBar = document.getElementById("cmMonstroDumpGraphCursorBar");
	progressSpan.innerHTML = parseInt(pourcent) + "%";
	cursorBar.style.width = parseInt(pourcent * 5) + "px";
}

/****************
 * API Dumpdata *
 ****************/
 
function updateDumpdata() {
	if (scriptData.current.running) return;
	scriptData.current.running = true;
	scriptData.current.type = 'Dumpdata';
	document.getElementById(scriptData.prefix + 'APIUpdateDumpdata').removeEventListener('click', updateDumpdata, false);
	document.getElementById(scriptData.prefix + 'APIUpdateDumpdata').innerHTML = 'Mise à jour en cours...';
	displayProgressBar();
	
	scriptData.current.servers = new Array();
	scriptData.current.serverIndex = 0;
	scriptData.current.initialState = -1;
	scriptData.current.infos = new Array();
	for (var i = 0; i < userParameters.servers.length; i++) {
		scriptData.current.servers[i] = -1;
	}
	if (scriptData.current.serverIndex >= 0) dumpdataGetCurrentId(); else messageWrite(":dislike: Pas de serveur actuellement configuré");
	
}

function dumpdataGetCurrentId() {
	GM_xmlhttpRequest({
		method: "GET",
		url: userParameters.servers[scriptData.current.serverIndex].url + 'dumpdata.' + userParameters.servers[scriptData.current.serverIndex].ext + '?id=' + userParameters.agencyId + '&pass=' + userParameters.agencyPass + '&action=get',
		headers: {
			'User-agent': 'Mozilla/4.0 (compatible) Greasemonkey/0.3',
			'Accept': 'application/atom+xml,application/xml,text/xml',
		},
		onload: function(responseDetails) {
			if (!responseDetails.responseText || responseDetails.responseText == "" || responseDetails.responseText.substr(0, 9) == "<!DOCTYPE" ||
				responseDetails.responseText.substr(0, 5) == "<html") {
				messageWrite(":dislike: Une erreur s'est produite (mauvaise réponse du [lien=" + this.url + "]script[/lien])...");
				return;
			}
			var i = scriptData.current.serverIndex;
			var response = responseDetails.responseText.split("\n");
			if (response[1] == 'ok') {
				scriptData.current.servers[i] = parseInt(response[2]);
				if ((typeof(scriptData.current.servers[i]) != 'number') || (!(scriptData.current.servers[i] >= 0))) {
					scriptData.current.servers[i] = -1;
					messageWrite(":charte: [b]" + userParameters.servers[i].name + "[/b] Entrées actuellement enregistrées [b]Erreur (" + response[2] + ")[/b]");
				} else {
					if ((scriptData.current.initialState == -1) || (scriptData.current.initialState > scriptData.current.servers[i])) scriptData.current.initialState = scriptData.current.servers[i];
					scriptData.current.state = scriptData.current.initialState;
					messageWrite(":charte: [b]" + userParameters.servers[i].name + "[/b] Entrées actuellement enregistrées [b][span-" + scriptData.prefix + "DumpdataStatusSpan" + i + "]" + scriptData.current.servers[i] + "[/span][/b]");
				}
			} else {
				scriptData.current.servers[i] = -1;
				messageWrite(":charte: [b]" + userParameters.servers[i].name + "[/b] Entrées actuellement enregistrées [b]Erreur (" + response[1] + ")[/b]");
			}
			scriptData.current.serverIndex = i + 1;
			if (scriptData.current.serverIndex >= userParameters.servers.length) dumpdataGetTotalCurrentRecords();
			else dumpdataGetCurrentId();
		},
		onerror: function(responseDetails) {
			var i = scriptData.current.serverIndex;
			scriptData.current.servers[i] = -1;
			messageWrite(":charte: [b]" + userParameters.servers[i].name + "[/b] Entrées actuellement enregistrées [b]Erreur ([lien=" + this.url + "]Lien[/lien] - " + responseDetails.status + " : " + responseDetails.statusText + ")[b]");
			scriptData.current.serverIndex = i + 1;
			if (scriptData.current.serverIndex >= userParameters.servers.length) dumpdataGetTotalCurrentRecords();
			else dumpdataGetCurrentId();
		}
	});
}

function dumpdataGetTotalCurrentRecords() {
	GM_xmlhttpRequest({
		method: "GET",
		url: "http://www.croquemonster.com/syndicate/dump?;page=99999;",
		headers: {
			'User-agent': 'Mozilla/4.0 (compatible) Greasemonkey/0.3',
			'Accept': 'application/atom+xml,application/xml,text/xml',
		},
		onload: function(responseDetails) {
			if (!responseDetails.responseXML)
				responseDetails.responseXML = new DOMParser().parseFromString(responseDetails.responseText, "text/xml");
			var dumpLogDoc = responseDetails.responseXML.documentElement;
			
			var dumpInfos = $xpath("//xhtml:div[@id='garbage']/xhtml:div[@class='garbageBody']/xhtml:div[@class='dumpInfos']/xhtml:ul/xhtml:li", dumpLogDoc, XPathResult.ANY_TYPE);
			var lis = new Array();
			
			while ((li = dumpInfos.iterateNext()) != null) lis.push(li);
			
			var text = '';
			for (var i = 0; i < lis.length; i++) {
				text = text + lis[i].lastChild.textContent + "\n";
				scriptData.current.infos[i] = lis[i].lastChild.textContent.replace(/^\s*/, '').replace(/\s*m.*/, '').replace(/\./g, '').replace(/,/g, '.');
			}			

			var tbody = $xpath("//xhtml:table[@id='dumpLogs']/xhtml:tbody", dumpLogDoc, XPathResult.ANY_TYPE).iterateNext();
			
			if (!tbody) {
				messageWrite(":dislike: Une erreur s'est produite ([lien=" + this.url + "]Lien[/lien] - dumpLogs non trouvé)");
			}
			
			var rows = tbody.getElementsByTagName('tr').length;
			
			var activePage = $xpath("//xhtml:div[@id='garbage']/xhtml:div[@class='garbageBody']/xhtml:div[@class='paginate']/xhtml:ul/xhtml:li[@class='activepage']", dumpLogDoc, XPathResult.STRING_TYPE).stringValue;
			
			scriptData.current.entries = (parseInt(activePage) - 1) * 15 + parseInt(rows);
			
			if ((typeof(scriptData.current.pageCount) != 'undefined') && (scriptData.current.pageCount == activePage)) {
				scriptData.current.serverIndex = 0;
				dumpdataSend();
			} else {
				scriptData.current.pageCount = activePage;
				for (var i = 0; i < scriptData.current.servers.length; i++) {
					if (scriptData.current.servers[i] >= 0)
						document.getElementById(scriptData.prefix + "DumpdataStatusSpan" + i).innerHTML = scriptData.current.servers[i] + ' / ' + scriptData.current.entries;
				}
				dumpdataRetrieveData();
			}
		},
		onerror: function(responseDetails) {
			messageWrite(":dislike: Une erreur s'est produite ([lien=" + this.url + "]Lien[/lien] - " + responseDetails.status + " : " + responseDetails.statusText + ")");
		}
	});	
}

function dumpdataRetrieveData() {
	var page = dumpdataGetCurrentPage();
		
	if (page <= 0) {
		scriptData.current.running = false;
		scriptData.current.type = '';
		document.getElementById(scriptData.prefix + 'APIUpdateDumpdata').innerHTML = 'Tas d\'ordure à jour';
		destroyProgressBar();
		return;
	}
	
	GM_xmlhttpRequest({
		method: "GET",
		url: "http://www.croquemonster.com/syndicate/dump?page=" + page + ";",
		headers: {
			'User-agent': 'Mozilla/4.0 (compatible) Greasemonkey/0.3',
			'Accept': 'application/atom+xml,application/xml,text/xml',
		},
		onload: function(responseDetails) {
			if (!responseDetails.responseXML)
				responseDetails.responseXML = new DOMParser().parseFromString(responseDetails.responseText, "text/xml");
			var dumpLogDoc = responseDetails.responseXML.documentElement;
			
			var tbody = $xpath("//xhtml:table[@id='dumpLogs']/xhtml:tbody", dumpLogDoc, XPathResult.ANY_TYPE).iterateNext();
			
			if (!tbody) {
				messageWrite(":dislike: Une erreur s'est produite ([lien=" + this.url + "]Lien[/lien] - dumpLogs non trouvé)");
			}
			
			var trs = tbody.getElementsByTagName('tr');
			
			scriptData.current.data = new Array();
			
			scriptData.current.firstId = 0;
			if (parseInt(scriptData.current.pageCount) == parseInt(page)) {
				scriptData.current.firstId = 1;
			} else {
				scriptData.current.firstId = 1 + (parseInt(scriptData.current.pageCount) - parseInt(page) - 1) * 15 + (scriptData.current.entries % 15);
			}
			
			for (var i = 0; i < trs.length; i++) {
				var id = scriptData.current.firstId + i;
				
				var tr = trs[(trs.length - i - 1)];
				var tds = tr.getElementsByTagName('td');
				var text = '';
				
				var user_id = tds[1].textContent;
				
				var d = tds[0].textContent.replace(/^(\n|\r|\s)+/, '');
				var date = (parseInt(d.substring(15,17).replace(/^0/, '')) + 2000) + '-' + d.substring(12,14) + '-' + d.substring(9,11) + ' ' + d.substring(0,2) + ':' + d.substring(3,5) + ':00';
				
				for (var j in tds) {
					var td = tds[j];
					text += td.textContent;
				}
				
				var type = 0;
				var quantity = '';
				var co2 = '';
				var concentration = '';
				
				var regexp12 = new RegExp("ajoute (.*) au tas d'ordures");
				var regexp3 = new RegExp("avale");
				var regexp4 = new RegExp("avale une .* et quitte votre tas");
				var regexp5 = new RegExp("le tas d'ordures, (.*) m.* produits");
				var regexp6 = new RegExp("envoie un monstre distraire GrosMiam \\((.*) concentration");
				var regexp7 = new RegExp("s'attaque .* votre magnifique tas d'ordures !");
				var regexp8 = new RegExp("est distrait .* part");
				var regexp9 = new RegExp("vomit sur votre tas");
				var regexpRes;
				if ((regexpRes = regexp12.exec(tds[2].textContent)) != null) {
					if (regexpRes[1] == "1 Lot d'ordures") type = 2; else type = 1;
				}
				else if (regexp7.exec(tds[2].textContent) != null) type = 7;
				else if (regexp8.exec(tds[2].textContent) != null) type = 8;
				else if ((regexpRes = regexp6.exec(tds[2].textContent)) != null) {
					type = 6;
					concentration = regexpRes[1];
				}
				else if (regexp4.exec(tds[2].textContent) != null) type = 4;
				else if (regexp3.exec(tds[2].textContent) != null) type = 3;
				else if ((regexpRes = regexp5.exec(tds[2].textContent)) != null) {
					type = 5;
					co2 = regexpRes[1];
				}
				else if (regexp9.exec(tds[2].textContent) != null) type = 9;
				
				if ((type <= 5) || (type == 9))
					quantity = parseInt(1000 * parseFloat(tds[3].textContent.replace(/^(\n|\r|\s|\+)+/, '').replace(/(\n|\r|\s|\+)+$/, '').replace(/\./g, '').replace(/,/g, '.')));
				
				
				if (type == 0) {
					messageWrite(':dislike: Erreur, mauvais type... (' + tds[2].textContent + ')');
					return;
				}
				
				scriptData.current.data[i] = id + ',' + user_id + ',' + date + ',' + type + ',' + quantity + ',' + co2 + ',' + concentration;
				
			}
			dumpdataGetTotalCurrentRecords();
		},
		onerror: function(responseDetails) {
			messageWrite(":dislike: Une erreur s'est produite ([lien=" + this.url + "]Lien[/lien] - " + responseDetails.status + " : " + responseDetails.statusText + ")");
		}
	});
}

function dumpdataSend() {	
	var i = scriptData.current.serverIndex;
	
	if (i >= scriptData.current.servers.length) {
		scriptData.current.state = -1;
		for (var j = 0; j < scriptData.current.servers.length; j++) {
			if ((scriptData.current.state == -1) || (scriptData.current.servers[j] < scriptData.current.state)) scriptData.current.state = scriptData.current.servers[j];
		}
		if (scriptData.current.entries > scriptData.current.initialState) {
			progressSet(100 * (parseInt(scriptData.current.state) - parseInt(scriptData.current.initialState)) / (parseInt(scriptData.current.entries) - parseInt(scriptData.current.initialState)));
		} else {
			progressSet(100);
		}
		delete(scriptData.current.data);
		dumpdataRetrieveData();
		return;
	}
	
	if (scriptData.current.servers[i] < scriptData.current.state) {
		if (scriptData.current.servers[i] >= 0) {
			document.getElementById(scriptData.prefix + "DumpdataStatusSpan" + i).innerHTML =  convertBBString("Erreur (Serveur distancé : " + scriptData.current.servers[i] + " / " + scriptData.current.state + ")");
			scriptData.current.servers[i] = -1;
		}
		scriptData.current.serverIndex++;
		dumpdataSend();
		return;
	}
	
	scriptData.current.tempData = new Array();
	for (var j = 0; j < scriptData.current.data.length; j++) {
		if (scriptData.current.firstId + j > scriptData.current.servers[i]) scriptData.current.tempData.push(scriptData.current.data[j]);
	}
	
	if (scriptData.current.tempData.length <= 0) {
		scriptData.current.serverIndex++;
		dumpdataSend();
		return;
	}
	
	var postData = '';
	
	for (var j = 0; j < scriptData.current.infos.length; j++) {
		postData += scriptData.dumpDataInfos[j] + '=' + encodeURI(scriptData.current.infos[j]) + '&';
	}
	
	postData += 'data=' + encodeURI(scriptData.current.tempData.join("\n"));
	
	GM_xmlhttpRequest({
		method: 'POST',
		url: userParameters.servers[scriptData.current.serverIndex].url + 'dumpdata.' + userParameters.servers[scriptData.current.serverIndex].ext + '?id=' + userParameters.agencyId + '&pass=' + userParameters.agencyPass + '&action=add',
		headers: {
			'User-agent': 'Mozilla/4.0 (compatible) Greasemonkey/0.3',
			'Content-type': 'application/x-www-form-urlencoded',
		},
		data: postData,
		onload: function(responseDetails) {
			var i = scriptData.current.serverIndex;
			if (!responseDetails.responseText || responseDetails.responseText == "" || responseDetails.responseText.substr(0, 9) == "<!DOCTYPE" ||
				responseDetails.responseText.substr(0, 5) == "<html") {
				scriptData.current.servers[i] = -1;
				document.getElementById(scriptData.prefix + "DumpdataStatusSpan" + i).innerHTML = convertBBString("Erreur ([lien=" + this.url + "]Lien[/lien])");
				scriptData.current.serverIndex++;
				delete(scriptData.current.tempData);
				dumpdataSend();
				return;
			}
			var response = responseDetails.responseText.split("\n");
			if (response[1] == 'ok') {
				scriptData.current.servers[i] += scriptData.current.tempData.length;
				document.getElementById(scriptData.prefix + "DumpdataStatusSpan" + i).innerHTML = scriptData.current.servers[i] + ' / ' + scriptData.current.entries;
			} else {
				scriptData.current.servers[i] = -1;
				document.getElementById(scriptData.prefix + "DumpdataStatusSpan" + i).innerHTML =  convertBBString("Erreur ([lien=" + this.url + "]Lien[/lien] - " + response[2] + ")");
			}
			delete(scriptData.current.tempData);
			scriptData.current.serverIndex++;
			dumpdataSend();
		},
		onerror: function(responseDetails) {
			var i = scriptData.current.serverIndex;
			scriptData.current.servers[i] = -1;
			document.getElementById(scriptData.prefix + "DumpdataStatusSpan" + i).innerHTML =  convertBBString("Erreur ([lien=" + this.url + "]Lien[/lien] - " + responseDetails.status + " : " + responseDetails.statusText + ")");
			delete(scriptData.current.tempData);
			scriptData.current.serverIndex++;
			dumpdataSend();
		}
	});
}

function dumpdataGetCurrentPage() {
	if (scriptData.current.state == -1) return 0;
	if (scriptData.current.state >= scriptData.current.entries) return 0; else return (1 + parseInt((scriptData.current.entries - (scriptData.current.state + 1)) / 15));
}

/***************
 * API Mbldata *
 ***************/
 
function updateMbldata() {
	if (scriptData.current.running) return;
	scriptData.current.running = true;
	scriptData.current.type = 'MblData';
	document.getElementById(scriptData.prefix + 'APIUpdateMbldata').removeEventListener('click', updateMbldata, false);
	document.getElementById(scriptData.prefix + 'APIUpdateMbldata').innerHTML = 'Mise à jour en cours...';
	displayProgressBar();
	
	scriptData.current.servers = new Array();
	scriptData.current.serverIndex = 0;
	scriptData.current.initialState = -1;
	for (var i = 0; i < userParameters.servers.length; i++) {
		scriptData.current.servers[i] = -1;
	}
	if (scriptData.current.serverIndex >= 0) mblGetCurrentId(); else messageWrite(":dislike: Pas de serveur actuellement configuré");
	
}

function mblGetCurrentId() {
	GM_xmlhttpRequest({
		method: "GET",
		url: userParameters.servers[scriptData.current.serverIndex].url + 'mbldata.' + userParameters.servers[scriptData.current.serverIndex].ext + '?id=' + userParameters.agencyId + '&pass=' + userParameters.agencyPass + '&action=get',
		headers: {
			'User-agent': 'Mozilla/4.0 (compatible) Greasemonkey/0.3',
			'Accept': 'application/atom+xml,application/xml,text/xml',
		},
		onload: function(responseDetails) {
			var i = scriptData.current.serverIndex;
			if (!responseDetails.responseText || responseDetails.responseText == "" || responseDetails.responseText.substr(0, 9) == "<!DOCTYPE" ||
				responseDetails.responseText.substr(0, 5) == "<html") {
				messageWrite(":dislike: Une erreur s'est produite (mauvaise réponse du [lien=" + this.url + "]script[/lien])...");
				scriptData.current.servers[i] = -1;
				scriptData.current.serverIndex = i + 1;
				if (scriptData.current.serverIndex >= userParameters.servers.length) mbldataGetTotalCurrentRecords();
				else mblGetCurrentId();
				return;
			}
			var response = responseDetails.responseText.split("\n");
			if (response[1] == 'ok') {
				scriptData.current.servers[i] = parseInt(response[2]);
				if ((typeof(scriptData.current.servers[i]) != 'number') || (!(scriptData.current.servers[i] >= 0))) {
					scriptData.current.servers[i] = -1;
					messageWrite(":charte: [b]" + userParameters.servers[i].name + "[/b] Entrées actuellement enregistrées [b]Erreur (" + response[2] + ")[/b]");
				} else {
					if ((scriptData.current.initialState == -1) || (scriptData.current.initialState > scriptData.current.servers[i])) scriptData.current.initialState = scriptData.current.servers[i];
					scriptData.current.state = scriptData.current.initialState;
					messageWrite(":charte: [b]" + userParameters.servers[i].name + "[/b] Entrées actuellement enregistrées [b][span-" + scriptData.prefix + "MbldataStatusSpan" + i + "]" + scriptData.current.servers[i] + "[/span][/b]");
				}
			} else {
				scriptData.current.servers[i] = -1;
				messageWrite(":charte: [b]" + userParameters.servers[i].name + "[/b] Entrées actuellement enregistrées [b]Erreur (" + response[1] + ")[/b]");
			}
			scriptData.current.serverIndex = i + 1;
			if (scriptData.current.serverIndex >= userParameters.servers.length) mbldataGetTotalCurrentRecords();
			else mblGetCurrentId();
		},
		onerror: function(responseDetails) {
			var i = scriptData.current.serverIndex;
			scriptData.current.servers[i] = -1;
			messageWrite(":charte: [b]" + userParameters.servers[i].name + "[/b] Entrées actuellement enregistrées [b]Erreur ([lien=" + this.url + "]Lien[/lien] - " + responseDetails.status + " : " + responseDetails.statusText + ")[b]");
			scriptData.current.serverIndex = i + 1;
			if (scriptData.current.serverIndex >= userParameters.servers.length) mbldataGetTotalCurrentRecords();
			else mblGetCurrentId();
		}
	});
}

function mbldataGetTotalCurrentRecords() {
	GM_xmlhttpRequest({
		method: "GET",
		url: "http://www.croquemonster.com/mbl/team/" + scriptData.syndicateId + "/?;page=99999;",
		headers: {
			'User-agent': 'Mozilla/4.0 (compatible) Greasemonkey/0.3',
			'Accept': 'application/atom+xml,application/xml,text/xml',
		},
		onload: function(responseDetails) {
			if (!responseDetails.responseXML)
				responseDetails.responseXML = new DOMParser().parseFromString(responseDetails.responseText, "text/xml");
			
			var mblLogDoc = responseDetails.responseXML.documentElement;
			
			var tbody = $xpath("//xhtml:div[@id='content']/xhtml:table/xhtml:tbody", mblLogDoc, XPathResult.ANY_TYPE).iterateNext();
			
			if (!tbody) {
				messageWrite(":dislike: Une erreur s'est produite ([lien=" + this.url + "]Lien[/lien] - content table non trouvé)");
			}
			
			var rows = tbody.getElementsByTagName('tr').length;
			
			var activePage = $xpath("//xhtml:div[@id='content']/xhtml:div[@class='paginate']/xhtml:ul/xhtml:li[@class='activepage']", mblLogDoc, XPathResult.STRING_TYPE).stringValue;
			
			scriptData.current.entries = (parseInt(activePage) - 1) * 15 + parseInt(rows);
			
			if ((typeof(scriptData.current.pageCount) != 'undefined') && (scriptData.current.pageCount == activePage)) {
				scriptData.current.serverIndex = 0;
				mbldataSend();
			} else {
				scriptData.current.pageCount = activePage;
				for (var i = 0; i < scriptData.current.servers.length; i++) {
					if (scriptData.current.servers[i] >= 0)
						document.getElementById(scriptData.prefix + "MbldataStatusSpan" + i).innerHTML = scriptData.current.servers[i] + ' / ' + scriptData.current.entries;
				}
				mbldataRetrieveData();
			}
		},
		onerror: function(responseDetails) {
			messageWrite(":dislike: Une erreur s'est produite ([lien=" + this.url + "]Lien[/lien] - " + responseDetails.status + " : " + responseDetails.statusText + ")");
		}
	});	
}

function mbldataRetrieveData() {
	var page = mbldataGetCurrentPage();
	
	if (page <= 0) {
		scriptData.current.running = false;
		scriptData.current.type = '';
		document.getElementById(scriptData.prefix + 'APIUpdateMbldata').innerHTML = 'Données Mbl à jour';
		destroyProgressBar();
		return;
	}
	
	GM_xmlhttpRequest({
		method: "GET",
		url: "http://www.croquemonster.com/mbl/team/" + scriptData.syndicateId + "/?;page=" + page + ";",
		headers: {
			'User-agent': 'Mozilla/4.0 (compatible) Greasemonkey/0.3',
			'Accept': 'application/atom+xml,application/xml,text/xml',
		},
		onload: function(responseDetails) {
			if (!responseDetails.responseXML)
				responseDetails.responseXML = new DOMParser().parseFromString(responseDetails.responseText, "text/xml");
			var dumpLogDoc = responseDetails.responseXML.documentElement;
			
			var tbody = $xpath("//xhtml:div[@id='content']/xhtml:table/xhtml:tbody", dumpLogDoc, XPathResult.ANY_TYPE).iterateNext();
			
			if (!tbody) {
				messageWrite(":dislike: Une erreur s'est produite ([lien=" + this.url + "]Lien[/lien] - content table non trouvé)");
			}
			
			var trs = tbody.getElementsByTagName('tr');
			
			scriptData.current.mids = new Array();
			
			scriptData.current.firstId = 0;
			if (parseInt(scriptData.current.pageCount) == parseInt(page)) {
				scriptData.current.firstId = 1;
			} else {
				scriptData.current.firstId = 1 + (parseInt(scriptData.current.pageCount) - parseInt(page) - 1) * 15 + (scriptData.current.entries % 15);
			}
			
			for (var i = 0; i < trs.length; i++) {
				var id = scriptData.current.firstId + i;
				
				var tr = trs[(trs.length - i - 1)];
				var tds = tr.getElementsByTagName('td');
				var text = '';
				
				var mid = tds[tds.length - 1].getElementsByTagName('a')[0].href.replace(/^.*\/([0-9]+)$/, "$1");
				
				scriptData.current.mids[id] = mid;
			}
			mbldataGetTotalCurrentRecords();
		},
		onerror: function(responseDetails) {
			messageWrite(":dislike: Une erreur s'est produite ([lien=" + this.url + "]Lien[/lien] - " + responseDetails.status + " : " + responseDetails.statusText + ")");
		}
	});
}

function mbldataSend() {
	var i = scriptData.current.serverIndex;
	
	if (i >= scriptData.current.servers.length) {
		scriptData.current.state = -1;
		for (var j = 0; j < scriptData.current.servers.length; j++) {
			if ((scriptData.current.state == -1) || (scriptData.current.servers[j] < scriptData.current.state)) scriptData.current.state = scriptData.current.servers[j];
		}
		if (scriptData.current.entries > scriptData.current.initialState) {
			progressSet(100 * (parseInt(scriptData.current.state) - parseInt(scriptData.current.initialState)) / (parseInt(scriptData.current.entries) - parseInt(scriptData.current.initialState)));
		} else {
			progressSet(100);
		}
		delete(scriptData.current.data);
		mbldataRetrieveMatchData();
		return;
	}
	
	if (scriptData.current.servers[i] < scriptData.current.state) {
		if (scriptData.current.servers[i] >= 0) {
			document.getElementById(scriptData.prefix + "MbldataStatusSpan" + i).innerHTML =  "Erreur (Serveur distancé : " + scriptData.current.servers[i] + " / " + scriptData.current.state + ")";
			scriptData.current.servers[i] = -1;
		}
		scriptData.current.serverIndex++;
		mbldataSend();
		return;
	}
	
	if (scriptData.current.servers[i] > scriptData.current.state) {
		scriptData.current.serverIndex++;
		mbldataSend();
		return;
	}
	
	var postData = 'id=' + (scriptData.current.state + 1) + '&mid=' + scriptData.current.mids[scriptData.current.state + 1] + '&data=' + encodeURI(scriptData.current.data);
	
	GM_xmlhttpRequest({
		method: 'POST',
		url: userParameters.servers[scriptData.current.serverIndex].url + 'mbldata.' + userParameters.servers[scriptData.current.serverIndex].ext + '?id=' + userParameters.agencyId + '&pass=' + userParameters.agencyPass + '&action=add',
		headers: {
			'User-agent': 'Mozilla/4.0 (compatible) Greasemonkey/0.3',
			'Content-type': 'application/x-www-form-urlencoded',
		},
		data: postData,
		onload: function(responseDetails) {
			var i = scriptData.current.serverIndex;
			if (!responseDetails.responseText || responseDetails.responseText == "" || responseDetails.responseText.substr(0, 9) == "<!DOCTYPE" ||
				responseDetails.responseText.substr(0, 5) == "<html") {
				scriptData.current.servers[i] = -1;
				document.getElementById(scriptData.prefix + "MbldataStatusSpan" + i).innerHTML =  convertBBString("Erreur ([lien=" + this.url + "]Lien[/lien])");
				scriptData.current.serverIndex++;
				delete(scriptData.current.tempData);
				mbldataSend();
				return;
			}
			var response = responseDetails.responseText.split("\n");
			if (response[1] == 'ok') {
				scriptData.current.servers[i]++;
				document.getElementById(scriptData.prefix + "MbldataStatusSpan" + i).innerHTML = scriptData.current.servers[i] + ' / ' + scriptData.current.entries;
			} else {
				scriptData.current.servers[i] = -1;
				document.getElementById(scriptData.prefix + "MbldataStatusSpan" + i).innerHTML =  convertBBString("Erreur ([lien=" + this.url + "]Lien[/lien] - " + response[2] + ")");
			}
			delete(scriptData.current.tempData);
			scriptData.current.serverIndex++;
			mbldataSend();
		},
		onerror: function(responseDetails) {
			var i = scriptData.current.serverIndex;
			scriptData.current.servers[i] = -1;
			document.getElementById(scriptData.prefix + "MbldataStatusSpan" + i).innerHTML =  convertBBString("Erreur ([lien=" + this.url + "]Lien[/lien] - " + responseDetails.status + " : " + responseDetails.statusText + ")");
			delete(scriptData.current.tempData);
			scriptData.current.serverIndex++;
			mbldataSend();
		}
	});
}

function mbldataGetCurrentPage() {
	if (scriptData.current.state >= scriptData.current.entries) return 0; else return (1 + parseInt((scriptData.current.entries - (scriptData.current.state + 1)) / 15));
}

/*************
 * Interface *
 *************/
function showServersList() {
	messageWrite(':charte: Liste des serveurs :');
	for (var i = 0; i < userParameters.servers.length; i++) {
		messageWrite(':fleche: ' + 
			'[linput-' + scriptData.prefix + 'Server' + i + 'Name=' + userParameters.servers[i].name + '/] ' +
			'[input-' + scriptData.prefix + 'Server' + i + 'Url=' + userParameters.servers[i].url + '/] ' + 
			'[tinput-' + scriptData.prefix + 'Server' + i + 'Ext=' + userParameters.servers[i].ext + '/] ' + 
			'[button-' + scriptData.prefix + 'ServersListRemoveServer' + i + '=Supprimer/]');
		document.getElementById(scriptData.prefix + 'ServersListRemoveServer' + i).addEventListener('click', serversListRemoveServer, false);
	}
	messageWrite(':fleche: [button-' + scriptData.prefix + 'UpdateServersList=Valider/] [button-' + scriptData.prefix + 'ServersListAddServer=Ajouter un serveur/]');
	document.getElementById(scriptData.prefix + 'ServersListAddServer').addEventListener('click', serversListAddServer, false);
	document.getElementById(scriptData.prefix + 'UpdateServersList').addEventListener('click', updateServersList, false);
}

function serversListAddServer() {
	var server = new Object();
	var i = userParameters.servers.length;
	server.name = 'Serveur ' + (i + 1);
	server.url = '';
	server.ext = 'php';
	userParameters.servers.push(server);
	messageWrite(':fleche: ' + 
		'[linput-' + scriptData.prefix + 'Server' + i + 'Name=' + userParameters.servers[i].name + '/] ' +
		'[input-' + scriptData.prefix + 'Server' + i + 'Url=' + userParameters.servers[i].url + '/] ' + 
		'[tinput-' + scriptData.prefix + 'Server' + i + 'Ext=' + userParameters.servers[i].ext + '/] ' + 
		'[button-' + scriptData.prefix + 'ServersListAddServer' + i + '=Ok/]');
	document.getElementById(scriptData.prefix + 'ServersListAddServer' + i).addEventListener('click', updateServersList, false);
}

function serversListRemoveServer() {
	var prefix = scriptData.prefix + 'ServersListRemoveServer';
	var i = parseInt(this.id.substr(prefix.length));
	userParameters.servers.splice(i, 1);
	var parent = this.parentNode.parentNode;
	parent.parentNode.removeChild(parent);
	setUserParameters();
}

function updateServersList() {
	for (var i = 0; i < userParameters.servers.length; i++) {
		userParameters.servers[i].name = document.getElementById(scriptData.prefix + 'Server' + i + 'Name').value;
		userParameters.servers[i].url = document.getElementById(scriptData.prefix + 'Server' + i + 'Url').value;
		userParameters.servers[i].ext = document.getElementById(scriptData.prefix + 'Server' + i + 'Ext').value;
	}
	setUserParameters();
}

/********
 * Init *
 ********/
function init() {
	if (typeof(userParameters.agencyId) == 'undefined') if (!getAgencyId()) return;
		//	alert(typeof(userParameters.agencyName) + "\n" + typeof(scriptData.syndicateId) + "\n" + typeof(scriptData.syndicateName));
	if ((typeof(userParameters.agencyName) == 'undefined') || (typeof(scriptData.syndicateId) == 'undefined')) if (!getAgencyName()) return;
	if (typeof(userParameters.agencyPass) == 'undefined') if (!getAgencyPass()) return;
	
	messageWrite(":charte: Nom de l'agence : [b]" + userParameters.agencyName + "[/b].");
	messageWrite(":charte: Syndicat : [b][lien=http://www.croquemonster.com/syndicate/view?id=" + scriptData.syndicateId + ";]" + scriptData.syndicateName + "[/lien][/b].");
	messageWrite(":charte: Clé d'API définie [button-" + scriptData.prefix + "AgencyPassResetButton=Effacer la clé/]");
	document.getElementById(scriptData.prefix + 'AgencyPassResetButton').addEventListener('click', resetAgencyPass, false);
	setUserParameters();
	showServersList();
	messageWrite(":fleche: [button-" + scriptData.prefix + "APIUpdateDumpdata=Mettre à jour le tas d'ordure/]");
	document.getElementById(scriptData.prefix + 'APIUpdateDumpdata').addEventListener('click', updateDumpdata, false);
	
//	messageWrite(":fleche: [button-" + scriptData.prefix + "APIUpdateMbldata=Mettre à jour les données Mbl/]");
//	document.getElementById(scriptData.prefix + 'APIUpdateMbldata').addEventListener('click', updateMbldata, false);
	
	if (!scriptData.initiated) {
		scriptData.initiated = true;
	}
}

init();
 