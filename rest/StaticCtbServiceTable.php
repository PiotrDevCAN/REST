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
class StaticCtbServiceTable extends DbTable
{
    const ENABLED = 'enabled';
    const DISABLED = 'disabled';

    static function getAllCtbSubService($predicate){
        $sql = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . allTables::$STATIC_ORGANISATION;
        $sql.= " WHERE 1=1 ";
        $sql.= empty($predicate) ? null : " AND " . $predicate;
        $sql .= " ORDER BY ORGANISATION, CTB_SUB_SERVICE  ";
        $resultSet = db2_exec($_SESSION['conn'], $sql);

        $allCtbServices = array();
        if($resultSet){
            while (($row=db2_fetch_assoc($resultSet))==true) {
                $allCtbServices[trim($row['ORGANISATION'])][] = trim($row['CTB_SUB_SERVICE']);
            }
        } else {
            DbTable::displayErrorMessage($resultSet,__CLASS__, __METHOD__, $sql);
            return false;
        }
        return $allCtbServices;
    }

    static function disableService($ctbService,$ctbSubService){
        $sql = 'UPDATE ';
        $sql.= $_SESSION['Db2Schema'] . "." . allTables::$STATIC_ORGANISATION;
        $sql.= " SET STATUS='" . self::DISABLED . "' ";
        $sql.= " WHERE ORGANISATION='" . db2_escape_string($ctbService) . "'  ";
        $sql.= "   AND CTB_SUB_SERVICE='" . db2_escape_string($ctbSubService) . "'  ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }


    static function enableService($ctbService,$ctbSubService){
        $sql = 'UPDATE ';
        $sql.= $_SESSION['Db2Schema'] . "." . allTables::$STATIC_ORGANISATION;
        $sql.= " SET STATUS='" . self::ENABLED . "' ";
        $sql.= " WHERE ORGANISATION='" . db2_escape_string($ctbService) . "'  ";
        $sql.= "   AND CTB_SUB_SERVICE='" . db2_escape_string($ctbSubService) . "'  ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }

    function returnForDataTables(){
        $sql = " SELECT * ";
        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        //        $data = array();
        $displayAble = array();

        while (($row=db2_fetch_assoc($rs))==true) {
            $display = array();
            $row = array_map('trim', $row);
            $display['ORGANISATION'] = $row['ORGANISATION'];
            $display['CTB_SUB_SERVICE'] = $row['CTB_SUB_SERVICE'];
            $display['STATUS'] = self::getStatusCellWithButton($row['STATUS'], $row['ORGANISATION'], $row['CTB_SUB_SERVICE']);
            //           $data[] = array($display['COUNTRY'],$display['MARKET'],$display['STATUS']);
            $displayAble[] = $display;
        }
        return $displayAble;
    }

    static function getStatusCellWithButton($status, $service, $subService){
        $checked = $status==self::ENABLED ? 'checked' : null;
        $buttons = "<input class='toggle' type='checkbox'  $checked data-toggle='toggle' data-status='" . $status . "' data-ctbservice='" . $service . "'  data-ctbsubservice='" . $subService . "' data-size='mini' >&nbsp;";
        return array('display'=>$buttons,'sort'=>$status);

    }



}