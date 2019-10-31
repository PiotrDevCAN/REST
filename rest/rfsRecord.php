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
    protected $CIO;
    protected $LINK_TO_PGMP;
    protected $RFS_CREATOR;
    protected $RFS_CREATED_TIMESTAMP;
    protected $ARCHIVE;
    protected $RFS_TYPE;
    protected $ILC_WORK_ITEM;
    protected $RFS_STATUS;

    const RFS_TYPE_TANDM = 'T&M';
    const RFS_TYPE_FIXED_PRICE = 'Fixed Price';
    static public $rfsType        = array(self::RFS_TYPE_TANDM,self::RFS_TYPE_FIXED_PRICE);

    const RFS_STATUS_LIVE     = 'Live';
    const RFS_STATUS_PIPELINE = 'Internal Pipeline';
    static public $rfsStatus  = array(self::RFS_STATUS_PIPELINE,self::RFS_STATUS_LIVE);


    static public $columnHeadings = array("RFS ID", "PRN", "Project Title", "Project Code", "Requestor Name", "Requestor Email", "CIO", "Link to PGMP", "RFS Creator", "RFS Created",'Archived','RFS Type','ILC Work Item');

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
        <form id='rfsForm' class="form-horizontal"  method='post'>
        <?php
        $this->additional_comments = null;

        $loader = new Loader();
        $allCio = $loader->load('CIO',allTables::$STATIC_CIO);
        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';

        ?>
        <div class="form-group " id="RFS_IDFormGroup">
			<div class='required'>
        		<label for="RFS_ID" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="">RFS ID</label>
        		<div class="col-md-2">
        		<input class="form-control" id="RFS_ID" name="RFS_ID" value="<?=$this->RFS_ID?>" placeholder="Enter RFS Id" required="required" type="text" <?=$notEditable?>>
        		<input id="originalRFS_ID" name="originalRFS_ID" value="<?=$this->RFS_ID?>" type="hidden">
        		</div>
        	</div>

        	<div class='col-md-1'></div>

        	<label for="PRN" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="">PRN</label>
            <div class="col-md-2">
            <input class="form-control" id="PRN" name="PRN" value="<?=$this->PRN?>" placeholder="PRN" type="text">
            <input id="originalPRN" name="originalPRN" value="<?=$this->PRN?>" type="hidden">
            </div>


        </div>
   		<div class="form-group"  id="PROJECT_TITLEFormGroup" >
              <label for="PROJECT_CODE" class="col-md-2	 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Project Code<br/><span style="font-size:0.8em">(WBS Number)</span> </label>
              <div class="col-md-3">
                  <input class="form-control " id="PROJECT_CODE" name="PROJECT_CODE" value="<?=$this->PROJECT_CODE?>" placeholder="Enter Code" type="text">
                  <input id="originalPROJECT_CODE" name="originalPROJECT_CODE" value="<?=$this->PROJECT_CODE?>" type="hidden">
              </div>

			<div class='required'>
            <label for="PROJECT_TITLE" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Project Title</label>
            <div class="col-md-5">
                <input class="form-control required" id="PROJECT_TITLE" name="PROJECT_TITLE" value="<?=$this->PROJECT_TITLE?>" placeholder="Enter Title" required="required" type="text">
              	<input id="originalPROJECT_TITLE" name="originalPROJECT_TITLE" value="<?=$this->PROJECT_TITLE?>" type="hidden">
            </div>
            </div>


        </div>

   		<div class="form-group required " id="REQUESTOR_NAMEFormGroup">
   			<label for="REQUESTOR_NAME" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="">Requestor Name</label>
   			<div class="col-md-3">
   			<input class="form-control" id="REQUESTOR_NAME" name="REQUESTOR_NAME" value="<?=$this->REQUESTOR_NAME?>" placeholder="Enter Requestor Name" required="required" type="text">
   			<input id="originalREQUESTOR_NAME" name="originalREQUESTOR_NAME" value="<?=$this->REQUESTOR_NAME?>" type="hidden">
   			</div>
   			<label for="REQUESTOR_EMAIL" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="">Requestor Email</label>
   			<div class="col-md-5">
   			<input class="form-control" id="REQUESTOR_EMAIL" name="REQUESTOR_EMAIL" value="<?=$this->REQUESTOR_EMAIL;?>" placeholder="Enter Requestor Email" required="required" type="email">
   			<input id="originalREQUESTOR_EMAIL" name="originalREQUESTOR_EMAIL" value="<?=$this->REQUESTOR_EMAIL;?>" type="hidden">
   			</div>
   		</div>

   		<div class='form-group required'>
        	<label for='CIO' class='col-md-2 control-label ceta-label-left'>CIO</label>
        	<div class='col-md-3'>
              	<select class='form-control select' id='CIO'
                  	          name='CIO'
                  	          required='required'
                  	          data-tags="true" data-placeholder="Select CIO" data-allow-clear="true"
                  	           >
            	<option value=''>Select CIO<option>
                <?php
                    foreach ($allCio as $key => $value) {
                         $displayValue = trim($value);
                         $returnValue  = trim($value);
                         ?><option value='<?=$returnValue?>' <?=trim($this->CIO)==$returnValue ? 'selected' : null;?>><?=$displayValue?></option><?php
                    }
                ?>
               	</select>
            </div>

            <div class='required'>
            <label for='rfsStatus' class='col-md-2 control-label ceta-label-left'>RFS Status</label>
        	<div class='form-group col-md-3' id='rfsStatus'>
        		<?php
        		foreach (self::$rfsStatus as $rfsState) {
        		    $checked = trim($this->RFS_STATUS)== $rfsState ? ' checked ' : null;
        		    $checked =  $_SESSION['isRfs'] && $rfsState==self::RFS_STATUS_PIPELINE? ' checked ' : $checked;
        		    $disabled = $_SESSION['isRfs'] ? ' disabled ' : null ;
        		    ?><label class="radio-inline"><input type="radio" name="RFS_STATUS" <?=$checked?> value='<?=$rfsState?>' required='required' <?=$disabled;?> ><?=$rfsState?></label>
        		    <?php
        		}
        		?>
            </div>
         	</div>



        </div>

        <div class="form-group " id="RFS_TypeIlcFormGroup">
			<div class='required'>
        	<label for="ILC_WORK_ITEM" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="">ILC Work Item</label>
            <div class="col-md-3">
            <input class="form-control" id="ILC_WORK_ITEM" name="ILC_WORK_ITEM" value="<?=$this->ILC_WORK_ITEM?>" placeholder="ILC_WORK_ITEM" type="text">
            </div>
        	</div>

			<div class='required'>
            <label for='Flags' class='col-md-2 control-label ceta-label-left'>Flags</label>
        	<div class='form-group col-md-3' id='Flags'>
        		<?php
        		foreach (self::$rfsType as $rfsType) {
        		    $checked = trim($this->RFS_TYPE)== $rfsType ? ' checked ' : null;
        		    ?><label class="radio-inline"><input type="radio" name="RFS_TYPE" <?=$checked?> value='<?=$rfsType?>' required='required' ><?=$rfsType?></label>
        		    <?php
        		}
        		?>
            </div>
         	</div>
        </div>

        <div class="form-group" id='LinkToPgmpFormGroup' >
            <label for="LINK_TO_PGMP" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Paste URL Link to PGMP Document" >Link to PGMP</label>
            <div class="col-md-7">
                <input class="form-control" id="LINK_TO_PGMP" name="LINK_TO_PGMP" value="<?=$this->LINK_TO_PGMP?>" placeholder="URL Link to PGMP" type="text">
              	<input id="originalLINK_TO_PGMP" name="originalLINK_TO_PGMP" value="<?=$this->LINK_TO_PGMP?>" type="hidden">
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

    static function htmlHeaderCells(){
        $headerCells = "";
        foreach (rfsRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        return $headerCells;
    }

}