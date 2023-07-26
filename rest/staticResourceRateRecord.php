<?php
namespace rest;

use itdq\DbRecord;
use itdq\FormClass;
use rest\traits\recordTrait;

/**
 *
 * @author gb001399
 *
 */
class staticResourceRateRecord extends DbRecord
{
    use recordTrait;

	public $ID;
    public $RESOURCE_TYPE_ID;
	public $PS_BAND_ID;
	public $BAND_ID;
	public $TIME_PERIOD_START;
	public $TIME_PERIOD_END;
	public $DAY_RATE;
	public $HOURLY_RATE;

	function displayForm($mode){
        $allButtons = array();

		$startDate = empty($this->TIME_PERIOD_START) ? null : new \DateTime($this->TIME_PERIOD_START);
		$startDateStr = empty($startDate) ? null : $startDate->format('d M y');
        $startDateStr2 = empty($startDate) ? null : $startDate->format('Y-m-d');
		
		$endDate = empty($this->TIME_PERIOD_END) ? null : new \DateTime($this->TIME_PERIOD_END);
		$endDateStr = empty($endDate) ? null : $endDate->format('d M y');
        $endDateStr2 = empty($endDate) ? null : $endDate->format('Y-m-d');
        ?>
        <form id='resourceRateForm' class="form-horizontal"  method='post'>
        <div class="form-group ">
			<div class='required'>
        		<label for='RESOURCE_TYPE_ID' class='col-md-2 control-label ceta-label-left'>Resource Type</label>
    	       	<div class='col-md-6'>
                <select class='form-control select'
                    id='RESOURCE_TYPE_ID'
                    name='RESOURCE_TYPE_ID'
                    required='required'
                    data-placeholder="Select Resource Type" 
                    data-allow-clear="true"
                >
                    <option value=''>Select Resource Type<option>
                </select>
                </div>
        	</div>
        </div>
		<div class="form-group ">
			<div class='required'>
			<label for='PS_BAND_ID' class='col-md-2 control-label ceta-label-left'>PS Band</label>
    	       	<div class='col-md-6'>
                <select class='form-control select'
                    id='PS_BAND_ID'
                    name='PS_BAND_ID'
                    required='required'
                    data-placeholder="Select PS Band" 
                    data-allow-clear="true"
                >
                    <option value=''>Select PS Band<option>
                </select>
                </div>
        	</div>
        </div>
		<!-- <div class="form-group ">
			<div class='required'>
			<label for='BAND_ID' class='col-md-2 control-label ceta-label-left'>Band</label>
    	       	<div class='col-md-6'>
                <select class='form-control select'
                    id='BAND_ID'
                    name='BAND_ID'
                    required='required'
                    data-placeholder="Select Band" 
                    data-allow-clear="true"
                >
                    <option value=''>Select Band<option>
                </select>
                </div>
        	</div>
        </div> -->
		<div class="form-group ">
			<div class='required'>
				<label for='TIME_PERIOD_START' class='col-md-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title=''>Time Period Start</label>
                <div class='col-md-6'>
                    <div id='calendarFormGroupTIME_PERIOD_START' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='TIME_PERIOD_START' data-link-format='yyyy-mm-dd-hh.ii.00'>
                        <input id='InputTIME_PERIOD_START' class='form-control' type='text'  value='<?=$startDateStr?>' placeholder='Select Time Period Start' required/>
                        <input type='hidden' id='TIME_PERIOD_START' name='TIME_PERIOD_START' value='<?=$startDateStr2?>' required/>
                        <span class='input-group-addon'><span id='calendarIconTIME_PERIOD_START' class='glyphicon glyphicon-calendar'></span></span>
                    </div>
                </div>
        	</div>
        </div>
		<div class="form-group ">
			<div class='required'>
				<label for='TIME_PERIOD_END' class='col-md-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title=''>Time Period End</label>
                <div class='col-md-6'>
                    <div id='calendarFormGroupTIME_PERIOD_END' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='TIME_PERIOD_END' data-link-format='yyyy-mm-dd-hh.ii.00'>
                        <input id='InputTIME_PERIOD_END' class='form-control' type='text'  value='<?=$endDateStr?>' placeholder='Select Time Period End' required/>
                        <input type='hidden' id='TIME_PERIOD_END' name='TIME_PERIOD_END' value='<?=$endDateStr2?>' required/>
                        <span class='input-group-addon'><span id='calendarIconTIME_PERIOD_END' class='glyphicon glyphicon-calendar'></span></span>
                    </div>
                </div>
        	</div>
        </div>
		<div class="form-group ">
			<div class='required'>
				<label for="DAY_RATE" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="">Day Rate</label>
                <div class="col-md-6">
                    <input type='number' step='0.01' min=0 class="form-control" id="DAY_RATE" name="DAY_RATE" value="<?=$this->DAY_RATE?>" placeholder="Day Rate" required >
                </div>
        	</div>
        </div>
		<div class="form-group ">
			<div class='required'>
				<label for="HOURLY_RATE" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="">Hourly Rate</label>
                <div class="col-md-6">
                    <input type='number' step='0.01' min=0 class="form-control" id="HOURLY_RATE" name="HOURLY_RATE" value="<?=$this->HOURLY_RATE?>" placeholder="Hourly Rate" required >
                </div>
        	</div>
        </div>
        <?php
        include_once 'includes/formMessageArea.html';

   		$this->formHiddenInput('ID','','ID');
		$this->formHiddenInput('mode',$mode,'mode');

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateResourceRate',null,'Update') :  $this->formButton('submit','Submit','saveResourceRate',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetResourceRate',null,'Reset','btn-warning');
   		$allButtons[] = $submitButton;
   		$allButtons[] = $resetButton;
   		?>
   		<div class='form-group'>
   		<div class='col-md-2'></div>
   		<div class='col-md-4'>
   		<?php
   		$this->formBlueButtons($allButtons);
   		?>
   		</div>
   		</div>
	</form>
    <?php

    }
}