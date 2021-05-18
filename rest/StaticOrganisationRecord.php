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
class staticOrganisationRecord extends DbRecord
{

    public $ORGANISATION;
    public $SERVICE;
    public $STATUS;


    function displayForm($mode=FormClass::modeDEFINE){
        $allButtons = array();
        ?>
        <form id='organisationForm' class="form-horizontal"  method='post'>
        <div class="form-group ">
			<div class='required'>
        		<label for="ORGANISATION" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Country">Organisation</label>
        		<div class="col-md-6">
        		<input class="form-control" id="ORGANISATION" name="ORGANISATION" value="<?=$this->ORGANISATION?>" placeholder="Enter Organisation" required="required" type="text" >
        		</div>
        	</div>
        </div>
        <div class="form-group ">
			<div class='required'>
        		<label for="SERVICE" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Market">Service</label>
        		<div class="col-md-6">
        		<input class="form-control" id="SERVICE" name="SERVICE" value="<?=$this->SERVICE?>" placeholder="Enter Service" required="required" type="text" >
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

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateOrganisation',null,'Update') :  $this->formButton('submit','Submit','saveOrganisation',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetOrganisation',null,'Reset','btn-warning');
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