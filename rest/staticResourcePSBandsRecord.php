<?php
namespace rest;

use itdq\DbRecord;
use itdq\FormClass;

/**
 *
 * @author gb001399
 *
 */
class staticResourcePSBandsRecord extends DbRecord
{

    public $ID;
	public $RESOURCE_NAME;
	public $PS_BAND_ID;

	function displayForm($mode){
        $allButtons = array();
        ?>
        <form id='resourcePSBandsForm' class="form-horizontal"  method='post'>
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
					<label for="PS_BAND_ID" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
						data-placement="top" title="" data-original-title="">PS Band</label>
					<div class="col-md-6">
						<select class="form-control select" id="PS_BAND_ID" name="PS_BAND_ID" required="required"
						data-placeholder="Select PS Band" data-allow-clear="true">
						<option value="">Select PS Band</option>
						<option></option>
					</select>
				</div>
				</div>
			</div>
			<?php
			include_once 'includes/formMessageArea.html';
			
			$this->formHiddenInput('ID','','ID');
			$this->formHiddenInput('mode',$mode,'mode');

			$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateResourcePSBand',null,'Update') :  $this->formButton('submit','Submit','saveResourcePSBand',null,'Submit');
			$resetButton  = $this->formButton('reset','Reset','resetResourcePSBand',null,'Reset','btn-warning');
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