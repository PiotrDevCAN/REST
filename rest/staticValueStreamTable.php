<?php
namespace rest;

use itdq\DbTable;

/**
 *
 * @author gb001399
 *
 * ALTER TABLE "ROB_DEV"."STATIC_ORGANISATION" ADD COLUMN "STATUS" CHAR(10) NOT NULL WITH DEFAULT 'enabled';
 * ALTER TABLE "REST_XT"."STATIC_ORGANISATION" ADD COLUMN "STATUS" CHAR(10) NOT NULL WITH DEFAULT 'enabled';
 *
 */
class staticValueStreamTable extends DbTable
{
    function returnForDataTables(){
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $displayAble = array();

        while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
            $display = array();
            $row = array_map('trim', $row);
            $display['VALUE_STREAM'] = $row['VALUE_STREAM'];
            $display['BUSINESS_UNIT'] = $row['BUSINESS_UNIT'];
            $displayAble[] = $display;
        }
        return $displayAble;
    }
}