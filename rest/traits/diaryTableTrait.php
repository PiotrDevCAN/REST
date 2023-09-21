<?php

namespace rest\traits;

use itdq\AllItdqTables;
use itdq\DbTable;
use itdq\DiaryTable;
use rest\allTables;

trait diaryTableTrait
{
    static $SEND_EMAIL_NOTIFICATION = true;
    static $DONT_SEND_EMAIL_NOTIFICATION = false;
    
    static $DIARY_ENTRY_ASSIGNMENT = 'assignment';
    
    // protected $diaryWording = array(self::$DIARY_ENTRY_ASSIGNMENT=>'');
    protected $diaryWording = array('assignment'=>'');
    
    static function insertEntry($entry,$resourceRef) {
        
        $diaryRef = DiaryTable::insertEntry($entry);
        
        $sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_DIARY . " ( DIARY_REFERENCE, RESOURCE_REFERENCE) ";
        $sql .= " Values ('" . htmlspecialchars(trim($diaryRef)) . "','" . htmlspecialchars($resourceRef) . "' ) ";
        
        $rs = sqlsrv_query( $GLOBALS['conn'], $sql );
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
        $sql.= " WHERE RD.RESOURCE_REFERENCE = " . htmlspecialchars($resourceReference);
        $sql.= " ORDER BY D.CREATED desc ";
        
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        if (! $rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        $comment = '<table class="table table-striped" ><tbody>';
        while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
            $comment.= "<tr><td class='diaryEntryCell'>" . $row['ENTRY'] . "</td><td class='diaryCreatorCell'><b>" . $row['CREATOR'] . "</b><br>" . $row['CREATED'] . "</small></td><tr>";
        }
        $comment.= '</tbody></table>';
        
        return $comment;
    }
    
    static function getLatestDiaryEntriesForRequests(array $requests){
        $sql = "select LR.RESOURCE_REFERENCE, LR.LATEST_DIARY_REF, D2.ENTRY, D2.CREATOR, D2.CREATED ";
        $sql.= "from ( Select RESOURCE_REFERENCE, MAX(DIARY_REFERENCE) as LATEST_DIARY_REF "; 
        $sql.= "       from " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_DIARY . " as LR ";
        $sql.= "       where RESOURCE_REFERENCE IN(" . htmlspecialchars(implode(',', $requests)).  ")  ";
        $sql.= "      group by RESOURCE_REFERENCE) AS LR ";
        $sql.= "left join " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$DIARY . " as D2 ";
        $sql.= "on LR.LATEST_DIARY_REF = D2.DIARY_REFERENCE ";
        $sql.= "order by 1";
        
        error_log($sql);
        
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        if (! $rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        $entries = array();
        while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
            $entries[$row['RESOURCE_REFERENCE']] = $row;
        }
        
        return $entries;   
    }
    
    function getArchieved($diaryReference=null){
        $sql  = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE DIARY_REFERENCE = '" . htmlspecialchars($diaryReference) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return $rs;
    }
}