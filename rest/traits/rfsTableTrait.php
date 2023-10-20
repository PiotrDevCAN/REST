<?php
namespace rest\traits;

use DateInterval;
use Exception;
use itdq\DbTable;
use itdq\DateClass;
use itdq\Navbar;
use rest\activeResourceTable;
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;
use rest\rfsRecord;
use rest\rfsTable;

trait rfsTableTrait
{
    protected $rfsMaxEndDate;

    // $days, $months, $years - values to add
    static function addTime(\DateTime $date_time, $days, $months, $years){
        if ($days) {
            $xDays = new \DateInterval('P'.$days.'D');
            $date_time->add($xDays);
        }

        // Preserve day number
        if ($months or $years) {
            $old_day = $date_time->format('d');
        }

        if ($months) {
            $xMonths = new \DateInterval('P'.$months.'M');
            $date_time->add($xMonths);
        }

        if ($years) {
            $xYears = new \DateInterval('P'.$years.'Y');
            $date_time->add($xYears);
        }
        
        // Patch for adding months or years    
        if ($months or $years) {
            $new_day = $date_time->format("d");

            // The day is changed - set the last day of the previous month
            if ($old_day != $new_day) {
                $subxDays = new \DateInterval('P'.$new_day.'D');
                $date_time->sub($subxDays);
            }
        }
        // You can chage returned format here
        // return $date_time->format('Y-m-d');
        return $date_time;
    }

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

    static function getThisMonthsClaimCutoff($dateObj){
        $thisMonthsClaimCutoff = DateClass::claimMonth($dateObj);
        $thisMonthsClaimCutoff = self::addTime($thisMonthsClaimCutoff, 1, 0, 0);
    
        return $thisMonthsClaimCutoff;
    }

    static function buildHTMLRequestsTable($tableId = 'rfs'){
        $nextMonthObj = new \DateTime();
        $thisMonthObj = new \DateTime();
        $thisYear = $thisMonthObj->format('Y');
        $thisMonth = $thisMonthObj->format('m');
        $thisMonthObj->setDate($thisYear, $thisMonth, 01);
        $thisMonthsClaimCutoff = self::getThisMonthsClaimCutoff($thisMonthObj->format('d-m-Y'));

        $nextMonthObj >= $thisMonthsClaimCutoff ? $nextMonthObj = self::addTime($nextMonthObj, 0, 1, 0) : null;
        $monthLabels = array();

        for ($i = 0; $i < 6; $i++) {
            $monthLabels[] = $nextMonthObj->format('M_y');
            $nextMonthObj = self::addTime($nextMonthObj, 0, 1, 0);
        }
        ?>
        <table id='<?=$tableId;?>Table_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
        <thead>
        <tr>
        <th>RFS ID</th><th>PRN</th><th>Project Title</th><th>Project Code</th><th>Requestor Name</th><th>Requestor Email</th><th>Value Stream</th><th>Business Unit</th>
        <th>Link to PGMP</th><th>RFS Creator</th><th>RFS Created</th>
        <th>Resource Ref</th><th>Organisation</th><th>Service</th><th>Description</th><th>Start Date</th><th>End Date</th>
        <th>Total Hours</th><th>Resource Name</th><th>Request Creator</th><th>Request Created</th>
        <th>Cloned From</th><th>Status</th><th>Rate Type</th><th>Hours Type</th><th>RFS Status</th><th>RFS Type</th>
        <?php 
        foreach ($monthLabels as $label) {
            ?><th><?=$label?></th><?php 
        }
        ?>
        </tr></thead>
        <tbody>
        </tbody>
        <tfoot><tr>
        <th>RFS ID</th><th>PRN</th><th>Project Title</th><th>Project Code</th><th>Requestor Name</th><th>Requestor Email</th><th>Value Stream</th><th>Business Unit</th>
        <th>RFS Creator</th><th>RFS Created</th>
        <th>Link to PGMP</th><th>Resource Ref</th><th>Organisation</th><th>Service</th><th>Description</th><th>Start Date</th><th>End Date</th>
        <th>Total Hours</th><th>Resource Name</th><th>Request Creator</th><th>Request Created</th>
        <th>Cloned From</th><th>Status</th><th>Rate Type</th><th>Hours Type</th><th>RFS Status</th><th>RFS Type</th>
        <?php 
        foreach ($monthLabels as $label) {
            ?><th><?=$label?></th><?php 
        }
        ?>
        </tr></tfoot>
        </table>
        <?php
    }

