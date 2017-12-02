	function initArray(pickerApp)
	{
		pickerApp.daysarray[0]=31;
		pickerApp.daysarray[1]=28;
		pickerApp.daysarray[2]=31;
		pickerApp.daysarray[3]=30;
		pickerApp.daysarray[4]=31;
		pickerApp.daysarray[5]=30;
		pickerApp.daysarray[6]=31;
		pickerApp.daysarray[7]=31;
		pickerApp.daysarray[8]=30;
		pickerApp.daysarray[9]=31;
		pickerApp.daysarray[10]=30;
		pickerApp.daysarray[11]=31;
		var i=pickerApp.dato.getFullYear();
		while (i >= 2000)
		{
			if (i==2000) pickerApp.daysarray[1]=29;
			i = i-4;
		}
	}
function printStyle(pickerApp)
	{
	document.write('<style type="text/css">\n');
	document.write('	.'+pickerApp.name+'datetd {\n');
	document.write('		font-family:'+pickerApp.dateFontFamily+';\n');
	document.write('		font-size:'+pickerApp.dateFontSize+'px;\n');
	document.write('		align:right;\n');
	document.write('		border : 1px Black;\n');
	document.write('		background : '+pickerApp.dateBgColor+'\n;');
	document.write('		text-color:'+pickerApp.dateFontColor+';\n');
	document.write('		color : '+pickerApp.dateFontColor+';\n');
	document.write('	}\n');
	document.write('	.'+pickerApp.name+'datetable {\n');
	document.write('		font-family:'+pickerApp.dateFontFamily+';\n');
	document.write('		font-size:'+pickerApp.dateFontSize+'px;\n');
	document.write('		align:right;\n');
	document.write('		border : 1px Black;\n');
	document.write('		background : '+pickerApp.dateBgColor+'\n;');
	document.write('		text-color:'+pickerApp.dateFontColor+';\n');
	document.write('		color : '+pickerApp.dateFontColor+';\n');
	document.write('	}\n');
	
	document.write('	.'+pickerApp.name+'datehead {\n');
	document.write('		font-family:'+pickerApp.headFontFamily+';\n');
	document.write('		font-size:'+pickerApp.headFontSize+'px;\n');
	document.write('		align:right;\n');
	document.write('		margin-bottom : 0px;\n');
	document.write('		background : '+pickerApp.headBgColor+'\n;');
	document.write('		text-color:'+pickerApp.headFontColor+';\n');
	document.write('		color : '+pickerApp.headFontColor+';\n');
	document.write('		margin-top : 0px;\n');
	document.write('		margin-left: 0px;\n');
	document.write('\n');
	document.write('	}\n');
	document.write('\n');
	document.write('	.'+pickerApp.name+'dateimg{\n');
	document.write('		cursor : hand;\n');
	document.write('	}\n');
	document.write('</style>\n');

	}

function newRow(start,end,pickerApp)
{

	document.writeln('<tr align=\"right\">');
	
	for(x=parseInt(start,10);x<parseInt(end,10)+1;x++)
	{
		document.writeln('<td class=\"'+pickerApp.name+'datetd\" align=\"right\" width=\"5\" ID=\"'+pickerApp.name+'td'+x+'\"><a href=\"#\" onClick=\"returnDate('+x+','+pickerApp.name+')\">'+x+'</a></td>\n');
	}
	
	
	document.writeln('</tr>');
}




