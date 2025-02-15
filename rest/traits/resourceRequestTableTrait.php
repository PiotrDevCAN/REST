<?php
namespace rest\traits;

ini_set('display_startup_errors',1);
ini_set('display_errors',1);

use itdq\DbTable;
use itdq\PhpMemoryTrace;
use itdq\Loader;
use itdq\Navbar;
use rest\activeResourceTable;
use rest\allTables;
use rest\rfsRecord;
use rest\rfsTable;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestDiaryTable;
use rest\resourceRequestHoursTable;

trait resourceRequestTableTrait
{
    use tableTrait;

    static $duplicate = 'Dup of';
    static $delta = 'Delta from';

    protected $loader;
    private $today;

    private $hrsThisWeekByResourceReference;
    private $lastDiaryEntriesByResourceReference;

    static function buildHTMLTable($tableId = 'resourceRequests', $startDate, $endDate){
        $RFSheaderCells = rfsRecord::htmlHeaderCellsStatic();
        $RRheaderCells = resourceRequestRecord::htmlHeaderCellsStatic($startDate);
        ?>
        <table id='<?=$tableId;?>Table_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
        <thead><tr><?=$RFSheaderCells . $RRheaderCells ;?></tr></thead>
        <tbody></tbody>
        <tfoot><tr><?=$RFSheaderCells . $RRheaderCells ;?></tr></tfoot>
        </table>
        <?php
    }

    function __construct($table, $pwd = null, $log = true){
        $this->loader = new Loader();
    
        $today = new \DateTime();
        $this->today = $today;
        
        if ($this->hrsThisWeekByResourceReference==null){
            $loader = $this->loader;
            $predicate = " WEEK_NUMBER='" . htmlspecialchars($today->format('W')) . "' ";
            $this->hrsThisWeekByResourceReference = $loader->loadIndexed('HOURS','RESOURCE_REFERENCE',allTables::$RESOURCE_REQUEST_HOURS, $predicate);            
        }

        parent::__construct ( $table, $pwd, $log );
    }

    function getResourceName($resourceReference){
        $sql  = " SELECT RESOURCE_NAME FROM  " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE RESOURCE_REFERENCE=" . htmlspecialchars($resourceReference);

        $rs = $this->execute($sql);
        
        if (!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        $row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
        
        return $row ? trim($row['RESOURCE_NAME']) : false;
    }
    
    function updateResourceName($resourceReference, $resourceName, $resourceEmail, $resourceKynEmail, $resourceCnum, $clear=null){
        
        $status = resourceRequestRecord::STATUS_ASSIGNED;
        if (!empty($clear)){
            $resourceName = '';
            $resourceEmail = '';
            $resourceKynEmail = '';
            $resourceCnum = '';
            $status=resourceRequestRecord::STATUS_NEW;
        } else if (empty($resourceReference) or empty($resourceName)){
            throw new \Exception('Paramaters Missing in call to ' . __FUNCTION__);
        }
        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET RESOURCE_NAME='" . htmlspecialchars($resourceName) . "', ";
        $sql .= " EMAIL_ADDRESS='" . htmlspecialchars($resourceEmail) . "', ";
        $sql .= " KYN_EMAIL_ADDRESS='" . htmlspecialchars($resourceKynEmail) . "', ";
        $sql .= " CNUM='" . htmlspecialchars($resourceCnum) . "', ";
        $sql .= " STATUS='" . $status . "' ";
        $sql .= " WHERE RESOURCE_REFERENCE=" . htmlspecialchars($resourceReference);
        
        $result = $this->execute($sql);
        
        return $result;
    }
    
    function populateLastDiaryEntriesArray(){
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$LATEST_DIARY_ENTRIES;
        
        error_log(__FILE__ . ":" . __LINE__ . ":" . $sql);
   
        $preExec = microtime(true);
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        $postExec = microtime(true);
        error_log(__FILE__ . ":" . __LINE__ . "Db2 exec:" . ($postExec-$preExec));
        
        if (!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
            return false;
        }
        
        while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
            $this->lastDiaryEntriesByResourceReference[$row['RESOURCE_REFERENCE']] = $row;        
        }
        
        error_log(__FILE__ . ":" . __LINE__ . "Elapsed:" . (microtime(true)-$postExec));
    }
    
