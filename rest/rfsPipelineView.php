<?php
namespace rest;

use itdq\DbTable;
use \DateTime;
use itdq\Navbar;

/*
 *
 *
create view ROB_DEV.RFS_PIPELINE
as
select RFS_ID, PROJECT_TITLE, count(RR.RESOURCE_REFERENCE) as REQUESTS, min(RR.START_DATE) as FROM, max(RR.END_DATE) as TO, VALUE_STREAM, LINK_TO_PGMP
from ROB_DEV.RFS as RFS
left join ROB_DEV.RESOURCE_REQUESTS as RR
on RFS.RFS_ID = RR.RFS
where RFS_STATUS = 'Internal Pipeline'
group by RFS_ID, PROJECT_TITLE, VALUE_STREAM, LINK_TO_PGMP
 *
 *
 */

class rfsPipelineView extends DbTable
{
    function returnAsArray($predicate=null){
        // $sql  = " SELECT * ";
        // $sql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS_PIPELINE . " as RFS ";
        // $sql .= " WHERE 1=1 " ;

        $sql = "select RFS_ID, PROJECT_TITLE, count(RR.RESOURCE_REFERENCE) as REQUESTS, min(RR.START_DATE) as FROM, max(RR.END_DATE) as TO, ORGANISATION, LINK_TO_PGMP";
		$sql .= " from " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " as RFS";
		$sql .= " left join " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " as RR";
		$sql .= " on RFS.RFS_ID = RR.RFS";
		$sql .= " where RFS_STATUS = '" . rfsRecord::RFS_STATUS_PIPELINE . "'";
		$sql .= " group by RFS_ID, PROJECT_TITLE, ORGANISATION, LINK_TO_PGMP";

        $sql .= !empty($predicate) ? " AND  $predicate " : null ;

        $resultSet = $this->execute($sql);

        $resultSet ? null : die("SQL Failed");

        $allData = array();

        while(($row = db2_fetch_assoc($resultSet))==true){
            $row = array_map('trim',$row);
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
        $rfsId = $row['RFS_ID'];
        $today = new \DateTime();
        $inTheFuture = !empty($row['FROM']) ? DateTime::createFromFormat('Y-m-d',$row['FROM'])>$today : false;
        $ableToGoLive = $inTheFuture>0;

        $disableIdNotAbleToGoLive = $ableToGoLive ? null : ' readonly ';
        $toolTip = $ableToGoLive ? "Click to toggle RFS to Live Status" : "Unable to go-live, check Resource Request Start Dates";
        $amberIfNotLive = $ableToGoLive ? ' btn-success ' : ' btn-warning ';

        $row['RFS_ID'] = "";
        $row['RFS_ID'].= "<button type='button' class='btn btn-xs $amberIfNotLive goLiveRfs ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI."' $disableIdNotAbleToGoLive aria-label='Left Align' data-rfsid='" .$rfsId . "'>";
        $row['RFS_ID'].= "<span class='glyphicon glyphicon-thumbs-up' aria-hidden='true' data-html='true' data-toggle='tooltip' title='$toolTip' ></span>";
        $row['RFS_ID'].= "</button>&nbsp;";

        $row['RFS_ID'].= "<button type='button' class='btn  btn-xs editRfs ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-rfsid='" .$rfsId . "'>";
        $row['RFS_ID'].= "<span class='glyphicon glyphicon-edit' aria-hidden='true'  data-toggle='tooltip' title='Edit RFS' ></span>";
        $row['RFS_ID'].= "</button>&nbsp;";

        $row['RFS_ID'].= "<button type='button' class='btn btn-danger btn-xs deleteRfs ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-rfsid='" .$rfsId . "'>";
        $row['RFS_ID'].= "<span class='glyphicon glyphicon-trash' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Delete RFS<br/><em>Can not be recovered</em>' ></span>";
        $row['RFS_ID'].= "</button>&nbsp;" . $rfsId;

        $linkToPgmp = trim($row['LINK_TO_PGMP']);
        $row['LINK_TO_PGMP'] = empty($linkToPgmp) ? null : "<a href='$linkToPgmp' target='_blank' >$linkToPgmp</a>";
    }



    function  archiveRfs($rfsid){
        if(empty($rfsid)){
            return false;
        }

        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS;
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