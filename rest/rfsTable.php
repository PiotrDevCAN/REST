<?php
namespace rest;

use itdq\DbTable;

class rfsTable extends DbTable
{
    protected $rfsMaxEndDate;

    static function loadKnownRfsToJs($predicate=null){
        $sql = " SELECT RFS_ID FROM " . $_SESSION['Db2Schema'] . "." .  allTables::$RFS;

        $rs = db2_exec($_SESSION['conn'], $sql);

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


        echo $sql;

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
        return $allData ;
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
            $row['RFS_ID'] = "<button type='button' class='btn btn-default btn-xs archiveRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-floppy-remove' aria-hidden='true'></span>
              </button>";
        } else {
            $row['RFS_ID'] = ""; /// NEed something so next statement can be an append.
        }        $row['RFS_ID'] .= "<button type='button' class='btn btn-default btn-xs editRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-edit' aria-hidden='true'></span>
              </button>"  . "&nbsp;" .  "<button type='button' class='btn btn-default btn-xs deleteRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-trash' aria-hidden='true'></span>
              </button>" . "&nbsp;" . $rfsId;
        $linkToPgmp = trim($row['LINK_TO_PGMP']);
        $row['LINK_TO_PGMP'] = empty($linkToPgmp) ? null : "<a href='$linkToPgmp' target='_blank' >$linkToPgmp</a>";
    }

    function  rfsMaxEndDate($rfsid){
        if(empty($this->rfsMaxEndDate)){
            // We've not populated the array of RFS & END_DATES, so do that now.
            $sql = " SELECT RFS, MAX(END_DATE) as END_DATE FROM " . $_SESSION['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS ;
            $sql .= " GROUP BY RFS ";

            $rs = db2_exec($_SESSION['conn'], $sql);

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

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        return true;
    }




}