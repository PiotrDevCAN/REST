<?php
namespace rest;

use itdq\DbTable;
use itdq\PhpMemoryTrace;
use itdq\Loader;
use \DateTime;
use Complex\Exception;

class resourceRequestTable extends DbTable {
    use tableTrait;

    const DUPLICATE = 'Dup of';
    const DELTA     = 'Delta from';
    
    private $hrsThisWeekByResourceReference;
    private $lastDiaryEntriesByResourceReference;

    static function buildHTMLTable($startDate,$endDate){
        $RRheaderCells = resourceRequestRecord::htmlHeaderCellsStatic($startDate);
        $RFSheaderCells = rfsRecord::htmlHeaderCellsStatic();
        ?>
        <table id='resourceRequestsTable_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
        <thead>
        <tr><?=$RFSheaderCells . $RRheaderCells ;?></tr></thead>
        <tbody>
        </tbody>
        <tfoot><tr><?=$RFSheaderCells . $RRheaderCells ;?></tr></tfoot>
        </table>
        <?php
    }

    function getResourceName($resourceReference){
        $sql  = " SELECT RESOURCE_NAME FROM  " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE RESOURCE_REFERENCE=" . db2_escape_string($resourceReference);

        $rs = $this->execute($sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        $row = db2_fetch_assoc($rs);
        
        return $row ? trim($row['RESOURCE_NAME']) : false;
    }
    
    function updateResourceName($resourceReference,$resourceName, $clear=null){
        
        $status = resourceRequestRecord::STATUS_ASSIGNED;
        if(!empty($clear)){
            $resourceName = '';
            $status=resourceRequestRecord::STATUS_NEW;
        } else if(empty($resourceReference) or empty($resourceName)){
            throw new \Exception('Paramaters Missing in call to ' . __FUNCTION__);
        }
        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET RESOURCE_NAME='" . db2_escape_string($resourceName) . "' ";
        $sql .= " , STATUS='" . $status . "' ";
        $sql .= " WHERE RESOURCE_REFERENCE=" . db2_escape_string($resourceReference);
        
        $result = $this->execute($sql);
        
        return $result;
    }
    
    function populateLastDiaryEntriesArray(){
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$LATEST_DIARY_ENTRIES;
        
        error_log(__FILE__ . ":" . __LINE__ . ":" . $sql);
   
        $preExec = microtime(true);
        $rs = db2_exec($GLOBALS['conn'], $sql);
        
        $postExec = microtime(true);
        error_log(__FILE__ . ":" . __LINE__ . "Db2 exec:" . ($postExec-$preExec));
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
            return false;
        }
        
        while(($row= db2_fetch_assoc($rs))==true){
            $this->lastDiaryEntriesByResourceReference[$row['RESOURCE_REFERENCE']] = $row;        
        }
        
        error_log(__FILE__ . ":" . __LINE__ . "Elapsed:" . (microtime(true)-$postExec));
     }
    
     function returnAsArray($startDate,$endDate, $predicate=null, $pipelineLiveArchive = 'Live', $withButtons=true){
        
//       $this->populateLastDiaryEntriesArray();        
               
        $autoCommit = db2_autocommit($GLOBALS['conn']);
        db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);        
        
        $resourceRequestHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
        $hoursRemainingByReference = $resourceRequestHoursTable->getHoursRemainingByReference();
        $monthNumber = 0;
        $startDateObj = new \DateTime($startDate);
        $endDateObj = new \DateTime($endDate);

        $day =  $startDateObj->format('d');
        if($day > 28){
            // We can't step through adding months if we start on 29th,30th or 31st.
            $year = $startDateObj->format('Y');
            $month = $startDateObj->format('m');
            $startDateObj->setDate($year, $month, '28');
        }
        if(empty($endDate)){
            $endDateObj = \DateTime::createFromFormat('Y-m-d',$startDateObj->format('Y-m-d'));
            $endDateObj->modify("+6 months");
        }

