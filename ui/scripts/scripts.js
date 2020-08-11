
function addChangeField(fieldName){
	var counter = 'counter' + fieldName;
	var count =document.getElementById(counter).value;

	var cell = 'inputCell' + fieldName + "_REF_1";
	var field = fieldName + "_REF_" + count;

	var cellElement = document.getElementById(cell);
	var fieldElement = document.getElementById(field);

	count++;

	var newId = fieldName + "_REF_" + count;

	document.getElementById(counter).value = count;

	var newField = jQuery(fieldElement).clone();
	jQuery(newField).attr("id",newId);
	jQuery(newField).attr("name",newId);
	jQuery(newField).appendTo(cellElement);

}



function setTableCellContents(cell,content){
	var tdCell = document.getElementById(cell);
	tdCell.innerHTML = content;
}

function ciratCategoriesValidate(){
	var x=document.forms["defineForm"]["CATEGORY"].value;
	var y=document.forms["defineForm"]["LIVE"].value;
	if (x==null || x=="")
	  {
	  alert("Please enter a category");
	  return false;
	  }
	if (y==null || y=="")
	{
	alert("Select Live status");
	return false;
	}
}

function checkCsvTableDefinitionForm(){
	var tableName = document.getElementById('tableName');
	var tableDescription = document.getElementById('tableDescription');

	if(tableName.value==''){
		alert('Please enter Name for Table');
		tableName.focus;
		return false;
	}

	if(tableDescription.value==''){
		alert('Please enter a description for the table');
		tableDescription.focus;
		return false;
	}
	return true;
}

function confirmDeleteTable(value){
	table = document.getElementById("tableName");
	selected = table.selectedIndex;
	check = confirm('Delete table ' + table.options[selected].value);
	if(check){
		document.getElementById("myForm").submit();
	} else {
		return;
	}
}

function confirmDeleteNEPAccount(accountName,pageName){
	if( confirm('Please confirm you wish to delete table ' + accountName + '\nAs doing so will cause all data associated with the table be deleted')){
		window.location = pageName;
	}
}

function clearEmailWarning(rand){
	alert(rand);
	var emailDiv = document.getElementById('email' + rand);
	alert(emailDiv.display);
	emailDiv.style.display = 'none';
	alert(emailDiv.display);
}

function populateArmForAccount(){
	var accountSelect   = document.getElementById('ACCOUNT');
	var selectedAccount = accountSelect.selectedIndex;

	var armNotesidField = document.getElementById('ARM_NOTESID');
	var armIntranetField = document.getElementById('ARM_INTRANET');

	if(selectedAccount > 0 ){
		armIntranetField.value = armIntranet[selectedAccount];
		armNotesidField.value = armNotesid[selectedAccount];
	}
}

function populateRmForCompetency(){
	var competencySelect   = document.getElementById('COMPETENCY');
	var selectedCompetency = competencySelect.selectedIndex;

	var RmNotesidField = document.getElementById('ARM_NOTESID');
	var RmIntranetField = document.getElementById('ARM_INTRANET');

	if(selectedCompetency > 0 ){
		RmIntranetField.value = rmIntranet[selectedCompetency];
		RmNotesidField.value = rmNotesid[selectedCompetency];
	}
}


function populateRequests(){
	var demand 		 	= document.getElementById('DEMAND_REFERENCE');
	var resourceSelect  = document.getElementById('RESOURCE_REFERENCE');
	var cloneSelect   	= document.getElementById('copy_id');

	var selectedDemand  = demand.selectedIndex;

	resourceSelect.options.length=0;
	cloneSelect.options.length=0;
	if(demand.selectedIndex > 0 ){
		resourceSelect.options[0] = new Option('Select..','');
		cloneSelect.options[0] = new Option('Select..','');
		resourceSelect.options[1] = new Option('New','New');
		for(i=0; i < requests[selectedDemand].length; i++ ){
			resourceSelect.options[resourceSelect.options.length] = new Option(requests[selectedDemand][i],requests[selectedDemand][i]);
			cloneSelect.options[cloneSelect.options.length] = new Option(requests[selectedDemand][i],requests[selectedDemand][i]);
		}
	}
	resourceSelect.disabled = false;
	cloneSelect.disabled = false;
}


