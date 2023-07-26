<?php
namespace rest;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;
use rest\traits\recordTrait;

/**
 *
 * @author gb001399
 *
 */
class rfsPcrRecord extends DbRecord
{
	use recordTrait;
	
	protected $RFS_ID;
    protected $PCR_ID;
    protected $PCR_NUMBER;
    protected $PCR_START_DATE;
	protected $PCR_END_DATE;
	protected $PCR_AMOUNT;
	protected $ARCHIVE;

    protected $rfsPcrTable;

	static public $columnHeadings = array(
		"PCR ID", 
		"RFS ID", 
		"PCR Number", 
		"PCR Start Date", 
		"PCR End Date", 
		"PCR Amount", 
		"Archive" 
	);

    function __construct($pwd=null){
        parent::__construct($pwd);
        $this->rfsPcrTable = new rfsPcrTable(allTables::$RFS_PCR);
    }

	static function htmlHeaderRow(){
        $headerRow = "<tr>";
        $headerRow .= rfsPcrRecord::htmlHeaderCellsStatic();

        $headerRow .= "</tr>";
        return $headerRow;
    }

    function htmlHeaderCells(){
        $headerCells = "";
        foreach (rfsPcrRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        return $headerCells;
    }

	static function htmlHeaderCellsStatic(){
        $headerCells = "";
        foreach (rfsPcrRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        return $headerCells;
    }

    function displayForm($mode) {
        ?>
		<form id='rfsPcrForm' class="form-horizontal" method='post'>
        <?php
        $this->additional_comments = null;
        
        $loader = new Loader();
        $rfsPredicate = rfsTable::rfsPredicateFilterOnPipelineNotArchived();
        $allRfs = $loader->load('RFS_ID',allTables::$RFS,$rfsPredicate);

        $today = new \DateTime();
       
        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';
		$readOnly = $mode==FormClass::$modeDISPLAY ? ' disabled ' : null;
        if ($mode==FormClass::$modeDISPLAY) {
            $notEditable = 'disabled';
        }
        ?>
        <div class="form-group " id="PCR_IDFormGroup">
			<div class='required'>
				<label for="PCR_ID" class="col-md-2 control-label ceta-label-left"
					data-toggle="tooltip" data-placement="top" title="">RFS ID</label>
				<div class="col-md-3">
                    <select class='form-control select' id='RFS'
                        name='RFS_ID'
                        required='required'
                        data-placeholder="Select RFS id" data-allow-clear="true"
                        <?=$notEditable?>
                    >
                    <option value=''>Select RFS<option>
                    <?php
                        foreach ($allRfs as $key => $value) {
                            $displayValue = trim($key);
                            $returnValue  = trim($key);
                            ?><option value='<?=$returnValue?>' <?=trim($this->RFS_ID)==$returnValue ? 'selected' : null?>><?=$displayValue?></option><?php
                        }
                    ?>
                    </select>
				</div>
			</div>
		</div>
        <div class="form-group " id="PCR_NUMBERFormGroup">
			<div class='required'>
				<label for="PCR_NUMBER"
					class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
					data-placement="top" title="">PCR Number</label>
				<div class="col-md-3">
					<input
					type='text'
					class='form-control'
					id='PCR_NUMBER'
					name='PCR_NUMBER'
					placeholder="PCR Number"
					required='required'
					/>
					<p id="rfsPcrIdInvalid" style="display:none; color: CRIMSON">RFS PCR ID does not meet XXXX-XXX-000000 pattern</p>
				</div>
			</div>
        </div>
		<div class='form-group required' id="PCR_TypeIlcFormGroup">
			<label for="PCR_START_DATE"
				class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
				data-placement="top" title="">PCR Start Date</label>
			<div class="col-md-3">
				<div id='calendarFormGroupPCR_START_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='PCR_START_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
					<input id='InputPCR_START_DATE' class='form-control' type='text'  value='' placeholder='Select Start Date' required  <?=$notEditable?>/>
					<input type='hidden' id='PCR_START_DATE' name='PCR_START_DATE' value='' required/>
					<span class='input-group-addon'><span id='calendarIconPCR_START_DATE' class='glyphicon glyphicon-calendar'></span></span>
				</div>
			</div>
		</div>
		<div class='form-group required' id="PCR_TypeIlcFormGroup">
			<label for="PCR_END_DATE"
				class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
				data-placement="top" title="">PCR End Date</label>
			<div class="col-md-3">
				<div id='calendarFormGroupPCR_END_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='PCR_END_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
					<input id='InputPCR_END_DATE' class='form-control' type='text'  value='' placeholder='Select Start Date' required  <?=$notEditable?>/>
					<input type='hidden' id='PCR_END_DATE' name='PCR_END_DATE' value='' required/>
					<span class='input-group-addon'><span id='calendarIconPCR_END_DATE' class='glyphicon glyphicon-calendar'></span></span>
				</div>
			</div>
		</div>
        <div class="form-group required" id="PCR_AMOUNTFormGroup">
            <label for="PCR_AMOUNT"
				class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
				data-placement="top" title="" required='required'>PCR Amount</label>
			<div class="col-md-3">
				<input type='number' step='0.01' min=0 class="form-control" id="PCR_AMOUNT" name="PCR_AMOUNT" value="" placeholder="PCR Amount" required >
			</div>
        </div>
		<?php 
		include_once 'includes/formMessageArea.html';
		?>
		<?php
			$allButtons = array();
			// $rfsCreator = $mode==FormClass::$modeEDIT ? $this->PCR_CREATOR : $_SESSION['ssoEmail']; 
			// $this->formHiddenInput('PCR_CREATOR',$rfsCreator,'PCR_CREATOR');
			// $token = $_SESSION['formToken'];
			// $this->formHiddenInput('formToken',$token,'formToken');
			$this->formHiddenInput('mode',$mode,'mode');
			$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateRfs',null,'Update') :  $this->formButton('submit','Submit','saveRfs',null,'Submit');
			$resetButton  = $this->formButton('reset','Reset','resetRfs',null,'Reset','btn-warning');
			$allButtons[] = $submitButton;
			$allButtons[] = $resetButton;
		?>
		<div class='col-md-2'></div>
		<?php
			if ($mode!==FormClass::$modeDISPLAY) {
				$this->formBlueButtons($allButtons);
			}
		?>
	</form>
	<?php
    }
}