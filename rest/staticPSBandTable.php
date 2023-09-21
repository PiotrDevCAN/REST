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
class staticPSBandTable extends DbTable
{
    function addGlyphicons(&$row){
        $bandId = trim($row['BAND_ID']);
        $band = trim($row['BAND']);
        $row['BAND_ID'] = "";
        $row['BAND_ID'] .="<button type='button' class='btn btn-danger btn-xs deleteRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-id='" . $bandId . "'>  
                <span class='glyphicon glyphicon-trash' aria-hidden='true'  data-toggle='tooltip' title='Delete Record' ></span>
            </button>";
        $row['BAND_ID'] .="&nbsp;<button type='button' class='btn btn-success btn-xs editRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI."' aria-label='Left Align' data-id='" . $bandId . "' data-band='" . $band . "'>
            <span data-toggle='tooltip' class='glyphicon glyphicon-edit ' aria-hidden='true' title='Edit Record'></span>
        </button>";
        $row['BAND_ID'] .= " <span>".$bandId."</span>";
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
            
            $this->addGlyphicons($row);
            
            $display = array();
            $row = array_map('trim', $row);
            
            $display['BAND_ID'] = $row['BAND_ID'];
            $display['BAND'] = $row['BAND'];
            $displayAble[] = $display;
        }
        return $displayAble;
    }
}