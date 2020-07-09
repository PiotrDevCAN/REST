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
class StaticOrganisationTable extends DbTable
{
    const ENABLED = 'enabled';
    const DISABLED = 'disabled';

    static function getAllOrganisationsAndServices($predicate){
        $sql = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . allTables::$STATIC_ORGANISATION;
        $sql.= " WHERE 1=1 ";
        $sql.= empty($predicate) ? null : " AND " . $predicate;
        $sql .= " ORDER BY ORGANISATION, SERVICE  ";
        $resultSet = db2_exec($GLOBALS['conn'], $sql);

        $allOrganisations = array();
        if($resultSet){
            while (($row=db2_fetch_assoc($resultSet))==true) {
                $allOrganisations[trim($row['ORGANISATION'])][] = trim($row['SERVICE']);
            }
        } else {
            DbTable::displayErrorMessage($resultSet,__CLASS__, __METHOD__, $sql);
            return false;
        }
        return $allOrganisations;
    }

    static function disableService($organisation,$service){
        $sql = 'UPDATE ';
        $sql.= $_SESSION['Db2Schema'] . "." . allTables::$STATIC_ORGANISATION;
        $sql.= " SET STATUS='" . self::DISABLED . "' ";
        $sql.= " WHERE ORGANISATION='" . db2_escape_string($organisation) . "'  ";
        $sql.= "   AND SERVICE='" . db2_escape_string($service) . "'  ";

        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }


    static function enableService($organisation,$service){
        $sql = 'UPDATE ';
        $sql.= $_SESSION['Db2Schema'] . "." . allTables::$STATIC_ORGANISATION;
        $sql.= " SET STATUS='" . self::ENABLED . "' ";
        $sql.= " WHERE ORGANISATION='" . db2_escape_string($organisation) . "'  ";
        $sql.= "   AND SERVICE='" . db2_escape_string($service) . "'  ";

        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }

    function returnForDataTables(){
        $sql = " SELECT * ";
        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;

        $rs = db2_exec($GLOBALS['conn'], $sql);

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
            $display['SERVICE'] = $row['SERVICE'];
            $display['STATUS'] = self::getStatusCellWithButton($row['STATUS'], $row['ORGANISATION'], $row['SERVICE']);
            //           $data[] = array($display['COUNTRY'],$display['MARKET'],$display['STATUS']);
            $displayAble[] = $display;
        }
        return $displayAble;
    }

    static function getStatusCellWithButton($status, $organisation, $service){
        $checked = $status==self::ENABLED ? 'checked' : null;
        $buttons = "<input class='toggle' type='checkbox'  $checked data-toggle='toggle' data-status='" . $status . "' data-organisation='" . $organisation . "'  data-service='" . $service . "' data-size='mini' >&nbsp;";
        return array('display'=>$buttons,'sort'=>$status);

    }



}