function printTable(pickerApp)
{
initArray(pickerApp);

document.write('<div id=\"'+pickerApp.name+'datepicker\" style=\"position:absolute;\">');

document.write('<table class="'+pickerApp.name+'datetable" width=\"'+pickerApp.width+'\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\">');
document.write('<tr>');
document.write('<td valign=\"top\" colspan=\"7\" class=\"'+pickerApp.name+'datehead\" nowrap>');
document.write('<table border=0 cellpadding=0 cellspacing=0>');
document.write('<tr><td class=\"'+pickerApp.name+'datehead\" id=\"'+pickerApp.name+'datetext\" width=\"'+eval(pickerApp.width-30)+'\" align=\"left\">');
document.write(pickerApp.datoStr);
document.write('</td><td align=\"middle\" width=\"30\" nowrap>');
document.write('<a href=\"#\" onClick=cycleDown('+pickerApp.name+');><img class='+pickerApp.name+'dateimg  name='+pickerApp.name+'leftimg id='+pickerApp.name+'leftimg src='+pickerApp.leftIMG.src+' border=0 vspace=0 hspace=0 ></a> ');
document.write('<a href=\"#\" onClick=cycleUp('+pickerApp.name+');><img class='+pickerApp.name+'dateimg  name='+pickerApp.name+'rightimg id='+pickerApp.name+'rightimg src='+pickerApp.rightIMG.src+' border=0  vspace=0 hspace=0 ></a>');
document.write('</td></tr></table>');
document.write('</td>');
document.write('</tr>');
document.write('<tr>');
if(pickerApp.weekstartat.toLowerCase()=='m')
{
document.write('<td class=\"'+pickerApp.name+'datetd\">'+pickerApp.mon+'</td>');
}
else
{
document.write('<td class=\"'+pickerApp.name+'datetd\">'+pickerApp.sun+'</td>');
document.write('<td class=\"'+pickerApp.name+'datetd\">'+pickerApp.mon+'</td>');
}
document.write('<td class=\"'+pickerApp.name+'datetd\">'+pickerApp.tue+'</td>');
document.write('<td class=\"'+pickerApp.name+'datetd\">'+pickerApp.wed+'</td>');
document.write('<td class=\"'+pickerApp.name+'datetd\">'+pickerApp.thu+'</td>');
document.write('<td class=\"'+pickerApp.name+'datetd\">'+pickerApp.fri+'</td>');
document.write('<td class=\"'+pickerApp.name+'datetd\">'+pickerApp.sat+'</td>');
if(pickerApp.weekstartat.toLowerCase()=='m')
{
	document.write('<td class=\"'+pickerApp.name+'datetd\">'+pickerApp.sun+'</td>');
}
document.write('</tr>');
newRow('1','7',pickerApp);
newRow('8','14',pickerApp);
newRow('15','21',pickerApp);
newRow('22','28',pickerApp);
newRow('29','35',pickerApp);
newRow('36','42',pickerApp);
document.write('<tr>');

document.write('</tr>');
document.writeln('</table>');

document.writeln('</div>');



}
function printPicker(pickerApp)
{
//alert(pickerApp.name);
document.write('<div id='+pickerApp.name+'picklink style=\"position:relative;left:0px;top:0px\">');
document.write('<a class='+pickerApp.name+'datetd href=# onClick=showDatePicker('+pickerApp.name+');><img  border=0 src='+pickerApp.pickIMG.src+' name='+pickerApp.name+'picker id='+pickerApp.name+'picker></a>');
document.write('</div>');	




}
function setProperties(pickerApp)
{	

	pickerApp.dato.setDate(1);
	if(pickerApp.dato.getDay()==0)
	{
		datovar=7;
		if(pickerApp.weekstartat.toLowerCase()!='m')
		{
			datovar=0;
		}
		
	}
	else
	{
		datovar = pickerApp.dato.getDay();
		if(pickerApp.weekstartat.toLowerCase()!='m')
		{
			datovar=datovar+1;
		}
	}
	
	changeDates(datovar,pickerApp.dato.getMonth(),pickerApp);
	id=eval('document.all.'+pickerApp.name+'datepicker');
	id.style.visibility="hidden";
	
	id=eval('document.all.'+pickerApp.name+'picklink');
	
	

}

	
	
	function changeDates(startDay,month,pickerApp)
{

	

	for(x=1;x<43;x++)
	{
		this.id=eval('document.all.'+pickerApp.name+'td'+x);

		this.id.innerText='';
		this.num=new String(id.id);
		this.num2=parseInt(num.substring(2,num.length),10);
	}
	this.startDag=startDay-startDay+1;
	if(startDay==0)
	{
	x=1;
	}
	else
	{
	x=startDay;
	}

	while(x<43 )
	{
		id=eval('document.all.'+pickerApp.name+'td'+x);
		if(startDag>pickerApp.daysarray[month])
		{
			break;
		}
	
		id.innerHTML='<a class=\"'+pickerApp.name+'datetd\" href=\"#\" onClick=\"returnDate('+startDag+','+pickerApp.name+')\">'+startDag+'</a>';	
	
		x++;
		++startDag;
	}



}

	
	
function showDatePicker(pickerApp)
{

id=eval('document.all.'+pickerApp.name+'picklink');
id.style.visibility="hidden";

id=eval('document.all.'+pickerApp.name+'datepicker');
id.style.visibility="";


for(x=0;x<document.all.length;x++)
{
	if(document.all[x]!=id)
	{
	id2=new String(document.all[x].id);
	if(id2.indexOf('picklink')>0)
	{
		document.all[x].style.visibility='hidden';
	}
	}
}
}
function returnDate(dag,pickerApp)
{
	retDateStr=pickerApp.dateformat

	if(dag <10)
	{
		dag='0'+dag;
	}
	mo=eval(pickerApp.dato.getMonth()+1);
	if(mo <10)
	{
		mo='0'+mo;
	}
	id=eval('document.all.'+pickerApp.name+'datepicker');
	id.style.visibility="hidden";
	id=eval('document.all.'+pickerApp.name+'picklink');
	id.style.visibility="";

	
var fld=eval(pickerApp.retField);

//Find day part of dateformat
pos1=retDateStr.indexOf('dd');


retDateStr=retDateStr.substring(0,pos1)+dag+retDateStr.substring(pos1+2);

//Find month part of dateformat
pos1=retDateStr.indexOf('mm');
retDateStr=retDateStr.substring(0,pos1)+mo+retDateStr.substring(pos1+2);
//find year part of dateformat
pos1=retDateStr.indexOf('yyyy');
if(pos1!=-1)
{
retDateStr=retDateStr.substring(0,pos1)+pickerApp.dato.getFullYear()+retDateStr.substring(pos1+4);
}
else
{
pos1=retDateStr.indexOf('yy');
retDateStr=retDateStr.substring(0,pos1)+new String(pickerApp.dato.getFullYear()).substring(2)+retDateStr.substring(pos1+2);
}
for(x=0;x<retDateStr.length;x++)
{
	if(retDateStr.charAt(x)=='#')
	{
		retDateStr=retDateStr.substring(0,x)+pickerApp.datesep+retDateStr.substring(x+1)
	}
}

//output date to field

fld.value=retDateStr;

//Show all pickers
for(x=0;x<document.all.length;x++)
{

	id2=new String(document.all[x].id);
	if(id2.indexOf('picklink')>0)
	{
		document.all[x].style.visibility='visible';
	}

}


}

