<?php
namespace rest;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;
use itdq\JavaScript;
use rest\traits\recordTrait;
use rest\traits\resourceRequestRecordTrait;

/**
 *
 * @author gb001399
 *
 */
class resourceRequestRecord extends DbRecord
{
    use recordTrait, resourceRequestRecordTrait;

    protected $RESOURCE_REFERENCE;
    protected $RFS;
    protected $ORGANISATION; // was known as CURRENT_PLATFORM
    protected $SERVICE;
    protected $DESCRIPTION;
    protected $START_DATE;
    protected $END_DATE;
    protected $RESOURCE_NAME;
    protected $RR_CREATOR;
    protected $RR_CREATED_TIMESTAMP;
    protected $CLONED_FROM;
    protected $STATUS;
    protected $RATE_TYPE;
    protected $HOURS_TYPE;
    protected $HRS_PER_WEEK;
    protected $TOTAL_HOURS; // ALTER TABLE "REST_DEV"."RESOURCE_REQUESTS" ADD COLUMN "TOTAL_HOURS" DECIMAL(8,2);
    protected $EMAIL_ADDRESS;
    protected $KYN_EMAIL_ADDRESS;

    static public $columnHeadings = array(
        "Resource Ref", 
        "RFS", 
        "Organisation", 
        "Service",
        "Description",
        "Start Date", 
        "End Date", 
        "Total Hours", 
        "Resource Name",
        "Resource Email Address",
        "Resource Kyndryl Email Address",
        // "Business Units Match",
        "Request Creator", 
        "Request Created",
        "Cloned From", 
        "Status",
        "Rate Type of Resource Request",
        "Hours Type"
    );

    CONST STATUS_NEW        = 'New';
//     CONST STATUS_REDIRECTED = 'Re-Directed';
//     CONST STATUS_PLATFORM   = 'With Platform Team';
//     CONST STATUS_REQUESTOR  = 'With Requestor';
    CONST STATUS_ASSIGNED   = 'Assigned';
    CONST STATUS_COMPLETED  = 'Completed';

    static public $allStatus = array(self::STATUS_NEW,self::STATUS_ASSIGNED,self::STATUS_COMPLETED);
    
    CONST RATE_TYPE_BLENDED      = 'Blended';
    CONST RATE_TYPE_PROFESSIONAL = 'Professional';
    static public $allRateTypes = array(self::RATE_TYPE_BLENDED,self::RATE_TYPE_PROFESSIONAL);
    
    CONST HOURS_TYPE_REGULAR      = 'Regular';
    CONST HOURS_TYPE_OT_WEEK_DAY  = 'Weekday Overtime';
    CONST HOURS_TYPE_OT_WEEK_END  = 'Weekend Overtime';
    static public $allHourTypes   = array(self::HOURS_TYPE_REGULAR,self::HOURS_TYPE_OT_WEEK_DAY,self::HOURS_TYPE_OT_WEEK_END);

