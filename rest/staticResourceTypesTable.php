<?php
namespace rest;

use itdq\DbTable;
use itdq\Navbar;

/**
 *
 * @author gb001399
 *
 * ALTER TABLE "ROB_DEV"."STATIC_ORGANISATION" ADD COLUMN "STATUS" CHAR(10) NOT NULL WITH DEFAULT 'enabled';
 * ALTER TABLE "REST_XT"."STATIC_ORGANISATION" ADD COLUMN "STATUS" CHAR(10) NOT NULL WITH DEFAULT 'enabled';
 *
 */
class staticResourceTypesTable extends DbTable
{
    function returnForResourceName($resourceName) {
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " AS RT";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_RESOURCE_TYPE . " AS SRT ";
        $sql.= " ON RT.RESOURCE_TYPE_ID = SRT.RESOURCE_TYPE_ID ";
        $sql.= " WHERE RT.RESOURCE_NAME = '" . $resourceName . "'";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        $row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

        return $row;
    }
}