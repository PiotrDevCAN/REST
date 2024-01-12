<?php
namespace rest;

use itdq\DbRecord;
use itdq\FormClass;

/**
 *
 * @author gb001399
 *
 */
class staticServiceRecord extends DbRecord
{

    public $SERVICE;

	function displayForm($mode){
        $allButtons = array();
        ?>
        <form id='serviceForm' class="form-horizontal"  method='post'>
        <div class="form-group ">
			<div class='required'>
        		<label for="SERVICE" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Market">Service</label>
        		<div class="col-md-6">
        		<input class="form-control" id="SERVICE" name="SERVICE" value="<?=$this->SERVICE?>" placeholder="Enter Service" required="required" type="text" >
        		</div>
        	</div>
        </div>
        <?php
   		$this->formHiddenInput('mode',$mode,'mode');

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateService',null,'Update') :  $this->formButton('submit','Submit','saveService',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetService',null,'Reset','btn-warning');
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