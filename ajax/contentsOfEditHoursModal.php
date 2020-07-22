<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;
use rest\resourceRequestHoursTable;
use rest\rfsTable;
use rest\rfsRecord;

set_time_limit(0);
ob_start();

$start = microtime(True);

$resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);

$resourceHoursRs  = $resourceHoursTable->getRsWithPredicate(" RESOURCE_REFERENCE='" . trim($_POST['resourceReference']) . "' ");
$resourceTotalHrs = $resourceHoursTable->getTotalHoursForRequest($_POST['resourceReference']);

ob_start();
?>

<div class="container-fluid">
<h2>Edit Hours for Resource Request</h2>


<div class='row'>
<div class='col-md-3'><div class="pull-right"><p><b>RFS:</b></p></div></div>
<div class='col-md-3'><p><span id='editHoursRfs'></span></p></div>

<div class='col-md-3'><div class="pull-right"><p><b>PRN:</b></p></div></div>
<div class='col-md-3'><p><span id='editHoursPrn'></span></p></div>
</div>
<div class='row'>
<div class='col-md-3'><div class="pull-right"><p><b>Value Stream:</b></p></div></div>
<div class='col-md-3'><p><span id='editHoursValueStream'></span></p></div>

<div class='col-md-3'><div class="pull-right"><p><b>Phase:</b></p></div></div>
<div class='col-md-3'><p><span id='editHoursPhase'></span></p></div>
</div>
<div class='row'>
<div class='col-md-3'><div class="pull-right"><p><b>Service:</b></p></div></div>
<div class='col-md-9'><p><span id='editHoursService'></span><br/><small>(<span id='editHoursSubService'></span>)</small></p></div>

<!-- <div class='col-md-2'><div class="pull-right"><p><b>Sub Service:</b></p></div></div> -->
<!-- <div class='col-md-4'><p><small></small></p></div> -->
</div>
<div class='row'>
<div class='col-md-3'><div class="pull-right"><p><b>Resource Name:</b></p></div></div>
<div class='col-md-9'><p><span id='editHoursResourceName'></span></p></div>
</div>

<hr/>

<form id='resourceHoursForm'>
   <input type='hidden' name='ModalResourceReference' id='ModalResourceReference' value='<?=trim($_POST['resourceReference'])?>' />
   <div class='row'>
   <div class='form-group' >
     <div id='ModalSTART_DATE" . "FormGroup' >
     <label for='ModalSTART_DATE' class='col-md-2 control-label ' data-toggle='tooltip' data-placement='top' title=''>Start Date</label>
       <div class='col-md-4'>
         <div id='calendarFormGroupModalSTART_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='ModalSTART_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
           <input id='InputModalSTART_DATE' class='form-control' type='text' readonly value='' placeholder='Select From' required />
           <input type='hidden' id='ModalSTART_DATE' name='ModalSTART_DATE' value='' />
           <span class='input-group-addon'><span id='calendarIconModalSTART_DATE' class='glyphicon glyphicon-calendar'></span></span>
           </div>
       </div>
   </div>

     <div id='ModalEND_DATE" . "FormGroup' >
     <label for='ModalEND_DATE' class='col-md-2 control-label ' data-toggle='tooltip' data-placement='top' title=''>End Date</label>
       <div class='col-md-4'>
         <div id='calendarFormGroupModalEND_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='ModalEND_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
           <input id='InputModalEND_DATE' class='form-control' type='text' readonly value='' placeholder='Select To' required />
           <input type='hidden' id='ModalEND_DATE' name='ModalEND_DATE' value='' />
           <input type='hidden' id='endDateWas' name='endDateWas' value='' />
           <span class='input-group-addon'><span id='calendarIconModalEND_DATE' class='glyphicon glyphicon-calendar'></span></span>
           </div>
       </div>
   </div>
   </div>
  </div>

   <div class='row'>
       <div class='form-group'>
       <label for="ModalHRS_PER_WEEK" class="col-md-2 control-label" data-toggle="tooltip" data-placement="top" title="">Hrs per Week</label>
       <div class="col-md-4">
       <input type='number' step='0.1' min=0 max=50 class="form-control" id="ModalHRS_PER_WEEK" name="ModalHRS_PER_WEEK" value="" placeholder="Avg hrs/Week" >
       </div>

       <label for="ModalTOTAL_HRS" class="col-md-2 control-label" data-toggle="tooltip" data-placement="top" title="">Total Hours</label>
       <div class="col-md-4">
       <input type='text' class="form-control" id="ModalTOTAL_HRS" name="ModalTOTAL_HRS" value="<?=$resourceTotalHrs;?>" placeholder="Total Hours" disabled >
       </div>
       </div>
	</div>

    <div class='row'>
    <div class='col-sm-2'></div>
       <div class='col-sm-8'>

       <button type="button" class="btn btn-sm btn-warning  " id='slipStartDate' disabled data-toggle='tooltip' data-placement='top' title='Will amend the Start Date of the Request, keeping the "profile" of the hours/week the same. Used to slip a request to a new start date, whilst maintaining the hours profile.' >Move Start Date</button>
       <button type="button" class="btn btn-sm btn-warning  " id='reinitialise' data-toggle='tooltip' data-placement='top' title='Using the supplied Start Date, End Date and Avg Hrs per Week, will reset the hours profile for the request' >Re-Initialise</button>
       <button type="button" class="btn btn-sm btn-warning  " id='moveEndDate' disabled data-toggle='tooltip' data-placement='top' title='Will amend the End Date of the Request, either deleting weeks or adding weeks as appropriate' >Change End Date</button>
       </div>
    <div class='col-sm-2'></div>
    </div>


