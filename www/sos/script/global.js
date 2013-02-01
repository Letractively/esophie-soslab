function setaction(value) {
	frmmain.pageaction.value = value;
	frmmain.submit();
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