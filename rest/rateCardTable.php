<?php
namespace rest;

use itdq\DbTable;
use itdq\Navbar;
use rest\traits\tableTrait;

class rateCardTable extends DbTable
{
    use tableTrait;

    function __construct($table, $pwd = null, $log = true)
    {
        parent::__construct ( $table, $pwd, $log );
    }

    static function buildHTMLTable($tableId = 'rateCard'){
        $headerCells = rateCardRecord::htmlHeaderCellsStatic();
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
        $resourceType = trim($row['RESOURCE_TYPE']);
        $PSBand = trim($row['BAND']);
        $bespokeRateId = trim($row['BESPOKE_RATE_ID']);
        $hasBespokeRate = trim($row['HAS_BESPOKE_RATE']);
        $row['RFS_ID'] = "";
        $row['RFS_ID'] .= " <span>".$rfsId."</span>";
        $row['RESOURCE_TYPE'] = "";
        $row['RESOURCE_TYPE'] .= " <span>".$resourceType."</span>";
        $row['BAND'] = "";
        $row['BAND'] .= " <span>".$PSBand."</span>";
        $row['HAS_BESPOKE_RATE'] = "";
        if (!empty($bespokeRateId)) {
            $row['HAS_BESPOKE_RATE'] .="&nbsp;<button type='button' class='btn btn-primary btn-xs previewRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI."' aria-label='Left Align' data-id='" . $bespokeRateId . "'>
                <span data-toggle='tooltip' class='glyphicon glyphicon-tag ' aria-hidden='true' title='Preview Bespoke Rate Record'></span>
            </button>";
        }
        $row['HAS_BESPOKE_RATE'] .= " <span>".$hasBespokeRate."</span>";
    }

    function returnAsArray($predicate=null){
        // $sql  = " SELECT RFS.*, RDR.* ";
        $sql  = " SELECT 
            RFS.RFS_ID, 
            RFS.RFS_CREATED_TIMESTAMP, 
            RR.RESOURCE_REFERENCE, 
            RR.RESOURCE_NAME, 
            SRT.RESOURCE_TYPE, 
            SPSB.BAND, 
            RT.PS_BAND_OVERRIDE,";
        $sql.=" CASE WHEN BR.BESPOKE_RATE_ID IS NOT NULL THEN 'Yes' ELSE 'No' END AS HAS_BESPOKE_RATE,";
        $sql.=" BR.BESPOKE_RATE_ID";

        $sql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " AS RR ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql .= " ON RR.RFS = RFS.RFS_ID ";

        // Resource Traits
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_TRAITS . " as RT ";
        $sql .= " ON RR.RESOURCE_NAME = RT.RESOURCE_NAME ";

        // Resource Type for Trait
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_RESOURCE_TYPE . " as SRT ";
        $sql .= " ON RT.RESOURCE_TYPE_ID = SRT.RESOURCE_TYPE_ID ";

        // PS Band for Trait
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_PS_BAND. " as SPSB ";
        $sql .= " ON RT.PS_BAND_ID = SPSB.BAND_ID ";

        // Check if Bespoke Rate record exists
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$BESPOKE_RATES . " as BR ";
        $sql .= " ON RR.RFS = BR.RFS_ID ";

        $sql .= " WHERE 1=1 " ;
        $sql .= " AND RFS.RFS_ID IS NOT NULL AND RFS.RFS_ID != ''";
        $sql .= " AND RR.RESOURCE_NAME IS NOT NULL AND RR.RESOURCE_NAME != ''";
        // $sql .= " AND (RT.ID IS NOT NULL OR BR.BESPOKE_RATE_ID IS NOT NULL)";
        $sql .= " AND (RT.ID IS NOT NULL)";
        $sql .= !empty($predicate) ? " AND  $predicate " : null ;

        $resultSet = $this->execute($sql);
        $resultSet ? null : die("SQL Failed");
        $allData = array();

        while(($row = sqlsrv_fetch_array($resultSet))==true){
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