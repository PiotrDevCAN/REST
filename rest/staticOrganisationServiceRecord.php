<?php
namespace rest;

use itdq\DbRecord;
use itdq\FormClass;

/**
 *
 * @author gb001399
 *
 */
class staticOrganisationServiceRecord extends DbRecord
{

    public $ORGANISATION;
    public $SERVICE;
    public $STATUS;

	function displayForm($mode){
        $allButtons = array();
        ?>
        <form id='organisationServiceForm' class="form-horizontal"  method='post'>
        <div class="form-group ">
			<div class='required'>
        		<label for="ORGANISATION" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Country">Organisation</label>
        		<div class="col-md-6">
					<select class='form-control select'
						id='ORGANISATION'
						name='ORGANISATION'
						required='required'
						data-placeholder="Select Organisation" 
						data-allow-clear="true"
					>
						<option value=''>Select Organisation<option>
					</select>
        		</div>
        	</div>
        </div>
        <div class="form-group ">
			<div class='required'>
        		<label for="SERVICE" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Market">Service</label>
        		<div class="col-md-6">
					<select class='form-control select'
						id='SERVICE'
						name='SERVICE'
						required='required'
						data-placeholder="Select Service" 
						data-allow-clear="true"
					>
						<option value=''>Select Service<option>
					</select>
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

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateOrganisationService',null,'Update') :  $this->formButton('submit','Submit','saveOrganisationService',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetOrganisationService',null,'Reset','btn-warning');
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