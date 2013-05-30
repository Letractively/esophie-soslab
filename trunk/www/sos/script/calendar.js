
//Setting
CalendarHolidayId				= "CalendarHoliday"; //followed by year. value format = "mmdd,mmdd,mmdd"
CalendarId						= "MyCalendar";
CalendarFirstDay				= 1; 				 //0=Sunday; 1=Monday
CalendarWithHoliday				= true;
CalendarOutOfRangeCanSelected	= true;
CalendarYearMin					= 1900;
CalendarYearMax					= 2099; 

//Constanta
CalendarDayList		= new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
CalendarMonthList   = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
CalendarIsIE 		= document.all ? true : false;

//Calendar Variable 
CalendarDate		= new Date();
CalendarY1			= 0;
CalendarX1			= 0;
CalendarX2			= 0;
CalendarY2			= 0;
CalendarMode		= 1;
CalendarObject		= null;
CalendarOutputDate 	= null;
CalendarOutputTime 	= null;
CalendarMouseIN		= false;
CalendarButton		= null;
CalendarLoadHoliday = false;

// Lost focus action - Start -----------------------------------------------------------------------------
function CalendarDateLostFocus(TxtDate,TxtTime)
{	
	var Today 			= new Date();
	var dateStr 		= new String();
	var	ret 			= false;
	var d = 0;
	var m = 0;
	var y = 0;
	
	pattern = new Array(5);
	pattern[0]= new Array(4);
	//pattern[0][0] = /^(\d{1,2})(\ )(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)(\ )(\d{4})$/;
	pattern[0][0] = /^(\d{1,2})(\ )([A-Za-z0-9]{1,9})(\ )(\d{2,4})$/;
	pattern[0][1] = 1; pattern[0][2] = 3; pattern[0][3] = 5;
	
	pattern[1]= new Array(4);
	//pattern[1][0] = /^(\d{1,2})(\ )(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)(\ )(\d{2})$/;
	pattern[1][0] = /^([A-Za-z]{1,9})(\ )(\d{1,2})(\ )(\d{2,4})$/;
	pattern[1][1] = 3; pattern[1][2] = 1; pattern[1][3] = 5; 
	
	pattern[2]= new Array(4);
	pattern[2][0] = /^(\d{1,2})$/;
	pattern[2][1] = 1; pattern[2][2] = 0; pattern[2][3] = 0;
	
	pattern[3]= new Array(4);
	pattern[3][0] = /^(\d{1,2})(\d{2})$/;
	pattern[3][1] = 2; pattern[3][2] = 1; pattern[3][3] = 0; 
	
	pattern[4]= new Array(4);
	pattern[4][0] = /^(\d{2,4})(\d{2})(\d{2})$/;
	pattern[4][1] = 3; pattern[4][2] = 2; pattern[4][3] = 1;
		
	CalendarOutputDate = document.getElementById(TxtDate);
	
	dateStr = CalendarOutputDate.value;
	dateStr = dateStr.toLowerCase();
	dateStr = dateStr.replace(/\\/g,' ');
	dateStr = dateStr.replace(/\//g,' ');
	dateStr = dateStr.replace(/-/g,' ');
	dateStr = dateStr.replace(/,/g,' ');
	while(dateStr.indexOf("  ") > -1 ) dateStr = dateStr.replace(/\s\s/g,' ');
	dateStr = dateStr.replace(/^\ +/,'').replace(/\ +$/,'');
	
	if (dateStr == '') return true;
	
	if (dateStr == 'n' || dateStr == 't')
	{		
		CalendarOutputDate.value = CalendarDateValue(Today);
		return true;
	}	

	for(i=0;i<pattern.length;i++)
	{
		matchArray = dateStr.match(pattern[i][0]);
		if (matchArray)
		{
			
			d = matchArray[pattern[i][1]];
			if (pattern[i][2])
				m = matchArray[pattern[i][2]];
			else
				m = Today.getMonth()+1;
			
			if (pattern[i][3])	
			{
				y = matchArray[pattern[i][3]];
				if (y.length == 2)
					y = Today.getFullYear().toString().substring(0,2) + y;
			}
			else
				y = Today.getFullYear();
			
			switch(m) 
			{
				case 'jan' : 
				case 'january' : 
				case 'januari' : 
					m = 1; break;
				case 'feb' : 
				case 'february' : 
				case 'februari' : 
					m = 2; break;
				case 'mar' :
				case 'march' : 
				case 'maret' : 
					m = 3; break;
				case 'apr' : 
				case 'april' : 
					m = 4; break;
				case 'may' : 
				case 'mei' : 
					m = 5; break;
				case 'jun' : 
				case 'june' : 
				case 'juni' : 
					m = 6; break;
				case 'jul' : 
				case 'july' : 
				case 'juli' : 
					m = 7; break;
				case 'aug' : 
				case 'august' :
				case 'agustus' : 					
					m = 8; break;
				case 'sep' : 
				case 'september' : 
					m = 9; break;
				case 'oct' : 
				case 'october' : 
				case 'oktober' : 
					m = 10; break;
				case 'nov' : 
				case 'november' : 
					m = 11; break;
				case 'dec' : 
				case 'december' :
				case 'desember' : 					
					m = 12; break;
			}	
			
			//alert(d + ' ' + m + ' ' + y);
			if (!isNaN(d) && !isNaN(m) && !isNaN(y))			
			{				
				if (CalendarIsDate(d,m,y))
				{
					ret = true;					
					oDate = new Date(y,m,d,Today.getHours(),Today.getMinutes(),Today.getSeconds());
					CalendarOutputDate.value = CalendarDateValue(oDate);
					break;					
				}
			}			
		} //else alert("ga cocok:" + dateStr + "<br>" + pattern[i][0]);
	}
	
	if (!ret) {
		alert("Invalid date, fill in with format \"dd/mm/yyyy\"\n(Year of date range between " + CalendarYearMin + " and " + CalendarYearMax + ")");
		CalendarOutputDate.focus();
		CalendarOutputDate.select();
	}
	return ret;
}

function CalendarTimeLostFocus(TxtTime,TxtDate)
{	
	var Today 			= new Date();
	var timeStr 		= new String();
	var	ret 			= false;
	var h = 0;
	var m = 0;
	var s = 0;
	
	pattern = new Array(3);
	pattern[0]= new Array(4);
	pattern[0][0] = /^(\d{1,2})$/;
	pattern[0][1] = 1; pattern[0][2] = 0; pattern[0][3] = 0;
	
	pattern[1]= new Array(4);
	pattern[1][0] = /^(\d{1,2})(\d{2})$/;
	pattern[1][1] = 1; pattern[1][2] = 2; pattern[1][3] = 0; 
	
	pattern[2]= new Array(4);
	pattern[2][0] = /^(\d{2,4})(\d{2})(\d{2})$/;
	pattern[2][1] = 1; pattern[2][2] = 2; pattern[2][3] = 3;
		
	CalendarOutputTime = document.getElementById(TxtTime);
	
	timeStr = CalendarOutputTime.value;
	timeStr = timeStr.replace(/\s/g,' ');

	if (timeStr == '') return true;
	
	if (timeStr == 'n' || timeStr == 't')
	{		
		CalendarOutputTime.value = CalendarTimeValue(Today);
		return true;
	}	

	for(i=0;i<pattern.length;i++)
	{
		matchArray = timeStr.match(pattern[i][0]);
		if (matchArray)
		{			
			h = matchArray[pattern[i][1]];
			if (pattern[i][2])
				m = matchArray[pattern[i][2]];
			else
				m = Today.getMinutes();
			
			if (pattern[i][3])	
				s = matchArray[pattern[i][3]];
			else
				s = Today.getSeconds();
						
			//alert(h + ' ' + m + ' ' + s);
			if (!isNaN(h) && !isNaN(m) && !isNaN(s))			
			{								
				if (CalendarIsTime(h,m,s))
				{
					ret = true;					
					oDate = new Date(Today.getFullYear(),Today.getMonth(),Today.getDate(),h,m,s);
					CalendarOutputTime.value = CalendarTimeValue(oDate);
					break;					
				}
			}			
		} //else alert("ga cocok:" + timeStr + "<br>" + pattern[i][0]);
	}
	
	if (!ret) {
		alert("Invalid time, Please fill in with format \"HH:MM:SS\" or \"HHMMSS\"");
		CalendarOutputTime.focus();
		CalendarOutputTime.select();
	}
	return ret;
}
// Lost focus action - End  ------------------------------------------------------------------------------

// Supporting Functions - Start ----------------------------------------------------------------------------
//function CalendarDateValue(obj) { return TwoDigit(obj.getDate()) + " " + CalendarMonthName(obj.getMonth(),true) + " " + obj.getFullYear(); }
function CalendarDateValue(obj) { return TwoDigit(obj.getDate()) + "/" + TwoDigit(obj.getMonth()+1) + "/" + obj.getFullYear(); }
function CalendarTimeValue(obj) { return TwoDigit(obj.getHours()) + ":" + TwoDigit(obj.getMinutes()) + ":" + TwoDigit(obj.getSeconds()); }

function TwoDigit(value)
{
	var str = new String();
	str = "0" + value;
	return str.substr(str.length-2,2);
}

function CalendarMonthName(mth,abbre)
{
	var str = new String();
	str = CalendarMonthList[mth];
	return abbre ? str.substr(0,3): str;
}

function CalendarIsDate(d,m,y) {
	var ret = true;
	var leap = (!(y % 4) && (y % 100) || !(y % 400));
	
	if (d < 1) ret = false;
	if (ret && m < 1 && m > 12) ret = false;
	if (ret && d > 31 && (m == 1 || m == 3 || m == 5 || m == 7 || m == 8 || m == 10 || m == 12)) ret = false;
	if (ret && d > 30 && (m == 4 || m == 6 || m == 9 || m == 11)) ret = false;
	if (ret && m == 2 && ((d > 29 && leap) || (d > 28 && !leap))) ret = false;
	
	return ret;
}

function CalendarIsTime(h,m,s) {
	var timePattern = /^(([0-1][0-9])|([2][0-3])):([0-5][0-9]):([0-5][0-9])?$/;	
	var timeStr = new String();
	
	timeStr = TwoDigit(h) + ':' + TwoDigit(m) + ':' + TwoDigit(s);
	var matchArray = timeStr.match(timePattern); 
	
	if (matchArray == null) return false;
	return true;
}
// Supporting Functions - End ------------------------------------------------------------------------------

// Calendar Holiday - Start --------------------------------------------------------------------------------
function CalendarUpdateHolidayList(year)
{
	var obj1 = document.getElementById(CalendarHolidayId);
	var obj2 = null;

	if (obj1)
	{
		for(idx=0;idx<obj1.children.length;idx++)
		{
			if(obj1.children[idx].id == CalendarHolidayId + year)
			{
				obj2 = obj1.children[idx];
				break;
			}
		}

		if(!obj2)
		{
			AJAXRequest(GetListHoliday,
				"index.php?exectype=ajax&exec=hdesk.general.calendar&calendaryear="+year+"&val="+Math.random());	
		}
	}

	return false;
}

function GetListHoliday()
{
	var idx;
	var obj1 = document.getElementById(CalendarHolidayId);
	var obj2 = null;
			
	ajx = new AJAX(this);
	this.ajx = ajx;
	switch(ajx.status) {
		case "complete" :
			mystr = new String;
			mystr = ajx.respon;
			if (mystr!= "") {
				data = mystr.split('\n');
				if (obj1 && data.length == 2)
				{
					obj2 = document.createElement("input");
					obj2.setAttribute('id',CalendarHolidayId + data[0]);
					obj2.setAttribute('type','hidden');
					obj1.appendChild(obj2);
					obj2.value = data[1];
				}
				
				if(CalendarDate.getFullYear() == data[0])
				{
					if(CalendarMode == 1)
					{
						CalendarBuild();						
					}
					CalendarLoadHoliday = false;
				}
				else
				{
					CalendarUpdateHolidayList(CalendarDate.getFullYear());
				}
				
			}
			break;
		case "error" :
			CalendarLoadHoliday = false;
			break;
	}
}
// Calendar Holiday - End ----------------------------------------------------------------------------------


// Calendar UI functions - Start ---------------------------------------------------------------------------
function CalendarShow(TxtDate,TxtTime,TxtHdn)
{
	var oElement = document.getElementById(TxtDate);
	if (!CalendarObject) CalendarObject = document.getElementById(CalendarId);

	document.onmousemove = CalendarCekMouseMove;
	document.onmousedown = CalendarCekMouseDown;
	if(!CalendarIsIE) document.captureEvents(Event.MOUSEMOVE);
	
	CalendarMouseIN		= false;
	
	if (oElement && CalendarObject)
	{
		CalendarY1    = oElement.offsetHeight;
		CalendarX1   = 0;
		
		//Find Position
		if( typeof( oElement.offsetParent ) != 'undefined' ) 
		{	
			for( var posX = 0, posY = 0; oElement; oElement = oElement.offsetParent ) {			
				CalendarX1 += oElement.offsetLeft;
				CalendarY1  += oElement.offsetTop;
			}	
		} 
		else
		{
			CalendarY1  = oElement.offsetTop;
			CalendarX1 = oElement.offsetLeft;
		}
	
		if (CalendarObject.style.display == "none" || CalendarObject.style.display == "")
		{
			CalendarOutputDate 		= document.getElementById(TxtDate);	
			CalendarOutputTime 		= document.getElementById(TxtTime);	
			CalendarMode = 1;
			CalendarBuild();
			CalendarObject.style.position	= "absolute";
			CalendarObject.style.top		= CalendarY1 +'px';
			CalendarObject.style.left		= CalendarX1 +'px';		
			CalendarObject.className		= "calendar";
			CalendarObject.style.display 	= "block";	
		}
	}
}

function CalendarIsHoliday(dateObj)
{
	var ret = false;
	var strHoliday	= new String();	
	var ObjHoliday = document.getElementById(CalendarHolidayId + dateObj.getFullYear());
	
	if (ObjHoliday) 
	{
		strHoliday = ObjHoliday.value;
		if (strHoliday.indexOf(TwoDigit(dateObj.getMonth()+1)+TwoDigit(dateObj.getDate())) > -1)
			ret = true;
	}
	
	return ret;
}

function CalendarBuild()
{
	var Today 			= new Date();
	var curDate			= new Date();
	var str				= new String();
	var strTemp			= new String();	
	var strSelectEvents;
	var dateIdx			= 1;
	var fontstylecustom = "style=\"cursor:pointercolor: blue;\"";
	
	if (CalendarObject)
	{				
		CalendarObject.innerHTML = "";

		str = "<table width=210 height=200 border=0 cellpadding=0 cellspacing=2 class=calendar>";
		
		switch(CalendarMode){
			case 1 :
				if (CalendarWithHoliday) { 
					if(!CalendarLoadHoliday) {
						CalendarLoadHoliday = true;
						CalendarUpdateHolidayList(CalendarDate.getFullYear());
					}
				}
				str += "<tr height=20><td colspan=7><table width=100%><tr>";
			/*	str += "<td align=left><a href=# onclick=\"CalendarSelect(2," + (CalendarDate.getMonth()-1)+ ")\" ><<</a></td>";
				str += "<td align=center><a href=# onclick=\"CalendarSelectHeader(2);\">" + CalendarMonthName(CalendarDate.getMonth(),false) + " " + CalendarDate.getFullYear() + "</a></td>";
				str += "<td align=right><a href=# onclick=\"CalendarSelect(2," + (CalendarDate.getMonth()+1)+ ")\" >>></a></td>";
			*/
				str += "<td align=left onclick=\"CalendarSelect(2," + (CalendarDate.getMonth()-1)+ ")\" " + fontstylecustom + "><<</td>";
				str += "<td align=center onclick=\"CalendarSelectHeader(2);\" " + fontstylecustom + ">" + CalendarMonthName(CalendarDate.getMonth(),false) + " " + CalendarDate.getFullYear() + "</td>";
				str += "<td align=right onclick=\"CalendarSelect(2," + (CalendarDate.getMonth()+1)+ ")\" " + fontstylecustom + ">>></td>";
				str += "</tr></table></td></tr>";
				
				//Day Name
				str += "<tr>";
				for (i=0;i<7;i++)
				{
					strTemp = CalendarDayList[(i+CalendarFirstDay) % 7];
					str += "<td align=center class=NameOfDays>" + strTemp.substr(0,2) + "</td>";
				}
				
				curDate = new Date(CalendarDate.getFullYear(),CalendarDate.getMonth(),1,0,0,0);
				while(curDate.getDay() != CalendarFirstDay)
				{
					curDate.setDate(curDate.getDate()-1);
					dateIdx--;
				}			
				
				for (row=0;row<6;row++)
				{
					str += "<tr>";
					for (col=0;col<7;col++)
					{			
						if (curDate.getFullYear() >= CalendarYearMin && curDate.getFullYear() <= CalendarYearMax)
						{
							strSelectEvents = "onclick=\"CalendarSelect(1," + dateIdx + ")\"";
							
							ClassName2 = "";
							if (CalendarWithHoliday && CalendarIsHoliday(curDate))
								ClassName2 = "Holiday";
																				
							ClassName3 = "";
							if (curDate.getFullYear() == Today.getFullYear() && curDate.getMonth() == Today.getMonth() && curDate.getDate() == Today.getDate())
								ClassName3 = "Today";
									
							if (curDate.getMonth() == CalendarDate.getMonth())
							{ 																	
								switch(curDate.getDay())
								{
									case 0 :
										ClassName = "Sunday";										
										break;
									case 6 :
										ClassName = "Saturday";											
										break;
									default:
										ClassName = "WorkDay";	
								}								
							}
							else
							{
								ClassName = "OutOfRange";		
								if (!CalendarOutOfRangeCanSelected) strSelectEvents = "";
							}
							str += "<td align=center " + strSelectEvents + " class=" + ClassName + ClassName2 + ClassName3 + ">" + curDate.getDate() + "</td>";
						}
						else
						{
							str += "<td align=center>&nbsp;</td>";

						}
						curDate.setDate(curDate.getDate()+1);	
						dateIdx++;
					}	
					str += "</tr>";
				}
				str += "</tr>";
				break;
			case 2 :					
				str += "<tr height=20><td colspan=4><table width=100%><tr>";
				/*
				str += "<td align=left><a href=# onclick=\"CalendarSelect(3," + (CalendarDate.getFullYear()-1)+ ")\"><<</a></td>";				
				str += "<td align=center><a href=# onclick=\"CalendarSelectHeader(3);\">" + CalendarDate.getFullYear() + "</a></td>";
				str += "<td align=right><a href=# onclick=\"CalendarSelect(3," + (CalendarDate.getFullYear()+1)+ ")\">>></a></td>";
				*/
				str += "<td align=left onclick=\"CalendarSelect(3," + (CalendarDate.getFullYear()-1)+ ")\" "+ fontstylecustom +"><<</td>";				
				str += "<td align=center "+ fontstylecustom +" onclick=\"CalendarSelectHeader(3);\">" + CalendarDate.getFullYear() + "</td>";
				str += "<td align=right "+ fontstylecustom +" onclick=\"CalendarSelect(3," + (CalendarDate.getFullYear()+1)+ ")\">>></td>";
				str += "</tr></table></td></tr>";
				
				MCur=0;
				for (row=0;row<3;row++)
				{					
					str += "<tr>";
					for (col=0;col<4;col++)
					{												
						str += "<td align=center onclick=\"CalendarSelect(2," + MCur+ ")\" class=Box>" + CalendarMonthName(MCur,true) + "</td>";						
						MCur++;	
					}	
					str += "</tr>";
				}
				str += "</tr>";				
				break;						
			case 3 :					
				YCur = CalendarDate.getFullYear() - (CalendarDate.getFullYear() % 10) - 1;
				str += "<tr height=20><td colspan=4><table width=100%><tr>";
				/*
				str += "<td align=left><a href=# onclick=\"CalendarSelect(4," + (YCur-10)+ ")\"><<</a></td>";
				str += "<td align=center><a href=# onclick=\"CalendarSelectHeader(4);\">" + (YCur+1) + " - " + (YCur+10)  + "</a></td>";
				str += "<td align=right><a href=# onclick=\"CalendarSelect(4," + (YCur+11)+ ")\">>></a></td>";
				*/
				str += "<td align=left onclick=\"CalendarSelect(4," + (YCur-10)+ ")\" "+ fontstylecustom +"><<</td>";
				str += "<td align=center onclick=\"CalendarSelectHeader(4);\" "+ fontstylecustom +">" + (YCur+1) + " - " + (YCur+10)  + "</td>";
				str += "<td align=right onclick=\"CalendarSelect(4," + (YCur+11)+ ")\" "+ fontstylecustom +">>></td>";
				str += "</tr></table></td></tr>";
				
				for (row=0;row<3;row++)
				{					
					str += "<tr>";
					for (col=0;col<4;col++)
					{												
						if (YCur >= CalendarYearMin && YCur <= CalendarYearMax)
						{
							if ((row == 0 && col == 0) || (row == 2 && col == 3))
							{
							 	str += "<td align=center onclick=\"CalendarSelect(3," + YCur + ")\" class=Box2>" + YCur + "</td>";
							}
							else
							{
								str += "<td align=center onclick=\"CalendarSelect(3," + YCur + ")\" class=Box>" + YCur + "</td>";
							}
						}
						else
						{
							str += "<td align=center class=Box>&nbsp;</td>";
						}
						YCur++;	
					}	
					str += "</tr>";
				}
				str += "</tr>";				
				break;

			case 4 :					
				YCur = CalendarDate.getFullYear() - (CalendarDate.getFullYear() % 100);
				str += "<tr height=20><td colspan=4><table width=100%><tr>";
				/*
				str += "<td align=left><a href=# onclick=\"CalendarSelect(5," + (YCur-99)+ ")\"><<</a></td>";
				str += "<td align=center><a href=#>" + (YCur) + " - " + (YCur+99)  + "</a></td>";
				str += "<td align=right><a href=# onclick=\"CalendarSelect(5," + (YCur+101)+ ")\">>></a></td>";
				*/
				str += "<td align=left "+fontstylecustom+" onclick=\"CalendarSelect(5," + (YCur-99)+ ")\"><<</td>";
				str += "<td align=center "+fontstylecustom+" >" + (YCur) + " - " + (YCur+99)  + "</td>";
				str += "<td align=right "+fontstylecustom+" onclick=\"CalendarSelect(5," + (YCur+101)+ ")\">>></td>";
				str += "</tr></table></td></tr>";

				YCur -= 10;
				for (row=0;row<3;row++)
				{					
					str += "<tr>";
					for (col=0;col<4;col++)
					{	
						if (  ((YCur) >= CalendarYearMin && (YCur) <= CalendarYearMax)
						    ||((YCur+9)>= CalendarYearMin && (YCur+9)<= CalendarYearMax))
						{
							Y1 = (YCur) >= CalendarYearMin ? (YCur)  : CalendarYearMin;
							Y2 = (YCur+9)<= CalendarYearMax ? (YCur+9) : CalendarYearMax;
							if ((row == 0 && col == 0) || (row == 2 && col == 3))
							{
							 	str += "<td align=center onclick=\"CalendarSelect(4," + Y1 + ")\" class=Box2>&nbsp;" + Y1 + "-<br>" + Y2 + "</td>";
							}
							else
							{
								str += "<td align=center onclick=\"CalendarSelect(4," + Y1 + ")\" class=Box>&nbsp;" + Y1 + "-<br>" + Y2 + "</td>";
							}
							
						}
						else
						{
							str += "<td align=center class=Box>&nbsp;</td>";
						}											
						
						YCur+=10;	
					}	
					str += "</tr>";
				}
				str += "</tr>";				
				break;

		}
		strTemp = CalendarMonthList[Today.getMonth()];   
		strTemp = CalendarDayList[Today.getDay()] + ", " + Today.getDate() + " " + strTemp.substr(0,3) + " " + Today.getFullYear(); 
		str += "<tr height=20><td colspan=7 align=center "+fontstylecustom+" ";
		str += "onclick=\"CalendarSelectToday()\">" + strTemp;
		str += "</td></tr>";
		str += "</table>";
		CalendarObject.innerHTML = str;
		CalendarX2	= CalendarX1 + CalendarObject.offsetWidth;
		CalendarY2	= CalendarY1 + CalendarObject.offsetHeight;
	}	
}

function CalendarSelectToday()
{
	var Today 	= new Date();
	CalendarDate = new Date(Today.getFullYear(),Today.getMonth(),Today.getDate(),Today.getHours(),Today.getMinutes(),Today.getSeconds());
	//CalendarMode = 1;
	//CalendarBuild();	
	
	if (CalendarOutputDate)			
	CalendarOutputDate.value = CalendarDateValue(CalendarDate);

	if (CalendarOutputTime)	
	{
		str = CalendarOutputTime.value;
		str = str.replace(/^\ +/,'').replace(/\ +$/,'');
		if (str == "")
		CalendarOutputTime.value = CalendarTimeValue(CalendarDate);
	}	
	CalendarObject.style.display = "none";
}

function CalendarSelect(mode,value)
{
	var Today 	= new Date();
	var str		= new String();
	switch(mode)
	{
		case 1 :
			CalendarDate = new Date(CalendarDate.getFullYear(),CalendarDate.getMonth(),value,Today.getHours(),Today.getMinutes(),Today.getSeconds());
			if (CalendarOutputDate)			
				CalendarOutputDate.value = CalendarDateValue(CalendarDate);
			
			if (CalendarOutputTime)	
			{
				str = CalendarOutputTime.value;
				str = str.replace(/^\ +/,'').replace(/\ +$/,'');
				//if (str == "")
					CalendarOutputTime.value = CalendarTimeValue(CalendarDate);
			}	
			CalendarObject.style.display = "none";
			break;
		case 2 :
			if (  (value >= 0 && value <= 11) 
                ||(value <  0 && CalendarDate.getFullYear()-1 >= CalendarYearMin)
			    ||(value > 11 && CalendarDate.getFullYear()+1 <= CalendarYearMax))
			{				
				CalendarMode = 1;
			
				if (Today.getFullYear() == CalendarDate.getFullYear() && Today.getMonth() == value)
				{
					CalendarDate = new Date(CalendarDate.getFullYear(),value,Today.getDate(),0,0,0);
				}
				else
				{
					CalendarDate = new Date(CalendarDate.getFullYear(),value,1,0,0,0);
				}

				CalendarBuild();
			}
			break;
		case 3 :
		case 4 :
		case 5 :
			if (value >= CalendarYearMin && value <= CalendarYearMax)
			{
				CalendarMode = mode-1;
				if (Today.getFullYear() == CalendarDate.getFullYear())
				{
					CalendarDate = new Date(value,Today.getMonth(),Today.getDate(),0,0,0);
				}
				else
				{
					CalendarDate = new Date(value,1,1,0,0,0);
				}

				CalendarBuild();
			}
			break;
	}
}

function CalendarSelectHeader(mode,value)
{
	switch(mode)
	{
		case 2 :
			CalendarMode=2;
			CalendarBuild();
			break;
		case 3 :
			CalendarMode=3;
			CalendarBuild();
			break;
		case 4 :
			CalendarMode=4;
			CalendarBuild();
			break;	
	}
}

function CalendarCekMouseMove(e)
{
	var mouseX = CalendarIsIE ? window.event.clientX : e.pageX;
	var mouseY = CalendarIsIE ? window.event.clientY : e.pageY;
	
	if (CalendarObject && CalendarObject.style.display == "block")
	{
		if (mouseX >= (CalendarX1 - document.body.scrollLeft) && mouseX <= (CalendarX1 + CalendarObject.offsetWidth - document.body.scrollLeft) &&
		    mouseY >= (CalendarY1 - document.body.scrollTop) && mouseY <= (CalendarY1 + CalendarObject.offsetHeight - document.body.scrollTop))
		{
			CalendarMouseIN = true;	
		}
		else
		{
			if (CalendarMouseIN)
			{
				document.onmousemove = null;
				document.onmousedown = null;
				CalendarObject.style.display = 'none';
				CalendarMouseIN = false;
			}
		}
		
	}
	/*
	obj = document.getElementById("info");
	obj.value = "Mouse: X="+(document.body.offsetLeft + mouseX)+",Y="+(document.body.offsetTop+mouseY);
	obj.value += "; screen: X="+document.body.scrollLeft +",Y="+document.body.scrollTop;
	obj.value += "; obj: X1="+CalendarX1+",Y1="+CalendarY1+",X2="+CalendarX2+",Y2="+CalendarY2;
	obj.value += "; obj: width="+CalendarObject.offsetWidth+",height="+CalendarObject.offsetHeight;
	obj.value += "; IN: "+CalendarMouseIN;
	*/
}

function CalendarCekMouseDown(e)
{
	var found = false;
	var obj   = CalendarIsIE ? window.event.srcElement : e.target;
	
	if (CalendarObject && CalendarObject.style.display == "block")
	{
		if(typeof( obj.offsetParent ) != 'undefined' ) {
			for(;obj;obj = obj.offsetParent ) {						
				if (obj == CalendarObject)
				{
					found = true;
					break;
				}	
			}
		}
		
		if (!found)
		{
			document.onmousemove = null;
			document.onmousedown = null;
			CalendarObject.style.display = 'none';
			CalendarMouseIN = false;
		}
	}
}