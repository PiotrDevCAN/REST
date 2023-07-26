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
class staticBandRecord extends DbRecord
{
    use recordTrait;
    public $BAND_ID;
    public $BAND;

	function displayForm($mode){
        $allButtons = array();
        ?>
        <form id='bandForm' class="form-horizontal"  method='post'>
        <div class="form-group ">
			<div class='required'>
        		<label for="BAND" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Country">Band</label>
        		<div class="col-md-6">
				<input class="form-control" id="BAND" name="BAND" value="<?=$this->BAND?>" placeholder="Enter Band" required="required" type="text" >
        		</div>
        	</div>
        </div>
        <?php
		$this->formHiddenInput('BAND_ID','','BAND_ID');
		$this->formHiddenInput('mode',$mode,'mode');

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateBand',null,'Update') :  $this->formButton('submit','Submit','saveBand',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetBand',null,'Reset','btn-warning');
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