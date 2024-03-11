<?php
namespace rest\traits;

use DateInterval;
use Exception;
use itdq\DbTable;
use itdq\Navbar;
use rest\allTables;
use rest\rfsRecord;
use rest\rfsTable;

trait rfsTableTrait
{
    use tableTrait;

    protected $rfsMaxEndDate;

    static function buildHTMLTable($tableId = 'rfs'){
        $RFSheaderCells = rfsRecord::htmlHeaderCellsStatic();
        ?>
        <table id='<?=$tableId;?>Table_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
        <thead><tr><?=$RFSheaderCells;?></tr></thead>
        <tbody></tbody>
        <tfoot><tr><?=$RFSheaderCells ;?></tr></tfoot>
        </table>
        <?php
    }

    static function rfsPredicateFilterOnPipeline($option=null){
        // Determines if the user is in a group that can only see the pipeline, can NOT see the pipline, or can see both pipeline and live.
        $predicate = null;
        if(empty($option)){
            switch (true) {
                case $_SESSION['isCdi']:
                case $_SESSION['isAdmin']:
                case $_SESSION['isReports']:
                    // can see BOTH
                    $predicate = null;
                    break;
                case $_SESSION['isRfs']:
                    // Can only see pipeline
                    $predicate =  " AND RFS_STATUS='" . rfsRecord::RFS_STATUS_PIPELINE . "' " ;
                    break;
                case $_SESSION['isSupply']:
                case $_SESSION['isSupplyX']:
                case $_SESSION['isDemand']:
                    // Can only see Live
                    $predicate =  " AND RFS_STATUS='" . rfsRecord::RFS_STATUS_LIVE . "' " ;
                    break;
                default:
                    $predicate =  null;
                    break;
            }
        } else {
             $predicate = " AND RFS_STATUS='" . trim($option) . "' " ;
        }
        return $predicate;
    }

    static function rfsPredicateFilterOnPipelineNotArchived($option=null){
        $predicate = null;

        $predicate = self::rfsPredicateFilterOnPipeline($option);
        $predicate .= " AND " . rfsTable::NOT_ARCHIVED;

        return $predicate;
    }

    static function archivedPredicate($tableAbbrv = null) {
        $predicate = '';
        $predicate.= !empty($tableAbbrv) ? $tableAbbrv ."." : null ;
        $predicate.= 'ARCHIVE IS NOT NULL ';
        return $predicate;
    }

    static function archivedInLast12MthsPredicate($tableAbbrv = null) {
        $predicate = "(";
        $predicate.= !empty($tableAbbrv) ? $tableAbbrv ."." : null ;
        $predicate.= "ARCHIVE >= DATEADD(month, -12, CURRENT_TIMESTAMP)";
        $predicate.= " AND ";
        $predicate.= !empty($tableAbbrv) ? $tableAbbrv ."." : null ;
        $predicate.= "ARCHIVE <= CURRENT_TIMESTAMP"; 
        $predicate.= ")";
        return $predicate;
    }

    static function notArchivedPredicate($tableAbbrv = null) {
        $predicate = '';
        $predicate.= !empty($tableAbbrv) ? $tableAbbrv ."." : null ;
        $predicate.= 'ARCHIVE IS NULL ';
        return $predicate;
    }

