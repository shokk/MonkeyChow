var agt = navigator.userAgent.toLowerCase();
var is_ie = (agt.indexOf('msie') != -1);
var is_ie5 = (agt.indexOf('msie 5') != -1);


/**
* send a GET behind the scenes to url
*
*/
function SendRequest(url) {
	var xmlhttp = CreateXmlHttpReq(DummyHandler);
	++uniqnum_counter;
	XmlHttpGET(xmlhttp, url + "&rand=" + uniqnum_counter);
}

function CreateXmlHttpReq(handler) {
	var xmlhttp = null;
	if (is_ie) {
		var control = (is_ie5) ? "Microsoft.XMLHTTP" : "Msxml2.XMLHTTP";
		try {
			xmlhttp = new ActiveXObject(control);
			xmlhttp.onreadystatechange = handler;
		} catch(e) {
			alert("You need to enable active scripting and activeX controls");
		}
	} else {
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onload = handler;
		xmlhttp.onerror = handler;
	}
	return xmlhttp;
}

var uniqnum_counter = (new Date).getTime();

function DummyHandler() { }

function twitterit(title,url,username,password) {
	var tinyurl = SendTINYURLRequest(url);
	SendTwitterRequest(title,tinyurl);
}

function SendTINYURLRequest(url) {
	var xmlhttp = CreateXmlHttpReq(DummyHandler);
	if (!xmlhttp) {
		alert('Error creating XMLHttpRequest()');
		return false;
	}
	var tinyurl = TINYURLHttpGET(xmlhttp, 'createtinyurl.php?url=' + url);
	return tinyurl;
}

function SendTwitterRequest(title,url) {
	var xmlhttp = CreateXmlHttpReq(DummyHandler);
	if (!xmlhttp) {
		alert('Error creating XMLHttpRequest()');
		return false;
	}
	var tweet = TwitterHttpGET(xmlhttp, 'createtweet.php?title=' + title + '&url=' + url);
	return tweet;
}

function TwitterHttpGET(xmlhttp, url) {
	xmlhttp.open('GET', url, false);
	xmlhttp.send(null);
	tinyurl = xmlhttp.responseText;
	if (xmlhttp.status == 200) { return tinyurl; }
		else return xmlhttp.status;
}

function TINYURLHttpGET(xmlhttp, url) {
	xmlhttp.open('GET', url, false);
	xmlhttp.send(null);
	tinyurl = xmlhttp.responseText;
	if (xmlhttp.status == 200) { return tinyurl; }
		else return xmlhttp.status;
}

function XmlHttpGET(xmlhttp, url) {
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function pressit(wpsite,title,url) {
		var d=document;
		var w=window;
		var g='http://' + wpsite + '/wp-admin/press-this.php?u='+url+'&t='+title+'&v=2';
		if(!w.open(g,'t','toolbar=0,resizable=0,scrollbars=1,status=1,width=700,height=500')){d.location.href=g;}
		setTimeout(a,0);
		void(0);
}
