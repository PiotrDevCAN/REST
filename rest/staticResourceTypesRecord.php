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
class staticResourceTypesRecord extends DbRecord
{
	use recordTrait;

    public $ID;
	public $RESOURCE_NAME;
	public $RESOURCE_TYPE_ID;

	function displayForm($mode){
        $allButtons = array();
        ?>
        <form id='resourceTypesForm' class="form-horizontal"  method='post'>
			<div class="form-group ">
				<div class='required'>
					<label for="RESOURCE_NAME" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
						data-placement="top" title="" data-original-title="">Resource Name</label>
					<div class="col-md-6">
						<select class="form-control select" id="RESOURCE_NAME" name="RESOURCE_NAME" required="required"
						data-placeholder="Select Resource" data-allow-clear="true">
						<option value="">Select Resource</option>
						<option></option>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group ">
				<div class='required'>
					<label for="RESOURCE_TYPE_ID" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
						data-placement="top" title="" data-original-title="">Resource Type</label>
					<div class="col-md-6">
						<select class="form-control select" id="RESOURCE_TYPE_ID" name="RESOURCE_TYPE_ID" required="required"
						data-placeholder="Select Resource Type" data-allow-clear="true">
						<option value="">Select Resource Type</option>
						<option></option>
					</select>
				</div>
				</div>
			</div>
			<?php
			include_once 'includes/formMessageArea.html';
			
			$this->formHiddenInput('ID','','ID');
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