    static function loadKnownRfsToJs($predicate=null){
        $sql = " SELECT RFS_ID FROM " . $GLOBALS['Db2Schema'] . "." .  allTables::$RFS;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        ?><script type="text/javascript">
        var knownRfs = [];
        <?php

        while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
            ?>knownRfs.push("<?=strtoupper(trim($row['RFS_ID']));?>");
            <?php
        }
        ?></script>
        <?php
    }

    static function getRequestorEmail($rfsId){
        $sql = " SELECT REQUESTOR_EMAIL ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS;
        $sql.= " WHERE RFS_ID='" . htmlspecialchars($rfsId) . "' ";
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);            
        }
        
        $row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
        return trim($row['REQUESTOR_EMAIL']);
    }

    function rfsMaxEndDate($rfsid){
        if(empty($this->rfsMaxEndDate)){
            // We've not populated the array of RFS & END_DATES, so do that now.
            $sql = " SELECT RFS, MAX(END_DATE) as END_DATE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS ;
            $sql .= " GROUP BY RFS ";

            $rs = sqlsrv_query($GLOBALS['conn'], $sql);

            if(!$rs) {
                DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            }

            while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
                $this->rfsMaxEndDate[strtoupper(trim($row['RFS']))] = isset($row['END_DATE']) ? trim($row['END_DATE']) : null ;
            }
        }
        return isset($this->rfsMaxEndDate[strtoupper(trim($rfsid))]) ? new \DateTime($this->rfsMaxEndDate[strtoupper(trim($rfsid))]) : false;
    }

    function addGlyphicons(&$row){
        $rfsId = trim($row['RFS_ID']);
        $rfsPcrId = trim($row['PCR_ID']);
        // $today = new \DateTime();
        // $rfsEndDate = $this->rfsMaxEndDate($rfsId);
        // $archiveable = false;
        // if($rfsEndDate){
        //     $archiveable = $rfsEndDate < $today ? true : false;
        // }
        $archiveable = true;
        $pipelineRfs  =  trim($row['RFS_STATUS'])==rfsRecord::RFS_STATUS_PIPELINE  ?  true : false;
        $row['RFS_ID'] = "";
        // $row['RFS_ID'] .="<button type='button' class='btn btn-info btn-xs editRfsId ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
        //         <span class='glyphicon glyphicon-random' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Set different ID for this entry and all related Resource Requests' ></span>
        //     </button>";
        // $row['RFS_ID'] .="<button type='button' class='btn btn-primary btn-xs extendRfs ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
        //         <span class='glyphicon glyphicon-tag' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Create RFS extension' ></span>
        //     </button>"  . "&nbsp";
        $row['RFS_ID'] .= $pipelineRfs  ? "<button type='button' class='btn btn-success btn-xs goLiveRfs ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-rfsid='" .$rfsId . "' >
                <span class='glyphicon glyphicon-thumbs-up' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Release to Live' ></span>
            </button>" : null;    
        // $row['RFS_ID'] .="<button disabled type='button' class='btn btn-success btn-xs slipRfs ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
        //       <span class='glyphicon glyphicon-calendar' aria-hidden='true'  data-toggle='tooltip' title='This function has been depricated' ></span>
        //      </button>";
        $row['RFS_ID'] .="<button type='button' class='btn btn-success btn-xs editRfs ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-rfsid='" .$rfsId . "'>              
                <span class='glyphicon glyphicon-edit' aria-hidden='true'  data-toggle='tooltip' title='Edit RFS' ></span>
            </button>";
        $row['RFS_ID'] .="<button type='button' class='btn btn-info btn-xs createPcr ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-rfsid='" .$rfsId . "' data-rfspcrid='" .$rfsPcrId . "'>
                <span class='glyphicon glyphicon-random' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Create PCR for RFS record' ></span>
            </button>&nbsp;&nbsp;";
        $row['RFS_ID'] .= $archiveable  ? "<button type='button' class='btn btn-warning btn-xs archiveRfs ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-rfsid='" .$rfsId . "' >
                <span class='glyphicon glyphicon-floppy-remove' aria-hidden='true' data-html='true' data-html='true' data-toggle='tooltip' title='Archive RFS Safer than deleting' ></span>
            </button>" : null;
        $row['RFS_ID'] .="<button type='button' class='btn btn-danger btn-xs deleteRfs ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_DEMAND." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_RFS."' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
                <span class='glyphicon glyphicon-trash' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Delete RFS Can not be recovered' ></span>
            </button>&nbsp;";

        $row['RFS_ID'] .= "<p>".$rfsId."</p>";
        $linkToPgmp = trim($row['LINK_TO_PGMP']);
        $row['LINK_TO_PGMP'] = empty($linkToPgmp) ? null : "<a href='$linkToPgmp' target='_blank' >$linkToPgmp</a>";
    }

    function returnAsArray($predicate=null, $withArchive=false, $withButtons = true, $disableCache = false){
        $sql  = " SELECT 
        RFS.RFS_ID, 
		RFS.PRN, 
		RFS.PROJECT_TITLE,
		RFS.PROJECT_CODE,
        RFS.REQUESTOR_NAME,
		RFS.REQUESTOR_EMAIL,
		VS.VALUE_STREAM,
		RFS.BUSINESS_UNIT,
		RFS.ILC_WORK_ITEM,
		RFS.ILC_WORK_ITEM_WEEKDAY_OVERTIME,
		RFS.ILC_WORK_ITEM_WEEKEND_OVERTIME,
		RFS.RFS_START_DATE,
		RFS.RFS_END_DATE,
		RFS.RFS_TYPE,
		RFS.RFS_STATUS,
		RFS.ARCHIVE,
		RFS.RFS_CREATOR, 
		RFS.RFS_CREATED_TIMESTAMP,
		RFS.LINK_TO_PGMP,
        RDR.*,
        RPCR.PCR_ID ";
        $sql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
        
        // Value Stream
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_VALUE_STREAM . " as VS ";
        $sql .= " ON RFS.VALUE_STREAM = VS.VALUE_STREAM_ID";
        
        $sql .= " LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$RFS_DATE_RANGE . " AS RDR ";
        $sql .= " ON RFS.RFS_ID = RDR.RFS ";
        $sql .= " LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$RFS_PCR . " AS RPCR ";
        $sql .= " ON RFS.RFS_ID = RPCR.RFS_ID ";
        $sql .= " WHERE 1=1 " ;
        $sql .= $withArchive ? " AND " . rfsTable::ARCHIVED : " AND " . rfsTable::NOT_ARCHIVED;
        $sql .= !empty($predicate) ? " AND  $predicate " : null ;
        
        $redis = $GLOBALS['redis'];
		$redisKey = md5($sql.'_key_'.$_ENV['environment']);
        if (!$redis->get($redisKey) || $disableCache) {
            $source = 'SQL Server';
            
            $resultSet = $this->execute($sql);
            $resultSet ? null : die("SQL Failed");
            
            $result = array();
            while($row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
                $testJson = json_encode($row);
                if(!$testJson){
                    break; // It's got invalid chars in it that will be a problem later.
                }
                $result[] = $row;
            }

            $redis->set($redisKey, json_encode($result));
            $redis->expire($redisKey, REDIS_EXPIRE);
        } else {
            $source = 'Redis Server';
            $result = json_decode($redis->get($redisKey), true);
        }

        $allData = array();
        $allData['data'] = array();
        if (is_iterable($result)) {
            foreach ($result as $key => $row) {
                $withButtons ? $this->addGlyphicons($row) : null;

                foreach ($row as $key => $data){
                    $row[] = trim($row[$key]);
                    unset($row[$key]);
                }
                $allData['data'][]  = $row;
            }
        };

        $allData['sql'] = $sql;
        $allData['source'] = $source;

        return $allData;
    }

    function archiveRfs($rfsid){
        if(empty($rfsid)){
            return false;
        }

        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET ARCHIVE = CURRENT_TIMESTAMP ";
        $sql .= " WHERE RFS_ID = '" . htmlspecialchars($rfsid) . "' " ;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        return true;
    }

    function getArchieved(){
        $sql  = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE " . rfsTable::NOT_ARCHIVED;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return $rs;
    }

    static function isValidPLDId($rfsId){
        if (preg_match("/^[a-zA-Z]{4}(-PLD-)[0-9]{4,}$/i", $rfsId)){
            return true;
        } else {
            return false;
        }
    }

    static function validateRfsIds($oldRfsId=null, $newRfsId=null){
        if (self::isValidPLDId($oldRfsId)){
            return true;
        } else {
            return false;
        }
    }

    static function updateRfsId($oldRfsId=null, $newRfsId=null){
        if(empty($oldRfsId) || empty($newRfsId)){
            return false;
        }

        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS;
        $sql .= " SET RFS_ID = '" . htmlspecialchars($newRfsId) . "' " ;
        $sql .= " WHERE RFS_ID = '" . htmlspecialchars($oldRfsId) . "' " ;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        } else {
            // rfsDiaryTable::insertEntry("RFS Id set from " . htmlspecialchars(trim($oldRfsId) . " to " . htmlspecialchars(trim($newRfsId)), $newRfsId);
        }

        return $rs;
    }
}