<?php
namespace rest;

use itdq\DbRecord;
use itdq\DbTable;

/**
 *
 * @author gb001399
 *
 */
class StaticCtbServiceTable extends DbRecord
{
    static function getAllCtbSubService(){
        $sql = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . allTables::$STATIC_CTB_SERVICE;
        $sql .= " ORDER BY CTB_SERVICE, CTB_SUB_SERVICE  ";
        $resultSet = db2_exec($_SESSION['conn'], $sql);

        $allCtbServices = array();
        if($resultSet){
            while (($row=db2_fetch_assoc($resultSet))==true) {
                $allCtbServices[trim($row['CTB_SERVICE'])][] = trim($row['CTB_SUB_SERVICE']);
            }
        } else {
            DbTable::displayErrorMessage($resultSet,__CLASS__, __METHOD__, $sql);
            return false;
        }
        return $allCtbServices;
    }

}