        $sql =  " WITH resource_hours as (";
        $sql .= "  SELECT RESOURCE_REFERENCE as RR ";
                

        while($startDateObj->format('Ym') <= $endDateObj->format('Ym')){
            $dataTableColName = "MONTH_" . substr("00" . ++$monthNumber,-2);
            $columnName =  $startDateObj->format('M_Y');
            $sql .= ",SUM(" . $columnName . ") as " . $dataTableColName;
            $startDateObj->modify('+1 month');
        }

        $sql .= " FROM ( ";
        $sql .= "        SELECT RESOURCE_REFERENCE ";

        $startDateObj = new \DateTime($startDate);
        $day =  $startDateObj->format('d');
        if($day > 28){
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
        
        $resourceRequestTable = $pipelineLiveArchive=='archive'  ? allTables::$ARCHIVED_RESOURCE_REQUESTS : allTables::$RESOURCE_REQUESTS;
        $resourceRequestHoursTable = $pipelineLiveArchive=='archive'  ? allTables::$ARCHIVED_RESOURCE_REQUEST_HOURS : allTables::$RESOURCE_REQUEST_HOURS;

        $sql .=  " FROM " . $GLOBALS['Db2Schema'] . "." . $resourceRequestHoursTable;
        $sql .= "   WHERE  ( claim_month >= " . $startDateObj->format('m') . " and claim_year = " . $startDateObj->format('Y') . ")  " ;
        $sql .= $startDateObj->format('Y') !==  $endDateObj->format('Y') ?  "    AND (claim_year > " . $startDateObj->format('Y') . " and claim_year < " . $endDateObj->format('Y') . " ) " : null;
        $sql .= "         AND (claim_month <= " . $endDateObj->format('m') . " and claim_year = " . $endDateObj->format('Y') . ")  " ;
        $sql .= " ) as resource_hours ";
        $sql .= " GROUP BY RESOURCE_REFERENCE ";
        $sql .= " ) ";
        $sql .= " SELECT RFS.*, RR.* ";
        $sql .= " ,  LD.LATEST_ENTRY, LD.CREATOR as ENTRY_CREATOR, LD.CREATED as ENTRY_CREATED ";
        $sql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql .= " LEFT JOIN  " . $GLOBALS['Db2Schema'] . "." . $resourceRequestTable. " as RR ";
        $sql .= " ON RR.RFS = RFS.RFS_ID ";
        $sql .= " LEFT JOIN  " . $GLOBALS['Db2Schema'] . "." . allTables::$LATEST_DIARY_ENTRIES. " as LD ";
        $sql .= " ON RR.RESOURCE_REFERENCE = LD.RESOURCE_REFERENCE ";

        $sql .=  " WHERE RR.RFS is not null ";
        $sql .= $pipelineLiveArchive=='archive'  ? " AND ARCHIVE is not null " : " AND ARCHIVE is null ";
        $sql .= $pipelineLiveArchive=='pipeline' ? " AND RFS_STATUS='" . rfsRecord::RFS_STATUS_PIPELINE . "' " : " AND RFS_STATUS!='" . rfsRecord::RFS_STATUS_PIPELINE . "' ";
        $sql .= !empty($predicate) ? " $predicate " : null ;

        $sql .= " ORDER BY RFS.RFS_CREATED_TIMESTAMP DESC ";

        
        error_log(__FILE__ . ":" . __LINE__ . ":" . $pipelineLiveArchive);
        error_log(__FILE__ . ":" . __LINE__ . ":" . $predicate);
        error_log(__FILE__ . ":" . __LINE__ . ":" . $sql);

        $resultSet = $this->execute($sql);

        $resultSet ? null : die("SQL Failed");

        $allData = array();
        $allData['data'] = array();

        while(($row = db2_fetch_assoc($resultSet))==true){
            PhpMemoryTrace::reportPeek(__FILE__,__LINE__);
            $testJson = json_encode($row);
            if(!$testJson){
                error_log("Invalid character found");
                error_log(print_r($row,true));
                
                throw new \Exception("Invalid character found in Row ");
                break; // It's got invalid chars in it that will be a problem later.
            }
            $row = array_map('trim',$row);
            $row['hours_to_go'] = isset($hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['hours']) ? $hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['hours'] : null;
            $row['weeks_to_go'] = isset($hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['weeks']) ? $hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['weeks'] : null;
            
            $withButtons ? $this->addGlyphicons($row) : null;
            $allData['data'][]  = $row;
        }

        $allData['sql'] = $sql;
        
        db2_autocommit($GLOBALS['conn'],$autoCommit);
        
        return $allData ;
    }

