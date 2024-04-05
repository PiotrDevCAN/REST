<?php
namespace rest\traits;

use rest\activeResourceTable;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\rfsNoneActiveRecord;
use rest\rfsTable;

trait rfsNoneActiveTrait
{
    use tableTrait, prepareDatesForQueryTrait, prepareDatesForResultsTrait;
    
    static function buildHTMLTable($tableId = 'noneActive'){

        $headerCells = rfsNoneActiveRecord::htmlHeaderCellsStatic();
        ?>
        <table id='<?=$tableId;?>Table_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
        <thead><tr><?=$headerCells ;?></tr></thead>
        <tbody></tbody>
        <tfoot><tr><?=$headerCells ;?></tr></tfoot>
        </table>
        <?php
    }

    function returnAsArray($predicate=null, $withArchive = false){

        $dates = self::prepareDatesForQuery();
        list(      
            'monthLabels' => $monthLabels,
            'monthDetails' => $monthDetails,
            'startYear' => $startYear,
            'startMonth' => $startMonth,
            'lastYear' => $lastYear,
            'lastMonth' => $lastMonth
        ) = $dates;

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
        $sql.= " SELECT RFS.RFS_ID,RFS.PRN,RFS.PROJECT_TITLE,RFS.PROJECT_CODE,RFS.REQUESTOR_NAME,RFS.REQUESTOR_EMAIL,VS.VALUE_STREAM,RFS.BUSINESS_UNIT ";
        $sql.= " ,RFS.LINK_TO_PGMP, ";
        $sql.= " RFS.RFS_CREATOR,RFS.RFS_CREATED_TIMESTAMP AS RFS_CREATED , ";
        $sql.= " RR.RESOURCE_REFERENCE,ORG.ORGANISATION,RR.SERVICE,RR.DESCRIPTION,RR.START_DATE,RR.END_DATE,RR.TOTAL_HOURS, ";
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
        
        // Value Stream
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_VALUE_STREAM . " as VS ";
        $sql.= " ON RFS.VALUE_STREAM = VS.VALUE_STREAM_ID";
        
        // Organization
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_ORGANISATION . " as ORG ";
        $sql.= " ON RR.ORGANISATION = ORG.ORGANISATION_ID";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE . " as AR ";
        $sql.= " ON LOWER(RR.RESOURCE_NAME) = LOWER(AR.NOTES_ID) ";
        $sql.= " OR RR.RESOURCE_NAME = AR.EMAIL_ADDRESS ";
        $sql.= " , CLAIM ";
        $sql.= " WHERE 1=1 " ;
        $sql.= " AND " . rfsTable::NOT_ARCHIVED;
        $sql.= " AND RR.RESOURCE_REFERENCE = CLAIM.RESOURCE_REFERENCE ";        
        $sql.= " AND (
            RR.RESOURCE_NAME NOT LIKE '" . resourceRequestTable::$duplicate . "%' 
            OR RR.RESOURCE_NAME NOT LIKE '" . resourceRequestTable::$delta . "%'
        )";
        $sql.= " AND RR.STATUS = '" . resourceRequestRecord::STATUS_ASSIGNED . "'";
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

            $rowDatesData = $this->prepareDatesForResults($row);
            list('startDate' => $startDate, 'startDateSortable' => $startDateSortable, 'endDate' => $endDate, 'endDateSortable' => $endDateSortable) = $rowDatesData;
            
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
}