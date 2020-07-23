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
class rfsRecord extends DbRecord
{

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

    protected $rfsTable;

    const RFS_TYPE_TANDM = 'T&M';
    const RFS_TYPE_FIXED_PRICE = 'Fixed Price';
    static public $rfsType        = array(self::RFS_TYPE_TANDM,self::RFS_TYPE_FIXED_PRICE);

    const RFS_STATUS_LIVE     = 'Live';
    const RFS_STATUS_PIPELINE = 'Internal Pipeline';
    static public $rfsStatus  = array(self::RFS_STATUS_PIPELINE,self::RFS_STATUS_LIVE);


    static public $columnHeadings = array("RFS ID", "PRN", "Project Title", "Project Code", "Requestor Name", "Requestor Email", "Value Stream", "Link to PGMP", "RFS Creator", "RFS Created",'Archived','RFS Type','ILC Work Item','RFS Status','Business Unit');

    function __construct($pwd=null){
        parent::__construct($pwd);
        $this->rfsTable = new rfsTable(allTables::$RFS);
    }

    function get($field){
        return empty($this->$field) ? null : $this->$field;
    }

    function set($field,$value){
        if(!property_exists(__CLASS__, $field)){
            return false;
        } else {
            $this->$field = $value;
        }
    }


    function displayForm($mode)
    {
        ?>
		<form id='rfsForm' class="form-horizontal" method='post'>
        <?php
        $this->additional_comments = null;

        $loader = new Loader();
        $allValueStream = $loader->load('VALUE_STREAM', allTables::$STATIC_VALUE_STREAM);
        $allBusinessUnit = $loader->load('BUSINESS_UNIT', allTables::$STATIC_BUSINESS_UNIT);
        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';

        ?>
        <div class="form-group " id="RFS_IDFormGroup">
		<div class='required'>
			<label for="RFS_ID" class="col-md-2 control-label ceta-label-left"
				data-toggle="tooltip" data-placement="top" title="">RFS ID</label>
			<div class="col-md-2">
				<input class="form-control" id="RFS_ID" name="RFS_ID"
					value="<?=$this->RFS_ID?>" placeholder="Enter RFS Id"
					required="required" type="text" <?=$notEditable?>
					maxlength="<?=$this->rfsTable->getColumnLength('RFS_ID');?>"> <input
					id="originalRFS_ID" name="originalRFS_ID"
					value="<?=$this->RFS_ID?>" type="hidden">
			</div>
		</div>

		<div class='col-md-1'></div>

		<label for="PRN" class="col-md-2 control-label ceta-label-left"
			data-toggle="tooltip" data-placement="top" title=""
			data-original-title="">PRN</label>
		<div class="col-md-2">
			<input class="form-control" id="PRN" name="PRN"
				value="<?=$this->PRN?>" placeholder="PRN" type="text" maxlength="24">
			<input id="originalPRN" name="originalPRN" value="<?=$this->PRN?>"
				type="hidden">
		</div>


	</div>
	<div class="form-group" id="PROJECT_TITLEFormGroup">
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
				maxlength="<?=$this->rfsTable->getColumnLength('PROJECT_CODE');?>">
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
					maxlength="<?=$this->rfsTable->getColumnLength('PROJECT_TITLE');?>">
				<input id="originalPROJECT_TITLE" name="originalPROJECT_TITLE"
					value="<?=$this->PROJECT_TITLE?>" type="hidden">
			</div>
		</div>


	</div>

	<div class="form-group required " id="REQUESTOR_NAMEFormGroup">
		<label for="REQUESTOR_NAME"
			class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
			data-placement="top" title="">Requestor Name</label>
		<div class="col-md-3">
			<input class="form-control" id="REQUESTOR_NAME" name="REQUESTOR_NAME"
				value="<?=$this->REQUESTOR_NAME?>"
				placeholder="Enter Requestor Name" required="required" type="text"
				maxlength="<?=$this->rfsTable->getColumnLength('REQUESTOR_NAME');?>">
			<input id="originalREQUESTOR_NAME" name="originalREQUESTOR_NAME"
				value="<?=$this->REQUESTOR_NAME?>" type="hidden">
		</div>
		<label for="REQUESTOR_EMAIL"
			class="col-md-2 control-label ceta-label-left" data-toggle="tooltip"
			data-placement="top" title="">Requestor Email</label>
		<div class="col-md-5">
			<input class="form-control" id="REQUESTOR_EMAIL"
				name="REQUESTOR_EMAIL" value="<?=$this->REQUESTOR_EMAIL;?>"
				placeholder="Enter Requestor Email" required="required" type="email"
				maxlength="<?=$this->rfsTable->getColumnLength('REQUESTOR_EMAIL');?>">
			<input id="originalREQUESTOR_EMAIL" name="originalREQUESTOR_EMAIL"
				value="<?=$this->REQUESTOR_EMAIL;?>" type="hidden">
		</div>
	</div>

	<div class='form-group required'>
		<label for='VALUE_STREAM' class='col-md-2 control-label ceta-label-left'>Value Stream</label>
		<div class='col-md-3'>
			<select class='form-control select' id='VALUE_STREAM'
					name='VALUE_STREAM' required='required'
					data-placeholder="Select Value Stream" data-allow-clear="true">
				<option value=''>Select Value Stream</option>
                <?php
                foreach ($allValueStream as $key => $value) {
                    $displayValue = trim($value);
                    $returnValue = trim($value);
                ?>
				<option value='<?=$returnValue?>' <?=trim($this->VALUE_STREAM)==$returnValue ? 'selected' : null;?>><?=$displayValue?></option>
				<?php } ?>
           	</select>
		</div>
		<label for='BUSINESS_UNIT' class='col-md-2 control-label ceta-label-left'>Business Unit</label>
		<div class='col-md-3'>
			<select class='form-control select' id='BUSINESS_UNIT'
					name='BUSINESS_UNIT' required='required'
					data-placeholder="Select Business Unit" data-allow-clear="true">
					<option value=''>Select Business Unit</option>
                	<?php
                    foreach ($allBusinessUnit as $key => $value) {
                        $displayValue = trim($value);
                        $returnValue = trim($value);
                    ?>
					<option value='<?=$returnValue?>'<?=trim($this->BUSINESS_UNIT)==$returnValue ? 'selected' : null;?>><?=$displayValue?></option><?php
                    }
                    ?>
          	</select>
		</div>
	</div>


	<div class="form-group " id="RFS_TypeIlcFormGroup">
			<div class='required'>
				<label for="ILC_WORK_ITEM"
					class="col-md-2 control-label ceta-label-left"
					data-toggle="tooltip" data-placement="top"
					title="Claim Code - max length <?=$this->rfsTable->getColumnLength('ILC_WORK_ITEM');?>"
					data-original-title="">ILC Work Item</label>
				<div class="col-md-3">
					<input class="form-control" id="ILC_WORK_ITEM" name="ILC_WORK_ITEM"
						value="<?=$this->ILC_WORK_ITEM?>" placeholder="ILC_WORK_ITEM"
						type="text"
						maxlength="<?=$this->rfsTable->getColumnLength('ILC_WORK_ITEM');?>">
				</div>
				
								<label for='RFS_TYPE'
					class='col-md-2 control-label ceta-label-left'>RFS Type</label>
				<div class="col-md-1">
        			<?php
        		      foreach (self::$rfsType as $rfsType) {
        		          $checked = trim($this->RFS_TYPE)== $rfsType ? ' checked ' : null;
        		      ?><label class="radio"><input type="radio"
						name="RFS_TYPE" <?=$checked?> value='<?=$rfsType?>'
						required='required'><small><?=str_replace(' ', '&nbsp;', $rfsType)?></small></label>
        		    	<?php
        	   	       }
        		?>
				</div>
				

				<label for='RFS_STATUS'
					class='col-md-2 control-label ceta-label-left'>RFS Status</label>
				<div class="col-md-1">
					<?php
        		      foreach (self::$rfsStatus as $rfsState) {
        		          $checked = trim($this->RFS_STATUS)== $rfsState ? ' checked ' : null;
        		          $checked =  $_SESSION['isRfs'] && $rfsState==self::RFS_STATUS_PIPELINE? ' checked ' : $checked;
        		          $checked =  $_SESSION['isDemand'] && $rfsState==self::RFS_STATUS_LIVE? ' checked ' : $checked;
        		          $disabled = $_SESSION['isRfs'] || $_SESSION['isDemand']  ? ' disabled ' : null ;
        		      ?><label class="radio"><input type="radio"
						name="RFS_STATUS" <?=$checked?> value='<?=$rfsState?>'
						required='required' <?=$disabled;?>><small><?=str_replace(' ', '&nbsp;', $rfsState)?></small></label>
        		    	<?php
        		      }
        		?>
				</div>
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
					maxlength="<?=$this->rfsTable->getColumnLength('LINK_TO_PGMP');?>">
				<input id="originalLINK_TO_PGMP" name="originalLINK_TO_PGMP"
					value="<?=$this->LINK_TO_PGMP?>" type="hidden">
			</div>
		</div>




        <?php
        $allButtons = array();
   		$this->formHiddenInput('RFS_CREATOR',$_SESSION['ssoEmail'],'RFS_CREATOR');
   		$this->formHiddenInput('mode',$mode,'mode');
   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateRfs',null,'Update') :  $this->formButton('submit','Submit','saveRfs',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetRfs',null,'Reset','btn-warning');
   		$allButtons[] = $submitButton;
   		$allButtons[] = $resetButton;
   		?>
   		<div class='col-md-2'></div>
   		<?php
   		$this->formBlueButtons($allButtons);
  		?>
	


</form>
<?php
    }

    static function htmlHeaderRow(){
        $headerRow = "<tr>";
        $headerRow .= rfsRecord::htmlHeaderCells();

        $headerRow .= "</tr>";
        return $headerRow;
    }

    function htmlHeaderCells(){
        $headerCells = "";
        foreach (rfsRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        return $headerCells;
    }

}