function populateJrss(){
	var competency 	 = document.getElementById('COMPETENCY');
	var jrssSelect	 = document.getElementById('jrss_name');
	var poolSelect   = document.getElementById('POOL_NAME');
	var rdmIField  = document.getElementById('RDM_INTRANET');
	var rdmNField   = document.getElementById('RDM_NOTESID');
	var i=0;

	var selectedCompetency = competency.selectedIndex;

	poolSelect.options.length=0;
	if(competency.selectedIndex > 0 ){
		poolSelect.options[0] = new Option('Select pool name....','');
		for(i=0; i < pools[selectedCompetency].length; i++ ){
			poolSelect.options[poolSelect.options.length] = new Option(pools[selectedCompetency][i],pools[selectedCompetency][i]);
		}
	}
	poolSelect.disabled = false;

	jrssSelect.options.length=0;
	if(competency.selectedIndex > 0 ){
		jrssSelect.options[0] = new Option('Select Jrss....','');
		for(i=0; i < jrss[selectedCompetency].length; i++ ){
			jrssSelect.options[jrssSelect.options.length] = new Option(jrss[selectedCompetency][i],jrss[selectedCompetency][i]);
		}
	}
	jrssSelect.disabled = false;

	rdmIField.value = rdmIntranet[selectedCompetency];
	rdmNField.value = rdmNotesid[selectedCompetency];

}


function disableManDays(){
	var manDays = document.getElementById('MAN_DAYS');
	var manWeeks = document.getElementById('MAN_WEEKS');
	var months = document.getElementById('DURATION');

	if(months.value==''){
		manDays.disabled = false;
		manWeeks.disabled = false;
	} else {
		manDays.value='';
		manDays.disabled = true;
		manWeeks.value='';
		manWeeks.disabled = true;
	}


}
function disableFte(){
	var months = document.getElementById('DURATION');
	var fte = document.getElementById('AVG_FTE');
	var manDays = document.getElementById('MAN_DAYS');
	var weeks = document.getElementById('MAN_WEEKS');

	if(manDays.value==''){
		months.readOnly = false;
		fte.readOnly = false;
		weeks.value='';

	} else {
		months.readOnly = true;
		fte.readOnly = true;
	}


}

function disableFoManDays(section){
	var manDays = document.getElementById('man_days' + section);
	var manWeeks = document.getElementById('man_weeks' + section);
	var months = document.getElementById('duration' + section);

	if(months.value==''){
		manDays.disabled = false;
		manWeeks.disabled = false;
	} else {
		manDays.value='';
		manDays.disabled = true;
		manWeeks.value='';
		manWeeks.disabled = true;
	}


}
function disableFoFte(section){
	var months = document.getElementById('duration' + section);
	var fte = document.getElementById('avg_fte' + section);
	var manDays = document.getElementById('man_days' + section);
	var weeks = document.getElementById('man_weeks' + section);

	if(manDays.value==''){
		months.readOnly = false;
		fte.readOnly = false;
		weeks.value='';

	} else {
		months.readOnly = true;
		fte.readOnly = true;
	}


}


function getRejectReason(url, demandRef){
//	alert(demandRef);
	var reason = prompt('Please enter the reason for the rejection');
//	var rejectField = document.getElementById(rejectFieldId);
//	rejectField.value = reason;
	var nextPage = url + '?function=ownerReject&DEMAND_REFERENCE=' + demandRef +'&reason=' + escape(reason)
	window.location.href = nextPage;
	return;
}

var resources=1;
function addResource(){
	var oldResource;
	var id;
	var obj;
	oldResource = resources
	resources++;
	if(resources<6){
		id = 'fulfilmentOption' + resources;
		obj = document.getElementById(id);
		obj.style.display="block";

	} else {
		alert("Max 5 Resources reached");
	}
};

function dim(elementId){
	var element = document.getElementById(elementId);

	element.style.color='blue';
}

function checkRpoFulfilment(){
//	alert('checking');
	var section2 = document.getElementById('fulfilmentOption2');
	var section3 = document.getElementById('fulfilmentOption3');
	var section4 = document.getElementById('fulfilmentOption4');
	var section5 = document.getElementById('fulfilmentOption5');

	var checkResult;
	checkResult = checkRpoFulfilmentSection(1);

//	alert(checkResult);
//	alert(section2.style.display);
//
	if(checkResult && section2.style.display == 'block'){
		checkResult = checkRpoFulfilmentSection(2);
	}
	if(checkResult && section3.style.display == 'block'){
		checkResult = checkRpoFulfilmentSection(3);
	}
	if(checkResult && section4.style.display == 'block'){
		checkResult = checkRpoFulfilmentSection(4);
	}
	if(checkResult && section5.style.display == 'block'){
		checkResult = checkRpoFulfilmentSection(5);
	}
	return checkResult;
}

