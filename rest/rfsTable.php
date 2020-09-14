<?php
namespace rest;

use itdq\DbTable;

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
        ?>console.log(knownRfs);<?php
        ?></script><?php

    }

    function returnAsArray($predicate=null, $withArchive=false){
        $sql  = " SELECT * ";
        $sql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql .= " WHERE 1=1 " ;
        $sql .= $withArchive ? " AND ARCHIVE is not null " : " AND ARCHIVE is null ";
        $sql .= !empty($predicate) ? " AND  $predicate " : null ;

        $resultSet = $this->execute($sql);
        $resultSet ? null : die("SQL Failed");
        $allData = null;

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
        $sql = "";
        $sql.=" WITH ";
        $sql.= " CLAIM(RESOURCE_REFERENCE, JAN_20,FEB_20,MAR_20,APR_20,MAY_20,JUN_20,JUL_20,AUG_20,SEP_20,OCT_20,NOV_20,DEC_20,";
        $sql.= "                           JAN_21,FEB_21,MAR_21,APR_21,MAY_21,JUN_21,JUL_21,AUG_21,SEP_21,OCT_21,NOV_21,DEC_21";
        $sql.= " ) AS ( ";
        $sql.=" select RESOURCE_REFERENCE RESOURCE_NAME, ";
        $sql.=" sum(JAN_20) as JAN_20, ";
        $sql.=" sum(FEB_20) as FEB_20, ";
        $sql.=" sum(MAR_20) as MAR_20, ";
        $sql.=" sum(APR_20) as APR_20, ";
        $sql.=" sum(MAY_20) as MAY_20, ";
        $sql.=" sum(JUN_20) as JUN_20, ";
        $sql.=" sum(JUL_20) as JUL_20, ";
        $sql.=" sum(AUG_20) as AUG_20, ";
        $sql.=" sum(SEP_20) as SEP_20, ";
        $sql.=" sum(OCT_20) as OCT_20, ";
        $sql.=" sum(NOV_20) as NOV_20, ";
        $sql.=" sum(DEC_20) as DEC_20, "; 
        $sql.=" sum(JAN_21) as JAN_21, ";
        $sql.=" sum(FEB_21) as FEB_21, ";
        $sql.=" sum(MAR_21) as MAR_21, ";
        $sql.=" sum(APR_21) as APR_21, ";
        $sql.=" sum(MAY_21) as MAY_21, "; 
        $sql.=" sum(JUN_21) as JUN_21, ";
        $sql.=" sum(JUL_21) as JUL_21, ";
        $sql.=" sum(AUG_21) as AUG_21, ";
        $sql.=" sum(SEP_21) as SEP_21, ";
        $sql.=" sum(OCT_21) as OCT_21, ";
        $sql.=" sum(NOV_21) as NOV_21, ";
        $sql.=" sum(DEC_21) as DEC_21 ";
        $sql.=" from ( ";
        $sql.="     select RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME, ";
        $sql.=" case when (CLAIM_YEAR = 2020 and CLAIM_MONTH = 1) then sum(hours) else null end as JAN_20, ";
        $sql.=" case when (CLAIM_YEAR = 2020 and CLAIM_MONTH = 2) then sum(hours) else null end as FEB_20, ";
        $sql.=" case when (CLAIM_YEAR = 2020 and CLAIM_MONTH = 3) then sum(hours) else null end as MAR_20, ";
        $sql.=" case when (CLAIM_YEAR = 2020 and CLAIM_MONTH = 4) then sum(hours) else null end as APR_20, ";
        $sql.=" case when (CLAIM_YEAR = 2020 and CLAIM_MONTH = 5) then sum(hours) else null end as MAY_20, ";
        $sql.=" case when (CLAIM_YEAR = 2020 and CLAIM_MONTH = 6) then sum(hours) else null end as JUN_20, ";
        $sql.=" case when (CLAIM_YEAR = 2020 and CLAIM_MONTH = 7) then sum(hours) else null end as JUL_20, ";
        $sql.=" case when (CLAIM_YEAR = 2020 and CLAIM_MONTH = 8) then sum(hours) else null end as AUG_20, ";
        $sql.=" case when (CLAIM_YEAR = 2020 and CLAIM_MONTH = 9) then sum(hours) else null end as SEP_20, ";
        $sql.=" case when (CLAIM_YEAR = 2020 and CLAIM_MONTH = 10) then sum(hours) else null end as OCT_20, ";
        $sql.=" case when (CLAIM_YEAR = 2020 and CLAIM_MONTH = 11) then sum(hours) else null end as NOV_20, ";
        $sql.=" case when (CLAIM_YEAR = 2020 and CLAIM_MONTH = 12) then sum(hours) else null end as DEC_20, ";
        $sql.=" case when (CLAIM_YEAR = 2021 and CLAIM_MONTH = 1) then sum(hours) else null end as JAN_21, ";
        $sql.=" case when (CLAIM_YEAR = 2021 and CLAIM_MONTH = 2) then sum(hours) else null end as FEB_21, ";
        $sql.=" case when (CLAIM_YEAR = 2021 and CLAIM_MONTH = 3) then sum(hours) else null end as MAR_21, ";
        $sql.=" case when (CLAIM_YEAR = 2021 and CLAIM_MONTH = 4) then sum(hours) else null end as APR_21, ";
        $sql.=" case when (CLAIM_YEAR = 2021 and CLAIM_MONTH = 5) then sum(hours) else null end as MAY_21, ";
        $sql.=" case when (CLAIM_YEAR = 2021 and CLAIM_MONTH = 6) then sum(hours) else null end as JUN_21, ";
        $sql.=" case when (CLAIM_YEAR = 2021 and CLAIM_MONTH = 7) then sum(hours) else null end as JUL_21, ";
        $sql.=" case when (CLAIM_YEAR = 2021 and CLAIM_MONTH = 8) then sum(hours) else null end as AUG_21, ";
        $sql.=" case when (CLAIM_YEAR = 2021 and CLAIM_MONTH = 9) then sum(hours) else null end as SEP_21, ";
        $sql.=" case when (CLAIM_YEAR = 2021 and CLAIM_MONTH = 10) then sum(hours) else null end as OCT_21, ";
        $sql.=" case when (CLAIM_YEAR = 2021 and CLAIM_MONTH = 11) then sum(hours) else null end as NOV_21, ";
        $sql.=" case when (CLAIM_YEAR = 2021 and CLAIM_MONTH = 12) then sum(hours) else null end as DEC_21 ";
        $sql.="      from REST_UT.RESOURCE_REQUESTS as RR ";
        $sql.="      left join REST_UT.RESOURCE_REQUEST_HOURS as RH ";
        $sql.="      on RR.RESOURCE_REFERENCE = RH.RESOURCE_REFERENCE ";
        $sql.=" WHERE 1=1 " ;
        $sql.=" AND CLAIM_YEAR in (2020,2021) ";
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
        $sql.= " RR.Hrs_Per_Week,RR.Resource_Name,RR.RR_CREATOR as REQUEST_CREATOR,RR.RR_CREATED_TIMESTAMP as Request_Created, ";
        $sql.= " RR.CLONED_FROM, RR.Status, CLAIM.* ";
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
            foreach ($row as $key=>$data){
                $row[] = trim($row[$key]);
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

        if($archiveable) {
            $row['RFS_ID'] = "<button type='button' class='btn btn-warning btn-xs archiveRfs accessRestrict accessAdmin accessDemand accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-floppy-remove' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Archive RFS<br/><em>Safer than deleting</em>' ></span>
              </button>";
        } else {
            $row['RFS_ID'] = ""; /// NEed something so next statement can be an append.
        }        $row['RFS_ID'] .="<button type='button' class='btn btn-success btn-xs slipRfs accessRestrict accessAdmin accessDemand accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-calendar' aria-hidden='true'  data-toggle='tooltip' title='Slip RFS' ></span></button>        
              <button type='button' class='btn btn-success btn-xs editRfs accessRestrict accessAdmin accessDemand accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>              
              <span class='glyphicon glyphicon-edit' aria-hidden='true'  data-toggle='tooltip' title='Edit RFS' ></span>
              </button>"  . "&nbsp;" .  "<button type='button' class='btn btn-danger btn-xs deleteRfs accessRestrict accessAdmin accessDemand accessCdi accessRfs' aria-label='Left Align' data-rfsid='" .$rfsId . "'>
              <span class='glyphicon glyphicon-trash' aria-hidden='true' data-html='true' data-toggle='tooltip' title='Delete RFS<br/><em>Can not be recovered</em>' ></span>
              </button>" . "&nbsp;" . $rfsId;
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




}