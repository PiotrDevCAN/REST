<?php
namespace rest;

use itdq\DbTable;

class rfsTable extends DbTable
{

    function returnAsArray($predicate=null){
        $sql .= " SELECT * ";
        $sql .= " FROM  " . $_SESSION['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql .= !empty($predicate) ? " WHERE  $predicate " : null ;

        $resultSet = $this->execute($sql);

        $resultSet ? null : die("SQL Failed");

        $allData = null;

        while(($row = db2_fetch_assoc($resultSet))==true){
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