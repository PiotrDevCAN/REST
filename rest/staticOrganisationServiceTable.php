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
class staticOrganisationServiceTable extends DbTable
{
    const ENABLED = 'enabled';
    const DISABLED = 'disabled';

    static function getAllOrganisationsAndServices($predicate){
        $sql = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_ORGANISATION;
        $sql.= " WHERE 1=1 ";
        $sql.= empty($predicate) ? null : " AND " . $predicate;
        $sql .= " ORDER BY ORGANISATION, SERVICE  ";
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        $allOrganisations = array();
        if($rs){
            while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
                $allOrganisations[trim($row['ORGANISATION'])][] = trim($row['SERVICE']);
            }
        } else {
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }
        return $allOrganisations;
    }

    static function disableService($organisation,$service){
        $sql = 'UPDATE ';
        $sql.= $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_ORGANISATION;
        $sql.= " SET STATUS='" . self::DISABLED . "' ";
        $sql.= " WHERE ORGANISATION='" . htmlspecialchars($organisation) . "'  ";
        $sql.= "   AND SERVICE='" . htmlspecialchars($service) . "'  ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }


    static function enableService($organisation,$service){
        $sql = 'UPDATE ';
        $sql.= $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_ORGANISATION;
        $sql.= " SET STATUS='" . self::ENABLED . "' ";
        $sql.= " WHERE ORGANISATION='" . htmlspecialchars($organisation) . "'  ";
        $sql.= "   AND SERVICE='" . htmlspecialchars($service) . "'  ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }

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
            $organisation = $row['ORGANISATION'];
            $service = $row['SERVICE'];
            $status = $row['STATUS'];
            $display['ORGANISATION'] = "";
            $display['ORGANISATION'] .="<button type='button' class='btn btn-success btn-xs editRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI."' aria-label='Left Align' data-organisation='" . $organisation . "' data-service='" . $service . "' data-status='" . $status . "'>
                <span data-toggle='tooltip' class='glyphicon glyphicon-edit ' aria-hidden='true' title='Edit Record'></span>
            </button>";
            $display['ORGANISATION'] .="&nbsp;<button type='button' class='btn btn-danger btn-xs deleteRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-organisation='" . $organisation . "' data-service='" . $service . "' data-status='" . $status . "'>  
                    <span class='glyphicon glyphicon-trash' aria-hidden='true'  data-toggle='tooltip' title='Delete Record' ></span>
                </button>";
            $display['ORGANISATION'] .= " <span>".$organisation."</span>";
            $display['SERVICE'] = $service;
            $display['STATUS'] = self::getStatusCellWithButton($row['STATUS'], $row['ORGANISATION'], $row['SERVICE']);
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