    function displayForm($mode)
    {
        $notEditable = $mode==FormClass::$modeEDIT ? ' disabled ' : null;
        $readOnly = $mode==FormClass::$modeDISPLAY ? ' disabled ' : null;
        if ($mode==FormClass::$modeDISPLAY) {
            $notEditable = 'disabled';
        }
        ?>
        <form id='resourceRequestForm' class="form-horizontal"  method='post'>
        <?php
        $this->additional_comments = null;

        $loader = new Loader();
        $rfsPredicate = rfsTable::rfsPredicateFilterOnPipelineNotArchived();
        $allRfs = $loader->load('RFS_ID',allTables::$RFS,$rfsPredicate);

        // $predicate = " STATUS='" . staticOrganisationTable::ENABLED . "' ";
        // $allOrganisation = $loader->load('ORGANISATION',allTables::$STATIC_ORGANISATION,$predicate);
        // $allService = staticOrganisationTable::getAllOrganisationsAndServices($predicate);
        // JavaScript::buildSelectArray($allService, 'organisation');

        $startDate = empty($this->START_DATE) ? null : new \DateTime($this->START_DATE);
        $startDateStr = empty($startDate) ? null : $startDate->format('d M y');
        $startDateStr2 = empty($startDate) ? null : $startDate->format('Y-m-d');
        $endDate = empty($this->END_DATE) ? null : new \DateTime($this->END_DATE);
        $endDateStr = empty($endDate) ? null : $endDate->format('d M y');
        $endDateStr2 = empty($endDate) ? null : $endDate->format('Y-m-d');
        
        $this->STATUS = empty($this->STATUS) ? 'New' : $this->STATUS;

        ?>
   		<div class='form-group required'>

            <label for='RFS' class='col-md-2 control-label ceta-label-left'>RFS ID</label>
        	<div class='col-md-3'>
              	<select class='form-control select' id='RFS'
                    name='RFS'
                    required='required'
                    data-placeholder="Select RFS id" data-allow-clear="true"
                    <?=$notEditable?>
                >
            	<option value=''>Select RFS<option>
                <?php
                    foreach ($allRfs as $key => $value) {
                         $displayValue = trim($key);
                         $returnValue  = trim($key);
                         ?><option value='<?=$returnValue?>' <?=trim($this->RFS)==$returnValue ? 'selected' : null?>><?=$displayValue?></option><?php
                    }
               ?>
               </select>
            </div>

        </div>

   		<div class='form-group' >
            <div id='START_DATEFormGroup' class='required'>
                <label for='START_DATE' class='col-md-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title=''>Start Date</label>
                <div class='col-md-3'>
                    <div id='calendarFormGroupSTART_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='START_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
                        <input id='InputSTART_DATE' class='form-control' type='text'  value='<?=$startDateStr?>' placeholder='Select Start Date' required  <?=$notEditable?>/>
                        <input type='hidden' id='START_DATE' name='START_DATE' value='<?=$startDateStr2?>' required/>
                        <!-- <input type='hidden' id='startDateWas' name='startDateWas' value='<?=$startDateStr2?>' required/> -->
                        <span class='input-group-addon'><span id='calendarIconSTART_DATE' class='glyphicon glyphicon-calendar'></span></span>
                    </div>
                </div>
            </div>

            <div id='END_DATEFormGroup' class='required'>
                <label for='END_DATE' class='col-md-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title=''>End Date</label>
                <div class='col-md-3'>
                    <div id='calendarFormGroupEND_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='END_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
                        <input id='InputEND_DATE' class='form-control' type='text'  value='<?=$endDateStr?>' placeholder='Select End Date' required <?=$notEditable?> />
                        <input type='hidden' id='END_DATE' name='END_DATE' value='<?=$endDateStr2?>' required />
                        <!-- <input type='hidden' id='endDateWas' name='endDateWas' value='<?=$endDateStr2?>' required/> -->
                        <span class='input-group-addon'><span id='calendarIconEND_DATE' class='glyphicon glyphicon-calendar'></span></span>
                    </div>
                </div>
            </div>
        </div>

   		<div class='form-group required'>
   		
            <div id='HrsPerWeekFormGroup'>
                <label for="TOTAL_HOURS" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="">Total Hours<br/><small>For this request</small></label>
                <div class="col-md-3">
                    <input type='number' step='0.01' min=0 max=6000 class="form-control" id="TOTAL_HOURS" name="TOTAL_HOURS" value="<?=$this->TOTAL_HOURS?>" placeholder="Total Hrs For RFS" <?=$notEditable?> required >
                    <input id="originalTotal_Hours" name="originalTotal_Hours" value="<?=$this->TOTAL_HOURS?>" type="hidden">
                </div>
            </div>
            
            <div id='HrsRateFormGroup'>
                <label class="col-md-offset-1 col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="">Rate Type</label>
                <div class="col-md-4">
                    <?php 
                    $this->RATE_TYPE = empty($this->RATE_TYPE) ? self::RATE_TYPE_BLENDED : $this->RATE_TYPE;
                    $disabledRateType = empty($this->RESOURCE_NAME) ? null : 'disabled';
                    if ($mode==FormClass::$modeDISPLAY) {
                        $disabledRateType = 'disabled';
                    }
                    foreach (self::$allRateTypes as $rateType) {
                        $checked = $rateType == $this->RATE_TYPE ? ' checked ' : null;
                        ?><label class="radio-inline"><input type="radio" name="RATE_TYPE" value='<?=$rateType?>' required <?=$checked;?> <?=$disabledRateType;?>><?=$rateType?></label><?php 
                    }
                    ?>
                    <input id="originalRateType" name="originalRateType" value="<?=$this->RATE_TYPE?>" type="hidden">
                </div>						 
            </div>

   		</div>
   		
        <div class='form-group required'>
   		    <div id='HrsTypeFormGroup'>
                <label class="col-md-offset-5 col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="">Hours Type</label>
                <div class="col-md-5">
                    <?php 
                    $this->HOURS_TYPE = empty($this->HOURS_TYPE) ? self::HOURS_TYPE_REGULAR : $this->HOURS_TYPE;
                    $disabledHoursType = empty($this->RESOURCE_NAME) ? null : 'disabled';
                    if ($mode==FormClass::$modeDISPLAY) {
                        $disabledHoursType = 'disabled';
                    }
                    foreach (self::$allHourTypes as $hoursType) {
                        $checked = $hoursType == $this->HOURS_TYPE ? ' checked ' : null;
                        ?><label class="radio-inline"><input type="radio" name="HOURS_TYPE" value='<?=$hoursType?>' required <?=$checked?> <?=$disabledHoursType;?>><?=$hoursType?></label><?php 
                    }
                    ?>
                    <input id="originalHoursType" name="originalHoursType" value="<?=$this->HOURS_TYPE?>" type="hidden">
                </div>						 
            </div>
        </div>

        <div class='form-group required'>
	       	<label for='ORGANISATION' class='col-md-2 control-label ceta-label-left'>Organisation</label>
    	       	<div class='col-md-3'>
                <select class='form-control select'
                    id='ORGANISATION'
                    name='ORGANISATION'
                    required='required'
                    data-placeholder="Select Organisation" 
                    data-allow-clear="true"
                    <?=$readOnly?>
                >
                    <option value=''>Select Organisation<option>
                </select>
                </div>
			<?php
			$disabledSubService = isset($this->ORGANISATION) && isset($this->SERVICE) ? null : 'disabled';
            if ($mode==FormClass::$modeDISPLAY) {
                $disabledSubService = 'disabled';
            }
			?>

          <label for='SERVICE' class='col-md-2 control-label ceta-label-left'>Service</label>
               <div class='col-md-4'>
               <select class='form-control select' id='SERVICE'
                    name='SERVICE'
                    required='required'
                    data-placeholder="Select Service"
                    data-allow-clear="true"
                    <?=$disabledSubService;?>
                >
                <option value=''>Select Service<option>
              </select>
              </div>
        </div>
        <?php
        if ($mode==FormClass::$modeDISPLAY) {
            $descriptionState = "READONLY";
        } else {
            $descriptionState = null;
        }
        $this->formTextArea('Description', 'DESCRIPTION', $descriptionState, null, null,2000, 'top',null, 10, "High level description of work required");
        
        include_once 'includes/formMessageArea.html';
		
        $this->formHiddenInput('mode',$mode,'mode');
        // $token = $_SESSION['formToken'];
        // $this->formHiddenInput('formToken',$token,'formToken');
   		$this->formHiddenInput('RESOURCE_REFERENCE',$this->RESOURCE_REFERENCE,'RESOURCE_REFERENCE');
        $this->formHiddenInput('RESOURCE_NAME',$this->RESOURCE_NAME,'RESOURCE_NAME');
   		$this->formHiddenInput('STATUS',$this->STATUS,'STATUS');
        $this->formHiddenInput('originalORGANISATION',$this->ORGANISATION,'originalORGANISATION');
        $this->formHiddenInput('originalSERVICE',$this->SERVICE,'originalSERVICE');
        $rrCreator = $mode==FormClass::$modeEDIT ? $this->RR_CREATOR : $_SESSION['ssoEmail'];
		$this->formHiddenInput('RR_CREATOR',$rrCreator,'RR_CREATOR');
   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateResourceRequest',null,'Update') :  $this->formButton('submit','Submit','saveResourceRequest',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetResourceRequest',null,'Reset','btn-warning');
   		$allButtons = array();
        $allButtons[] = $submitButton;
   		$allButtons[] = $resetButton;
   		?>
   		<div class='col-md-2'></div>
   		<?php
            if ($mode!==FormClass::$modeDISPLAY) {
                $this->formBlueButtons($allButtons);
            }
   		?>
	</form>
    <?php
    }
}