function checkRpoFulfilmentSection(section){
//	alert("Checking " + section);
	var site = document.getElementById('site_type' + section);
	var location = document.getElementById('location' + section);
//	var pool = document.getElementById('pool_name' + section);
	var reso = document.getElementById('resource' + section + "_INTRANET");
	var start = document.getElementById('start_date' + section);
	var months = document.getElementById('duration' + section);
	var avgFte = document.getElementById('avg_fte' + section);
	var manDays = document.getElementById('man_days' + section);
	var manWeeks = document.getElementById('man_weeks' + section);



	if(site.value == ''){
		alert("Please enter Site Type details");
		site.focus();
		return false;
	}
//	if(pool.value == '' && reso.value==''){
//		alert("Please enter Pool or Resource Name");
//		pool.focus();
//		return false;
//	}
	if(start.value == ''){
		alert("Please enter a Start Date");
		start.focus();
		return false;
	}

//	alert('Months ' + months);
//	alert('Man Days' + manDays);
//	alert('avgFte' + avgFte);
//	alert('manWeeks' + manWeeks);


	return checkEffortDuration(months, manWeeks, avgFte, manDays);


}



function checkAssignToRpoForm(){
	var intranetId = document.getElementById('RPO_INTRANET');
	var offerTarget = document.getElementById('RPO_OFFER_TARGET');
	if(intranetId.value == ''){
		alert("Please enter Resource Pool Owner (Dispatcher) details");
		intranetId.focus();
		return false;
	}
	if(offerTarget.value == ''){
		alert("Please enter Commit Date");
		offerTarget.focus();
		return false;
	}
	return TRUE;
}

function checkResourceForm(){
	var jrssName = document.getElementById('jrss_name');
	var band = document.getElementById('BAND');
	var cost = document.getElementById('RESOURCE_COST');
	var pool = document.getElementById('POOL_NAME');
	var claim = document.getElementById('CLAIM_ACCOUNT_ID');
	var siteType = document.getElementById('SITE_TYPE');
	var location = document.getElementById('LOCATION');
	var startDate = document.getElementById('START_DATE');
	var months = document.getElementById('DURATION');
	var avgFte = document.getElementById('AVG_FTE');

	var manDays = document.getElementById('MAN_DAYS');
	var manWeeks = document.getElementById('MAN_WEEKS');

	var tasks = document.getElementById('REQUEST_CONSIDERATION');
	var technical = document.getElementById('TECH_NOTESID');

	var priorityFlag =  document.getElementById('PRIORITY_FLAG');
	var priorityJustification = document.getElementById('PRIORITY_JUSTIFICATION');

	if(jrssName.value == ''){
		alert("Please select Competency:JRSS");
		jrssName.focus();
		return false;
	}
	if(band.value == ''){
		alert("Please select Band");
		band.focus();
		return false;
	}


	if(cost.value != '' ){
		try {
	        var inpVal = parseInt(cost.value,10);
	        if (isNaN(inpVal)) {
	            var msg = "Resource Cost must be numeric.";
	            var err = new Error(msg);
	            if (!err.message) {
	                err.message = msg;
	            }
	            throw err;
	        }
	    } catch (e) {
	        alert(e.message);
	        cost.focus();
	        return false;
	    }
	}

	if(pool.value == ''){
		alert("Please select Pool");
		pool.focus();
		return false;
	}

	if(siteType.value == ''){
		alert("Please select Site Type");
		siteType.focus();
		return false;
	}

	if(tasks.value == ''){
		alert("Please enter high level details of the tasks to be performed by this resource.");
		tasks.focus();
		return false;
	}

	if(priorityFlag.selectedIndex == 0){
		alert("You must select Yes or No for the Priority Flag");
		priorityFlag.focus();
	}

	if(priorityFlag.selectedIndex == 1){
		if(priorityJustification.value==''){
			alert("You have indicated this is a Priority Request, please provide your justifaction, or set Priority Flag to No");
			priorityJustification.focus();
		}
	}

	if(startDate.value == ''){
		alert("Please select a Start Date for this request");
		startDate.focus();
	}


	var checkResult;
	checkResult = checkEffortDuration(months, manDays, avgFte, manWeeks);

	return checkResult;
}