<div class='form-horizontal'>
<?php

$monthColours = array(1=>'#bdbdbd',2=>'#eeeeee',3=>'#bdbdbd',4=>'#eeeeee',5=>'#bdbdbd',6=>'#eeeeee',7=>'#bdbdbd',8=>'#eeeeee',9=>'#bdbdbd',10=>'#eeeeee',11=>'#bdbdbd',12=>'#eeeeee',);
$claimMonths = array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec',);



while (($row = db2_fetch_assoc($resourceHoursRs))==true){
    $week = $row['DATE'];
    $hours = $row['HOURS'];

    $stripe = $monthColours[$row['CLAIM_MONTH']];
    $weekObj = new DateTime($week);
    ?>

    <div id='ModalHrsForWeekFormGroup<?=$week?>' class='form-group' style='background:<?=$stripe?>'>
     <label for='ModalHRSForWeek<?=$week?>' class='col-md-6 control-label ' data-toggle='tooltip' data-placement='top' title='Hours for week <?=$week?>'><?=$weekObj->format('\W\e\e\k W - dS M y')?></label>
       <div class='col-md-3'>
      <input type='number' step='0.1' min=0 max=50 class="form-control" id="ModalHRSForWeek<?=$week?>" name="ModalHRSForWeek<?=$week?>" value="<?=$hours;?>" placeholder="Hrs/Week" >
      </div>
      <div class='col-md-3'>
      <p>Claim: <?=$claimMonths[$row['CLAIM_MONTH']]?></p>
      </div>
    </div>
    <?php
}
?>
</div>
</form>
</div>
<?php

$modalBody = ob_get_clean();
ob_start();

?><button type="button" class="btn btn-warning" id='saveAdjustedHoursWithDelta' data-toggle='tooltip' data-placement='top' title='Will save this request with the adjusted hours BUT also create a new request for the hours that have been removed from this request'>Auto-Delta</button><?php
?><button type="button" class="btn btn-primary" id='saveAdjustedHours' data-toggle='tooltip' data-placement='top' title='Will save the adjusted hours/week, without changing the Start or End Dates.'>Adjust Hrs Profile</button><?php
?><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
<?php
$modalFooter = ob_get_clean();
ob_start();

$end = microtime(true);
$elapsed = ($end-$start);
$result = array('editResourceHours'=>$modalBody,'editResourceHoursFooter'=>$modalFooter,'start'=>$start,'end'=>$end,'elapsed'=>$elapsed);
echo json_encode($result);

