<?php
namespace rest;

use itdq\DbRecord;
use itdq\FormClass;

/**
 *
 * @author gb001399
 *
 */
class staticBusinessUnitRecord extends DbRecord
{
    protected $BUSINESS_UNIT;

    function displayForm($mode){
        $allButtons = array();
        ?>
        <form id='businessUnitForm' class="form-horizontal"  method='post'>
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

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateBusinessUnit',null,'Update') :  $this->formButton('submit','Submit','saveBusinessUnit',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetBusinessUnit',null,'Reset','btn-warning');
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