    function returnForAPI($startDate, $endDate, $predicate=null, $pipelineLiveArchive = 'Live', $withButtons = true, $page = false, $perPage = false){

        // $this->populateLastDiaryEntriesArray();

        $resourceRequestHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
        $hoursData = $resourceRequestHoursTable->getHoursRemainingByReference();
        list(
            'data' => $hoursRemainingByReference,
            'source' => $source
        ) = $hoursData;
        $monthNumber = 0;
        $startDateObj = new \DateTime($startDate);
        $endDateObj = new \DateTime($endDate);

        $day =  $startDateObj->format('d');
        if ($day > 28){
            // We can't step through adding months if we start on 29th,30th or 31st.
            $year = $startDateObj->format('Y');
            $month = $startDateObj->format('m');
            $startDateObj->setDate($year, $month, '28');
        }
        if (empty($endDate)){
            $endDateObj = \DateTime::createFromFormat('Y-m-d',$startDateObj->format('Y-m-d'));
            $endDateObj->modify("+6 months");
        }

        $sql =  " WITH resource_hours AS (";
        $sql .= " SELECT RESOURCE_REFERENCE AS RR ";
        
        while($startDateObj->format('Ym') <= $endDateObj->format('Ym')){
            $dataTableColName = "MONTH_" . substr("00" . ++$monthNumber,-2);
            $columnName =  $startDateObj->format('M_Y');
            $sql .= ",SUM(" . $columnName . ") as " . $dataTableColName;
            $startDateObj->modify('+1 month');
        }

        $sql .= " FROM ( ";
        $sql .= " SELECT RESOURCE_REFERENCE ";

        $startDateObj = new \DateTime($startDate);
        $day =  $startDateObj->format('d');
        if ($day > 28){
            // We can't step through adding months if we start on 29th,30th or 31st.
            $year = $startDateObj->format('Y');
            $month = $startDateObj->format('m');
            $startDateObj->setDate($year, $month, '28');
        }

        while($startDateObj->format('Ym') <= $endDateObj->format('Ym')){
            $columnName =  $startDateObj->format('M_Y');
            $sql .= ", CASE WHEN ( claim_month = ". $startDateObj->format('m') . " and claim_year = " . $startDateObj->format('Y') . " ) then hours else null end as " . $columnName ;
            $startDateObj->modify('+1 month');
        }

        $startDateObj = new \DateTime($startDate);
        
        // $resourceRequestTableName = $pipelineLiveArchive=='archive'  ? allTables::$ARCHIVED_RESOURCE_REQUESTS : allTables::$RESOURCE_REQUESTS;
        // $resourceRequestHoursTableName = $pipelineLiveArchive=='archive'  ? allTables::$ARCHIVED_RESOURCE_REQUEST_HOURS : allTables::$RESOURCE_REQUEST_HOURS;

        $resourceRequestTableName = allTables::$RESOURCE_REQUESTS;
        $resourceRequestHoursTableName = allTables::$RESOURCE_REQUEST_HOURS;

        $sql .=  " FROM " . $GLOBALS['Db2Schema'] . "." . $resourceRequestHoursTableName;
        $sql .= "  WHERE  ( claim_month >= " . $startDateObj->format('m') . " and claim_year = " . $startDateObj->format('Y') . ")  " ;
        $sql .= $startDateObj->format('Y') !==  $endDateObj->format('Y') ?  "    AND (claim_year > " . $startDateObj->format('Y') . " and claim_year < " . $endDateObj->format('Y') . " ) " : null;
        $sql .= " AND (claim_month <= " . $endDateObj->format('m') . " and claim_year = " . $endDateObj->format('Y') . ")  " ;
        $sql .= " ) AS resource_hours ";
        $sql .= " GROUP BY RESOURCE_REFERENCE ";
        $sql .= " ) ";
        $sql .= " SELECT RFS.*, RR.* ";
        $sql .= " , CASE WHEN ( LOWER(RFS.BUSINESS_UNIT) = LOWER(AR.TRIBE_NAME_MAPPED) ) then 'Yes' else 'No' end as BUSINESS_UNIT_MATCH";
        $sql .= " ,  LD.LATEST_ENTRY, LD.CREATOR as ENTRY_CREATOR, LD.CREATED as ENTRY_CREATED ";
        $sql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . $resourceRequestTableName. " as RR ";
        $sql .= " ON RR.RFS = RFS.RFS_ID ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$LATEST_DIARY_ENTRIES. " as LD ";
        $sql .= " ON RR.RESOURCE_REFERENCE = LD.RESOURCE_REFERENCE ";

        // Active Resources table
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE . " as AR ";
        $sql .= " ON LOWER(RR.RESOURCE_NAME) = LOWER(AR.NOTES_ID) ";
        $sql .= " OR RR.RESOURCE_NAME = AR.EMAIL_ADDRESS ";

        $sql .=  " WHERE RR.RFS is not null ";
        $sql .= $pipelineLiveArchive=='archive'  ? " AND " . rfsTable::ARCHIVED : " AND " . rfsTable::NOT_ARCHIVED;
        $sql .= $pipelineLiveArchive=='pipeline' ? " AND RFS_STATUS='" . rfsRecord::RFS_STATUS_PIPELINE . "' " : " AND RFS_STATUS!='" . rfsRecord::RFS_STATUS_PIPELINE . "' ";
        $sql .= !empty($predicate) ? " $predicate " : null ;
        
        $sql .= " ORDER BY RFS.RFS_CREATED_TIMESTAMP DESC ";

        $setPageSizeFromData = false;
        if ($page === false && $perPage === false) {
            $setPageSizeFromData = true;
        }

        if ($perPage === false) {
            $perPage = 1000;
        }

        // if ($page === false) {
        //     $page = 1;
        //     $sql.= " FOR FETCH ONLY ";
        // } else {
        //     $sql.= " LIMIT ".$perPage;
        //     $sql.= " OFFSET ".(($page -1) * $perPage);
        //     $sql.= " FOR FETCH ONLY ";
        // }

        if ($page === false) {
            $page = 1;
        } else {
            $sql.= ' OFFSET ' . (($page -1) * $perPage);
            $sql.= ' ROWS FETCH FIRST ' . $perPage;
            $sql.= ' ROWS ONLY';
        }

        error_log(__FILE__ . ":" . __LINE__ . ":" . $pipelineLiveArchive);
        error_log(__FILE__ . ":" . __LINE__ . ":" . $predicate);
        error_log(__FILE__ . ":" . __LINE__ . ":" . $sql);

        $resultSet = $this->execute($sql);

        $resultSet ? null : die("SQL Failed");

        $allData = array();
        $allData['data'] = array();

        while($row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
            PhpMemoryTrace::reportPeek(__FILE__,__LINE__);
            $testJson = json_encode($row);
            if (!$testJson){
                error_log("Invalid character found");
                error_log(print_r($row,true));
                
                throw new \Exception("Invalid character found in Row ");
                break; // It's got invalid chars in it that will be a problem later.
            }
            $row = array_map('trim', $row);
            $row['hours_to_go'] = isset($hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['hours']) ? $hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['hours'] : null;
            $row['weeks_to_go'] = isset($hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['weeks']) ? $hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['weeks'] : null;
            
            $withButtons ? $this->addGlyphicons($row) : null;
            $allData['data'][]  = $row;
        }

        $allData['sql'] = $sql;
        $allData['page'] = $page;
        if ($setPageSizeFromData === true) {
            $allData['per_page'] = count($allData['data']);
        } else {
            $allData['per_page'] = $perPage;
        }

        return $allData;
    }

