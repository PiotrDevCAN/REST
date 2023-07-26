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
class staticBespokeRateRecord extends DbRecord
{
    use recordTrait;

	public $BESPOKE_RATE_ID;
    public $RFS_ID;
    public $RESOURCE_REFERENCE;
    public $RESOURCE_TYPE_ID;
    public $PS_BAND_ID;

	function displayForm($mode){
        $allButtons = array();
        ?>
        <form id='bespokeRateForm' class="form-horizontal"  method='post'>
        <div class="form-group ">
			<div class='required'>
        		<label for='RFS_ID' class='col-md-2 control-label ceta-label-left'>RFS</label>
    	       	<div class='col-md-6'>
                <select class='form-control select'
                    id='RFS_ID'
                    name='RFS_ID'
                    required='required'
                    data-placeholder="Select RFS" 
                    data-allow-clear="true"
                >
                    <option value=''>Select RFS<option>
                </select>
                </div>
        	</div>
        </div>
        <div class="form-group ">
			<div class='required'>
        		<label for='RESOURCE_REFERENCE' class='col-md-2 control-label ceta-label-left'>Resource Request</label>
    	       	<div class='col-md-6'>
                <select class='form-control select'
                    id='RESOURCE_REFERENCE'
                    name='RESOURCE_REFERENCE'
                    required='required'
                    data-placeholder="Select Resource Request" 
                    data-allow-clear="true"
                    disabled
                >
                    <option value=''>Select Resource Request<option>
                </select>
                </div>
        	</div>
        </div>
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
                    disabled
                >
                    <option value=''>Select PS Band<option>
                </select>
                </div>
        	</div>
        </div>
        <?php
        include_once 'includes/formMessageArea.html';
        
   		$this->formHiddenInput('ID','','BESPOKE_RATE_ID');
		$this->formHiddenInput('mode',$mode,'mode');

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateResourceRate',null,'Update') :  $this->formButton('submit','Submit','saveBespokeRate',null,'Submit');
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