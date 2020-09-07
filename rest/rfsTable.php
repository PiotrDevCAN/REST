<?php
namespace rest;

use itdq\DbTable;

class rfsTable extends DbTable
{
    protected $rfsMaxEndDate;

    static function rfsPredicateFilterOnPipeline($option=null){
        // Determines if the user is in a group that can only see the pipeline, can NOT see the pipline, or can see both pipeline and live.
        $predicate = null;

        if(empty($option)){
            switch (true) {
                case $_SESSION['isCdi']:
                case $_SESSION['isAdmin']:
                case $_SESSION['isReports']:
                    // can see BOTH
                    $predicate = null;
                    break;
                case $_SESSION['isRfs']:
                    // Can only see pipeline
                   $predicate =  " AND RFS_STATUS='" . rfsRecord::RFS_STATUS_PIPELINE . "' " ;
                    break;
                case $_SESSION['isSupply']:
                case $_SESSION['isDemand']:
                    // Can only see Live
                      $predicate =  " AND RFS_STATUS='" . rfsRecord::RFS_STATUS_LIVE . "' " ;
                    break;
                default:
                    $predicate =  null;
                    break;
            }
        } else {
             $predicate = " AND RFS_STATUS='" . trim($option) . "' " ;
        }
        return $predicate;
    }

    static function loadKnownRfsToJs($predicate=null){
        $sql = " SELECT RFS_ID FROM " . $_SESSION['Db2Schema'] . "." .  allTables::$RFS;

        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        ?><script type="text/javascript">
        var knownRfs = [];
        <?php

        while(($row=db2_fetch_assoc($rs))==true){
            ?>knownRfs.push("<?=trim($row['RFS_ID']);?>");
            <?php
        }
        ?>console.log(knownRfs);<?php
        ?></script><?php

    }

    function returnAsArray($predicate=null, $withArchive=false){
        $sql  = " SELECT * ";
        $sql .= " FROM  " . $_SESSION['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql .= " WHERE 1=1 " ;
        $sql .= $withArchive ? " AND ARCHIVE is not null " : " AND ARCHIVE is null ";
        $sql .= !empty($predicate) ? " AND  $predicate " : null ;

        $resultSet = $this->execute($sql);
        $resultSet ? null : die("SQL Failed");
        $allData = null;

        while(($row = db2_fetch_assoc($resultSet))==true){
            $testJson = json_encode($row);
            if(!$testJson){
                break; // It's got invalid chars in it that will be a problem later.
            }
            $this->addGlyphicons($row);
            foreach ($row as $key=>$data){
                $row[] = trim($row[$key]);
                unset($row[$key]);
            }
            $allData[]  = $row;            
        }
        return array('data'=>$allData,'sql'=>$sql);
    }


    function addGlyphicons(&$row){
        $rfsId = trim($row['RFS_ID']);
        $today = new \DateTime();
        $rfsEndDate = $this->rfsMaxEndDate($rfsId);
        $archiveable = false;
        if($rfsEndDate){
            $archiveable = $rfsEndDate < $today ? true : false;
        }

        if($archiveable) {
            $row['RFS_ID'] = "<button type='button' class='btn btn-warning btn-xs archiveRfs accessRestrict accessAdmin accessDemand accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-floppy-remove' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Archive RFS<br/><em>Safer than deleting</em>' ></span>
              </button>";
        } else {
            $row['RFS_ID'] = ""; /// NEed something so next statement can be an append.
        }        $row['RFS_ID'] .="<button type='button' class='btn btn-success btn-xs slipRfs accessRestrict accessAdmin accessDemand accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-calendar' aria-hidden='true'  data-toggle='tooltip' title='Slip RFS' ></span></button>        
              <button type='button' class='btn btn-success btn-xs editRfs accessRestrict accessAdmin accessDemand accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>              
              <span class='glyphicon glyphicon-edit' aria-hidden='true'  data-toggle='tooltip' title='Edit RFS' ></span>
              </button>"  . "&nbsp;" .  "<button type='button' class='btn btn-danger btn-xs deleteRfs accessRestrict accessAdmin accessDemand accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-trash' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Delete RFS<br/><em>Can not be recovered</em>' ></span>
              </button>" . "&nbsp;" . $rfsId;
        $linkToPgmp = trim($row['LINK_TO_PGMP']);
        $row['LINK_TO_PGMP'] = empty($linkToPgmp) ? null : "<a href='$linkToPgmp' target='_blank' >$linkToPgmp</a>";
    }

    function  rfsMaxEndDate($rfsid){
        if(empty($this->rfsMaxEndDate)){
            // We've not populated the array of RFS & END_DATES, so do that now.
            $sql = " SELECT RFS, MAX(END_DATE) as END_DATE FROM " . $_SESSION['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS ;
            $sql .= " GROUP BY RFS ";

            $rs = db2_exec($GLOBALS['conn'], $sql);

            if(!$rs) {
                DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            }

            while (($row=db2_fetch_assoc($rs))==true) {
                $this->rfsMaxEndDate[strtoupper(trim($row['RFS']))] = isset($row['END_DATE']) ? trim($row['END_DATE']) : null ;
            }
        }
        return isset($this->rfsMaxEndDate[strtoupper(trim($rfsid))]) ? new \DateTime($this->rfsMaxEndDate[strtoupper(trim($rfsid))]) : false;

    }

    function  archiveRfs($rfsid){
        if(empty($rfsid)){
            return false;
        }

        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . allTables::$RFS;
        $sql .= " SET ARCHIVE = CURRENT TIMESTAMP ";
        $sql .= " WHERE RFS_ID ='" . db2_escape_string($rfsid) . "' " ;

        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        return true;
    }




}