function checkEffortDuration(months, manWeeks, avgFte, manDays){
	if(months.value=='' && manDays.value==''){
		alert("Please enter a value for Months or Man Days");
		return false;
	}

	if(months.value != '' ){
		try {
	        var inpVal = parseInt(months.value,10);
	        if (isNaN(inpVal)) {
	            var msg = "Months must be numeric.";
	            var err = new Error(msg);
	            if (!err.message) {
	                err.message = msg;
	            }
	            throw err;
	        }
	    } catch (e) {
	        alert(e.message);
	        months.focus();
	        return false;
	    }
	}

	if(manDays.value != '' ){
		try {
	        var inpVal = parseInt(manDays.value,10);
	        if (isNaN(inpVal)) {
	            var msg = "Man Days must be numeric.";
	            var err = new Error(msg);
	            if (!err.message) {
	                err.message = msg;
	            }
	            throw err;
	        }
	    } catch (e) {
	        alert(e.message);
	        manDays.focus();
	        return false;
	    }
	}


	if(months.value != '' && avgFte.value==''){
		alert("Please enter a value for Monthly FTE");
		return false;
	}

	if(months.value != '' && avgFte.value!=''){
		try {
	        var inpVal = parseFloat(avgFte.value);
	        if (isNaN(inpVal)) {
	            var msg = "Monthly FTE must be numeric.";
	            var err = new Error(msg);
	            if (!err.message) {
	                err.message = msg;
	            }
	            throw err;
	        }
	    } catch (e) {
	        alert(e.message);
	        avgFte.focus();
	        return false;
	    }
	}

	if(manDays.value != '' && manWeeks.value==''){
		alert("Please enter a value for Weeks");
		return false;
	}

	if(manDays.value != '' && manWeeks.value != ''){
		try {
	        var inpVal = parseFloat(manWeeks.value);
	        if (isNaN(inpVal)) {
	            var msg = "Weeks must be numeric.";
	            var err = new Error(msg);
	            if (!err.message) {
	                err.message = msg;
	            }
	            throw err;
	        }
	    } catch (e) {
	        alert(e.message);
	        manWeeks.focus();
	        return false;
	    }
	}
	return true;
}