function parseMonth(month,pickerApp)
{
	monStr=pickerApp.months[parseInt(month,10)];

return monStr;
}


function cycleDown(pickerApp)
{
pickerApp.dato.setMonth(pickerApp.dato.getMonth()-1);
id=eval('document.all.'+pickerApp.name+'datetext');
id.innerText=parseMonth(pickerApp.dato.getMonth(),pickerApp)+" " +pickerApp.dato.getFullYear();


pickerApp.dato.setDate(1);
initArray(pickerApp);
if(pickerApp.dato.getDay()==0)
	{
		datovar=7;
		if(pickerApp.weekstartat.toLowerCase()!='m')
		{
			datovar=0;
		}
		
	}
	else
	{
		datovar = pickerApp.dato.getDay();
		if(pickerApp.weekstartat.toLowerCase()!='m')
		{
			datovar=datovar+1;
		}
	}

changeDates(datovar,pickerApp.dato.getMonth(),pickerApp);

}

function cycleUp(pickerApp)
{

pickerApp.dato.setMonth(pickerApp.dato.getMonth()+1);
id=eval('document.all.'+pickerApp.name+'datetext');

id.innerText=parseMonth(pickerApp.dato.getMonth(),pickerApp)+" " +pickerApp.dato.getFullYear();

pickerApp.dato.setDate(1);
initArray(pickerApp);
if(pickerApp.dato.getDay()==0)
	{
		datovar=7;
		if(pickerApp.weekstartat.toLowerCase()!='m')
		{
			datovar=0;
		}
		
	}
	else
	{
		datovar = pickerApp.dato.getDay();
		if(pickerApp.weekstartat.toLowerCase()!='m')
		{
			datovar=datovar+1;
		}
	}
changeDates(datovar,pickerApp.dato.getMonth(),pickerApp);


}

function DatePicker(name,properties)
{

	
	this.name=name;
	this.leftIMG = new Image();
	this.rightIMG = new Image();
	this.pickIMG = new Image();
	this.leftIMG.src=properties.leftIMG;
	this.rightIMG.src=properties.rightIMG;
	this.pickIMG.src=properties.pickIMG;
	this.retField=properties.retField;
	this.dato= new Date();
	this.selectedDate="";
	this.daysarray = new Array(12);
	this.width=135;

/*Font properties*/
	
	this.headBgColor=properties.headBgColor;
	this.headFontSize=properties.headFontSize;
	this.headFontFamily=properties.headFontFamily;
	this.headFontColor=properties.headFontColor;
	this.dateBgColor=properties.dateBgColor;
	this.dateFontSize=properties.dateFontSize;
	this.dateFontFamily=properties.dateFontFamily;
	this.dateFontColor=properties.dateFontColor;
	
	this.weekstartat=properties.weekstartat;
	this.dateformat=properties.dateformat;
	this.datesep=properties.datesep;
	
	this.mon=properties.mon;
	this.tue=properties.tue;
	this.wed=properties.wed;
	this.thu=properties.thu;
	this.fri=properties.fri;
	this.sat=properties.sat;
	this.sun=properties.sun;
	
	this.months=new Array(12);
	this.months[0]=properties.jan;
	this.months[1]=properties.feb;
	this.months[2]=properties.mar;
	this.months[3]=properties.apr;
	this.months[4]=properties.may;
	this.months[5]=properties.jun;
	this.months[6]=properties.jul;
	this.months[7]=properties.aug;
	this.months[8]=properties.sep;
	this.months[9]=properties.oct;
	this.months[10]=properties.nov;
	this.months[11]=properties.dec;
	this.datoStr = parseMonth(this.dato.getMonth(),this)+" " +this.dato.getFullYear();
function startPicker(who)
{
	printStyle(who);
	printTable(who);
	printPicker(who);
	setProperties(who);
}
	startPicker(this);

	return this;
}

