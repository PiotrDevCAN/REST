<?php
namespace rest;

use itdq\DbRecord;
use itdq\FormClass;

/**
 *
 * @author gb001399
 *
 */
class staticValueStreamRecord extends DbRecord
{
    protected $VALUE_STREAM;
    protected $BUSINESS_UNIT;

    function displayForm($mode){
        $allButtons = array();
        ?>
        <form id='valueStreamForm' class="form-horizontal"  method='post'>
        <div class="form-group ">
			<div class='required'>
        		<label for="VALUE_STREAM" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Value Stream">Value Stream</label>
        		<div class="col-md-6">
        		<input class="form-control" id="VALUE_STREAM" name="VALUE_STREAM" value="<?=$this->VALUE_STREAM?>" placeholder="Enter Value Stream" required="required" type="text" >
        		</div>
        	</div>
        </div>
        <div class="form-group ">
			<div class='required'>
        		<label for="BUSINESS_UNIT" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Business Unit">Business Unit</label>
        		<div class="col-md-6">
        		<input class="form-control" id="BUSINESS_UNIT" name="BUSINESS_UNIT" value="<?=$this->BUSINESS_UNIT?>" placeholder="Enter Business Unit" required="required" type="text" >
        		</div>
        	</div>
        </div>
        <?php
   		$this->formHiddenInput('mode',$mode,'mode');

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateValueStream',null,'Update') :  $this->formButton('submit','Submit','saveValueStream',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetValueStream',null,'Reset','btn-warning');
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