function checkDemandForm(){
	var title = document.getElementById('TITLE');
	var account = document.getElementById('ACCOUNT');
	var competency = document.getElementById('COMPETENCY');
	var projectName = document.getElementById('PROJECT_NAME');
	var demandSource = document.getElementById('DEMAND_SOURCE');
	var countryTo = document.getElementById('COUNTRY_TO');
	var clientStatus = document.getElementById('CLIENT_STATUS');
	var summary = document.getElementById('SUMMARY');
	var justification = document.getElementById('BUSINESS_JUSTIFICATION');
	var owner = document.getElementById('OWNER_INTRANET');
	var fundingType = document.getElementById('FUNDING_TYPE');

	if(title.value =='' ){
		alert("Please provide a title for this Demand");
		title.focus();
		return false;
	}

	if(account.value =='' && competency.value =='' && projectName.value ==''){
		alert("Please select Account or Competency");
		account.focus();
		return false;
	}
	if(demandSource.value == ''){
		alert("Please select Demand Type");
		demandSource.focus();
		return false;
	}
	if(countryTo.value == ''){
		alert("Please select Demand Owning Country");
		countryTo.focus();
		return false;
	}
	if(clientStatus.value == ''){
		alert("Please select Client Status");
		clientStatus.focus();
		return false;
	}

	if(fundingTypw.value == ''){
		alert("Please select Funding Type");
		fundingType.focus();
		return false;
	}

	// Check the Financial Fields.
	var checkOK = "0123456789,.";

	var oneTimeRevenue = document.getElementById('ONE_TIME_REVENUE');
	var onGoingRevenue = document.getElementById('ONGOING_REVENUE');
	var gp = document.getElementById('GP');
	var tcv = document.getElementById('TCV')
	var resourceCost = document.getElementById('RESOURCE_COST');
	var totalCost = document.getElementById('TOTAL_COST');
	var funding = document.getElementById('FUNDING_TYPE');

//	var checkRevenue = true;
//	var checkGP = true;
//	var checkCost = true;
//	var checkFunding = true;
//	var checkTcv = true;

//	switch(demandSource.value){
//	case "Internal Project":
//	case "Overhead":
//	case "IGA":
//	case "Pan-IOT":
//		checkRevenue = false;
//		checkGP = false;
//		checkTcv = false;
//		break;
//	case "Export":
//		checkCost = false;
//		checkRevenue = false;
//		checkGP = false;
//		checkFunding = false;
//		checkTcv = false;
//		break;
//	case "New Deal - Original":
//		checkCost = false;
//		checkRevenue = false;
//		checkGP = false;
//		checkFunding = false;
//		checkTcv = true;
//		break;
//	case "RFS":
//		checkCost = false;
//		checkRevenue = false;
//		checkGP = false;
//		checkFunding = true;
//		checkTcv = false;
//		break;
//
//	default:
//		checkCost = true;
//		checkRevenue = true;
//		checkGP = true;
//		checkFunding = true;
//		checkTcv = false;
//	}

	if((oneTimeRevenue.disabled == false) && checkRevenue && oneTimeRevenue.value==''){
		alert("Please enter One Time Revenue in $K");
		oneTimeRevenue.focus();
		return false;
	} else if((onGoingRevenue.disabled == false) && onGoingRevenue.value==''){
		alert("Please enter Ongoing Revenue in $K");
		onGoingRevenue.focus();
		return false;
	} else if(oneTimeRevenue.disabled == false){
	    try {
	        var inpVal = parseInt(oneTimeRevenue.value,10);
	        if (isNaN(inpVal)) {
	            var msg = "One Time Revenue must be numeric.";
	            var err = new Error(msg);
	            if (!err.message) {
	                err.message = msg;
	            }
	            throw err;
	        }
	    } catch (e) {
	        alert(e.message);
	        oneTimeRevenue.focus();
	        return false;
	    }
	    try {
	        var inpVal = parseInt(onGoingRevenue.value,10);
	        if (isNaN(inpVal)) {
	            var msg = "On Going Revenue must be numeric.";
	            var err = new Error(msg);
	            if (!err.message) {
	                err.message = msg;
	            }
	            throw err;
	        }
	    } catch (e) {
	        alert(e.message);
	        onGoingRevenue.focus();
	        return false;
	    }
	}

	if((gp.disabled == false) && gp.value==''){
		alert("Please enter the GP%");
		gp.focus();
		return false;
	}

	if((tcv.disabled == false) && tcv.value==''){
		alert("Please enter the TCV");
		tcv.focus();
		return false;
	}

	if((resourceCost.disabled == false) && resourceCost.value==''){
		alert("Please enter Resource Cost in $K");
		return false;
	} else if(checkCost){
		try {
	        var inpVal = parseInt(resourceCost.value,10);
	        if (isNaN(inpVal)) {
	            var msg = "Resource Cost must be numeric.";
	            var err = new Error(msg);
	            if (!err.message) {
	                err.message = msg;
	            }
	            throw err;
	        }
	    } catch (e) {
	        alert(e.message);
	        resourceCost.focus();
	        return false;
	    }
	}

	if((totalCost.disabled == false) && totalCost.value==''){
		alert("Please enter Total Cost in $K");
		return false;
	} else if((totalCost.disabled == false)){
		try {
	        var inpVal = parseInt(totalCost.value,10);
	        if (isNaN(inpVal)) {
	            var msg = "Total Cost must be numeric.";
	            var err = new Error(msg);
	            if (!err.message) {
	                err.message = msg;
	            }
	            throw err;
	        }
	    } catch (e) {
	        alert(e.message);
	        totalCost.focus();
	        return false;
	    }
	}

	if((totalCost.disabled == false) && ( resourceCost.value > totalCost.value)  ){
		alert("Resource Cost cannot exceed Total Cost");
        resourceCost.focus();
		return false;
	}

	if((funding.disabled == false) && funding.value==''){
		alert("Please select Funding Type");
		funding.focus();
		return false;
	}

	if(summary.value == ''){
		alert("Please enter a short Summary for this demand.");
		summary.focus();
		return false;
	}

	var summaryLength = document.getElementById('SUMMARYLength');

	if(summary.value.length > summaryLength.value){
		alert("Summary is " + summary.value.length + " characters which exceeds max length of " + summaryLength.value);
		summary.focus();
		return false;
	}

//	if(justification.value == ''){
//		alert("Please enter a short Business Justification for this demand.");
//		justification.focus();
//		return false;
//	}

	var justificationLength = document.getElementById('BUSINESS_JUSTIFICATIONLength');

	if(justification.value.length > justificationLength.value){
		alert("Justification is " + justification.value.length + " characters which exceeds max length of " + justificationLength.value);
		summary.focus();
		return false;
	}

	if(owner.value == ''){
		alert("Please enter the Owner for this Demand.");
		owner.focus();
		return false;
	}
	/*
	 * If we get - all must be ok.
	 */

	return true;
}


