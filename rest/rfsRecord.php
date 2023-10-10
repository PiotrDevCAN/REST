<?php
namespace rest;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;
use rest\traits\recordTrait;
use rest\traits\rfsRecordTrait;

/**
 *
 * @author gb001399
 *
 */
class rfsRecord extends DbRecord
{
	use recordTrait, rfsRecordTrait;
	
    protected $RFS_ID;
    protected $PRN;
    protected $PROJECT_TITLE;
    protected $PROJECT_CODE;
    protected $REQUESTOR_NAME;
    protected $REQUESTOR_EMAIL;
    protected $VALUE_STREAM;// was CIO
    protected $LINK_TO_PGMP;
    protected $RFS_CREATOR;
    protected $RFS_CREATED_TIMESTAMP;
    protected $ARCHIVE;
    protected $RFS_TYPE;
    protected $ILC_WORK_ITEM;
    protected $RFS_STATUS;
    protected $BUSINESS_UNIT;
    protected $RFS_END_DATE;
	protected $RFS_START_DATE;

	protected $ILC_WORK_ITEM_WEEKDAY_OVERTIME;
	protected $ILC_WORK_ITEM_WEEKEND_OVERTIME;

    protected $rfsTable;

    const RFS_TYPE_TANDM = 'T&M';
    const RFS_TYPE_FIXED_PRICE = 'Fixed Price';
    static public $rfsType = array(self::RFS_TYPE_TANDM,self::RFS_TYPE_FIXED_PRICE);

    const RFS_STATUS_LIVE     = 'Live';
    const RFS_STATUS_PIPELINE = 'Internal Pipeline';
    static public $rfsStatus  = array(self::RFS_STATUS_PIPELINE,self::RFS_STATUS_LIVE);
    static public $rfsStatusMapping  = array('pipeline'=>self::RFS_STATUS_PIPELINE,'live'=>self::RFS_STATUS_LIVE);

	// All Tribe Names in REST
	CONST ENTERPRISE_TRANSFORMATION = 'Enterprise Transformation';
	CONST COMMERCIAL = 'Commercial';
	CONST RETAIL = 'Retail';
	CONST CYBER = 'Cyber';
	CONST INSURANCE_ENTERPRISE_PROGRAMMES = 'Insurance & Enterprise Programmes';
	CONST FUTURE_CLOUD = 'Future Cloud';

	static public $allTribeNames = array(
		self::ENTERPRISE_TRANSFORMATION,
		self::COMMERCIAL,
		self::RETAIL,
		self::CYBER,
		self::INSURANCE_ENTERPRISE_PROGRAMMES,
		self::FUTURE_CLOUD
	);

	static public $tribeNameMapping = array(
		'Evolution' => self::FUTURE_CLOUD,
		'Retail & Community Banking' => self::RETAIL
	);

    static public $columnHeadings = array(
		"RFS ID", 
		"PRN", 
		"Project Code", 
		"Project Title", 
		"Requestor Name", 
		"Requestor Email", 
		"Value Stream", 
		"Business Unit",
		"ILC WI - Reg",
		"ILC WI - WD Overtime",
		"ILC WI - WEND Overtime",
		"RFS Start Date",
		"RFS End Date",
		"RFS Type",
		"RFS Status",
		"Archived",
		"RFS Creator", 
		"RFS Created",
		"Link to PGMP",
	);

    function __construct($pwd=null){
        parent::__construct($pwd);
        $this->rfsTable = new rfsTable(allTables::$RFS);
    }