    function returnAsArrayStatistics($startDate, $endDate, $predicate=null, $pipelineLiveArchive = 'Live', $withButtons = true, $page = false, $perPage = false){

        // $this->populateLastDiaryEntriesArray();
        
        $resourceRequestTableName = allTables::$RESOURCE_REQUESTS;

        $countSql = " SELECT COUNT(*) AS COUNTER ";
        $countSql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $countSql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . $resourceRequestTableName. " as RR ";
        $countSql .= " ON RR.RFS = RFS.RFS_ID ";
        $countSql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$LATEST_DIARY_ENTRIES. " as LD ";
        $countSql .= " ON RR.RESOURCE_REFERENCE = LD.RESOURCE_REFERENCE ";

        // Active Resources table
        $countSql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE . " as AR ";
        $countSql .= " ON LOWER(RR.RESOURCE_NAME) = LOWER(AR.NOTES_ID) ";
        $countSql .= " OR RR.RESOURCE_NAME = AR.EMAIL_ADDRESS ";

        $countSql .=  " WHERE RR.RFS is not null ";
        $countSql .= $pipelineLiveArchive=='archive'  ? " AND " . rfsTable::ARCHIVED : " AND " . rfsTable::NOT_ARCHIVED;
        $countSql .= $pipelineLiveArchive=='pipeline' ? " AND RFS_STATUS='" . rfsRecord::RFS_STATUS_PIPELINE . "' " : " AND RFS_STATUS!='" . rfsRecord::RFS_STATUS_PIPELINE . "' ";
        $countSql .= !empty($predicate) ? " $predicate " : null ;

        $data = array();

        $rs = sqlsrv_query($GLOBALS['conn'], $countSql, $data);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $countSql);
            return false;
        }
        
        $counter = 0;
        while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
            $counter = $row['COUNTER'];
        }

        $sql = 'Statistics only';

        $allData = array();
        $allData['data'] = array();

        if ($page === false) {
            $page = 1;
        }
        if ($perPage === false) {        
            $perPage = 1000;
        }
        $allData['sql'] = $sql;
        $allData['count_sql'] = $countSql;

        $allData['page'] = $page;
        $allData['per_page'] = $perPage;
        $allData['total'] = $counter;
        $allData['total_pages'] = ceil( $counter / $perPage );

        return $allData;
    }

    function returnAsArray($predicate=null, $pipelineLiveArchive = 'Live', $withButtons = true, $disableCache = false){
        
        // $this->populateLastDiaryEntriesArray();

        $resourceRequestHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
        $hoursData = $resourceRequestHoursTable->getHoursRemainingByReference();
        list(
            'data' => $hoursRemainingByReference,
            'source' => $source
        ) = $hoursData;
        
        $resourceRequestTableName = allTables::$RESOURCE_REQUESTS;
        // $resourceRequestHoursTableName = allTables::$RESOURCE_REQUEST_HOURS;
        $sql = " SELECT RFS.RFS_ID,";
        $sql .= " RFS.PRN,";
        $sql .= " RFS.PROJECT_TITLE,";
        $sql .= " RFS.PROJECT_CODE,";
        $sql .= " RFS.REQUESTOR_NAME,";
        $sql .= " RFS.REQUESTOR_EMAIL,";
        $sql .= " VS.VALUE_STREAM,";
        $sql .= " RFS.LINK_TO_PGMP,";
        $sql .= " RFS.RFS_CREATOR,";
        $sql .= " RFS.RFS_CREATED_TIMESTAMP,";
        $sql .= " RFS.ARCHIVE,";
        $sql .= " RFS.RFS_TYPE,";
        $sql .= " RFS.ILC_WORK_ITEM,";
        $sql .= " RFS.RFS_STATUS,";
        $sql .= " RFS.BUSINESS_UNIT,";
        $sql .= " RFS.RFS_START_DATE,";
        $sql .= " RFS.RFS_END_DATE,";
        $sql .= " RR.RESOURCE_REFERENCE,";
        $sql .= " RR.RFS,";
        $sql .= " ORG.ORGANISATION,";
        $sql .= " SRV.SERVICE,";
        $sql .= " RR.DESCRIPTION,";
        $sql .= " RR.START_DATE,";
        $sql .= " RR.END_DATE,";
        $sql .= " RR.TOTAL_HOURS,";
        $sql .= " RR.RESOURCE_NAME,";			
        $sql .= " RR.RR_CREATOR,";
        $sql .= " RR.RR_CREATED_TIMESTAMP,";
        $sql .= " RR.CLONED_FROM,";
        $sql .= " RR.STATUS,";
        $sql .= " RR.RATE_TYPE,";
        $sql .= " RR.HOURS_TYPE,";
        $sql .= " RR.EMAIL_ADDRESS AS RESOURCE_EMAIL_ADDRESS,";
        $sql .= " RR.KYN_EMAIL_ADDRESS AS RESOURCE_KYN_EMAIL_ADDRESS,";
        $sql .= " RR.CNUM AS RESOURCE_CNUM,";
        $sql .= " LD.LATEST_ENTRY,";
        $sql .= " LD.CREATOR as ENTRY_CREATOR,";
        $sql .= " LD.CREATED AS ENTRY_CREATED,";

        // Active Person data
        $sql .= "(SELECT TOP 1 STATUS FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE . " AS AR";
        $sql .= " WHERE LOWER(RR.RESOURCE_NAME) = LOWER(AR.NOTES_ID) ";
        $sql .= " OR RR.RESOURCE_NAME = AR.EMAIL_ADDRESS ";
        $sql .= ") AS VBAC_STATUS,";

        // Resource Trait        
        $sql .= " RTR.ID AS RESOURCE_TRAIT_ID,";
        $sql .= " RTR.PS_BAND_OVERRIDE AS INDIVIDUAL_PS_BAND_OVERRIDE,";
        $sql .= " RT_SRT.RESOURCE_TYPE_ID AS INDIVIDUAL_RESOURCE_TYPE_ID,";
        $sql .= " RT_SRT.RESOURCE_TYPE AS INDIVIDUAL_RESOURCE_TYPE,";
        $sql .= " RT_SPSB.BAND_ID AS INDIVIDUAL_PS_BAND_ID,";
        $sql .= " RT_SPSB.BAND AS INDIVIDUAL_PS_BAND,";

        // Rate Card - Resource Rates
        $sql .= " RTR.ID AS INDIVIDUAL_RATE_CARD_ID,";
        $sql .= " RRC.DAY_RATE AS INDIVIDUAL_DAY_RATE,";
        $sql .= " RRC.HOURLY_RATE AS INDIVIDUAL_HOURLY_RATE,";

        // Bespoke Rate
        $sql .= " BR.BESPOKE_RATE_ID,";
        $sql .= " BR_SRT.RESOURCE_TYPE_ID AS REQUEST_RESOURCE_TYPE_ID,";
        $sql .= " BR_SRT.RESOURCE_TYPE AS REQUEST_RESOURCE_TYPE,";
        $sql .= " BR_SPSB.BAND_ID AS REQUEST_PS_BAND_ID,";
        $sql .= " BR_SPSB.BAND AS REQUEST_PS_BAND";

        $sql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . $resourceRequestTableName. " AS RR ";
        $sql .= " ON RR.RFS = RFS.RFS_ID ";

        // Value Stream
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_VALUE_STREAM . " as VS ";
        $sql .= " ON RFS.VALUE_STREAM = VS.VALUE_STREAM_ID";

        // Organisation
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_ORGANISATION . " as ORG ";
        $sql .= " ON RR.ORGANISATION = ORG.ORGANISATION_ID";

        // Service
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_SERVICE . " as SRV ";
        $sql .= " ON RR.SERVICE = SRV.SERVICE_ID";

        // Resource Trait
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_TRAITS . " as RTR ";
        $sql .= " ON RR.RESOURCE_NAME = RTR.RESOURCE_NAME ";
        
        // Resource Type for RT
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_RESOURCE_TYPE . " as RT_SRT ";
        $sql .= " ON RTR.RESOURCE_TYPE_ID = RT_SRT.RESOURCE_TYPE_ID ";
        
        // PS Band for RT
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_PS_BAND. " as RT_SPSB ";
        $sql .= " ON RTR.PS_BAND_ID = RT_SPSB.BAND_ID ";

        // Rate Card
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_TYPE_RATES . " as RRC ";
        $sql .= " ON RTR.RESOURCE_TYPE_ID = RRC.RESOURCE_TYPE_ID AND RTR.PS_BAND_ID = RRC.PS_BAND_ID";

        // Bespoke Rate
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$BESPOKE_RATES. " AS BR ";
        $sql .= " ON LOWER(BR.RFS_ID) = LOWER(RR.RFS) AND LOWER(BR.RESOURCE_REFERENCE) = LOWER(RR.RESOURCE_REFERENCE)";

        // Resource Type for BR
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_RESOURCE_TYPE . " as BR_SRT ";
        $sql .= " ON BR.RESOURCE_TYPE_ID = BR_SRT.RESOURCE_TYPE_ID ";

        // PS Band for BR
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_PS_BAND. " as BR_SPSB ";
        $sql .= " ON BR.PS_BAND_ID = BR_SPSB.BAND_ID ";
        
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$LATEST_DIARY_ENTRIES. " AS LD ";
        $sql .= " ON RR.RESOURCE_REFERENCE = LD.RESOURCE_REFERENCE ";
        $sql .= " WHERE RR.RFS is not null ";

        $sql .= $pipelineLiveArchive=='archive'  ? " AND " . rfsTable::ARCHIVED : " AND " . rfsTable::NOT_ARCHIVED;
        $sql .= $pipelineLiveArchive=='pipeline' ? " AND RFS_STATUS='" . rfsRecord::RFS_STATUS_PIPELINE . "' " : " AND RFS_STATUS!='" . rfsRecord::RFS_STATUS_PIPELINE . "' ";
        $sql .= !empty($predicate) ? " $predicate " : null ;

        $sql .= " ORDER BY RFS.RFS_CREATED_TIMESTAMP DESC ";
        
        error_log(__FILE__ . ":" . __LINE__ . ":" . $pipelineLiveArchive);
        error_log(__FILE__ . ":" . __LINE__ . ":" . $predicate);
        error_log(__FILE__ . ":" . __LINE__ . ":" . $sql);

        $allData = array();
        $allData['data'] = array();
        $badRecords = 0;

        $redis = $GLOBALS['redis'];
        $redisKey = md5($sql.'_key_'.$_ENV['environment']);
        if (!$redis->get($redisKey) || $disableCache) {
            $source = 'SQL Server';
            
            $resultSet = $this->execute($sql);
            $resultSet ? null : die("SQL Failed");

            $result = array();
            while($row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
                PhpMemoryTrace::reportPeek(__FILE__,__LINE__);
                $testJson = json_encode($row);
                if(!$testJson){
                    $badRecords ++;
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

        if (is_iterable($result)) {
            foreach ($result as $key => $row) {
                $row = array_map('trim', $row);
                $row['hours_to_go'] = isset($hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['hours']) ? $hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['hours'] : null;
                $row['weeks_to_go'] = isset($hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['weeks']) ? $hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['weeks'] : null;
                
                $withButtons ? $this->addGlyphicons($row) : null;
                $allData['data'][]  = $row;
            }
        }

        $allData['sql'] = $sql;
        $allData['source'] = $source;
        $allData['badRecords'] = $badRecords;

        return $allData;
    }

    function addGlyphicons(&$row){
        
        PhpMemoryTrace::reportPeek(__FILE__,__LINE__);
        $rfsId = $row['RFS_ID'];
        $resourceReference = $row['RESOURCE_REFERENCE'];
        
        $resourceName = $row['RESOURCE_NAME'];
        $resourceEmailAddress = $row['RESOURCE_EMAIL_ADDRESS'];
        $resourceKynEmailAddress = $row['RESOURCE_KYN_EMAIL_ADDRESS'];
        $resourceCnum = $row['RESOURCE_CNUM'];
        $prn = $row['PRN'];
        $valuestream = $row['VALUE_STREAM'];
        $businessunit = $row['BUSINESS_UNIT'];

        $startDate4Picka = !empty($row['START_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['START_DATE'])->format('Y-m-d') : null;
        $startDate = !empty($row['START_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['START_DATE'])->format('d M Y') : null;
        $startDateSortable = !empty($row['START_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['START_DATE'])->format('Y-m-d') : null;
        $startDateObj = !empty($row['START_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['START_DATE']) : null;
        
        $endDate4Picka = !empty($row['END_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['END_DATE'])->format('Y-m-d') : null;
        $endDate = !empty($row['END_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['END_DATE'])->format('d M Y') : null;
        $endDateSortable = !empty($row['END_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['END_DATE'])->format('Y-m-d') : null;
        $endDateObj = !empty($row['END_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['END_DATE']) : null;
        
        is_object($endDateObj) ? $endDateObj->setTime(23, 59, 59) : null; // When setting completed, compare against midnight on END_DATE
        
        $rfsStartDate = !empty($row['RFS_START_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['RFS_START_DATE'])->format('d M Y') : null;
        $rfsStartDate4Picka = !empty($row['RFS_START_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['RFS_START_DATE'])->format('Y-m-d') : null;
        
        $rfsEndDate = !empty($row['RFS_END_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['RFS_END_DATE'])->format('d M Y') : null;
        $rfsEndDate4Picka = !empty($row['RFS_END_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['RFS_END_DATE'])->format('Y-m-d') : null;
        
        $totalHours = $row['TOTAL_HOURS'];

        $displayHrsPerWeek = "";
        $hrsThisWeek =   $displayHrsPerWeek.= isset($this->hrsThisWeekByResourceReference[$resourceReference]) ?  $this->hrsThisWeekByResourceReference[$resourceReference] : "N/A";
        
        $hoursType = $row['HOURS_TYPE'];
        $rateType = $row['RATE_TYPE'];
        $status = !empty($row['STATUS']) ? $row['STATUS'] : resourceRequestRecord::STATUS_NEW;
        $organisation = $row['ORGANISATION'];
        $service = $row['SERVICE'];
        // $individualTypeId = $row['INDIVIDUAL_RESOURCE_TYPE_ID'];
        $individualType = $row['INDIVIDUAL_RESOURCE_TYPE'];
        // $individualPSBandId = $row['INDIVIDUAL_PS_BAND_ID'];
        $individualPSBand = $row['INDIVIDUAL_PS_BAND'];
        $individualPSBandOverride = $row['INDIVIDUAL_PS_BAND_OVERRIDE'];

        $rateCardId = $row['INDIVIDUAL_RATE_CARD_ID'];
        $individualDayRate = $row['INDIVIDUAL_DAY_RATE'];
        $individualHourlyRate = $row['INDIVIDUAL_HOURLY_RATE'];

        $bespokeRateId = $row['BESPOKE_RATE_ID'];
        $resourceTraitId = $row['RESOURCE_TRAIT_ID'];

        // $RRResourceTypeId = $row['REQUEST_RESOURCE_TYPE_ID'];
        // $RRResourceType = $row['REQUEST_RESOURCE_TYPE'];
        // $RRPSBandId = $row['REQUEST_PS_BAND_ID'];
        // $RRPSBand = $row['REQUEST_PS_BAND'];

        $editable = true;
        
        // override Hours Type if Rate Type equals Blended
        // switch ($rateType) {
        //     case resourceRequestRecord::RATE_TYPE_BLENDED:
        //         $row['HOURS_TYPE'] = '';
        //         break;
        //     case resourceRequestRecord::RATE_TYPE_PROFESSIONAL:
        //         $displayValues = '';
        //         $displayValues.= "<span><b>Resource Request</b><br/>";
        //         $displayValues.= "Rate Type: <b>$rateType</b><br/>";
        //         $displayValues.="</span>";
                
        //         if (!empty(trim($resourceName))) {
        //             $displayValues.= "<span><b>Individual</b><br/>";
        //             $displayValues.= "<hr style='margin: 2px 0;'/>";
        //             $displayValues.= "Resource Type: <b>$individualType</b><br/>";
        //             $displayValues.= "PS Band: <b>$individualPSBand</b><br/>";
        //             $displayValues.= "Overrides PS Band: <b>$individualPSBandOverride</b><br/>";
        //             if (!empty($resourceTraitId)) {
        //                 $displayValues.= "<hr style='margin: 2px 0;'/>";
        //                 $displayValues.= "Day Rate: <b>$individualDayRate</b><br/>";
        //                 $displayValues.= "Hourly Rate: <b>$individualHourlyRate</b><br/>";
        //             }
        //             $displayValues.="</span>";

        //             if (empty($resourceTraitId)) {
        //                 $assignColor = 'text-danger';
        //                 $displayValues.="<span class='$assignColor'>";
        //                 $displayValues.="This resource is not allocated to a Resource Type/PS Band, please contact the RFS Team (enter contact details) to have them populate their record";
        //                 $displayValues.="</span>";
        //             } else {
        //                 $displayValues.="<button type='button' class='btn btn-xs overrideBespokeRate ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_SUPPLY." ".Navbar::$ACCESS_SUPPLY_X." ' aria-label='Left Align'
        //                     data-id='" . $resourceTraitId . "'>
        //                     <span data-toggle='tooltip' title='Override Bespoke Rate' class='glyphicon glyphicon-check ' aria-hidden='true' ></span>
        //                 </button>";

        //                 if (empty($rateCardId)) {
        //                     $assignColor = 'text-danger';
        //                     $displayValues.="<br/><span class='$assignColor'>";
        //                     $displayValues.="Selected Resource Type and PS Band values do not match to Resource Rates";
        //                     $displayValues.="</span>";
        //                 }
        //             }
                    
        //         } else {
        //             $displayValues.= "<span><b>Unallocated resource</b></span>";
        //         }

        //         $row['RATE_TYPE'] = $displayValues;
        //         break;
        //     default:
        //         break;
        // }

        $completeable = (($status == resourceRequestRecord::STATUS_ASSIGNED) && ($endDateObj < $this->today)) ? true : false; // Someone has been assigned and the End Date has passed.;

        switch (true) {
            case $this->today < $startDateObj:
                $assignColor = 'text-success';
                $started     = resourceRequestRecord::DATE_STATUS_PLANNED;
                break;
            case $this->today <= $endDateObj:
                $assignColor = 'text-warning';
                $started     = resourceRequestRecord::DATE_STATUS_ACTIVE;
                break;
            case $this->today > $endDateObj:
                $assignColor = 'text-danger';
                $started     = resourceRequestRecord::DATE_STATUS_COMPLETED;
                $editable = false;
                break;
            default:
                $assignColor = 'text-primary';
                $started     = resourceRequestRecord::DATE_STATUS_UNCLEAR;
            break;
        }

        strstr($_ENV['environment'], 'ut') ? $editable = true : null;
        strstr($_ENV['environment'], 'ut') ? $completeable = true : null;

        (stripos($_ENV['environment'], 'dev') || stripos($_ENV['environment'], 'local')) ? $editable = true : null;
        (stripos($_ENV['environment'], 'dev') || stripos($_ENV['environment'], 'local')) ? $completeable = true : null;

        $row['STATUS'] = $completeable ? 
        "<button type='button' class='btn btn-xs changeStatusCompleted ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_SUPPLY." ".Navbar::$ACCESS_SUPPLY_X." ' aria-label='Left Align'
            data-rfs='" .$rfsId . "'
            data-resourcereference='" .$resourceReference . "'
            data-prn='" .$prn . "'
            data-valuestream='" .$valuestream . "'
            data-status='" . $status . "'
            data-organisation='" .$organisation .  "'
            data-service='" . $service . "'
            data-resourcename='" . $resourceName . "'
            data-resourceemailaddress='" . $resourceEmailAddress . "'
            data-resourcekynemailaddress='" . $resourceKynEmailAddress . "'
            data-start='" . $startDate4Picka . "'
            data-end='" . $endDate4Picka . "'
         >
         <span data-toggle='tooltip' title='Change Status to Completed' class='glyphicon glyphicon-check ' aria-hidden='true' ></span>
            </button>&nbsp;<span class='$assignColor'>$status</span>" : "<span class='$assignColor'>$status</span>";


        $editButtonColor = empty($resourceName) ? 'text-success' : 'text-warning';
        $editButtonColor = substr($resourceName,0,strlen(resourceRequestTable::$duplicate))==resourceRequestTable::$duplicate ? 'text-success' : $editButtonColor;
        $editButtonColor = substr($resourceName,0,strlen(resourceRequestTable::$delta))==resourceRequestTable::$delta ? 'text-success' : $editButtonColor;

        $duplicatable = true; //Can clone any record.

        $canBeAmendedByDemandTeam = empty(trim($resourceName)) ? Navbar::$ACCESS_DEMAND : null; // Demand can amend any Request that is yet to have resource allocated to it.

        $displayedResourceName = "<span class='dataOwner' ";
        $displayedResourceName.= "  data-rfs='" .$rfsId . "' ";
        $displayedResourceName.= "  data-resourcereference='" .$resourceReference . "' ";
        $displayedResourceName.= "  data-bespokerate='" .$bespokeRateId . "' ";
        $displayedResourceName.= "  data-resourcetrait='" .$resourceTraitId . "' ";
        $displayedResourceName.= "  data-prn='" .$prn . "' ";
        $displayedResourceName.= "  data-valuestream='" . $valuestream. "' ";
        $displayedResourceName.= "  data-businessunit='" . $businessunit. "' ";
        $displayedResourceName.= "  data-status='" . $status . "' ";
        $displayedResourceName.= "  data-service='" .$service .  "' ";
        $displayedResourceName.= "  data-organisation='" .$organisation .  "' ";
        $displayedResourceName.= "  data-subservice='" . $service . "' ";
        $displayedResourceName.= "  data-resourcename='" . $resourceName . "' ";
        $displayedResourceName.= "  data-resourceemailaddress='" . $resourceEmailAddress . "' ";
        $displayedResourceName.= "  data-resourcekynemailaddress='" . $resourceKynEmailAddress . "' ";
        $displayedResourceName.= "  data-resourcecnum='" . $resourceCnum . "' ";
        $displayedResourceName.= "  data-start='" . $startDate . "' ";
        $displayedResourceName.= "  data-startpika='" . $startDate4Picka . "' ";
        $displayedResourceName.= "  data-end='" . $endDate . "' ";
        $displayedResourceName.= "  data-endpika='" . $endDate4Picka . "' ";
        $displayedResourceName.= "  data-rfsstartdate='" . $rfsStartDate . "' ";
        $displayedResourceName.= "  data-rfsstartdatepika='" . $rfsStartDate4Picka . "' ";
        $displayedResourceName.= "  data-rfsenddate='" . $rfsEndDate . "' ";
        $displayedResourceName.= "  data-rfsenddatepika='" . $rfsEndDate4Picka . "' ";
        $displayedResourceName.= "  data-hrs='" . $totalHours . "' ";
        $displayedResourceName.= "  data-hrsperweek='" . $hrsThisWeek . "' ";
        $displayedResourceName.= "  data-hrstype='" . $hoursType . "' ";
        $displayedResourceName.= "  data-ratetype='" . $rateType . "' ";
        $displayedResourceName.= "  >";

        $displayedResourceName.= $editable ? 
            "<button type='button' class='btn btn-xs editResource ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_SUPPLY." ".Navbar::$ACCESS_SUPPLY_X." ' aria-label='Left Align' data-reference='" .$resourceReference . "' data-type='" .$service . "' data-resource-name='" . $resourceName . "' >
                <span data-toggle='tooltip' class='glyphicon glyphicon-user $editButtonColor' aria-hidden='true' title='Edit Assigned Resource'></span>
            </button>" : null;
        $displayedResourceName.= $editable ? 
            "<button type='button' class='btn btn-xs editHours ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_SUPPLY." ".Navbar::$ACCESS_SUPPLY_X." $canBeAmendedByDemandTeam ' aria-label='Left Align' data-reference='" . $resourceReference . "'  data-startDate='" . $startDate . "' >
                <span data-toggle='tooltip' class=' glyphicon glyphicon-time text-primary' aria-hidden='true' title='Edit Dates/Hours'></span>
            </button>" : null;
        $displayedResourceName.= $editable ?
            "<button type='button' class='btn btn-xs endEarly ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_SUPPLY." ".Navbar::$ACCESS_SUPPLY_X." ' aria-label='Left Align' data-reference='" . $resourceReference . "'  data-endDate='" . $endDate . "' >
                <span data-toggle='tooltip' class=' glyphicon glyphicon-flash text-primary' aria-hidden='true' title='Indicate Completed'></span>
            </button>" : null;
        $displayResourceName = $resourceName;

        $resName = $editButtonColor == 'text-success' ? "<i>$resourceName</i>" : $displayResourceName;

        $resName = empty(trim($resourceName)) ? "<i>Unallocated</i>" : $resName;
        $resName = substr($resourceName,0,strlen(resourceRequestTable::$delta))==resourceRequestTable::$delta ? "<i>Unallocated</i>" : $resName;
        $resName = substr($resourceName,0,strlen(resourceRequestTable::$duplicate))==resourceRequestTable::$duplicate ? "<i>Unallocated</i>" : $resName;

        if (!empty(trim($resourceName))) {
            switch($row['VBAC_STATUS']) {
                case activeResourceTable::INT_STATUS_ACTIVE:
                    $resName = '<span class="text-success" title="Employee found in vBAC and is ACTIVE">' . $resName .'</span>';
                    break;
                case activeResourceTable::INT_STATUS_INACTIVE:
                    $resName = '<span class="text-warning" title="Employee found in vBAC altough is INACTIVE">' . $resName .'</span>';
                    break;
                default:
                    $resName = '<span class="text-danger" title="Employee not found in vBAC - missing data issue">' . $resName .'</span>';
                    break;
            }
        }

        $displayedResourceName.= "&nbsp;" . $resName;
        $displayedResourceName.= "</span>";
        
        $calendarEntry = !empty($row['LATEST_ENTRY']) ?  $row['LATEST_ENTRY'] . " <small>" . $row['ENTRY_CREATOR'] . ' ' . $row['ENTRY_CREATED'] . "</small>" : null;
//        $calendarEntry = "<small>Latest diary entry not currently available</small>";      
        
        $displayedResourceName.= "<br/><button type='button' class='btn btn-xs btnOpenDiary ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_SUPPLY." ".Navbar::$ACCESS_SUPPLY_X." ".Navbar::$ACCESS_DEMAND." ' ";
        $displayedResourceName.= " aria-label='Left Align'  ";
        $displayedResourceName.= " data-reference='" .$resourceReference . "' ";
        $displayedResourceName.= " data-rfs='" .$rfsId . "'  ";
        $displayedResourceName.= " data-organisation='" .$organisation . "'  ";
        $displayedResourceName.= " > ";
        $displayedResourceName.= "<span data-toggle='tooltip' title='Open Diary' class='glyphicon glyphicon-book ' aria-hidden='true' ></span>";
        $displayedResourceName.= "</button><div class='latestDiary'>" . $calendarEntry . "</div>";
        
        $row['RESOURCE_NAME']   = array('display'=> $displayedResourceName, 'sort'=>$resourceName);

        $displayRfsId = $rfsId . " : " . $row['RESOURCE_REFERENCE'];
        $displayRfsId.= $row['CLONED_FROM']> 0 ? "&nbsp;<i>(" . $row['CLONED_FROM'] . ")</i>" : null;
        
        $displayRfsId.= "<br/><span class='dataOwner' ";
        $displayRfsId.= "  data-rfs='" .$rfsId . "' ";
        $displayRfsId.= "  data-resourcereference='" .$resourceReference . "' ";
        $displayRfsId.= "  data-prn='" .$prn . "' ";
        $displayRfsId.= "  data-valuestream='" . $valuestream. "' ";
        $displayRfsId.= "  data-status='" . $status . "' ";
        $displayRfsId.= "  data-service='" .$service .  "' ";
        $displayRfsId.= "  data-subservice='" . $service . "' ";
        $displayRfsId.= "  data-resourcename='" . $resourceName . "' ";
        $displayRfsId.= "  data-start='" . $startDate . "' ";
        $displayRfsId.= "  data-end='" . $endDate . "' ";
        $displayRfsId.= "  data-hrs='" . $totalHours . "' ";
        $displayRfsId.= "  >";
        
        $displayRfsId.= $editable ?
            "<button type='button' class='btn btn-success btn-xs editRecord ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI." $canBeAmendedByDemandTeam ' aria-label='Left Align' data-reference='" .$resourceReference . "' data-type='" .$service . "' >
                <span data-toggle='tooltip' class='glyphicon glyphicon-edit ' aria-hidden='true' title='Edit Resource Request'></span>
            </button>" : null;
        
        $displayRfsId.= $duplicatable && $editable ?
            "<button type='button' class='btn btn-xs requestDuplication ".Navbar::$ACCESS_RESTRICT." ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI." ".Navbar::$ACCESS_SUPPLY." ".Navbar::$ACCESS_SUPPLY_X." $canBeAmendedByDemandTeam' aria-label='Left Align'
                data-reference='" . $resourceReference . "'
                data-rfs='" . $row['RFS_ID'] . "'
                data-type='" . $row['SERVICE'] . "'
                data-start='" . $row['START_DATE'] . "'
            >
                <span data-toggle='tooltip' class='glyphicon glyphicon-duplicate text-primary' aria-hidden='true' title='Clone Resource Request'></span>
            </button>" : null;

        $displayRfsId.= $editable ? 
            "<button type='button' class='btn btn-xs archiveRecord ".Navbar::$ACCESS_RESTRICT." $canBeAmendedByDemandTeam ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI." ' aria-label='Left Align' data-reference='" .$resourceReference . "' data-platform='" .trim($row['ORGANISATION']) .  "' data-rfs='" .trim($row['RFS_ID']) . "' data-type='" . $service . "' >
                <span data-toggle='tooltip' title='Archive Resource Request' class='glyphicon glyphicon-hourglass text-warning ' aria-hidden='true' ></span>
            </button>": null;

        $displayRfsId.= $editable ? 
            "<button type='button' class='btn btn-xs deleteRecord ".Navbar::$ACCESS_RESTRICT." $canBeAmendedByDemandTeam ".Navbar::$ACCESS_ADMIN." ".Navbar::$ACCESS_CDI." ' aria-label='Left Align' data-reference='" .$resourceReference . "' data-platform='" .trim($row['ORGANISATION']) .  "' data-rfs='" .trim($row['RFS_ID']) . "' data-type='" . $service . "' >
                <span data-toggle='tooltip' title='Delete Resource Request' class='glyphicon glyphicon-trash text-warning ' aria-hidden='true' ></span>
            </button>": null;
        
        $row['RFS'] = array('display'=> $displayRfsId, 'sort'=>$rfsId);
        
        $displayStartDate = '';
        $displayStartDate.= "<span class='$assignColor'>$startDate  to  $endDate <br/>";
        $displayStartDate.= "Total Hours: " . $row['TOTAL_HOURS'] . "<br/>";
        $displayStartDate.= ($started == resourceRequestRecord::DATE_STATUS_ACTIVE) ? "Hrs This Week: " . $hrsThisWeek . "<br/>" : null;
        $displayStartDate.= (isset($row['hours_to_go'])) ? "Hrs remaining:" . $row['hours_to_go'] . "<br/>" : null;
        $displayStartDate.= (isset($row['weeks_to_go'])) ? "Weeks remaining:" . $row['weeks_to_go'] . "<br/>" : null;
        $displayStartDate.= "$started";
        
        $row['START_DATE'] = array('display'=> $displayStartDate, 'sort'=>$startDateSortable);
        $row['END_DATE'] = array('display'=> $endDate, 'sort'=>$endDateSortable);
        
        $totalHours = $row['TOTAL_HOURS'];
        
        $displayHrsPerWeek = "";
        
        $displayHrsPerWeek = "Total Hrs:" . $totalHours . "<br/>";
        $displayHrsPerWeek.= ($started == resourceRequestRecord::DATE_STATUS_ACTIVE) ? "This Week:" . $hrsThisWeek : null;
        
        $row['TOTAL_HOURS'] = array('display'=>$displayHrsPerWeek,'sort'=>$totalHours);
        
        $row['ORGANISATION']=array('display'=>$row['ORGANISATION'] . "<br/><small>" . $row['SERVICE'] . "</small>", 'sort'=>$organisation);
        
    }

    static function setEndDate($resourceReference, $endDate){
        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
        $sql .= "  SET END_DATE = '" . htmlspecialchars($endDate) ."' ";
        $sql .= " WHERE RESOURCE_REFERENCE=" . htmlspecialchars($resourceReference) ." ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        return true;
    }
    
    static function setStartDate($resourceReference, $startDate){
        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
        $sql .= "  SET START_DATE = '" . htmlspecialchars($startDate) ."' ";
        $sql .= " WHERE RESOURCE_REFERENCE=" . htmlspecialchars($resourceReference) ." ";
  
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if (!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return true;
    }
    
    static function setHrsPerWeek($resourceReference, $hrsPerWeek){
        $hrsPerWeekInt = intval($hrsPerWeek);
        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
        $sql .= "  SET HRS_PER_WEEK     = " . htmlspecialchars($hrsPerWeek) ;
        $sql .= "  ,   HRS_PER_WEEK_INT = " . htmlspecialchars($hrsPerWeekInt) ;
        $sql .= " WHERE RESOURCE_REFERENCE=" . htmlspecialchars($resourceReference) ." ";
      
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if (!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return true;
    }
    
    static function setTotalHours($resourceReference, $totalHours){
        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
        $sql .= "  SET TOTAL_HOURS     = " . htmlspecialchars($totalHours) ;
        $sql .= " WHERE RESOURCE_REFERENCE=" . htmlspecialchars($resourceReference) ." ";
       
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if (!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return true;
    }

    static function getDetailsforRfsDateSlip($rfsId=null){
       
        $sql = " SELECT RESOURCE_REFERENCE, START_DATE, END_DATE, ORGANISATION, SERVICE, DESCRIPTION ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
        $sql.= " WHERE RFS = '" . htmlspecialchars($rfsId) . "' ";
        
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if (!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
     
        $data = array();
        while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
            $data[$row['RESOURCE_REFERENCE']] = $row;
        }
        
        return !empty($data) ? $data : false;
    }

    static function setRequestStatus($resourceRequest=null, $status=null){
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
        $sql.= !empty($status) && !empty($resourceRequest) ? " SET STATUS='" . htmlspecialchars(trim($status)) . "' " : null ;
        $sql.= !empty($status) && !empty($resourceRequest) ? " WHERE RESOURCE_REFERENCE=" . htmlspecialchars($resourceRequest) . "  " : null;
        
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if (!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        } else {
            resourceRequestDiaryTable::insertEntry("Status set to " . htmlspecialchars(trim($status)), $resourceRequest); 
        }
        return $rs;
    }

    function getArchieved($rfsId=null){
        $sql  = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE RFS = '" . htmlspecialchars($rfsId) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return $rs;
    }

    static function updateRfsId($oldRfsId=null, $newRfsId=null){
        if(empty($oldRfsId) || empty($newRfsId)){
            return false;
        }

        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
        $sql .= " SET RFS = '" . htmlspecialchars($newRfsId) . "' " ;
        $sql .= " WHERE RFS = '" . htmlspecialchars($oldRfsId) . "' " ;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        } else {
            resourceRequestDiaryTable::insertEntry("RFS Id set from " . htmlspecialchars(trim($oldRfsId)) . " to " . htmlspecialchars(trim($newRfsId)), $newRfsId);
        }

        return $rs;
    }

    static function getAllRFSsAndResourceRequests($predicate){
        $sql = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
        $sql.= " WHERE 1=1 ";
        $sql.= empty($predicate) ? null : " AND " . $predicate;
        $sql .= " ORDER BY RFS, RESOURCE_REFERENCE  ";
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        $allResourceRequests = array();
        if($rs){
            while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
                $allResourceRequests[trim($row['RFS'])][] = trim($row['RESOURCE_REFERENCE']);
            }
        } else {
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }
        return $allResourceRequests;
    }

    static function getAllRFSsAndResourceRequestsExtended($predicate){
        $sql = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
        $sql.= " WHERE 1=1 ";
        $sql.= empty($predicate) ? null : " AND " . $predicate;
        $sql .= " ORDER BY RFS, RESOURCE_REFERENCE  ";
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        $allResourceRequests = array();
        if($rs){
            while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
                $allResourceRequests[trim($row['RFS'])][] = array_map('trim', $row);
            }
        } else {
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }
        return $allResourceRequests;
    }
}