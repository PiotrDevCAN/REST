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

    static public $columnHeadings = array("RFS ID", "PRN", "Project Title", "Project Code", "Requestor Name", "Requestor Email", "CIO", "Link to PGMP", "RFS Creator", "RFS Created");

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
        <div class="form-group required" id="RFS_IDFormGroup">

        	<label for="RFS_ID" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="">RFS ID</label>
        	<div class="col-md-2">
        	<input class="form-control" id="RFS_ID" name="RFS_ID" value="<?=$this->RFS_ID?>" placeholder="Enter RFS Id" required="required" type="text" <?=$notEditable?>>
        	<input id="originalRFS_ID" name="originalRFS_ID" value="<?=$this->RFS_ID?>" type="hidden">
        	</div>

        	<div class='col-md-1'></div>

        	<label for="PRN" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="">PRN</label>
            <div class="col-md-2">
            <input class="form-control" id="PRN" name="PRN" value="<?=$this->PRN?>" placeholder="PRN" type="text">
            <input id="originalPRN" name="originalPRN" value="<?=$this->PRN?>" type="hidden">
            </div>


        </div>
   		<div class="form-group required" id="PROJECT_TITLEFormGroup" >

              <label for="PROJECT_CODE" class="col-md-2	 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Project Code</label>
              <div class="col-md-3">
                  <input class="form-control " id="PROJECT_CODE" name="PROJECT_CODE" value="<?=$this->PROJECT_CODE?>" placeholder="Enter Code" required="required" type="text">
                  <input id="originalPROJECT_CODE" name="originalPROJECT_CODE" value="<?=$this->PROJECT_CODE?>" type="hidden">
              </div>

            <label for="PROJECT_TITLE" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Project Title</label>
            <div class="col-md-5">
                <input class="form-control required" id="PROJECT_TITLE" name="PROJECT_TITLE" value="<?=$this->PROJECT_TITLE?>" placeholder="Enter Title" required="required" type="text">
              	<input id="originalPROJECT_TITLE" name="originalPROJECT_TITLE" value="<?=$this->PROJECT_TITLE?>" type="hidden">
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
        </div>

        <?php
   		$this->formHiddenInput('RFS_CREATOR',$GLOBALS['ltcuser']['mail'],'RFS_CREATOR');
   		$this->formHiddenInput('mode',$mode,'mode');
   		$this->formInput('Link to PGMP','LINK_TO_PGMP');

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateRfs',null,'Update') :  $this->formButton('submit','Submit','saveRfs',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetRfs',null,'Reset','btn-warning');
   		$allButtons[] = $submitButton;
   		$allButtons[] = $resetButton;
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