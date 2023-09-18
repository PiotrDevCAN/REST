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
class staticResourceRateTable extends DbTable
{    
    function addGlyphicons(&$row){
        $resourceRateId = $row['ID'];
        $resourceType = $row['RESOURCE_TYPE'];
        $row['RESOURCE_TYPE'] = "";
        $row['RESOURCE_TYPE'] .="<button type='button' class='btn btn-danger btn-xs deleteRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-id='" . $resourceRateId . "'>  
                <span class='glyphicon glyphicon-trash' aria-hidden='true' data-toggle='tooltip' title='Delete Record' ></span>
            </button>";
        $row['RESOURCE_TYPE'] .="&nbsp;<button type='button' class='btn btn-success btn-xs editRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-id='" . $resourceRateId . "'>  
                <span class='glyphicon glyphicon-edit' aria-hidden='true' data-toggle='tooltip' title='Edit Record' ></span>
            </button>";
        $row['RESOURCE_TYPE'] .= " <span>".$resourceType."</span>";
    }

    function returnForDataTables(){
        $sql = " SELECT *, PSB.BAND AS PS_BAND ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " AS RTR ";

        // Resource Type
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_RESOURCE_TYPE . " AS RT ";
        $sql.= " ON RTR.RESOURCE_TYPE_ID = RT.RESOURCE_TYPE_ID ";

        // PS Band
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_PS_BAND . " AS PSB ";
        $sql.= " ON RTR.PS_BAND_ID = PSB.BAND_ID ";

        // Band
        // $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_BAND . " AS B ";
        // $sql.= " ON RTR.BAND_ID = B.BAND_ID ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $displayAble = array();

        while($row = sqlsrv_fetch_array($rs)){
            
            $this->addGlyphicons($row);
            
            $display = array();
            $row = array_map('trim', $row);
            
            $display['RESOURCE_TYPE'] = $row['RESOURCE_TYPE'];
            $display['PS_BAND'] = $row['PS_BAND'];
            // $display['BAND'] = $row['BAND'];
            $display['TIME_PERIOD_START'] = $row['TIME_PERIOD_START'];
            $display['TIME_PERIOD_END'] = $row['TIME_PERIOD_END'];
            $display['DAY_RATE'] = $row['DAY_RATE'];
            $display['HOURLY_RATE'] = $row['HOURLY_RATE'];
            $displayAble[] = $display;
        }
        return $displayAble;
    }

    function returnForResourceTypeRate($resourceTypeId, $PSBandId) {
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " AS RTR ";

        // Resource Type
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_RESOURCE_TYPE . " AS RT ";
        $sql.= " ON RTR.RESOURCE_TYPE_ID = RT.RESOURCE_TYPE_ID ";

        // PS Band
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_PS_BAND . " AS PSB ";
        $sql.= " ON RTR.PS_BAND_ID = PSB.BAND_ID ";

        // Band
        // $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_BAND . " AS B ";
        // $sql.= " ON RTR.BAND_ID = B.BAND_ID ";
        
        $sql.= " WHERE RTR.RESOURCE_TYPE_ID = '" . $resourceTypeId . "'";
        $sql.= " AND  RTR.PS_BAND_ID = '" . $PSBandId . "'";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        $row = sqlsrv_fetch_array($rs);

        return $row;
    }

    function returnPSBandsForResourceTypeId($resourceTypeId) {
        $sql = " SELECT SPSB.BAND_ID, SPSB.BAND ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " AS RT";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_RESOURCE_TYPE . " AS SRT ";
        $sql.= " ON RT.RESOURCE_TYPE_ID = SRT.RESOURCE_TYPE_ID ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_PS_BAND . " AS SPSB ";
        $sql.= " ON RT.PS_BAND_ID = SPSB.BAND_ID ";
        $sql.= " WHERE RT.RESOURCE_TYPE_ID = '" . $resourceTypeId . "'";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        $data = array();
        while($row = sqlsrv_fetch_array($rs)){
            $data[$row['BAND_ID']] = $row['BAND'];
        }
        return $data;
    }

    function returnBandsForResourceTypeId($resourceTypeId) {
        $sql = " SELECT SB.BAND_ID, SB.BAND ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " AS RT";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_RESOURCE_TYPE . " AS SRT ";
        $sql.= " ON RT.RESOURCE_TYPE_ID = SRT.RESOURCE_TYPE_ID ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_BAND . " AS SB ";
        $sql.= " ON RT.BAND_ID = SB.BAND_ID ";
        $sql.= " WHERE RT.RESOURCE_TYPE_ID = '" . $resourceTypeId . "'";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        $data = array();
        while($row = sqlsrv_fetch_array($rs)){
            $data[$row['BAND_ID']] = $row['BAND'];
        }
        return $data;
    }
}