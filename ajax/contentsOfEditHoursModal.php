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
// $resourceTotalHrs = $resourceHoursTable->getTotalHoursForRequest($_POST['resourceReference']);


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
           <input type='hidden' id='startDateWas' name='startDateWas' value='' />
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
       <div class='form-group total-hours-group'>
       <label for="ModalTOTAL_HOURS" class="col-md-2 control-label" data-toggle="tooltip" data-placement="top" title="">Total Hours</label>
       <div class="col-md-4">
       <input type='number' step='0.01' min=0 max=50 class="form-control" id="ModalTOTAL_HOURS" name="ModalTOTAL_HOURS" value="" placeholder="Total Hours" >
       <input type='hidden' id='originalTotalHours' >
       <input type='hidden' id='ModalHOURS_TYPE' name='ModalHOURS_TYPE' >
       </div>
       </div>
	</div>

    <div class='row'>
    <div class='col-sm-2'></div>
       <div class='col-sm-8'>
       <button type="button" class="btn btn-sm btn-warning  " id='reinitialise' data-toggle='tooltip' data-placement='top' title='Using the Start Date, End Date and Total Hours from this form, will reset the hours profile for the request' >Re-Initialise</button>
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
      <input type='number' step='0.01' min=0 max=50 class="form-control hrsForWeek" id="ModalHRSForWeek<?=$week?>" name="ModalHRSForWeek<?=$week?>" value="<?=$hours;?>" placeholder="Hrs/Week" >
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

?><button type="button" disabled class="btn btn-warning" id='saveAdjustedHoursWithDelta' data-toggle='tooltip' data-placement='top' title='feature temporarily suspended.'>Auto-Delta</button><?php
?><button type="button" class="btn btn-primary" id='saveAdjustedHours' title=''>Adjust Hrs Profile</button><?php
?><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
<?php
$modalFooter = ob_get_clean();
ob_start();

$end = microtime(true);
$elapsed = ($end-$start);
$result = array('editResourceHours'=>$modalBody,'editResourceHoursFooter'=>$modalFooter,'start'=>$start,'end'=>$end,'elapsed'=>$elapsed);
echo json_encode($result);

