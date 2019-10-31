<?php
namespace rest;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;

/**
 *
 * @author gb001399
 *
 */
class StaticCtbServiceRecord extends DbRecord
{

    public $CTB_SERVICE;
    public $CTB_SUB_SERVICE;
    public $STATUS;


    function displayForm($mode=FormClass::modeDEFINE){
        $allButtons = array();
        ?>
        <form id='ctbServiceForm' class="form-horizontal"  method='post'>
        <div class="form-group ">
			<div class='required'>
        		<label for="CTB_SERVICE" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Country">CTB Service</label>
        		<div class="col-md-6">
        		<input class="form-control" id="CTB_SERVICE" name="CTB_SERVICE" value="<?=$this->CTB_SERVICE?>" placeholder="Enter CTB Service" required="required" type="text" >
        		</div>
        	</div>
        </div>
        <div class="form-group ">
			<div class='required'>
        		<label for="CTB_SUB_SERVICE" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Market">CTB Sub Service</label>
        		<div class="col-md-6">
        		<input class="form-control" id="CTB_SUB_SERVICE" name="CTB_SUB_SERVICE" value="<?=$this->CTB_SUB_SERVICE?>" placeholder="Enter CTB Sub Service" required="required" type="text" >
        		</div>
        	</div>
        </div>
		<div class="form-group required">

        	<label class='col-md-2 control-label ceta-label-left'>Status</label>
        	<div class='col-md-4'>
  		 		<label class="radio-inline col-sm-3"><input type="radio" name="statusRadio" value='disabled' id='statusRadioDisabled' >Disabled</label>
		 		<label class="radio-inline col-sm-3"><input type="radio" name="statusRadio" value='enabled'  id='statusRadioEnabled' checked  >Enabled</label>
		 	</div>
		</div>
        <?php
   		$this->formHiddenInput('mode',$mode,'mode');

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateCtbService',null,'Update') :  $this->formButton('submit','Submit','saveCtbService',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetCtbervice',null,'Reset','btn-warning');
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