function DemandTypeSel(){
	var demandSource = document.getElementById('DEMAND_SOURCE');
	var clientStatus = document.getElementById('CLIENT_STATUS');

	// Deal with the financial options
	var oneTimeRevenue = document.getElementById('ONE_TIME_REVENUE');
	var onGoingRevenue = document.getElementById('ONGOING_REVENUE');
	var resourceCost = document.getElementById('RESOURCE_COST');
	var totalCost = document.getElementById('TOTAL_COST');
	var gp = document.getElementById('GP');
	var funding_type = document.getElementById('FUNDING_TYPE');
	var tcv = document.getElementById('TCV');
	var labelResourceCost = document.getElementById('labelRESOURCE_COST');
	labelResourceCost.innerHTML = 'Resource Cost $K';
	var labelTotalCost = document.getElementById('labelTOTAL_COST');
	labelTotalCost.innerHTML = 'Total Cost $K';


	// alert(demandSource.value);

	switch(demandSource.value){
	case "Internal Project":
		labelCost.innerHTML = 'Funding $K';
	case "Overhead":
	case "IGA":
	case "Pan-IOT":
		gp.value = '';
		gp.disabled = true;
		oneTimeRevenue.value = '';
		onGoingRevenue.value = '';
		oneTimeRevenue.disabled = true;
		onGoingRevenue.disabled = true;
		tcv.value = '';
		tcv.disabled = true;
		break;
	case "Export":
		oneTimeRevenue.value = '';
		oneTimeRevenue.disabled = true;
		onGoingRevenue.value = '';
		onGoingRevenue.disabled = true;
		totalCost.value = '';
		totalCost.disabled = true;
		resourceCost.value = '';
		resourceCost.disabled = true;
		gp.value = '';
		gp.disabled = true;
		funding_type.value = '';
		funding_type.disabled = true;
		tcv.value = '';
		tcv.disabled = true;
		break;
	case "New Deal - Original":
		oneTimeRevenue.value = '';
		oneTimeRevenue.disabled = true;
		onGoingRevenue.value = '';
		onGoingRevenue.disabled = true;
		totalCost.value = '';
		totalCost.disabled = true;
		resourceCost.value = '';
		resourceCost.disabled = true;
		gp.value = '';
		gp.disabled = false;
		funding_type.value = '';
		funding_type.disabled = false;
		tcv.value = '';
		tcv.disabled = false;

		break;
	default:
		oneTimeRevenue.disabled = false;
		onGoingRevenue.disabled = false;
		totalCost.disabled = false;
		resourceCost.disabled = false;
		gp.disabled = false;
		funding_type.disabled = false;
		tcv.disabled = true;

	}

	// Deal with the client status options
	switch(demandSource.value){
	case "Overhead":
	case "Internal Project":
	case "Export":
	case "IGA":
	case "Pan-IOT":
	case "Internal Project":
	case "Export":
		clientStatus.options.length=0;
		clientStatus.options[0]= new Option("Not Applicable","Not Applicable",true, true);
	break;
	default:
		clientStatus.options.length=0;
		clientStatus.options[0]= new Option("Select...","",true, true);
		clientStatus.options[1]= new Option("Unsigned","Unsigned",false, false);
		clientStatus.options[2]= new Option("Signed","Signed",false, false);
	}
}


function DemandAccSel(){
	var account = document.getElementById('ACCOUNT');
	var competency = document.getElementById('COMPETENCY');
	if(account.selectedIndex > 0){
		competency.selectedIndex = 0;
		competency.disabled = true;
	} else {
		competency.disabled = false;
	}
	populateArmForAccount();
}