    function displayForm($mode) {
        ?>
		<form id='rfsForm' class="form-horizontal" method='post'>
        <?php
        $this->additional_comments = null;
        $today = new \DateTime();

        $loader = new Loader();
        $allValueStream = $loader->load('VALUE_STREAM', allTables::$STATIC_VALUE_STREAM);
       
        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';
		$readOnly = $mode == FormClass::$modeDISPLAY ? ' readonly ' : null;
        if ($mode == FormClass::$modeDISPLAY) {
            $notEditable = 'readonly';
        }
        ?>
        <div class="form-group " id="RFS_IDFormGroup">
			<div class='required'>
				<label for="RFS_ID" class="col-md-2 control-label ceta-label-left"
					data-toggle="tooltip" data-placement="top" title="">RFS ID</label>
				<div class="col-md-3">
					<input class="form-control" id="RFS_ID" name="RFS_ID"
						value="<?=$this->RFS_ID?>" placeholder="Enter RFS Id"
						required="required" type="text" <?=$notEditable?>
						maxlength="<?=$this->rfsTable->getColumnLength('RFS_ID');?>"> <input
						id="originalRFS_ID" name="originalRFS_ID"
						value="<?=$this->RFS_ID?>" type="hidden">
					<p id="rfsIdInvalid" style="display:none; color: CRIMSON">RFS ID does not meet XXXX-XXX-000000 pattern</p>
				</div>
			</div>

			<label for="PRN" class="col-md-2 control-label ceta-label-left"
				data-toggle="tooltip" data-placement="top" title=""
				data-original-title="">PRN</label>
			<div class="col-md-5">
				<input class="form-control" id="PRN" name="PRN"
					value="<?=$this->PRN?>" placeholder="PRN" type="text" maxlength="24"
					<?=$readOnly?>
				>
				<input id="originalPRN" name="originalPRN" value="<?=$this->PRN?>"
					type="hidden">
			</div>

		</div>
		<div class="form-group" id="PROJECT_CODEFormGroup">
			<label for="PROJECT_CODE"
				class="col-md-2	 control-label ceta-label-left" data-toggle="tooltip"
				data-placement="top" data-original-title=""
				title="Project Code - max length <?=$this->rfsTable->getColumnLength('PROJECT_CODE');?>">Project
				Code<br />
			<span style="font-size: 0.8em">(WBS Number)</span>
			</label>
			<div class="col-md-3">
				<input class="form-control " id="PROJECT_CODE" name="PROJECT_CODE"
					value="<?=$this->PROJECT_CODE?>" placeholder="Enter Code"
					type="text"
					maxlength="<?=$this->rfsTable->getColumnLength('PROJECT_CODE');?>"
					<?=$readOnly?>
				>
				<input id="originalPROJECT_CODE" name="originalPROJECT_CODE"
					value="<?=$this->PROJECT_CODE?>" type="hidden">
			</div>

			<div class='required'>
				<label for="PROJECT_TITLE"
					class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
					data-placement="top" title="" data-original-title="">Project Title</label>
				<div class="col-md-5">
					<input class="form-control required" id="PROJECT_TITLE"
						name="PROJECT_TITLE" value="<?=$this->PROJECT_TITLE?>"
						placeholder="Enter Title" required="required" type="text"
						maxlength="<?=$this->rfsTable->getColumnLength('PROJECT_TITLE');?>"
						<?=$readOnly?>
					>
					<input id="originalPROJECT_TITLE" name="originalPROJECT_TITLE"
						value="<?=$this->PROJECT_TITLE?>" type="hidden">
				</div>
			</div>
		</div>

		<div class="form-group required " id="REQUESTOR_NAMEFormGroup">
			<label for="REQUESTOR_EMAIL"
				class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
				data-placement="top" title="">Requestor Email</label>
			<div class="col-md-3">
				<input class="form-control typeahead" id="REQUESTOR_EMAIL"
					name="REQUESTOR_EMAIL" value="<?=$this->REQUESTOR_EMAIL;?>"
					placeholder="Enter Requestor Kyndryl Email" required="required" type="email"
					maxlength="<?=$this->rfsTable->getColumnLength('REQUESTOR_EMAIL');?>"
					data-toggle="tooltip" data-placement="top" title="Kyndryl Email Address"
					<?=$readOnly?>
				>
				<input id="originalREQUESTOR_EMAIL" name="originalREQUESTOR_EMAIL"
					value="<?=$this->REQUESTOR_EMAIL;?>" type="hidden">
				<p id="IBMNotAllowed" style="display:none; color: CRIMSON">IBM / Ocean email address is no longer allowed.</p>
			</div>
			<label for="REQUESTOR_NAME"
				class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
				data-placement="top" title="">Requestor Name</label>
			<div class="col-md-5">
				<input class="form-control" id="REQUESTOR_NAME" name="REQUESTOR_NAME"
					value="<?=$this->REQUESTOR_NAME?>"
					placeholder="Enter Requestor Kyndryl Email first" required="required" type="text"
					maxlength="<?=$this->rfsTable->getColumnLength('REQUESTOR_NAME');?>"
					readonly
				>
				<input id="originalREQUESTOR_NAME" name="originalREQUESTOR_NAME"
					value="<?=$this->REQUESTOR_NAME?>" type="hidden">
			</div>
		</div>

		<div class='form-group required'>
			<label for='VALUE_STREAM' class='col-md-2 control-label ceta-label-left'>Value Stream</label>
			<div class='col-md-3'>
				<select class='form-control select' id='VALUE_STREAM'
					name='VALUE_STREAM' required='required'
					data-placeholder="Select Value Stream" data-allow-clear="true"
					<?=$readOnly?>
				>
					<option value=''>Select Value Stream</option>
					<?php
					foreach ($allValueStream as $key => $value) {
						$displayValue = trim($value);
						$returnValue = trim($value);
					?>
					<option value='<?=$returnValue?>' <?=htmlspecialchars_decode(trim($this->VALUE_STREAM))==htmlspecialchars_decode($returnValue) ? 'selected' : null;?>><?=$displayValue?></option>
					<?php } ?>
				</select>
			</div>
			<label for='BUSINESS_UNIT' class='col-md-2 control-label ceta-label-left'>Business Unit</label>
			<div class='col-md-5'>
				<input class="form-control " id="BUSINESS_UNIT" name="BUSINESS_UNIT"
					value="<?=$this->BUSINESS_UNIT?>" placeholder="Select Value Stream first"
					type="text"
					maxlength="<?=$this->rfsTable->getColumnLength('BUSINESS_UNIT');?>"
					readonly >
			</div>
		</div>
		<div class='form-group' id="RFS_TypeIlcFormGroup">
			<label for="RFS_END_DATE"
				class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
				data-placement="top" title="">RFS End Date</label>
			<div class="col-md-3">
				<div id='calendarFormGroupRFS_END_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='RFS_END_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
					<input id='InputRFS_END_DATE' class='form-control' type='text'  value='<?=$this->RFS_END_DATE;?>' placeholder='Select End Date' required  <?=$notEditable?>/>
					<input type='hidden' id='RFS_END_DATE' name='RFS_END_DATE' value='<?=$this->RFS_END_DATE;?>' required/>
					<span class='input-group-addon'><span id='calendarIconRFS_END_DATE' class='glyphicon glyphicon-calendar'></span></span>
				</div>
				<input type='hidden' id='RFS_START_DATE' name='RFS_START_DATE' value='<?=$this->RFS_START_DATE;?>' required/>
			</div>
			<div class='required' >
				<label for='RFS_TYPE'
					class='col-md-2 control-label ceta-label-left '>RFS Type</label>
				<div class="col-md-1">
					<?php
						foreach (self::$rfsType as $rfsType) {
							$checked = htmlspecialchars_decode(trim($this->RFS_TYPE))==htmlspecialchars_decode(trim($rfsType)) ? ' checked ' : null;
						?><label class="radio"><input type="radio"
						name="RFS_TYPE" <?=$checked?> value='<?=$rfsType?>'
						required='required' <?=$readOnly?>><small><?=str_replace(' ', '&nbsp;', $rfsType)?></small></label>
						<?php
						}
				?>
				</div>
			</div>
			<div class='required' >
				<label for='RFS_STATUS'
					class='col-md-2 control-label ceta-label-left'>RFS Status</label>
				<div class="col-md-1">
					<?php
						foreach (self::$rfsStatus as $rfsState) {
							$checked = trim($this->RFS_STATUS)== $rfsState ? ' checked ' : null;
							$checked = !$_SESSION['isAdmin'] &&  $_SESSION['isRfs']     && $rfsState==self::RFS_STATUS_PIPELINE? ' checked ' : $checked;
							$checked = !$_SESSION['isAdmin'] &&   $_SESSION['isDemand'] && $rfsState==self::RFS_STATUS_LIVE? ' checked ' : $checked;
							$disabled =!$_SESSION['isAdmin'] && ($_SESSION['isRfs'] || $_SESSION['isDemand'])  ? ' disabled ' : null ;
						?><label class="radio"><input type="radio"
						name="RFS_STATUS" <?=$checked?> value='<?=$rfsState?>'
						required='required' <?=$disabled;?> <?=$readOnly?>><small><?=str_replace(' ', '&nbsp;', $rfsState)?></small></label>
						<?php
						}
				?>
				</div>
			</div>			
		</div>
		<div class='form-group' id="RFS_TypeIlcFormGroup">
			<div class='required_not_yet'>
				<label for="ILC_WORK_ITEM"
					class="col-md-2 control-label ceta-label-left"
					data-toggle="tooltip" data-placement="top"
					title="Claim Code - max length <?=$this->rfsTable->getColumnLength('ILC_WORK_ITEM');?>"
					data-original-title="">ILC Work Item</label>
				<div class="col-md-3">
					<input class="form-control" id="ILC_WORK_ITEM" name="ILC_WORK_ITEM"
						value="<?=$this->ILC_WORK_ITEM?>" placeholder="ILC Work Item"
						type="text"
						maxlength="<?=$this->rfsTable->getColumnLength('ILC_WORK_ITEM');?>"
						disabled
						>
					<input type='hidden' id='ILC_WORK_ITEM_WEEKDAY_OVERTIME' name='ILC_WORK_ITEM_WEEKDAY_OVERTIME' value='<?=$this->ILC_WORK_ITEM_WEEKDAY_OVERTIME;?>'/>
					<input type='hidden' id='ILC_WORK_ITEM_WEEKEND_OVERTIME' name='ILC_WORK_ITEM_WEEKEND_OVERTIME' value='<?=$this->ILC_WORK_ITEM_WEEKEND_OVERTIME;?>'/>
				</div>
			</div>			
		</div>
		<div id="invalidILCWorkItems" class="col-md-12" style="display:none; color: CRIMSON">
			<div class="col-md-2">
			</div>				
			<div class="col-md-7">
				<p>Provided value of ILC Work Item is incorrect.</p>
				<p>The value has to be 9 characters lenght.</p>
			</div>
		</div>
		<div class="form-group" id='LinkToPgmpFormGroup'>
			<label for="LINK_TO_PGMP"
				class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
				data-placement="top" title="Paste URL Link to PGMP Document">Link to
				PGMP</label>
			<div class="col-md-7">
				<input class="form-control" id="LINK_TO_PGMP" name="LINK_TO_PGMP"
					value="<?=$this->LINK_TO_PGMP?>" placeholder="URL Link to PGMP"
					type="text"
					maxlength="<?=$this->rfsTable->getColumnLength('LINK_TO_PGMP');?>"
					<?=$readOnly?>
				>
				<input id="originalLINK_TO_PGMP" name="originalLINK_TO_PGMP"
					value="<?=$this->LINK_TO_PGMP?>" type="hidden">
			</div>
		</div>
		<?php 
		include_once 'includes/formMessageArea.html';
		?>
		<?php
			$allButtons = array();
			$rfsCreator = $mode==FormClass::$modeEDIT ? $this->RFS_CREATOR : $_SESSION['ssoEmail']; 
			$this->formHiddenInput('RFS_CREATOR',$rfsCreator,'RFS_CREATOR');
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
    
    function displaySlipRfs() {
        $details = resourceRequestTable::getDetailsforRfsDateSlip($this->RFS_ID);
        $today = new \DateTime
    	?>
		<form id='rfsSlipDates' class="form-horizontal" method='post'>
		<?php 
		if ($details && count($details) > 0) {
			foreach ($details as $rr => $requestDetails) {
				
				$startDate = new \DateTime($requestDetails['START_DATE']);
				$endDate   = new \DateTime($requestDetails['END_DATE']);
				
				$startDateStr = empty($startDate) ? null : $startDate->format('d M y');
				$startDateStr2 = empty($startDate) ? null : $startDate->format('Y-m-d');
				
				$endDateStr = empty($endDate) ? null : $endDate->format('d M y');
				$endDateStr2 = empty($endDate) ? null : $endDate->format('Y-m-d');
				
				$rr = $requestDetails['RESOURCE_REFERENCE'];
				
				?>
				<div class='form-group' >		    
				<div class='col-md-12' style='background:#eeeeee;border:#cccccc'>
				<small>
				<?="Ref:" . $requestDetails['RESOURCE_REFERENCE']. "<br/>" . $requestDetails['ORGANISATION'] . ":" . $requestDetails['SERVICE'] . "<br/>" . $requestDetails['DESCRIPTION']?>
				</small>
				</div>
				</div>
				<?php 
				if( $startDate < $today){
					$startDateStr = null;
					$startDateStr2 = null;
					$endDateStr = null;
					?>
					<div class='form-group' >
					<div class='col-md-8 col-md-offset-2' >
					<div class="alert alert-warning" role="alert">The Start Date(<?=$startDateStr?>) for this request has past. Please set new Dates</div>
					</div>
					</div>
					<?php    
				}
				?>
				<div class='form-group required' >
				<label for='START_DATE_<?=$rr?>' class='col-md-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title=''>Start Date</label>
				<div class='col-md-3'>
				<div class='input-group date form_datetime ' data-date-format='dd MM yyyy - HH:ii p' data-link-field='START_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
				<input class='form-control startDate' type='text' readonly value='<?=$startDateStr?>' placeholder='Select Start Date' required data-reference='<?=$rr?>' />
				<input type='hidden' class='startDate2' id='START_DATE_<?=$rr?>' name='START_DATE_<?=$rr?>' value='<?=$startDateStr2?>' data-reference='<?=$rr?>' />
				<span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span></span>
				</div>
				</div>

				<div class='form-group required'>
				<label for='END_DATE_<?=$rr?>' class='col-md-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title=''>End Date</label>
				<div class='col-md-3'>
				<div class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='END_DATE<?=$rr?>' data-link-format='yyyy-mm-dd-hh.ii.00'>
				<input class='form-control endDate' type='text' readonly value='<?=$endDateStr?>' placeholder='Select End Date' required  data-reference='<?=$rr?>' />
				<input type='hidden' class='endDate2' id='END_DATE_<?=$rr?>' name='END_DATE_<?=$rr?>' value='<?=$endDateStr2?>'  data-reference='<?=$rr?>' disabled />
				<span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span></span>
				</div>
				</div>
				</div>
				</div>
				<?php		    
			}
		}	
		?>		
		</form>
	<?php 
	}
}