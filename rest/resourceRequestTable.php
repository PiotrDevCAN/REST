<?php
namespace rest;

use itdq\DbTable;

class resourceRequestTable extends DbTable
{

    function buildHTMLTable($startDate,$endDate){
        ?>
        <table id='resourceRequestsTable_id' class="table table-striped table-bordered" cellspacing="0" width="100%">
		<thead>
   		<?php
    		resourceRequestRecord::htmlHeaderRow();
    	?>
    	</thead>
    	<tbody>
    		<tr><</tr>
    	</tbody>
		</table>
        <?php
    }

    function updateResourceName($resourceReference,$resourceName){
        if(empty($resourceReference) or empty($resourceName)){
            throw new \Exception('Paramaters Missing in call to ' . __FUNCTION__);
        }

        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET RESOURCE_NAME='" . db2_escape_string($resourceName) . "' ";
        $sql .= " WHERE RESOURCE_REFERENCE=" . db2_escape_string($resourceReference);

        $result = $this->execute($sql);

        return $result;
    }

    function updatePlatformTypePrnCode($resourceReference,$platform, $resourceType, $prn, $projectCode){
        if(empty($resourceReference) or empty($platform) or empty($resourceType) or empty($prn) or empty($projectCode)){
            throw new \Exception('Parameters Missing in call to ' . __FUNCTION__);
        }
        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET CURRENT_PLATFORM='" . db2_escape_string($platform) . "', ";
        $sql .= " RESOURCE_TYPE='" . db2_escape_string($resourceType). "' ,";
        $sql .= " DRAWN_DOWN_FOR_PRN='" . db2_escape_string($prn) . "' ,";
        $sql .= " DRAWN_DOWN_FOR_PROJECT_CODE='" . db2_escape_string($projectCode) . "' ";
        $sql .= " WHERE RESOURCE_REFERENCE=" . db2_escape_string($resourceReference);

        $result = $this->execute($sql);

        return $result;
    }




