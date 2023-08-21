<?php

namespace rest\traits;

use itdq\DbTable;
use rest\traits\diaryTableTrait;

trait resourceRequestDiaryTableTrait
{
    use diaryTableTrait;

    function getArchieved($resourceReference=null){
        $sql  = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE RESOURCE_REFERENCE = '" . htmlspecialchars($resourceReference) . "' ";

        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return $rs;
    }
}