function DemandCompSel(){
	var account = document.getElementById('ACCOUNT');
	var competency = document.getElementById('COMPETENCY');
	if(competency.selectedIndex > 0){
		account.selectedIndex = 0;
		account.disabled = true;
	} else {
		account.disabled = false;
	}
	populateRmForCompetency();

}


function setDemandRef(){
	var eDemRef = document.getElementById('e_DEMAND_REF_ID');
	var sDemRef = document.getElementById('DEMAND_REF_ID');
	if(eDemRef.value!=''){
		sDemRef.value = eDemRef.value;
	}
	return true;
}



function initialize() {
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = {
      zoom: 8,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);
  }

function TripleToggle(item1,item2, item3){
	Toggle(item1);
	Toggle(item2);
	Toggle(item3);
}


function DoubleToggle(item1,item2){
	Toggle(item1);
	Toggle(item2);
}



function Toggle(item) {
   obj=document.getElementById(item);
   visible=(obj.style.display!='none')
   itemKey='x'+item;
   key=document.getElementById(itemKey);
   if (visible) {
     obj.style.display='none';
     key.innerHTML="<img src='images/icon-list-open.gif' width='12' height='12' hspace='0' vspace='0' border='0' alt='expand section' />";
   } else {
      obj.style.display="block";
      key.innerHTML="<img src='images/icon-list-close.gif' width='12' height='12' hspace='0' vspace='0' border='0' alt='collapse section' />";
   }
}

function ToggleNoGraph(item){
	alert('toggleng');
   obj=document.getElementById(item);
   alert(obj);
   visible=(obj.style.display!='none')
   if (visible) {
     obj.style.display='none';
   } else {
      obj.style.display="block";
   }
}

function TogglePriorityJustification(item,dropDown){
	   selection=document.getElementById(dropDown);
	   if(selection.selectedIndex == 1){
		   show(item);
	   } else {
		   hide(item);
	   }
}

function Flip(key,item1,item2){
	   obj1=document.getElementById(item1);
	   obj2=document.getElementById(item2);
	   visible1=(obj1.style.display!='none');
	   itemKey='x'+key;
	   labKey = 'lab'+key;
	   key=document.getElementById(itemKey);
	   label=document.getElementById(labKey);
	   if (visible1) {
	     obj1.style.display='none';
	     obj2.style.display="block";
	     key.innerHTML="<img src='images/icon-list-open.gif' width='12' height='12' hspace='0' vspace='0' border='0' alt='expand section' />";
	     label.innerHTML="<B>CSV Format<B>";
	   } else {
	      obj1.style.display="block";
	      obj2.style.display="none";
	      key.innerHTML="<img src='images/icon-list-close.gif' width='12' height='12' hspace='0' vspace='0' border='0' alt='collapse section' />";
	      label.innerHTML="<B>New Line Format<B>";
	   }
}

function hide(item){
	   obj=document.getElementById(item);
       obj.style.display='none';
}

function show(item){
	   obj=document.getElementById(item);
       obj.style.display='block';
}

function selected(id){
	obj = document.getElementById(id);
	obj.style.backgroundColor = 'yellow';
}

function popUp(URL) {
 window.open(URL, '_blank', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,scrollbars=1,width=1200,height=600,left = 240,top = 312');
	}

function openRejectComment(id){
	obj = document.getElementById(id);
	obj.style.display="block";
}

function checkReject(){
	if(document.myForm.rejectComment.value==''){
		alert("Please provide an explanation for rejecting these assessments");
		document.myForm.erjectComment.focus();
		return false;
	}
	return TRUE;
}

function getDateFormat(){
	var reply = prompt("Please enter Date Format for ","");
	return reply;
}

function pa_nep_accounts_validate(){
	var x=document.forms["defineForm"]["ACCOUNT_NAME"].value;
	var y=document.forms["defineForm"]["ACTIVE"].value;
	if (x==null || x=="")
	  {
	  alert("Please enter an account name");
	  return false;
	  }
	if (y==null || y=="")
	{
	alert("Active/Inactive?");
	return false;
	}
}

function confirmDeleteUploadLog(fileName, timeStamp, pageName){
	if( confirm('Please confirm you wish to delete the file ' + fileName + '\nUploaded on ' + timeStamp)){
		window.location = pageName;
	}
}

function confirmDeleteBoardedAccount(account, pageName){
	if( confirm('Please confirm you wish to delete ' + account)){
		window.location = pageName;
	}
}
