<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;
use rest\resourceRequestHoursTable;
use rest\rfsTable;
use rest\rfsRecord;

set_time_limit(0);
ob_start();

$rfsTable = new rfsTable(allTables::$RFS);
$rfsRecord = new rfsRecord();

$resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
$resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
$resourceRecord = new resourceRequestRecord();

$resourceRecord->setFromArray($resourceTable->getWithPredicate(" RESOURCE_REFERENCE='" . trim($_POST['resourceReference']) . "' "));

$ctb_subserviceervice = $resourceRecord->get('CTB_SUB_SERVICE');
$bulkWorkOrder = trim($ctb_subserviceervice) == resourceRequestRecord::$bulkWorkOrder ? true : false;


$parentBWO = $resourceRecord->get('PARENT_BWO');
$clonedFromBwo = !empty($parentBWO) ? true : false;

$resourceHoursRs = $resourceHoursTable->getRsWithPredicate(" RESOURCE_REFERENCE='" . trim($_POST['resourceReference']) . "' ");
$resourceTotalHrs = $resourceHoursTable->getTotalHoursForRequest($_POST['resourceReference']);

$startDate = new DateTime($resourceRecord->get('START_DATE'));
$endDate = new DateTime($resourceRecord->get('END_DATE'));

$rfsData = $rfsTable->getWithPredicate(" RFS_ID='". trim($resourceRecord->get('RFS')) . "' ");
$rfsRecord->setFromArray($rfsData);

// ob_clean();

ob_start();

?>

<div class="container-fluid">
<h2>Edit Hours for Resource Request</h2>


<div class='row'>
<div class='col-md-6'><h5><b>RFS:</b><?=trim($resourceRecord->get('RFS'));?></h5></div>
<div class='col-md-6'><h5><b>PRN:</b><?=trim($rfsRecord->get('PRN'));?></h5></div>
</div>
<div class='row'>
<div class='col-md-6'><h5><b>CIO:</b><?=trim($rfsRecord->get('CIO'));?></h5></div>
<div class='col-md-6'><h5><b>Phase:</b><?=trim($resourceRecord->get('PHASE'));?></h5></div>
</div>

<div class='row'>
<div class='col-md-12'><h5><b>CTB Sub Service:</b><?=trim($resourceRecord->get('CTB_SUB_SERVICE'));?></h5></div>
</div>
<div class='row'>
<div class='col-md-12'><h5><b>Resource Name:</b><?=trim($resourceRecord->get('RESOURCE_NAME'));?></h5></div>
</div>

<form id='resourceHoursForm'>
   <input type='hidden' name='ModalResourceReference' id='ModalResourceReference' value='<?=trim($resourceRecord->get('RESOURCE_REFERENCE'))?>' />
   <div class='row'>
   <div class='form-group' >
     <div id='ModalSTART_DATE" . "FormGroup' >
     <label for='ModalSTART_DATE' class='col-md-1 control-label ' data-toggle='tooltip' data-placement='top' title=''>Start Date</label>
       <div class='col-md-4'>
         <div id='calendarFormGroupModalSTART_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='ModalSTART_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
           <input id='InputModalSTART_DATE' class='form-control' type='text' readonly value='' placeholder='Select From' required />
           <input type='hidden' id='ModalSTART_DATE' name='ModalSTART_DATE' value='<?=trim($resourceRecord->get('START_DATE'));?>' />
           <span class='input-group-addon'><span id='calendarIconModalSTART_DATE' class='glyphicon glyphicon-calendar'></span></span>
           </div>
       </div>
   </div>

     <div id='ModalEND_DATE" . "FormGroup' >
     <label for='ModalEND_DATE' class='col-md-1 control-label ' data-toggle='tooltip' data-placement='top' title=''>End Date</label>
       <div class='col-md-4'>
         <div id='calendarFormGroupModalEND_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='ModalEND_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
           <input id='InputModalEND_DATE' class='form-control' type='text' readonly value='' placeholder='Select To' required />
           <input type='hidden' id='ModalEND_DATE' name='ModalEND_DATE' value='<?=$resourceRecord->get('END_DATE');?>' />
           <input type='hidden' id='endDateWas' name='endDateWas' value='<?=$resourceRecord->get('END_DATE');?>' />
           <span class='input-group-addon'><span id='calendarIconModalEND_DATE' class='glyphicon glyphicon-calendar'></span></span>
           </div>
       </div>
   </div>
   </div>
  </div>

   <div class='row'>
       <div class='form-group'>
       <label for="ModalHRS_PER_WEEK" class="col-md-1 control-label" data-toggle="tooltip" data-placement="top" title="">Hrs per Week</label>
       <div class="col-md-4">
       <input type='number' step='0.1' min=0 max=50 class="form-control" id="ModalHRS_PER_WEEK" name="ModalHRS_PER_WEEK" value="<?=$resourceRecord->get('HRS_PER_WEEK');?>" placeholder="Avg hrs/Week" >
       </div>

       <label for="ModalTOTAL_HRS" class="col-md-1 control-label" data-toggle="tooltip" data-placement="top" title="">Total Hours</label>
       <div class="col-md-4">
       <input type='text' class="form-control" id="ModalTOTAL_HRS" name="ModalTOTAL_HRS" value="<?=$resourceTotalHrs;?>" placeholder="Total Hours" disabled=true>
       </div>
       </div>
	</div>

    <div class='row'>
    <div class='col-sm-2'></div>
       <div class='col-sm-8'>

        <?php
        if(!$clonedFromBwo && !$bulkWorkOrder){
            ?><button type="button" class="btn btn-sm btn-warning  " id='slipStartDate'>Move Start Date</button><?php
            ?><button type="button" class="btn btn-sm btn-warning  " id='reinitialise'>Re-Initialise</button><?php
            ?><button type="button" class="btn btn-sm btn-warning  " id='moveEndDate'>Change End Date</button><?php
        }
        ?>
       </div>
    <div class='col-sm-2'></div>
    </div>


<div class='form-horizontal'>
<?php
$resourceRecord->get('START_DATE');
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

if(trim($resourceRecord->get('CTB_SUB_SERVICE'))==trim(resourceRequestRecord::$bulkWorkOrder)){
    ?><button type="button" class="btn btn-warning" id='saveAdjustedHoursWithDrawDown'>Draw Down</button><?php
} elseif(!$clonedFromBwo) { // If this a drawn down - they can't addjust the hours.
    ?><button type="button" class="btn btn-warning" id='saveAdjustedHoursWithDelta'>Auto-Delta</button><?php
    ?><button type="button" class="btn btn-primary" id='saveAdjustedHours'>Adjust Hrs Profile</button><?php
}

?>
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
<?php
$modalFooter = ob_get_clean();
$result = array('editResourceHours'=>$modalBody,'editResourceHoursFooter'=>$modalFooter);
echo json_encode($result);

