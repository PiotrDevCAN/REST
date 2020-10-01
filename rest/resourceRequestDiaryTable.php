<?php
namespace rest;

use itdq\DbTable;
use itdq\DateClass;
use itdq\DiaryTable;
use itdq\AllItdqTables;


class resourceRequestDiaryTable extends DbTable
{
    
    static function insertEntry( $entry,$resourceRef) {
        
        $diaryRef = DiaryTable::insertEntry($entry);
        
        $sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_DIARY . " ( DIARY_REFERENCE, RESOURCE_REFERENCE) ";
        $sql .= " Values ('" . db2_escape_string(trim($diaryRef)) . "','" . db2_escape_string($resourceRef) . "' ) ";
        
        $rs = DB2_EXEC ( $GLOBALS['conn'], $sql );
        if (! $rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);     
            return false;
        }
        return	db2_last_insert_id($GLOBALS['conn']);        
    }
    
    static function getFormattedDiaryForResourceRequest($resourceReference){
        
        $sql = " SELECT ENTRY, CREATOR, CREATED ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$DIARY . " AS D ";
        $sql.= " LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_DIARY . " AS RD ";
        $sql.= " ON RD.DIARY_REFERENCE = D.DIARY_REFERENCE ";
        $sql.= " WHERE RD.RESOURCE_REFERENCE = " . db2_escape_string($resourceReference);
        $sql.= " ORDER BY D.CREATED desc ";
        
        $rs = db2_exec($GLOBALS['conn'], $sql);
        if (! $rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        $comment = '';
        while(($row=db2_fetch_assoc($rs))==true){            
            $comment.= $row['ENTRY'] . "<br/><small><b>" . $row['CREATOR'] . "</b><br>" . $row['CREATED'] . "</small><br/>";
        }
        
        return $comment;
        
    }
    
    static function getLatestDiaryEntriesForRequests(array $requests){
        $sql = "select LR.RESOURCE_REFERENCE, LR.LATEST_DIARY_REF, D2.ENTRY, D2.CREATOR, D2.CREATED ";
        $sql.= "from ( Select RESOURCE_REFERENCE, MAX(DIARY_REFERENCE) as LATEST_DIARY_REF "; 
        $sql.= "       from " . $GLOBALS['Db2Schema'] . "." . \rest\allTables::$RESOURCE_REQUEST_DIARY . " as LR ";
        $sql.= "       where RESOURCE_REFERENCE IN(" . db2_escape_string(implode(',', $requests)).  ")  ";
        $sql.= "      group by RESOURCE_REFERENCE) AS LR ";
        $sql.= "left join " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$DIARY . " as D2 ";
        $sql.= "on LR.LATEST_DIARY_REF = D2.DIARY_REFERENCE ";
        $sql.= "order by 1";
        
        error_log($sql);
        
        $rs = db2_exec($GLOBALS['conn'], $sql);
        if (! $rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        $entries = array();
        while(($row=db2_fetch_assoc($rs))==true){
            $entries[$row['RESOURCE_REFERENCE']] = $row;
        }
        
        return $entries;
        
        
    }
    


}