    function addGlyphicons(&$row){
        $today = new \DateTime();
       // $complimentaryDateFields = resourceRequestHoursTable::getDateComplimentaryFields($today);
        
        if($this->hrsThisWeekByResourceReference==null){
            $loader = new Loader();            
            $predicate = " WEEK_NUMBER='" . db2_escape_string($today->format('W')) . "' ";
            $this->hrsThisWeekByResourceReference = $loader->loadIndexed('HOURS','RESOURCE_REFERENCE',allTables::$RESOURCE_REQUEST_HOURS, $predicate);            
        }

        PhpMemoryTrace::reportPeek(__FILE__,__LINE__);
        $rfsId = $row['RFS_ID'];
        $resourceReference = $row['RESOURCE_REFERENCE'];
        
        $resourceName = $row['RESOURCE_NAME'];
        $prn = $row['PRN'];
        $valuestream = $row['VALUE_STREAM'];
        $businessunit = $row['BUSINESS_UNIT'];
        $startDate4Picka = !empty($row['START_DATE']) ? Datetime::createFromFormat('Y-m-d', $row['START_DATE'])->format('Y-m-d') : null;
        $endDate4Picka = !empty($row['END_DATE'])     ? Datetime::createFromFormat('Y-m-d', $row['END_DATE'])->format('Y-m-d') : null;
        $startDate = !empty($row['START_DATE']) ? Datetime::createFromFormat('Y-m-d', $row['START_DATE'])->format('d M Y') : null;
        $startDateObj = !empty($row['START_DATE']) ? Datetime::createFromFormat('Y-m-d', $row['START_DATE']) : null;
        $startDateSortable = !empty($row['START_DATE']) ? Datetime::createFromFormat('Y-m-d', $row['START_DATE'])->format('Ymd') : null;
        $rfsEndDate      = !empty($row['RFS_END_DATE'])     ? Datetime::createFromFormat('Y-m-d', $row['RFS_END_DATE'])->format('d M Y') : null;
        $endDate         = !empty($row['END_DATE'])     ? Datetime::createFromFormat('Y-m-d', $row['END_DATE'])->format('d M Y') : null;
        $endDateSortable = !empty($row['END_DATE'])     ? Datetime::createFromFormat('Y-m-d', $row['END_DATE'])->format('Ymd') : null;
        $endDateObj = !empty($row['END_DATE'])   ? Datetime::createFromFormat('Y-m-d', $row['END_DATE']) : null;
        is_object($endDateObj) ?  $endDateObj->setTime(23, 59, 59) : null; // When setting completed, compare against midnight on END_DATE
        $totalHours = $row['TOTAL_HOURS'];
        $hoursType = $row['HOURS_TYPE'];
        $status = !empty($row['STATUS']) ? $row['STATUS'] : resourceRequestRecord::STATUS_NEW;
        $organisation = $row['ORGANISATION'];
        $service = $row['SERVICE'];
        $editable = true;
        
        $completeable = (($status == 'Assigned') && ($endDateObj < $today)) ? true : false; // Someone has been assigned and the End Date has passed.
        
        switch (true) {
            case $today < $startDateObj:
                $assignColor = 'text-success';
                $started     = '<br/>Planned';
                break;
            case $today <= $endDateObj:
                $assignColor = 'text-warning';
                $started     = 'Active';
                break;
            case $today > $endDateObj:
                $assignColor = 'text-danger';
                $started     = 'Completed';
                $editable = false;
                break;           
            default:
                $assignColor = 'text-primary';
                $started     = 'Unclear';
            break;
        }
        
        $row['STATUS']= $completeable ? 
        "<button type='button' class='btn btn-xs changeStatusCompleted accessRestrict accessAdmin accessCdi accessSupply ' aria-label='Left Align'
                    data-rfs='" .$rfsId . "'
                    data-resourcereference='" .$resourceReference . "'
                    data-prn='" .$prn . "'
                    data-valuestream='" .$valuestream . "'
                    data-status='" . $status . "'
                    data-organisation='" .$organisation .  "'
                    data-service='" . $service . "'
                    data-resourcename='" . $resourceName . "'
                    data-start='" . $startDate4Picka . "'
                    data-end='" . $endDate4Picka . "'


         >
         <span data-toggle='tooltip' title='Change Status to Completed' class='glyphicon glyphicon-check ' aria-hidden='true' ></span>
            </button>&nbsp;<span class='$assignColor'>$status</span>" : "<span class='$assignColor'>$status</span>";

        $editButtonColor = empty($resourceName) ? 'text-success' : 'text-warning';
        $editButtonColor = substr($resourceName,0,strlen(resourceRequestTable::DUPLICATE))==resourceRequestTable::DUPLICATE ? 'text-success' : $editButtonColor;
        $editButtonColor = substr($resourceName,0,strlen(resourceRequestTable::DELTA))==resourceRequestTable::DELTA ? 'text-success' : $editButtonColor;

        $resName = $editButtonColor == 'text-success' ? "<i>$resourceName</i>" : $resourceName;

        $duplicatable = true; //Can clone any record.

        $canBeAmendedByDemandTeam = empty(trim($resourceName)) ? 'accessDemand' : null; // Demand can amend any Request that is yet to have resource allocated to it.

        $displayedResourceName = "<span class='dataOwner' ";
        $displayedResourceName.= "  data-rfs='" .$rfsId . "' ";
        $displayedResourceName.= "  data-resourcereference='" .$resourceReference . "' ";
        $displayedResourceName.= "  data-prn='" .$prn . "' ";
        $displayedResourceName.= "  data-valuestream='" . $valuestream. "' ";
        $displayedResourceName.= "  data-businessunit='" . $businessunit. "' ";
        $displayedResourceName.= "  data-status='" . $status . "' ";
        $displayedResourceName.= "  data-service='" .$service .  "' ";
        $displayedResourceName.= "  data-organisation='" .$organisation .  "' ";
//        $row['RESOURCE_NAME'].= "  data-subservice='" . $service . "' ";
        $displayedResourceName.= "  data-resourcename='" . $resourceName . "' ";
        $displayedResourceName.= "  data-start='" . $startDate . "' ";
        $displayedResourceName.= "  data-startpika='" . $startDate4Picka . "' ";
        $displayedResourceName.= "  data-end='" . $endDate . "' ";
        $displayedResourceName.= "  data-endpika='" . $endDate4Picka . "' ";
        $displayedResourceName.= "  data-rfsenddate='" . $rfsEndDate . "' ";
        $displayedResourceName.= "  data-hrs='" . $totalHours . "' ";
        $displayedResourceName.= "  data-hrstype='" . $hoursType . "' ";
        $displayedResourceName.= "  >";

//        $row['RESOURCE_NAME'].= $editable ? 
//            "<button type='button' class='btn btn-xs editRecord accessRestrict accessAdmin accessCdi $canBeAmendedByDemandTeam ' aria-label='Left Align' data-reference='" .$resourceReference . "' data-type='" .$service . "' >
//                <span data-toggle='tooltip' class='glyphicon glyphicon-edit ' aria-hidden='true' title='Edit Resource Request'></span>
//            </button>" : null;
        $displayedResourceName.= $editable ? 
            "<button type='button' class='btn btn-xs editResource accessRestrict accessAdmin accessCdi accessSupply ' aria-label='Left Align' data-reference='" .$resourceReference . "' data-type='" .$service . "' data-resource-name='" . $resourceName . "' >
                <span data-toggle='tooltip' class='glyphicon glyphicon-user $editButtonColor' aria-hidden='true' title='Edit Assigned Resource'></span>
            </button>" : null;
        $displayedResourceName.= $editable ? 
            "<button type='button' class='btn btn-xs editHours accessRestrict accessAdmin accessCdi accessSupply $canBeAmendedByDemandTeam ' aria-label='Left Align' data-reference='" . $resourceReference . "'  data-startDate='" . $startDate . "' >
                <span data-toggle='tooltip' class=' glyphicon glyphicon-time text-primary' aria-hidden='true' title='Edit Dates/Hours'></span>
            </button>" : null;
        $displayedResourceName.= $editable ?
            "<button type='button' class='btn btn-xs endEarly accessRestrict accessAdmin accessCdi accessSupply ' aria-label='Left Align' data-reference='" . $resourceReference . "'  data-endDate='" . $endDate . "' >
                <span data-toggle='tooltip' class=' glyphicon glyphicon-flash text-primary' aria-hidden='true' title='Indicate Completed'></span>
            </button>" : null;
        
        $resName = empty(trim($resourceName)) ? "<i>Unallocated</i>" : $resName;
        $resName = substr($resourceName,0,strlen(resourceRequestTable::DELTA))==resourceRequestTable::DELTA ? "<i>Unallocated</i><br/>" . trim($resourceName) : $resName;

        $displayedResourceName.= "&nbsp;" . $resName ;
        $displayedResourceName.= "</span>";
        
        $calendarEntry = !empty($row['LATEST_ENTRY']) ?  $row['LATEST_ENTRY'] . " <small>" . $row['ENTRY_CREATOR'] . ' ' . $row['ENTRY_CREATED'] . "</small>" : null;
//        $calendarEntry = "<small>Latest diary entry not currently available</small>";      
        
        $displayedResourceName.= "<br/><button type='button' class='btn btn-xs btnOpenDiary accessRestrict accessAdmin accessCdi accessSupply accessDemand ' ";
        $displayedResourceName.= "     aria-label='Left Align'  ";
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
            "<button type='button' class='btn btn-xs editRecord accessRestrict accessAdmin accessCdi $canBeAmendedByDemandTeam ' aria-label='Left Align' data-reference='" .$resourceReference . "' data-type='" .$service . "' >
                <span data-toggle='tooltip' class='glyphicon glyphicon-edit ' aria-hidden='true' title='Edit Resource Request'></span>
            </button>" : null;
        
        $displayRfsId.= $duplicatable && $editable ?
            "<button type='button' class='btn btn-xs requestDuplication accessRestrict accessAdmin accessCdi accessSupply $canBeAmendedByDemandTeam' aria-label='Left Align'
                data-reference='" . $resourceReference . "'
                data-rfs='" . $row['RFS_ID'] . "'
                data-type='" . $row['SERVICE'] . "'
                data-start='" . $row['START_DATE'] . "'
            >
                <span data-toggle='tooltip' class='glyphicon glyphicon-duplicate text-primary' aria-hidden='true' title='Clone Resource Request'></span>
            </button>" : null;
        
        $displayRfsId.= $editable ? 
            "<button type='button' class='btn btn-xs deleteRecord accessRestrict $canBeAmendedByDemandTeam accessAdmin accessCdi ' aria-label='Left Align' data-reference='" .$resourceReference . "' data-platform='" .trim($row['ORGANISATION']) .  "' data-rfs='" .trim($row['RFS_ID']) . "' data-type='" . $service . "' >
                <span data-toggle='tooltip' title='Delete Resource Request' class='glyphicon glyphicon-trash text-warning ' aria-hidden='true' ></span>
            </button>": null;
        
        $row['RFS'] = array('display'=> $displayRfsId, 'sort'=>$rfsId);
        
        $displayHrsPerWeek = "";
        $hrsThisWeek =   $displayHrsPerWeek.= isset($this->hrsThisWeekByResourceReference[$resourceReference]) ?  $this->hrsThisWeekByResourceReference[$resourceReference] : "N/A";
        
        $displayStartDate = '';
        $displayStartDate.= "<span class='$assignColor'>$startDate  to  $endDate <br/>";
        $displayStartDate.= "Total Hours: " . $row['TOTAL_HOURS'] . "<br/>";
        $displayStartDate.= ($started == 'Active') ? "Hrs This Week: " . $hrsThisWeek . "<br/>" : null;
        $displayStartDate.= (isset($row['hours_to_go'])) ? "Hrs remaining:" . $row['hours_to_go'] . "<br/>" : null;
        $displayStartDate.= (isset($row['weeks_to_go'])) ? "Weeks remaining:" . $row['weeks_to_go'] . "<br/>" : null;
        $displayStartDate.= "$started";
        
        $row['START_DATE'] = array('display'=> $displayStartDate, 'sort'=>$startDateSortable);
        $row['END_DATE'] = array('display'=> $endDate, 'sort'=>$endDateSortable);
        
        $totalHours = $row['TOTAL_HOURS'];
        
        $displayHrsPerWeek = "";
        
        $displayHrsPerWeek = "Total Hrs:" . $totalHours . "<br/>";
        $displayHrsPerWeek.= ($started == 'Active') ? "This Week:" . $hrsThisWeek : null;
        
        $row['TOTAL_HOURS'] = array('display'=>$displayHrsPerWeek,'sort'=>$totalHours);
        
        $row['ORGANISATION']=array('display'=>$row['ORGANISATION'] . "<br/><small>" . $row['SERVICE'] . "</small>", 'sort'=>$organisation);
        
    }
    
