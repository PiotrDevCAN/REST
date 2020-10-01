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
        
        $rs = DB2_EXEC ( $_SESSION ['conn'], $sql );
        if (! $rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        return	db2_last_insert_id($_SESSION ['conn']);        
    }
    
    static function getFormattedDiaryForResourceRequest($resourceReference){
        
        $sql = " SELECT ENTRY, CREATOR, CREATED ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$DIARY . " AS D ";
        $sql.= " LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_DIARY . " AS RD ";
        $sql.= " ON RD.DIARY_REFERENCE = D.DIARY_REFERENCE ";
        $sql.= " WHERE RD.RESOURCE_REFERENCE = " . db2_escape_string($resourceReference);
        $sql.= " ORDER BY D.CREATED desc ";
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        if (! $rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        $comment = '';
        while(($row=db2_fetch_assoc($rs))==true){            
            $comment.= $row['ENTRY'] . "<br/><small><b>" . $row['CREATOR'] . "</b><br>" . $row['CREATED'] . "</small><br/>";
        }
        
        return $comment;
        
        
        
    }


}