<?php
namespace rest;

use itdq\DbRecord;
use itdq\FormClass;

/**
 *
 * @author gb001399
 *
 */
class staticValueStreamBusinessUnitRecord extends DbRecord
{
    protected $VALUE_STREAM;
    protected $BUSINESS_UNIT;

    function displayForm($mode){
        $allButtons = array();
        ?>
        <form id='valueStreamBusinessUnitForm' class="form-horizontal"  method='post'>
        <div class="form-group ">
			<div class='required'>
        		<label for="VALUE_STREAM" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Value Stream">Value Stream</label>
        		<div class="col-md-6">
					<select class='form-control select'
						id='VALUE_STREAM'
						name='VALUE_STREAM'
						required='required'
						data-placeholder="Select Value Stream" 
						data-allow-clear="true"
					>
						<option value=''>Select Value Stream<option>
					</select>
        		</div>
        	</div>
        </div>
        <div class="form-group ">
			<div class='required'>
        		<label for="BUSINESS_UNIT" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Business Unit">Business Unit</label>
        		<div class="col-md-6">
					<select class='form-control select'
						id='BUSINESS_UNIT'
						name='BUSINESS_UNIT'
						required='required'
						data-placeholder="Select Business Unit" 
						data-allow-clear="true"
					>
						<option value=''>Select Business Unit<option>
					</select>
        		</div>
        	</div>
        </div>
        <?php
   		$this->formHiddenInput('mode',$mode,'mode');

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateValueStreamBusinessUnit',null,'Update') :  $this->formButton('submit','Submit','saveValueStreamBusinessUnit',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetValueStreamBusinessUnit',null,'Reset','btn-warning');
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