    static function buildHTMLPipelineTable($tableId = 'rfs'){
        ?>
        <table id='<?=$tableId;?>Table_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
        <thead>
        <tr><th>RFS ID</th><th>Title</th><th>Resource Req.</th><th>From</th><th>To</th><th>Value Stream</th><th>Link to PGMP</th></tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot><tr><th>RFS ID</th><th>Title</th><th>Resource Req.</th><th>From</th><th>To</th><th>Value Stream</th><th>Link to PGMP</th></tr></tfoot>
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
        $predicate.= ' ARCHIVE IS NOT NULL ';
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
        $predicate.= ' ARCHIVE IS NULL ';
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

    function prepareDatesForQuery(){
        
        // The first month we need to show them is the CLAIM month they are currently in. 
        // So start with today, and get the next Claim Cut off - th
        
        $startMonthObj = new \DateTime();
        $thisMonthObj = new \DateTime();

        $thisYear = $thisMonthObj->format('Y');
        $thisMonth = $thisMonthObj->format('m');
        $thisDay = '01';
        $thisMonthObj->setDate($thisYear, $thisMonth, $thisDay);
        $thisMonthsClaimCutoff = self::getThisMonthsClaimCutoff($thisMonthObj->format('d-m-Y'));

        $startMonthObj >= $thisMonthsClaimCutoff ? $startMonthObj = self::addTime($startMonthObj, 0, 1, 0) : null;
        $startYear  = $startMonthObj->format('Y');
        $startMonth = $startMonthObj->format('m');
        
        $lastMonthObj = clone $startMonthObj;
        $lastMonthObj = self::addTime($lastMonthObj, 0, 6, 0);
        $lastYear = $lastMonthObj->format('Y');
        $lastMonth = $lastMonthObj->format('m');
                 
        $nextMonthObj = clone $startMonthObj;
        $monthLabels = array();
        $monthDetails = array();
        
        for ($i = 0; $i < 6; $i++) {
            $monthLabels[] = $nextMonthObj->format('M_y');
            $monthDetails[$i]['year'] = $nextMonthObj->format('Y');
            $monthDetails[$i]['month'] = $nextMonthObj->format('m');
            $nextMonthObj = self::addTime($nextMonthObj, 0, 1, 0);
        }
        
        $dates = array(            
            'monthLabels' => $monthLabels,
            'monthDetails' => $monthDetails,
            'startYear' => $startYear,
            'startMonth' => $startMonth,
            'lastYear' => $lastYear,
            'lastMonth' => $lastMonth
        );

        return $dates;
    }

    function prepareDatesForResults($row){
        $startDate = !empty($row['START_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['START_DATE'])->format('d M Y') : null;
        $startDateSortable = !empty($row['START_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['START_DATE'])->format('Ymd') : null;
        $endDate = !empty($row['END_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['END_DATE'])->format('d M Y') : null;
        $endDateSortable = !empty($row['END_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['END_DATE'])->format('Ymd') : null;
        
        $dates = array(            
            'startDate' => $startDate,
            'startDateSortable' => $startDateSortable,
            'endDate' => $endDate,
            'endDateSortable' => $endDateSortable
        );

        return $dates;
    }

    function prepareListQuery(){

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

    function returnAsArray($predicate=null, $withArchive=false){
        $sql  = " SELECT 
        RFS.RFS_ID, 
		RFS.PRN, 
		RFS.PROJECT_CODE,
		RFS.PROJECT_TITLE,
		RFS.REQUESTOR_NAME,
		RFS.REQUESTOR_EMAIL,
		RFS.VALUE_STREAM,
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
        $sql .= " LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$RFS_DATE_RANGE . " AS RDR ";
        $sql .= " ON RFS.RFS_ID = RDR.RFS ";
        $sql .= " LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$RFS_PCR . " AS RPCR ";
        $sql .= " ON RFS.RFS_ID = RPCR.RFS_ID ";
        $sql .= " WHERE 1=1 " ;
        $sql .= $withArchive ? " AND " . rfsTable::ARCHIVED : " AND " . rfsTable::NOT_ARCHIVED;
        $sql .= !empty($predicate) ? " AND  $predicate " : null ;
        
        $redis = $GLOBALS['redis'];
		$redisKey = md5($sql.'_key_'.$_ENV['environment']);
        if (!$redis->get($redisKey)) {
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
        if (is_iterable($result)) {
            foreach ($result as $key => $row) {
                $this->addGlyphicons($row);

                foreach ($row as $key => $data){
                    $row[] = trim($row[$key]);
                    unset($row[$key]);
                }
                $allData[]  = $row;
            }
        };
        echo $source;
        return array('data'=>$allData, 'sql'=>$sql);
    }

    function returnClaimReportAsArray($predicate=null, $withArchive=false){
        
        $dates = $this->prepareDatesForQuery();
        $monthLabels = $dates['monthLabels'];
        $monthDetails = $dates['monthDetails'];

        $startYear = $dates['startYear'];
        $startMonth = $dates['startMonth'];

        $lastYear = $dates['lastYear'];
        $lastMonth = $dates['lastMonth'];

        $sql = $this->prepareListQuery();

        $sql = "";
        $sql.=" WITH ";
        $sql.= " CLAIM(RESOURCE_REFERENCE, RESOURCE_NAME ";

        foreach ($monthLabels as $label) {
            $sql.=",  $label ";
        }

        $sql.= " ) AS ( ";
        $sql.=" SELECT RESOURCE_REFERENCE, RESOURCE_NAME ";

        foreach ($monthLabels as $label) {
            $sql.=", SUM($label) as $label ";
        }

        $sql.=" FROM ( ";
        $sql.="     SELECT RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME ";
        
        foreach ($monthDetails as $key => $detail) {
            $sql.=", CASE WHEN (CLAIM_YEAR = " . $detail['year'] . " AND CLAIM_MONTH = " . $detail['month'] . ") THEN SUM(CAST(HOURS as decimal(6,2))) ELSE null END AS " . $monthLabels[$key];
        }
        $sql.="      FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        $sql.="      LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS . " AS RH ";
        $sql.="      ON RR.RESOURCE_REFERENCE = RH.RESOURCE_REFERENCE ";
        $sql.=" WHERE 1=1 " ;
        $sql.=" AND ( ";
        $sql.="        (CLAIM_YEAR = $startYear AND ( CLAIM_MONTH >= $startMonth AND CLAIM_MONTH <= " .  ($startMonth+6) . " )) ";
        $sql.="      OR " ;
        $sql.="        (CLAIM_YEAR = $lastYear  AND ( CLAIM_MONTH <= $lastMonth  AND CLAIM_MONTH >= " . ($lastMonth-6) . "  )) ";
        $sql.="    ) ";
        $sql.="      GROUP BY RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME, CLAIM_YEAR, CLAIM_MONTH ";
        $sql.="      ) AS data ";
        $sql.="      GROUP BY RESOURCE_REFERENCE, RESOURCE_NAME ";
        // $sql.="      ORDER BY 1,2 ";
        $sql.=" ) ";
        $sql.=" " ;
        $sql.= " SELECT RFS.RFS_ID,RFS.PRN,RFS.PROJECT_TITLE,RFS.PROJECT_CODE,RFS.REQUESTOR_NAME,RFS.REQUESTOR_EMAIL,RFS.VALUE_STREAM,RFS.BUSINESS_UNIT ";
        $sql.= " ,RFS.LINK_TO_PGMP, ";
        $sql.= " RFS.RFS_CREATOR,RFS.RFS_CREATED_TIMESTAMP AS RFS_CREATED , ";
        $sql.= " RR.RESOURCE_REFERENCE,RR.ORGANISATION,RR.SERVICE,RR.DESCRIPTION,RR.START_DATE,RR.END_DATE,RR.TOTAL_HOURS, ";
        $sql.= " ";
        $sql.= "( CASE 
            WHEN CHARINDEX('" . resourceRequestTable::$duplicate . "', RR.RESOURCE_NAME) != 0 THEN null
            WHEN CHARINDEX('" . resourceRequestTable::$delta . "', RR.RESOURCE_NAME) != 0 THEN null
            ELSE RR.RESOURCE_NAME
        END) AS RESOURCE_NAME,";
        $sql.= " RR.RR_CREATOR AS REQUEST_CREATOR,RR.RR_CREATED_TIMESTAMP AS REQUEST_CREATED, ";
        $sql.= " RR.CLONED_FROM, RR.STATUS, RR.RATE_TYPE, RR.HOURS_TYPE, RFS.RFS_STATUS, RFS.RFS_TYPE, CLAIM.* ";
        $sql.= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        $sql.= " ON RR.RFS =  RFS.RFS_ID ";
        $sql.= " , CLAIM ";
        $sql.= " WHERE 1=1 " ;
        $sql.= " AND " . rfsTable::NOT_ARCHIVED;
        $sql.= " AND RR.RESOURCE_REFERENCE = CLAIM.RESOURCE_REFERENCE ";
        $sql.= !empty($predicate) ? " AND  $predicate " : null ;
        
        $redis = $GLOBALS['redis'];
		$redisKey = md5($sql.'_key_'.$_ENV['environment']);
        if (!$redis->get($redisKey)) {
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
        if (is_iterable($result)) {
            foreach ($result as $key => $row) {
                $rowDates = $this->prepareDatesForResults($row);
                $startDate = $rowDates['startDate'];
                $startDateSortable = $rowDates['startDateSortable'];
                $endDate = $rowDates['endDate'];
                $endDateSortable = $rowDates['endDateSortable'];
                
                $row['START_DATE'] = array('display'=> $startDate,'sort'=>$startDateSortable);
                $row['END_DATE']   = array('display'=> $endDate, 'sort'=>$endDateSortable);
                
                foreach ($row as $key => $data){ 
                    $row[] = ! is_array($row[$key]) ? trim($row[$key]) : $row[$key];
                    unset($row[$key]);
                }
                $allData[]  = $row;
            }
        };
        echo $source;
        return array('data'=>$allData, 'sql'=>$sql);
    }

    function returnClaimReportAsJson($predicate=null, $withArchive=false, $rsOnly = false){
        
        $dates = $this->prepareDatesForQuery();
        $monthLabels = $dates['monthLabels'];
        $monthDetails = $dates['monthDetails'];

        $startYear = $dates['startYear'];
        $startMonth = $dates['startMonth'];

        $lastYear = $dates['lastYear'];
        $lastMonth = $dates['lastMonth'];

        $sql = "";
        $sql.=" WITH ";
        $sql.= " CLAIM(RESOURCE_REFERENCE, RESOURCE_NAME, EMAIL_ADDRESS, KYN_EMAIL_ADDRESS ";

        foreach ($monthLabels as $label) {
            $sql.=",  $label ";
        }

        $sql.= " ) AS ( ";
        $sql.=" SELECT RESOURCE_REFERENCE, RESOURCE_NAME, EMAIL_ADDRESS, KYN_EMAIL_ADDRESS ";
        
        foreach ($monthLabels as $label) {
            $sql.=", SUM($label) AS $label ";
        }

        $sql.=" FROM ( ";
        $sql.="     SELECT RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME, RR.EMAIL_ADDRESS, RR.KYN_EMAIL_ADDRESS ";
        
        foreach ($monthDetails as $key => $detail) {
            $sql.=", CASE WHEN (CLAIM_YEAR = " . $detail['year'] . " AND CLAIM_MONTH = " . $detail['month'] . ") THEN SUM(CAST(HOURS as decimal(6,2))) ELSE null END AS " . $monthLabels[$key];
        }
        $sql.="      FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        $sql.="      LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS . " AS RH ";
        $sql.="      ON RR.RESOURCE_REFERENCE = RH.RESOURCE_REFERENCE ";
        $sql.=" WHERE 1=1 " ;
        $sql.=" AND ( ";
        $sql.="        (CLAIM_YEAR = $startYear AND ( CLAIM_MONTH >= $startMonth and CLAIM_MONTH <= " .  ($startMonth+6) . " )) ";
        $sql.="      OR " ;
        $sql.="        (CLAIM_YEAR = $lastYear  AND ( CLAIM_MONTH <= $lastMonth  and CLAIM_MONTH >= " . ($lastMonth-6) . "  )) ";
        $sql.="    ) ";
        $sql.="      GROUP BY RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME, RR.EMAIL_ADDRESS, RR.KYN_EMAIL_ADDRESS, CLAIM_YEAR, CLAIM_MONTH ";
        $sql.="      ) AS data ";
        $sql.="      GROUP BY RESOURCE_REFERENCE, RESOURCE_NAME, EMAIL_ADDRESS, KYN_EMAIL_ADDRESS ";
        // $sql.="      ORDER BY 1,2 ";
        $sql.=" ) ";
        $sql.=" " ;
        $sql.= " SELECT RFS.RFS_ID,RFS.PRN,RFS.PROJECT_TITLE,RFS.PROJECT_CODE,RFS.REQUESTOR_NAME,RFS.REQUESTOR_EMAIL,RFS.VALUE_STREAM,RFS.BUSINESS_UNIT ";
        $sql.= " ,RFS.LINK_TO_PGMP, ";
        $sql.= " RFS.RFS_CREATOR,RFS.RFS_CREATED_TIMESTAMP AS RFS_CREATED , ";
        $sql.= " RR.RESOURCE_REFERENCE,RR.ORGANISATION,RR.SERVICE,RR.DESCRIPTION,RR.START_DATE,RR.END_DATE,RR.TOTAL_HOURS, ";
        $sql.= " ";
        $sql.= "( CASE 
            WHEN CHARINDEX('" . resourceRequestTable::$duplicate . "', RR.RESOURCE_NAME) != 0 THEN null
            WHEN CHARINDEX('" . resourceRequestTable::$delta . "', RR.RESOURCE_NAME) != 0 THEN null
            ELSE RR.RESOURCE_NAME
        END) AS RESOURCE_NAME,";
        $sql.= " RR.EMAIL_ADDRESS, "; 
        $sql.= " RR.KYN_EMAIL_ADDRESS, ";
        $sql.= " RR.RR_CREATOR AS REQUEST_CREATOR,RR.RR_CREATED_TIMESTAMP AS REQUEST_CREATED, ";
        $sql.= " RR.CLONED_FROM, RR.STATUS, RR.RATE_TYPE, RR.HOURS_TYPE, RFS.RFS_STATUS, RFS.RFS_TYPE, CLAIM.* ";
        $sql.= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        $sql.= " ON RR.RFS =  RFS.RFS_ID ";
        $sql.= " , CLAIM ";
        $sql.= " WHERE 1=1 " ;
        $sql.= " AND " . rfsTable::NOT_ARCHIVED;
        $sql.= " AND RR.RESOURCE_REFERENCE = CLAIM.RESOURCE_REFERENCE ";
        $sql.= !empty($predicate) ? " AND  $predicate " : null ;
        $resultSet = $this->execute($sql);
        $resultSet ? null : die("SQL Failed");
        $allData = array();

        switch (true) {
            case $rsOnly:
                return $resultSet;
                break;
            case $resultSet:
                while($row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
                    $testJson = json_encode($row);
                    if(!$testJson){
                        break; // It's got invalid chars in it that will be a problem later.
                    }
                    
                    $rowDates = $this->prepareDatesForResults($row);
                    $startDate = $rowDates['startDate'];
                    $startDateSortable = $rowDates['startDateSortable'];
                    $endDate = $rowDates['endDate'];
                    $endDateSortable = $rowDates['endDateSortable'];
                    
                    $row['START_DATE'] = $startDate;
                    $row['START_DATE_RAW'] = $startDateSortable;
                    $row['END_DATE']   = $endDate;
                    $row['END_DATE_RAW']   = $endDateSortable;
        
                    foreach ($row as $key => $data){
                        // $row[] = ! is_array($row[$key]) ? trim($row[$key]) : $row[$key];
                        // unset($row[$key]);
                        $row[$key] = ! is_array($row[$key]) ? trim($row[$key]) : $row[$key]; 
                    }
                    $allData[]  = $row;
                }
                return array('data'=>$allData, 'sql'=>$sql);            
            default:
                return false;
                break;
        }
    }

    function returnNoneActiveReportAsArray($predicate=null, $withArchive = false){

        $dates = $this->prepareDatesForQuery();
        $monthLabels = $dates['monthLabels'];
        $monthDetails = $dates['monthDetails'];

        $startYear = $dates['startYear'];
        $startMonth = $dates['startMonth'];

        $lastYear = $dates['lastYear'];
        $lastMonth = $dates['lastMonth'];

        $sql = "";
        $sql.=" WITH ";
        $sql.= " CLAIM(RESOURCE_REFERENCE, RESOURCE_NAME ";

        foreach ($monthLabels as $label) {
            $sql.=",  $label ";
        }

        $sql.= " ) AS ( ";
        $sql.=" SELECT RESOURCE_REFERENCE, RESOURCE_NAME ";
        
        foreach ($monthLabels as $label) {
            $sql.=", SUM($label) as $label ";
        }

        $sql.=" FROM ( ";
        $sql.="     SELECT RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME ";
        
        foreach ($monthDetails as $key => $detail) {
            $sql.=", CASE WHEN (CLAIM_YEAR = " . $detail['year'] . " AND CLAIM_MONTH = " . $detail['month'] . ") THEN SUM(CAST(HOURS as decimal(6,2))) ELSE null END AS " . $monthLabels[$key];
        }
        $sql.="      FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        $sql.="      LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS . " AS RH ";
        $sql.="      on RR.RESOURCE_REFERENCE = RH.RESOURCE_REFERENCE ";
        $sql.=" WHERE 1=1 " ;
        $sql.=" AND ( ";
        $sql.="        (CLAIM_YEAR = $startYear AND ( CLAIM_MONTH >= $startMonth AND CLAIM_MONTH <= " .  ($startMonth+6) . " )) ";
        $sql.="      OR " ;
        $sql.="        (CLAIM_YEAR = $lastYear  AND ( CLAIM_MONTH <= $lastMonth  AND CLAIM_MONTH >= " . ($lastMonth-6) . "  )) ";
        $sql.="    ) ";
        $sql.="      group by RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME, CLAIM_YEAR, CLAIM_MONTH ";
        $sql.="      ) AS data ";
        $sql.="      group by RESOURCE_REFERENCE, RESOURCE_NAME ";
        // $sql.="      order by 1,2 ";
        $sql.=" ) ";
        $sql.=" " ;
        $sql.= " SELECT RFS.RFS_ID,RFS.PRN,RFS.PROJECT_TITLE,RFS.PROJECT_CODE,RFS.REQUESTOR_NAME,RFS.REQUESTOR_EMAIL,RFS.VALUE_STREAM,RFS.BUSINESS_UNIT ";
        $sql.= " ,RFS.LINK_TO_PGMP, ";
        $sql.= " RFS.RFS_CREATOR,RFS.RFS_CREATED_TIMESTAMP AS RFS_CREATED , ";
        $sql.= " RR.RESOURCE_REFERENCE,RR.ORGANISATION,RR.SERVICE,RR.DESCRIPTION,RR.START_DATE,RR.END_DATE,RR.TOTAL_HOURS, ";
        $sql.= " ";
        $sql.= "( CASE 
            WHEN CHARINDEX('" . resourceRequestTable::$duplicate . "', RR.RESOURCE_NAME) != 0 THEN null
            WHEN CHARINDEX('" . resourceRequestTable::$delta . "', RR.RESOURCE_NAME) != 0 THEN null
            ELSE RR.RESOURCE_NAME
        END) AS RESOURCE_NAME,";
        $sql.= " RR.RR_CREATOR AS REQUEST_CREATOR,RR.RR_CREATED_TIMESTAMP AS REQUEST_CREATED, ";
        $sql.= " RR.CLONED_FROM, RR.STATUS, RR.RATE_TYPE, RR.HOURS_TYPE, RFS.RFS_STATUS, RFS.RFS_TYPE, CLAIM.* ";
        $sql.= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        $sql.= " ON RR.RFS =  RFS.RFS_ID ";        
        // $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE . " as AR ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE . " AS AR ";
        $sql.= " ON LOWER(RR.RESOURCE_NAME) = LOWER(AR.NOTES_ID) ";        
        $sql.= " , CLAIM ";
        $sql.= " WHERE 1=1 " ;
        $sql.= " AND " . rfsTable::NOT_ARCHIVED;
        $sql.= " AND RR.RESOURCE_REFERENCE = CLAIM.RESOURCE_REFERENCE ";        
        $sql.= " AND (
            RR.RESOURCE_NAME NOT LIKE '" . resourceRequestTable::$duplicate . "%' 
            OR RR.RESOURCE_NAME NOT LIKE '" . resourceRequestTable::$delta . "%'
        )";
        $sql.= " AND RR.Status = '" . resourceRequestRecord::STATUS_ASSIGNED . "'";
        $sql.= " AND AR.STATUS = '" . activeResourceTable::INT_STATUS_INACTIVE . "'";
        $sql.= !empty($predicate) ? " AND  $predicate " : null ;

        $resultSet = $this->execute($sql);
        $resultSet ? null : die("SQL Failed");
        $allData = array();

        while($row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
            $testJson = json_encode($row);
            if(!$testJson){
                break; // It's got invalid chars in it that will be a problem later.
            }     
            
            $rowDates = $this->prepareDatesForResults($row);
            $startDate = $rowDates['startDate'];
            $startDateSortable = $rowDates['startDateSortable'];
            $endDate = $rowDates['endDate'];
            $endDateSortable = $rowDates['endDateSortable'];
            
            $row['START_DATE'] = array('display'=> $startDate,'sort'=>$startDateSortable);
            $row['END_DATE']   = array('display'=> $endDate, 'sort'=>$endDateSortable);
            
            foreach ($row as $key => $data){ 
                $row[] = ! is_array($row[$key]) ? trim($row[$key]) : $row[$key];
                unset($row[$key]);
            }
            $allData[]  = $row;
        }
        return array('data'=>$allData, 'sql'=>$sql);
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