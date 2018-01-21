<?php
namespace rest;

use itdq\DbTable;

class rfsTable extends DbTable
{

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

    function returnAsArray($predicate=null){
        $sql .= " SELECT * ";
        $sql .= " FROM  " . $_SESSION['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql .= !empty($predicate) ? " WHERE  $predicate " : null ;

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
        return $allData ;
    }


    function addGlyphicons(&$row){
        $rfsId = trim($row['RFS_ID']);
        $row['RFS_ID'] = "<button type='button' class='btn btn-default btn-xs editRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-edit' aria-hidden='true'></span>
              </button>"  . "&nbsp;" .  "<button type='button' class='btn btn-default btn-xs deleteRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-trash' aria-hidden='true'></span>
              </button>" . "&nbsp;" . $rfsId;
        $linkToPgmp = trim($row['LINK_TO_PGMP']);
        $row['LINK_TO_PGMP'] = empty($linkToPgmp) ? null : "<a href='$linkToPgmp' target='_blank' >$linkToPgmp</a>";
    }



}