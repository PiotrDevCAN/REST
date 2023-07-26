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
class staticBespokeRateTable extends DbTable
{
    function addGlyphicons(&$row){
        $rfsId = trim($row['RFS_ID']);
        $bespokeRateId = trim($row['BESPOKE_RATE_ID']);
        $resourceType = trim($row['RESOURCE_TYPE']);
        $PSBand = trim($row['PS_BAND']);
        $row['RFS_ID'] = "";
        $row['RFS_ID'] .="<button type='button' class='btn btn-danger btn-xs deleteRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-id='" . $bespokeRateId . "'>  
                <span class='glyphicon glyphicon-trash' aria-hidden='true'  data-toggle='tooltip' title='Delete Record' ></span>
            </button>";
        $row['RFS_ID'] .="&nbsp;<button type='button' class='btn btn-success btn-xs editRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI."' aria-label='Left Align' data-id='" . $bespokeRateId . "'>
            <span data-toggle='tooltip' class='glyphicon glyphicon-edit ' aria-hidden='true' title='Edit Record'></span>
        </button>";
        $row['RFS_ID'] .= " <span>".$rfsId."</span>";
        $row['RESOURCE_TYPE'] = "";
        $row['RESOURCE_TYPE'] .= " <span>".$resourceType."</span>";
        $row['PS_BAND'] = "";
        $row['PS_BAND'] .= " <span>".$PSBand."</span>";
    }

    function returnForDataTables(){
        $sql = " SELECT 
            B.*,
            RR.RESOURCE_NAME,
            RT.RESOURCE_TYPE AS RESOURCE_TYPE,
            PSB.BAND AS PS_BAND,
            RT.RESOURCE_TYPE_ID,
            PSB.BAND_ID AS PS_BAND_ID";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " AS B ";

        // Resource Requests
        $sql .= " LEFT JOIN " .  $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " as RR ";
        $sql .= " ON B.RESOURCE_REFERENCE = RR.RESOURCE_REFERENCE ";

        // Resource Type
        $sql .= " LEFT JOIN " .  $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_RESOURCE_TYPE . " as RT ";
        $sql .= " ON B.RESOURCE_TYPE_ID = RT.RESOURCE_TYPE_ID ";
        
        // PS Band
        $sql .= " LEFT JOIN " .  $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_PS_BAND . " as PSB ";
        $sql .= " ON B.PS_BAND_ID = PSB.BAND_ID ";
        
        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        $displayAble = array();

        while (($row = db2_fetch_assoc($rs))==true) {

            $this->addGlyphicons($row);
            
            $row = array_map('trim', $row);
            $displayAble[] = $row;
        }
        return $displayAble;
    }
}