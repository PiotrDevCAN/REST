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
class staticResourceTraitsTable extends DbTable
{
    function addGlyphicons(&$row){
        $id = trim($row['ID']);
        $row['ID'] = "";
        $row['ID'] .="<button type='button' class='btn btn-danger btn-xs deleteRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-id='" . $id . "'>  
                <span class='glyphicon glyphicon-trash' aria-hidden='true'  data-toggle='tooltip' title='Delete Record' ></span>
            </button>";
        $row['ID'] .="&nbsp;<button type='button' class='btn btn-success btn-xs editRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-id='" . $id . "'>  
            <span class='glyphicon glyphicon-edit' aria-hidden='true'  data-toggle='tooltip' title='Edit Record' ></span>
        </button>";
    }

    function returnForDataTables(){
        $sql = " SELECT RT.*,  SRT.RESOURCE_TYPE, SPSB.BAND AS PS_BAND ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " AS RT";

        // Resource Type
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_RESOURCE_TYPE . " as SRT ";
        $sql .= " ON RT.RESOURCE_TYPE_ID = SRT.RESOURCE_TYPE_ID ";

        // PS Band
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_PS_BAND. " as SPSB ";
        $sql .= " ON RT.PS_BAND_ID = SPSB.BAND_ID ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        $displayAble = array();

        while (($row = sqlsrv_fetch_array($rs))==true) {
            
            $this->addGlyphicons($row);
            
            $display = array();
            $row = array_map('trim', $row);
            $display['ID'] = $row['ID'];
            $display['RESOURCE_NAME'] = $row['RESOURCE_NAME'];
            $display['RESOURCE_TYPE'] = $row['RESOURCE_TYPE'];
            $display['PS_BAND'] = $row['PS_BAND'];
            $display['PS_BAND_OVERRIDE'] = $row['PS_BAND_OVERRIDE'];
            $displayAble[] = $display;
        }
        return $displayAble;
    }
    
    // function returnForResourceName($resourceName) {
    //     $sql = " SELECT * ";
    //     $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " AS RT";
    //     $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_RESOURCE_TYPE . " AS SRT ";
    //     $sql.= " ON RT.RESOURCE_TYPE_ID = SRT.RESOURCE_TYPE_ID ";
    //     $sql.= " WHERE RT.RESOURCE_NAME = '" . $resourceName . "'";

    //     $rs = sqlsrv_query($GLOBALS['conn'], $sql);

    //     if(!$rs){
    //         DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
    //         return false;
    //     }
    //     $row = sqlsrv_fetch_array($rs);

    //     return $row;
    // }
}