    function returnAsArray($startDate,$endDate, $predicate=null){
        $startDateObj = new \DateTime($startDate);
        $endDateObj = new \DateTime($endDate);

        if(empty($endDate)){
            $endDateObj->modify("+6 months");
        }


        $sql =  " WITH resource_hours as (";
        $sql .= "  SELECT RESOURCE_REFERENCE as RR ";

        while($startDateObj->format('Ym') <= $endDateObj->format('Ym')){
            $columnName =  $startDateObj->format('M_Y');
            $sql .= ",SUM(" . $columnName . ") as " . $columnName;
            $startDateObj->modify('+1 month');
        }

        $sql .= " FROM ( ";
        $sql .= "        SELECT RESOURCE_REFERENCE ";

        $startDateObj = new \DateTime($startDate);

        while($startDateObj->format('Ym') <= $endDateObj->format('Ym')){
            $columnName =  $startDateObj->format('M_Y');
            $sql .= ", CASE WHEN ( claim_month = ". $startDateObj->format('m') . " and claim_year = " . $startDateObj->format('Y') . " ) then hours else null end as " . $columnName ;
            $startDateObj->modify('+1 month');
        }

        $startDateObj = new \DateTime($startDate);

        $sql .=  " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
        $sql .= "   WHERE  ( claim_month >= " . $startDateObj->format('m') . " and claim_year >= " . $startDateObj->format('Y') . ")  " ;
        $sql .= "         OR (claim_year > " . $startDateObj->format('Y') . " and claim_year < " . $endDateObj->format('Y') . " ) " ;
        $sql .= "         OR (claim_month <= " . $endDateObj->format('m') . " and claim_year >= " . $endDateObj->format('Y') . ")  " ;
        $sql .= " ) as hoursData ";
        $sql .= " GROUP BY RESOURCE_REFERENCE ";
        $sql .= " ) ";
        $sql .= " SELECT * ";
        $sql .= " FROM  " . $_SESSION['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql .= " LEFT JOIN  " . $_SESSION['Db2Schema'] . "." . $this->tableName . " as RR ";
        $sql .= " ON RR.RFS = RFS.RFS_ID ";
        $sql .= " left join resource_hours as RH ";
        $sql .= " ON RR.RESOURCE_REFERENCE = RH.RR ";

        $sql .=  " WHERE RR.RFS is not null ";
        $sql .= !empty($predicate) ? " AND $predicate " : null ;

        $sql .= " ORDER BY RFS.RFS_CREATED_TIMESTAMP DESC ";


        echo $sql;

        $resultSet = $this->execute($sql);

        $resultSet ? null : die("SQL Failed");

        $allData = null;

        while(($row = db2_fetch_assoc($resultSet))==true){
            $this->addGlyphicons($row);
            foreach ($row as $key=>$data){
                $row[] = trim($row[$key]);
                unset($row[$key]);
            }
            $allData[]  = $row;
        }
        return $allData ;
    }


    function addGlyphicons(&$row){
        $resourceReference = trim($row['RESOURCE_REFERENCE']);
        $resourceName = trim($row['RESOURCE_NAME']);
        $startDate = trim($row['START_DATE']);
        $resourceType = trim($row['RESOURCE_TYPE']);
        $bwo_parent = trim($row['PARENT_BWO']);
        $description = trim($row['DESCRIPTION']);
        $isBulkWorkOrder = $resourceType==resourceRequestRecord::$bulkWorkOrder;
        $clonedFromBwo = ((!empty($bwo_parent) && !$bwo_parent==0 )) ? true : false;

        $editButtonColor = empty($resourceName) ? 'text-success' : 'text-warning';
        $editButtonColor = substr($resourceName,0,6)=='Dup of' ? 'text-success' : $editButtonColor;
        $editButtonColor = substr($resourceName,0,10)=='Delta from' ? 'text-danger' : $editButtonColor;

        $duplicatable = ((substr($resourceName,0,6)=='Dup of') or (substr($resourceName,0,10)=='Delta from') or ($isBulkWorkOrder) or ($clonedFromBwo)) ? false : true;
        $canAssignPerson = $isBulkWorkOrder ? false : true;

        $timeIcon = $isBulkWorkOrder ? 'glyphicon-download-alt' : 'glyphicon-time';

        $row['DESCRIPTION'] =
        "<button type='button' class='btn btn-default btn-xs deleteRecord' aria-label='Left Align' data-reference='" .$resourceReference . "' data-platform='" .trim($row['CURRENT_PLATFORM']) .  "' data-rfs='" .trim($row['RFS_ID']) . "' data-type='" . $resourceType . "' >
            <span class='glyphicon glyphicon-trash ' aria-hidden='true' title='Delete Resource'></span>
            </button>&nbsp;" . $description;
        $row['RESOURCE_NAME'] =
            "<button type='button' class='btn btn-default btn-xs editRecord' aria-label='Left Align' data-reference='" .$resourceReference . "' data-type='" .$resourceType . "' data-parent='" . $bwo_parent . "' >
            <span class='glyphicon glyphicon-edit ' aria-hidden='true' title='Edit Resource Name'></span>
            </button>";

        $row['RESOURCE_NAME'] .= $canAssignPerson ?
             "<button type='button' class='btn btn-default btn-xs editResource' aria-label='Left Align' data-reference='" .$resourceReference . "' data-type='" .$resourceType . "' data-parent='" . $bwo_parent . "' >
              <span class='glyphicon glyphicon-user $editButtonColor' aria-hidden='true'></span>
              </button>" : null;

        $row['RESOURCE_NAME'] .=
            "<button type='button' class='btn btn-default btn-xs editHours' aria-label='Left Align' data-reference='" . $resourceReference . "'  data-startDate='" . $startDate . "' >
             <span class='glyphicon " . $timeIcon . " text-primary' aria-hidden='true'></span>
             </button>";
        $row['RESOURCE_NAME'] .= trim($row['RESOURCE_TYPE'])==resourceRequestRecord::$bulkWorkOrder ?
            "<button type='button' class='btn btn-xs seekBwo' aria-label='Left Align'
                data-reference='" . $resourceReference . "'                        >
                <span class='glyphicon glyphicon-search text-primary' aria-hidden='true'></span>
            </button>" : null ;
        $row['RESOURCE_NAME'] .= $duplicatable ?
              "<button type='button' class='btn btn-xs requestDuplication' aria-label='Left Align'
                    data-reference='" . $resourceReference . "'
                    data-rfs='" . $row['RFS_ID'] . "'
                    data-type='" . $row['RESOURCE_TYPE'] . "'
                    data-start='" . $row['START_DATE'] . "'
                  >
              <span class='glyphicon glyphicon-duplicate text-primary' aria-hidden='true'></span>
              </button>" : null;
        $row['RESOURCE_NAME'] .= "&nbsp;" . $resourceName ;


        if(trim($row['CURRENT_PLATFORM']) == resourceRequestRecord::$tbd){
            $row['CURRENT_PLATFORM'] =   "<button type='button' class='btn btn-xs setPlatformTypePrnCode' aria-label='Left Align'
                    data-reference='" . $resourceReference . "' data-type='" .$resourceType . "' data-parent='" . $bwo_parent . "' >
              <span class='glyphicon glyphicon-edit text-primary' aria-hidden='true'></span>
              </button>";
        }
        if(trim($row['RESOURCE_TYPE']) == resourceRequestRecord::$tbd){
            $row['RESOURCE_TYPE'] =   "<button type='button' class='btn btn-xs setPlatformTypePrnCode' aria-label='Left Align'
                    data-reference='" . $resourceReference . "' data-type='" .$resourceType . "' data-parent='" . $bwo_parent . "'
                    id='setResourceType" . $resourceReference . "'  >
              <span class='glyphicon glyphicon-edit text-primary' aria-hidden='true'></span>
              </button>";
        }
    }
}