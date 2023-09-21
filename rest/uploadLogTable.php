<?php
namespace rest;

use itdq\DbTable;

class uploadLogTable extends DbTable
{
    function lastCompletedLoad($uploadTable= null){

        // $uploadTable = empty($uploadTable)? allTables::$INFLIGHT_PROJECTS : $uploadTable;

        $sql = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE UPLOAD_STATUS='Completed' AND UPLOAD_TABLENAME='" . $uploadTable . "' ";
        $sql .= " ORDER BY UPLOAD_ID DESC ";
        $sql .= " OPTIMIZE FOR 1 ROW ";

        $resultSet = $this->execute($sql);
        $row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC);

        if($row){
            $uploadLogRecord = new uploadLogRecord();
            $uploadLogRecord->setFromArray($row);
            return $uploadLogRecord;
        } else {
            return false;
        }
    }

    function lastLoad($uploadTable= null){

        // $uploadTable = empty($uploadTable)? allTables::$INFLIGHT_PROJECTS : $uploadTable;

        $sql = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE UPLOAD_TABLENAME='" . $uploadTable . "' ";
        $sql .= " ORDER BY UPLOAD_ID DESC ";
        $sql .= " OPTIMIZE FOR 1 ROW    ";

        $resultSet = $this->execute($sql);

        $row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC);

        if($row){
            $uploadLogRecord = new uploadLogRecord();
            $uploadLogRecord->setFromArray($row);
            return $uploadLogRecord;
        } else {
            return false;
        }
    }

    function wasLastLoadSuccesssful($uploadTable= null){
        $lastCompletedLoad = $this->lastCompletedLoad($uploadTable);
        $lastLoad = $this->lastLoad($uploadTable);
        return array('Successful' => $lastCompletedLoad->UPLOAD_ID == $lastLoad->UPLOAD_ID, 'lastCompletedLogRecord'=>$lastCompletedLoad, 'lastLoadLogRecord'=>$lastLoad) ;
    }
}