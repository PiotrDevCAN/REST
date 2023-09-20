<?php
namespace rest;

use itdq\DbTable;
use itdq\Navbar;
use rest\traits\tableTrait;

class RRBespokeRateTable extends DbTable
{
    use tableTrait;

    function __construct($table, $pwd = null, $log = true)
    {
        parent::__construct ( $table, $pwd, $log );
    }

    static function buildHTMLTable($tableId = 'bespokeRate'){
        $headerCells = RRBespokeRateRecord::htmlHeaderCellsStatic();
        ?>
        <table id='<?=$tableId;?>Table_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: noneaa;'>
        <thead><tr><?=$headerCells ;?></tr></thead>
        <tbody></tbody>
        <tfoot><tr><?=$headerCells ;?></tr></tfoot>
        </table>
        <?php
    }

    function addGlyphicons(&$row){
        $rfsId = trim($row['RFS_ID']);
        $resourceReferenceId = trim($row['RESOURCE_REFERENCE']);
        $bespokeRateId = trim($row['BESPOKE_RATE_ID']);
        $resourceTypeId = trim($row['RESOURCE_TYPE_ID']);
        $resourceType = trim($row['RESOURCE_TYPE']);
        $PSBandId = trim($row['BAND_ID']);
        $PSBand = trim($row['BAND']);
        if (!empty($bespokeRateId)) {
            $row['RFS_ID'] = "";
            $row['RFS_ID'] .= " <span>".$rfsId."</span>";
        }
        $row['RESOURCE_TYPE'] = "";
        $row['RESOURCE_TYPE'] .= "<p>".$resourceType."</p>";
        $row['BAND'] = "";
        $row['BAND'] .= "<p>".$PSBand."</p>";
    }

    function returnAsArray($predicate=null){
        // $sql  = " SELECT RFS.*, RDR.* ";
        $sql  = " SELECT RFS.RFS_ID, RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME, SRT.RESOURCE_TYPE, SPSB.BAND, SRT.RESOURCE_TYPE_ID, SPSB.BAND_ID, BR.BESPOKE_RATE_ID";
        $sql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " AS RR ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql .= " ON RR.RFS = RFS.RFS_ID ";

        // Bespoke Rates
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$BESPOKE_RATES. " AS BR ";
        $sql .= " ON LOWER(BR.RFS_ID) = LOWER(RR.RFS) AND LOWER(BR.RESOURCE_REFERENCE) = LOWER(RR.RESOURCE_REFERENCE)";

        // Resource Type for BR
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_RESOURCE_TYPE . " as SRT ";
        $sql .= " ON BR.RESOURCE_TYPE_ID = SRT.RESOURCE_TYPE_ID ";

        // PS Band for BR
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_PS_BAND. " as SPSB ";
        $sql .= " ON BR.PS_BAND_ID = SPSB.BAND_ID ";
        
        $sql .= " WHERE 1=1 " ;
        $sql .= " AND RFS.RFS_ID IS NOT NULL AND RFS.RFS_ID != ''";
        $sql .= " AND BR.BESPOKE_RATE_ID IS NOT NULL";
        // $sql .= " AND RR.RESOURCE_NAME IS NOT NULL AND RR.RESOURCE_NAME != ''";
        $sql .= !empty($predicate) ? " AND  $predicate " : null ;
        
        $resultSet = $this->execute($sql);
        $resultSet ? null : die("SQL Failed");
        $allData = array();

        while($row = sqlsrv_fetch_array($resultSet)){
            $testJson = json_encode($row);
            if(!$testJson){
                break; // It's got invalid chars in it that will be a problem later.
            }
            $this->addGlyphicons($row);
            
            foreach ($row as $key => $data){
                $row[] = trim($row[$key]);
                unset($row[$key]);
            }
            $allData[]  = $row;            
        }
        return array('data'=>$allData, 'sql'=>$sql);
    }
}