    static function setEndDate($resourceReference, $endDate){
        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . \rest\allTables::$RESOURCE_REQUESTS;
        $sql .= "  SET END_DATE = DATE('" . db2_escape_string($endDate) ."') ";
        $sql .= " WHERE RESOURCE_REFERENCE=" . db2_escape_string($resourceReference) ." ";

        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        return true;
    }
    
    static function setStartDate($resourceReference, $startDate){
        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . \rest\allTables::$RESOURCE_REQUESTS;
        $sql .= "  SET START_DATE = DATE('" . db2_escape_string($startDate) ."') ";
        $sql .= " WHERE RESOURCE_REFERENCE=" . db2_escape_string($resourceReference) ." ";
  
        $rs = db2_exec($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return true;
    }
    
    static function setHrsPerWeek($resourceReference, $hrsPerWeek){
        $hrsPerWeekInt = intval($hrsPerWeek);
        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . \rest\allTables::$RESOURCE_REQUESTS;
        $sql .= "  SET HRS_PER_WEEK     = " . db2_escape_string($hrsPerWeek) ;
        $sql .= "  ,   HRS_PER_WEEK_INT = " . db2_escape_string($hrsPerWeekInt) ;
        $sql .= " WHERE RESOURCE_REFERENCE=" . db2_escape_string($resourceReference) ." ";
      
        $rs = db2_exec($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return true;
    }
    
    static function setTotalHours($resourceReference, $totalHours){
        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . \rest\allTables::$RESOURCE_REQUESTS;
        $sql .= "  SET TOTAL_HOURS     = " . db2_escape_string($totalHours) ;
        $sql .= " WHERE RESOURCE_REFERENCE=" . db2_escape_string($resourceReference) ." ";
       
        $rs = db2_exec($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return true;
    }
    
    static function getVbacActiveResourcesForSelect2(){

        unset($_SESSION['vbacEmployees']);
        unset($_SESSION['myTribe']);

        if(!isset($_SESSION['vbacEmployees']) || !isset($_SESSION['myTribe'])){
            $_SESSION['vbacEmployees'] = array();
            
            $url = $_ENV['vbac_url'] . '/api/squadTribePlus.php?token=' . $_ENV['vbac_api_token'] . '&withProvClear=true&plus=SQUAD_NAME,P.EMAIL_ADDRESS,TRIBE_NAME';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER,         1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER,        FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  // it doesn't like the self signed certs on Cirrus
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);

            $allEmployeesJson = curl_exec($ch);
            $allEmployees = json_decode($allEmployeesJson);
            
            foreach ($allEmployees as $employeeDetails) {
                $_SESSION['vbacEmployees'][] = array(
                    'id'=>trim($employeeDetails->NOTES_ID),
                    'text'=>trim($employeeDetails->NOTES_ID),
                    'role'=>trim($employeeDetails->SQUAD_NAME),
                    'tribe'=>trim($employeeDetails->TRIBE_NAME),
                    'distance'=>'remote'
                );
                !empty($employeeDetails->TRIBE_NAME) ?  $_SESSION['allTribes'][trim($employeeDetails->TRIBE_NAME)] = trim($employeeDetails->TRIBE_NAME)  : null ;
                if(strtolower($employeeDetails->EMAIL_ADDRESS)==strtolower($_SESSION['ssoEmail'])) {
                    $_SESSION['myTribe'] = $employeeDetails->TRIBE_NAME;
                }
            }
        }     
        
        $vbacEmployees = $_SESSION['vbacEmployees'];

        if (isset($_SESSION['myTribe'])) {
            $myTribe = $_SESSION['myTribe'];
        } else {
            $myTribe = NULL;
        }
         // Find business unit for this tribe.     
//         $bestMatchScore = 0;
//         $bestMatch = '';
//         if(!empty($myTribe)){
//             foreach ($_SESSION['allTribes'] as $tribe) {
//                 $matchScore = similar_text($myTribe, $tribe);
//                 if($matchScore > $bestMatchScore){
//                     $bestMatchScore = $matchScore;
//                     $bestMatch = $tribe;
//                 }
//             }
//         } else {
//             throw new Exception("No tribe found for : " . $_SESSION['ssoEmail']);
//         }
        
        // process the employees, flagging as 'local' those in the "myTribe" tribe     

        $tribeEmployees = array();
        foreach ($vbacEmployees as $value) {
//          error_log('notesId:' . $value['id'] . ' tribe:' . $value['tribe'] . " bestmatch:" . $bestMatch . " bu:" . $businessUnit);            $
            if(strtolower(substr(trim($value['id']), -4))=='/ibm'){  // Filter out invalid Notes Ids
                if($value['tribe'] === $myTribe) {
                    $value['distance'] = 'local';
                    $tribeEmployees[] = $value;
                } else {
                    if($_SESSION['isAdmin']){
                        $tribeEmployees[] = $value;
                    }
                }
            }
        }

        return $tribeEmployees;
    }
    
    static function getDetailsforRfsDateSlip($rfsId=null){
       
        $sql = " SELECT RESOURCE_REFERENCE, START_DATE, END_DATE, ORGANISATION, SERVICE, DESCRIPTION ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
        $sql.= " WHERE RFS = '" . db2_escape_string($rfsId) . "' ";
        
        $rs = db2_exec($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
     
        $data = array();
        while (($row=db2_fetch_assoc($rs))==true) {
            $data[$row['RESOURCE_REFERENCE']] = $row;
        }
        
        return !empty($data) ? $data : false;
        
    }

    static function setRequestStatus($resourceRequest=null, $status=null){
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
        $sql.= !empty($status) && !empty($resourceRequest) ? " SET STATUS='" . db2_escape_string(trim($status)) . "' " : null ;
        $sql.= !empty($status) && !empty($resourceRequest) ? " WHERE RESOURCE_REFERENCE=" . db2_escape_string($resourceRequest) . "  " : null;
        
        $rs = db2_exec($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        } else {
            resourceRequestDiaryTable::insertEntry("Status set to " . db2_escape_string(trim($status)), $resourceRequest); 
        }
        return $rs;
    }
}