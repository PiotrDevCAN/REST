<?php
namespace rest;

use itdq\DbTable;
use itdq\DateClass;

class rfsTable extends DbTable
{
    protected $rfsMaxEndDate;

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

    static function loadKnownRfsToJs($predicate=null){
        $sql = " SELECT RFS_ID FROM " . $GLOBALS['Db2Schema'] . "." .  allTables::$RFS;

        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        ?><script type="text/javascript">
        var knownRfs = [];
        <?php

        while(($row=db2_fetch_assoc($rs))==true){
            ?>knownRfs.push("<?=trim($row['RFS_ID']);?>");
            <?php
        }
        ?></script><?php

    }

    function returnAsArray($predicate=null, $withArchive=false){
        $sql  = " SELECT RFS.*, RDR.* ";
        $sql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql .= " LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$RFS_DATE_RANGE . " as RDR ";
        $sql .= " ON RFS.RFS_ID = RDR.RFS ";
        $sql .= " WHERE 1=1 " ;
        $sql .= $withArchive ? " AND ARCHIVE is not null " : " AND ARCHIVE is null ";
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
         
            
            foreach ($row as $key=>$data){
                $row[] = trim($row[$key]);
                unset($row[$key]);
            }
            $allData[]  = $row;            
        }
        return array('data'=>$allData,'sql'=>$sql);
    }

    function returnClaimReportAsArray($predicate=null, $withArchive=false){
        
        // The first month we need to show them is the CLAIM month they are currently in. 
        // So start with today, and get the next Claim Cut off - th
        
        $startMonthObj = new \DateTime();
        $thisMonthObj = new \DateTime();
        $thisMonthObj->setDate($thisMonthObj->format('Y'), $thisMonthObj->format('m'), 01);
        $thisMonthsClaimCutoff = DateClass::claimMonth($thisMonthObj->format('d-m-Y'));
      
        $startMonthObj > $thisMonthsClaimCutoff ? $startMonthObj->add(new \DateInterval('P1M')) : null;
        $startYear  = $startMonthObj->format('Y');
        $startMonth = $startMonthObj->format('m');
        
        $lastMonthObj = clone $startMonthObj;
        $sixMonths = new \DateInterval('P6M');
        $lastMonthObj->add($sixMonths);
        $lastYear = $lastMonthObj->format('Y');
        $lastMonth = $lastMonthObj->format('m');
                 
        $nextMonthObj = clone $startMonthObj;
        $oneMonth = new \DateInterval('P1M');
        $monthLabels = array();
        $monthDetails = array();
        
        for ($i = 0; $i < 6; $i++) {
            $monthLabels[] = $nextMonthObj->format('M_y');
            $monthDetails[$i]['year'] = $nextMonthObj->format('Y');
            $monthDetails[$i]['month'] = $nextMonthObj->format('m');
            $nextMonthObj->add($oneMonth);
        }
           
        $sql = "";
        $sql.=" WITH ";
        $sql.= " CLAIM(RESOURCE_REFERENCE ";

        foreach ($monthLabels as $label) {
            $sql.=",  $label ";
        }

        $sql.= " ) AS ( ";
        $sql.=" select RESOURCE_REFERENCE RESOURCE_NAME ";
        
        foreach ($monthLabels as $label) {
            $sql.=", sum($label) as $label ";
        }

        $sql.=" from ( ";
        $sql.="     select RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME ";
        
        foreach ($monthDetails as $key => $detail) {
            $sql.=", case when (CLAIM_YEAR = " . $detail['year'] . " and CLAIM_MONTH = " . $detail['month'] . ") then sum(hours) else null end as " . $monthLabels[$key];
        }
        $sql.="      from " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " as RR ";
        $sql.="      left join " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS . " as RH ";
        $sql.="      on RR.RESOURCE_REFERENCE = RH.RESOURCE_REFERENCE ";
        $sql.=" WHERE 1=1 " ;
        $sql.=" AND ( ";
        $sql.="        ( CLAIM_YEAR = $startYear AND ( CLAIM_MONTH >= $startMonth and CLAIM_MONTH <= " .  ($startMonth+6) . " )) ";
        $sql.="      or " ;
        $sql.="        (CLAIM_YEAR = $lastYear  AND ( CLAIM_MONTH <= $lastMonth  and CLAIM_MONTH >= " . ($lastMonth-6) . "  )) ";
        $sql.="    ) ";
        $sql.="      group by RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME, CLAIM_YEAR, CLAIM_MONTH ";
        $sql.="      ) as data ";
        $sql.="      group by RESOURCE_REFERENCE, RESOURCE_NAME ";
        $sql.="      order by 1,2 ";
        $sql.=" ) ";
        $sql.=" " ;
        $sql.= " SELECT RFS.RFS_ID,RFS.PRN,RFS.Project_Title,RFS.Project_Code,RFS.Requestor_Name,RFS.Requestor_Email,RFS.Value_Stream,RFS.Business_Unit ";
        $sql.= " ,RFS.Link_to_PGMP, ";
        $sql.= " RFS.RFS_Creator,RFS.RFS_Created_timestamp as RFS_CREATED , ";
        $sql.= " RR.Resource_Reference,RR.Organisation,RR.Service,RR.Description,RR.Start_Date,RR.End_Date, ";
        $sql.= " RR.TOTAL_HOURS,RR.Resource_Name,RR.RR_CREATOR as REQUEST_CREATOR,RR.RR_CREATED_TIMESTAMP as Request_Created, ";
        $sql.= " RR.CLONED_FROM, RR.Status, RR.Rate_Type, RR.Hours_Type, RFS.RFS_STATUS,   CLAIM.* ";
        $sql.= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql.= " LEFT JOIN  " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " as RR ";
        $sql.= " ON RR.RFS =  RFS.RFS_ID ";
        $sql.= " , CLAIM ";
        $sql.= " WHERE 1=1 " ;
        $sql.= " AND ARCHIVE is null ";
        $sql.= " AND RR.RESOURCE_REFERENCE = CLAIM.RESOURCE_REFERENCE ";
        $sql.= !empty($predicate) ? " AND  $predicate " : null ;
        
        $resultSet = $this->execute($sql);
        $resultSet ? null : die("SQL Failed");
        $allData = null;
        
        while(($row = db2_fetch_assoc($resultSet))==true){
            $testJson = json_encode($row);
            if(!$testJson){
                break; // It's got invalid chars in it that will be a problem later.
            }     
            
            $startDate = !empty($row['START_DATE']) ? \Datetime::createFromFormat('Y-m-d', $row['START_DATE'])->format('d M Y') : null;
            $startDateSortable = !empty($row['START_DATE']) ? \Datetime::createFromFormat('Y-m-d', $row['START_DATE'])->format('Ymd') : null;
            $endDate         = !empty($row['END_DATE'])     ? \Datetime::createFromFormat('Y-m-d', $row['END_DATE'])->format('d M Y') : null;
            $endDateSortable = !empty($row['END_DATE'])     ? \Datetime::createFromFormat('Y-m-d', $row['END_DATE'])->format('Ymd') : null;
            
            $row['START_DATE'] = array('display'=> $startDate,'sort'=>$startDateSortable);
            $row['END_DATE']   = array('display'=> $endDate, 'sort'=>$endDateSortable);
            
            foreach ($row as $key=>$data){ 
                $row[] = ! is_array($row[$key]) ? trim($row[$key]) : $row[$key];
                unset($row[$key]);
            }
            $allData[]  = $row;
        }
        return array('data'=>$allData,'sql'=>$sql);
    }

    function returnLeftReportAsArray($predicate=null, $withArchive=false){
        
        // The first month we need to show them is the CLAIM month they are currently in. 
        // So start with today, and get the next Claim Cut off - th
        
        $startMonthObj = new \DateTime();
        $thisMonthObj = new \DateTime();
        $thisMonthObj->setDate($thisMonthObj->format('Y'), $thisMonthObj->format('m'), 01);
        $thisMonthsClaimCutoff = DateClass::claimMonth($thisMonthObj->format('d-m-Y'));
      
        $startMonthObj > $thisMonthsClaimCutoff ? $startMonthObj->add(new \DateInterval('P1M')) : null;
        $startYear  = $startMonthObj->format('Y');
        $startMonth = $startMonthObj->format('m');
        
        $lastMonthObj = clone $startMonthObj;
        $sixMonths = new \DateInterval('P6M');
        $lastMonthObj->add($sixMonths);
        $lastYear = $lastMonthObj->format('Y');
        $lastMonth = $lastMonthObj->format('m');
                 
        $nextMonthObj = clone $startMonthObj;
        $oneMonth = new \DateInterval('P1M');
        $monthLabels = array();
        $monthDetails = array();
        
        for ($i = 0; $i < 6; $i++) {
            $monthLabels[] = $nextMonthObj->format('M_y');
            $monthDetails[$i]['year'] = $nextMonthObj->format('Y');
            $monthDetails[$i]['month'] = $nextMonthObj->format('m');
            $nextMonthObj->add($oneMonth);
        }
           
        $sql = "";
        $sql.=" WITH ";
        $sql.= " CLAIM(RESOURCE_REFERENCE ";

        foreach ($monthLabels as $label) {
            $sql.=",  $label ";
        }

        $sql.= " ) AS ( ";
        $sql.=" select RESOURCE_REFERENCE RESOURCE_NAME ";
        
        foreach ($monthLabels as $label) {
            $sql.=", sum($label) as $label ";
        }

        $sql.=" from ( ";
        $sql.="     select RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME ";
        
        foreach ($monthDetails as $key => $detail) {
            $sql.=", case when (CLAIM_YEAR = " . $detail['year'] . " and CLAIM_MONTH = " . $detail['month'] . ") then sum(hours) else null end as " . $monthLabels[$key];
        }
        $sql.="      from " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " as RR ";
        $sql.="      left join " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS . " as RH ";
        $sql.="      on RR.RESOURCE_REFERENCE = RH.RESOURCE_REFERENCE ";
        $sql.=" WHERE 1=1 " ;
        $sql.=" AND ( ";
        $sql.="        ( CLAIM_YEAR = $startYear AND ( CLAIM_MONTH >= $startMonth and CLAIM_MONTH <= " .  ($startMonth+6) . " )) ";
        $sql.="      or " ;
        $sql.="        (CLAIM_YEAR = $lastYear  AND ( CLAIM_MONTH <= $lastMonth  and CLAIM_MONTH >= " . ($lastMonth-6) . "  )) ";
        $sql.="    ) ";
        $sql.="      group by RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME, CLAIM_YEAR, CLAIM_MONTH ";
        $sql.="      ) as data ";
        $sql.="      group by RESOURCE_REFERENCE, RESOURCE_NAME ";
        $sql.="      order by 1,2 ";
        $sql.=" ) ";
        $sql.=" " ;
        $sql.= " SELECT RFS.RFS_ID,RFS.PRN,RFS.Project_Title,RFS.Project_Code,RFS.Requestor_Name,RFS.Requestor_Email,RFS.Value_Stream,RFS.Business_Unit ";
        $sql.= " ,RFS.Link_to_PGMP, ";
        $sql.= " RFS.RFS_Creator,RFS.RFS_Created_timestamp as RFS_CREATED , ";
        $sql.= " RR.Resource_Reference,RR.Organisation,RR.Service,RR.Description,RR.Start_Date,RR.End_Date, ";
        $sql.= " RR.TOTAL_HOURS,RR.Resource_Name,RR.RR_CREATOR as REQUEST_CREATOR,RR.RR_CREATED_TIMESTAMP as Request_Created, ";
        $sql.= " RR.CLONED_FROM, RR.Status, RR.Rate_Type, RR.Hours_Type, RFS.RFS_STATUS,   CLAIM.* ";
        $sql.= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql.= " LEFT JOIN  " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " as RR ";
        $sql.= " ON RR.RFS =  RFS.RFS_ID ";        
        $sql.= " LEFT JOIN  " . $GLOBALS['Db2Schema'] . "." . allTables::$INACTIVE_PERSON . " as IP ";
        $sql.= " ON IP.NOTES_ID =  RR.RESOURCE_NAME ";        
        $sql.= " , CLAIM ";
        $sql.= " WHERE 1=1 " ;
        $sql.= " AND ARCHIVE is null ";
        $sql.= " AND RR.RESOURCE_REFERENCE = CLAIM.RESOURCE_REFERENCE ";        
        $sql.= " AND IP.NOTES_ID IS NOT NULL ";
        $sql.= !empty($predicate) ? " AND  $predicate " : null ;
        
        $resultSet = $this->execute($sql);
        $resultSet ? null : die("SQL Failed");
        $allData = null;
        
        while(($row = db2_fetch_assoc($resultSet))==true){
            $testJson = json_encode($row);
            if(!$testJson){
                break; // It's got invalid chars in it that will be a problem later.
            }     
            
            $startDate = !empty($row['START_DATE']) ? \Datetime::createFromFormat('Y-m-d', $row['START_DATE'])->format('d M Y') : null;
            $startDateSortable = !empty($row['START_DATE']) ? \Datetime::createFromFormat('Y-m-d', $row['START_DATE'])->format('Ymd') : null;
            $endDate         = !empty($row['END_DATE'])     ? \Datetime::createFromFormat('Y-m-d', $row['END_DATE'])->format('d M Y') : null;
            $endDateSortable = !empty($row['END_DATE'])     ? \Datetime::createFromFormat('Y-m-d', $row['END_DATE'])->format('Ymd') : null;
            
            $row['START_DATE'] = array('display'=> $startDate,'sort'=>$startDateSortable);
            $row['END_DATE']   = array('display'=> $endDate, 'sort'=>$endDateSortable);
            
            foreach ($row as $key=>$data){ 
                $row[] = ! is_array($row[$key]) ? trim($row[$key]) : $row[$key];
                unset($row[$key]);
            }
            $allData[]  = $row;
        }
        return array('data'=>$allData,'sql'=>$sql);
    }

    function addGlyphicons(&$row){
        $rfsId = trim($row['RFS_ID']);
        $today = new \DateTime();
        $rfsEndDate = $this->rfsMaxEndDate($rfsId);
        $archiveable = false;
        if($rfsEndDate){
            $archiveable = $rfsEndDate < $today ? true : false;
        }
        
        $pipelineRfs  =  trim($row['RFS_STATUS'])==rfsRecord::RFS_STATUS_PIPELINE  ?  true : false;

        if($archiveable) {
            $row['RFS_ID'] = "<button type='button' class='btn btn-warning btn-xs archiveRfs accessRestrict accessAdmin accessDemand accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "' >
              <span class='glyphicon glyphicon-floppy-remove' aria-hidden='true' data-html='true' data-html='true' data-toggle='tooltip' title='Archive RFS Safer than deleting' ></span>
              </button>";
        } else {
            $row['RFS_ID'] = ""; /// NEed something so next statement can be an append.
        }
        
        $row['RFS_ID'] .= $pipelineRfs  ? "<button type='button' class='btn btn-success btn-xs goLiveRfs accessRestrict accessAdmin accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "' >
              <span class='glyphicon glyphicon-thumbs-up' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Release to Live' ></span>
              </button>&nbsp;" : null;    
        $row['RFS_ID'] .="<button disabled  type='button' class='btn btn-success btn-xs slipRfs accessRestrict accessAdmin accessDemand accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-calendar' aria-hidden='true'  data-toggle='tooltip' title='This function has been depricated' ></span></button>";        
        $row['RFS_ID'] .="<button type='button' class='btn btn-success btn-xs editRfs accessRestrict accessAdmin accessDemand accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>              
              <span class='glyphicon glyphicon-edit' aria-hidden='true'  data-toggle='tooltip' title='Edit RFS' ></span>
              </button>"  . "&nbsp;" .  "<button type='button' class='btn btn-danger btn-xs deleteRfs accessRestrict accessAdmin accessDemand accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-trash' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Delete RFS Can not be recovered' ></span>
              </button>" . "&nbsp;";

        $row['RFS_ID'] .= $rfsId;
        $linkToPgmp = trim($row['LINK_TO_PGMP']);
        $row['LINK_TO_PGMP'] = empty($linkToPgmp) ? null : "<a href='$linkToPgmp' target='_blank' >$linkToPgmp</a>";
    }

    function  rfsMaxEndDate($rfsid){
        if(empty($this->rfsMaxEndDate)){
            // We've not populated the array of RFS & END_DATES, so do that now.
            $sql = " SELECT RFS, MAX(END_DATE) as END_DATE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS ;
            $sql .= " GROUP BY RFS ";

            $rs = db2_exec($GLOBALS['conn'], $sql);

            if(!$rs) {
                DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            }

            while (($row=db2_fetch_assoc($rs))==true) {
                $this->rfsMaxEndDate[strtoupper(trim($row['RFS']))] = isset($row['END_DATE']) ? trim($row['END_DATE']) : null ;
            }
        }
        return isset($this->rfsMaxEndDate[strtoupper(trim($rfsid))]) ? new \DateTime($this->rfsMaxEndDate[strtoupper(trim($rfsid))]) : false;

    }

    function  archiveRfs($rfsid){
        if(empty($rfsid)){
            return false;
        }

        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS;
        $sql .= " SET ARCHIVE = CURRENT TIMESTAMP ";
        $sql .= " WHERE RFS_ID ='" . db2_escape_string($rfsid) . "' " ;

        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        return true;
    }
    
    
    static function getRequestorEmail($rfsId){
        $sql = " SELECT REQUESTOR_EMAIL ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . \rest\allTables::$RFS;
        $sql.= " WHERE RFS_ID='" . db2_escape_string($rfsId) . "' ";
        $rs = db2_exec($GLOBALS['conn'], $sql);
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);            
        }
        
        $row = db2_fetch_assoc($rs);
        return trim($row['REQUESTOR_EMAIL']);
    }




}