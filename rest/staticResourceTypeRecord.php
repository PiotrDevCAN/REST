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
class staticResourceTypeRecord extends DbRecord
{
    use recordTrait;

    public $RESOURCE_TYPE_ID;
	public $RESOURCE_TYPE;
	public $HRS_PER_DAY;

	function displayForm($mode){
        $allButtons = array();
        ?>
        <form id='resourceTypeForm' class="form-horizontal"  method='post'>
        <div class="form-group ">
			<div class='required'>
        		<label for="RESOURCE_TYPE" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Country">Resource Type</label>
        		<div class="col-md-6">
        		<input class="form-control" id="RESOURCE_TYPE" name="RESOURCE_TYPE" value="<?=$this->RESOURCE_TYPE?>" placeholder="Enter Resource Type" required="required" type="text" >
        		</div>
        	</div>
        </div>
		<div class="form-group ">
			<div class='required'>
        		<label for="HRS_PER_DAY" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Country">Hours per Day</label>
        		<div class="col-md-6">
        		<input class="form-control" id="HRS_PER_DAY" name="HRS_PER_DAY" value="<?=$this->HRS_PER_DAY?>" placeholder="Enter Hours per Day" required="required" type="text" >
        		</div>
        	</div>
		</div>
        <?php
		$this->formHiddenInput('RESOURCE_TYPE_ID','','RESOURCE_TYPE_ID');
   		$this->formHiddenInput('mode',$mode,'mode');

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateResourceType',null,'Update') :  $this->formButton('submit','Submit','saveResourceType',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetResourceType',null,'Reset','btn-warning');
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