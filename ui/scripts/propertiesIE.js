/*
 * Copyright (c) 2000 Bjørn Bouet Smith (b.smith@get2net.dk)
 * Please use this script everywhere you like, but keep this header intact.
 * The datepicker only works with Internet Explorer 4+
 * I have only testet it with IE 5+, but it should work with 4.x as well
 * Comments and questions try my e-mail.
*/


/*Create initial properties object*/
myPickProp2= new Object();

/*Images*/
myPickProp2.leftIMG='ui/images/left.gif';
myPickProp2.rightIMG='ui/images/right.gif'
myPickProp2.pickIMG='ui/images/cal_icon.gif'
/*End images*/

/*Size of datepicker - Will scale if fonts are too big to fit*/
myPickProp2.width=135;
/* End size*/

/*Font properties*/
	/*month & year header*/
	myPickProp2.headBgColor='#C0C0FF';
	myPickProp2.headFontSize=12; //pixels
	myPickProp2.headFontFamily='Verdana,Helvetica,Arial';
	myPickProp2.headFontColor='#000000';

	/*The individual days*/
	myPickProp2.dateBgColor='#FFFFFF';
	myPickProp2.dateFontSize=12; //pixels
	myPickProp2.dateFontFamily='Verdana,Helvetica,Arial';
	myPickProp2.dateFontColor='#000000';

/*End font properties*/	


/*Date properties*/
myPickProp2.weekstartat='s';  //can be either s for sunday or m for monday
myPickProp2.dateformat='mm#dd#yy'; // use dd for day, mm for month, and yy or yyyy for two-digit or four-digit year. Must be separated with #
myPickProp2.datesep='/'; // the date separator that your language use. ie / or -
/*End date properties*/

/*Day names*/

//Should be pretty explanatory. Object.mon=monday etc.
myPickProp2.mon='Mo';
myPickProp2.tue='Tu';
myPickProp2.wed='We';
myPickProp2.thu='Th';
myPickProp2.fri='Fr';
myPickProp2.sat='Sa';
myPickProp2.sun='Su';
/*End day names*/


/*Month names*/
myPickProp2.jan='January';
myPickProp2.feb='February';
myPickProp2.mar='March';
myPickProp2.apr='April';
myPickProp2.may='May';
myPickProp2.jun='June';
myPickProp2.jul='July';
myPickProp2.aug='August';
myPickProp2.sep='September';
myPickProp2.oct='October';
myPickProp2.nov='November';
myPickProp2.dec='December';
/*End month names*/

/*Return field for the date ie. which html-field should the date be returned to, when the user selects a date*/
myPickProp2.retField="document._DominoForm.date2";
/*End return field*/

//Call the constructor with (name, properties object) as parameters
/*
It is possible to have more than one date picker on the same page. 
Just give the objects and properties objects different names.
And remember to create a properties object for each date picker

ie. 
myPick2=new DatePicker('myPick2',myPickProp2);
myPick3=new DatePicker('myPick3',myPickProp3);
*/
myPick2=new DatePicker('myPick2',myPickProp2);