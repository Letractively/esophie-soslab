function setaction(value) {
        var formobj = document.getElementById('frmmain');
	formobj.pageaction.value = value;
	formobj.submit();
}

function seturl(value) {
        var formobj = document.getElementById('frmmain');
	formobj.action = value;
        formobj.method = 'get';
	formobj.submit();
}

function checkfailed(msg)
{
	alert(msg);
	return false;
}

function isvaliddate(objname)
{
	var ret = false;
	
	var obj = document.getElementById(objname);
	
	if (obj)
	{	
		if (obj.value != '')
		{
			dmy = obj.value.split("/"); 
			ret = checkdate(dmy[0],dmy[1],dmy[2]);
		} else ret = true;
	}
	return ret;
}

function checkdate(d,m,y) {
	var ret = true;
	var leap = (!(y % 4) && (y % 100) || !(y % 400));
	
	if (d < 1) ret = false;
	if (ret && m < 1 && m > 12) ret = false;
	if (ret && d > 31 && (m == 1 || m == 3 || m == 5 || m == 7 || m == 8 || m == 10 || m == 12)) ret = false;
	if (ret && d > 30 && (m == 4 || m == 6 || m == 9 || m == 11)) ret = false;
	if (ret && m == 2 && ((d > 29 && leap) || (d > 28 && !leap))) ret = false;
	
	return ret;
}

function gotopage(filename,param)
{
	var formobj = document.getElementById('frmexec');
        formobj.action = filename;
	arr1 = param.split(';');
	for (var i=0; i < arr1.length; i++) 
	{
		arr2 = arr1[i].split('=');
		oHid = document.createElement('input');
		oHid.type="hidden";
		oHid.name = arr2[0];
		oHid.id = arr2[0];
		oHid.value = arr2[1];
		formobj.appendChild(oHid);
	}	
	formobj.submit();
}

function showPopUp(el) {
	var cvr = document.getElementById("cover")
	var dlg = document.getElementById(el)
	cvr.style.display = "block"
	dlg.style.display = "block"
	if (document.body.style.overflow = "hidden") {
		cvr.style.width = "100%"
		cvr.style.height = "100%"
	}
}
function closePopUp(el) {
	var cvr = document.getElementById("cover")
	var dlg = document.getElementById(el)
	cvr.style.display = "none"
	dlg.style.display = "none"
	document.body.style.overflowY = "scroll"
}

function AJAX(obj) {
	this.obj 	= obj.xmlHttp?obj.xmlHttp:obj; 
	this.status = "loading";
	this.respon = "";
	this.errno  = 0;
	if( this.obj.readyState == 4) {
		if (this.obj.status == 200 ) {
			this.status  = "complete";
			this.respon = this.obj.responseText;
		} else {
			this.errno	= this.obj.status;
			this.status = "error";
		}
	}
}

function AJAXRequest(respon,request) {
	var xmlHttp;
	try {    // Firefox, Opera 8.0+, Safari    
		xmlHttp=new XMLHttpRequest();    
	} catch (e) {    // Internet Explorer    
		try {      
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");      
		} catch (e) {      
			try {        
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");        
			} catch (e) {        
				alert("Your browser does not support AJAX!");        
				return false;        
			}      
		}    
	}  
	this.xmlHttp = xmlHttp;
	this.xmlHttp.onreadystatechange= respon;
    this.xmlHttp.open("GET",request,true);
	this.xmlHttp.setRequestHeader("PRAGMA", "NO-CACHE");     
	this.xmlHttp.setRequestHeader("CACHE-CONTROL", "NO-CACHE");     
	this.xmlHttp.setRequestHeader("REFRESH", "0;URL="+request);     
	this.xmlHttp.setRequestHeader("EXPIRES", "0");     
    this.xmlHttp.send(null);		
}
