<?php
namespace rest;

use itdq\DbTable;
use itdq\Navbar;
use rest\traits\tableTrait;

class rfsPcrTable extends DbTable
{
    use tableTrait;

    function __construct($table, $pwd = null, $log = true)
    {
        parent::__construct ( $table, $pwd, $log );
    }

    static function buildHTMLTable($tableId = 'rfsPcr'){
        $RFSPcrHeaderCells = rfsPcrRecord::htmlHeaderCellsStatic();
        ?>
        <table id='<?=$tableId;?>Table_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
        <thead><tr><?=$RFSPcrHeaderCells;?></tr></thead>
        <tbody></tbody>
        <tfoot><tr><?=$RFSPcrHeaderCells ;?></tr></tfoot>
        </table>
        <?php
    }

    function addGlyphicons(&$row){
        $rfsId = trim($row['RFS_ID']);
        $rfsPcrId = trim($row['PCR_ID']);
        $rfsPcrNumber = trim($row['PCR_NUMBER']);
        $archiveable = true;
        $row['PCR_ID'] = "";
        $row['PCR_ID'] .="<button type='button' class='btn btn-success btn-xs createPcr ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-id='" .$rfsPcrId . "' data-rfsid='" .$rfsId . "' data-pcrnumber='" .$rfsPcrNumber . "'>              
                <span class='glyphicon glyphicon-edit' aria-hidden='true'  data-toggle='tooltip' title='Edit RFS Pcr' ></span>
            </button>";
        $row['PCR_ID'] .= $archiveable  ? "<button type='button' class='btn btn-warning btn-xs archiveRfsPcr ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-id='" .$rfsPcrId . "' data-rfsid='" .$rfsId . "' data-pcrnumber='" .$rfsPcrNumber . "'>
                <span class='glyphicon glyphicon-floppy-remove' aria-hidden='true' data-html='true' data-html='true' data-toggle='tooltip' title='Archive RFS PCR Safer than deleting' ></span>
            </button>" : null;
        $row['PCR_ID'] .="<button type='button' class='btn btn-danger btn-xs deleteRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-id='" .$rfsPcrId . "' data-rfsid='" .$rfsId . "' data-pcrnumber='" .$rfsPcrNumber . "'>
                <span class='glyphicon glyphicon-trash' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Delete RFS PCR Can not be recovered' ></span>
            </button>&nbsp;";
        $row['PCR_ID'] .= "<p>".$rfsPcrId."</p>";
    }

    function returnAsArray($predicate=null){
        // $sql  = " SELECT RFS.*, RDR.* ";
        $sql  = " SELECT *";
        $sql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS_PCR . " AS PCR ";
        $sql .= " WHERE 1=1 " ;
        $sql .= !empty($predicate) ? " AND  $predicate " : null ;
        
        $resultSet = $this->execute($sql);
        $resultSet ? null : die("SQL Failed");
        $allData = array();

        while(($row = db2_fetch_assoc($resultSet))==true){
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

    function archivePcr($pcrid){
        if(empty($pcrid)){
            return false;
        }

        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET ARCHIVE = CURRENT TIMESTAMP ";
        $sql .= " WHERE PCR_ID = '" . db2_escape_string($pcrid) . "' " ;

        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        return true;
    }
}