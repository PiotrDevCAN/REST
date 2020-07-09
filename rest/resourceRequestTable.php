<?php
namespace rest;

use itdq\DbTable;
use itdq\PhpMemoryTrace;
use \DateTime;

class resourceRequestTable extends DbTable
{
    const DUPLICATE = 'Dup of';
    const DELTA     = 'Delta from';

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

    function updateResourceName($resourceReference,$resourceName, $clear=null){

        $status = resourceRequestRecord::STATUS_ASSIGNED;
        if(!empty($clear)){
            $resourceName = '';
            $status=resourceRequestRecord::STATUS_NEW;
        } else if(empty($resourceReference) or empty($resourceName)){
                throw new \Exception('Paramaters Missing in call to ' . __FUNCTION__);
        }
        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET RESOURCE_NAME='" . db2_escape_string($resourceName) . "' ";
        $sql .= " , STATUS='" . $status . "' ";
        $sql .= " WHERE RESOURCE_REFERENCE=" . db2_escape_string($resourceReference);

        $result = $this->execute($sql);

        return $result;
    }

    function returnAsArray($startDate,$endDate, $predicate=null, $withArchive = false){
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

        $sql .=  " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
        $sql .= "   WHERE  ( claim_month >= " . $startDateObj->format('m') . " and claim_year = " . $startDateObj->format('Y') . ")  " ;
        $sql .= $startDateObj->format('Y') !==  $endDateObj->format('Y') ?  "    AND (claim_year > " . $startDateObj->format('Y') . " and claim_year < " . $endDateObj->format('Y') . " ) " : null;
        $sql .= "         AND (claim_month <= " . $endDateObj->format('m') . " and claim_year = " . $endDateObj->format('Y') . ")  " ;
        $sql .= " ) as resource_hours ";
        $sql .= " GROUP BY RESOURCE_REFERENCE ";
        $sql .= " ) ";
        $sql .= " SELECT * ";
        $sql .= " FROM  " . $_SESSION['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql .= " LEFT JOIN  " . $_SESSION['Db2Schema'] . "." . $this->tableName . " as RR ";
        $sql .= " ON RR.RFS = RFS.RFS_ID ";
        $sql .= " left join resource_hours as RH ";
        $sql .= " ON RR.RESOURCE_REFERENCE = RH.RR ";

        $sql .=  " WHERE RR.RFS is not null ";
        $sql .= $withArchive ? " AND ARCHIVE is not null " : " AND ARCHIVE is null ";
        $sql .= !empty($predicate) ? " AND $predicate " : null ;

        $sql .= " ORDER BY RFS.RFS_CREATED_TIMESTAMP DESC ";

        error_log($sql);

        $resultSet = $this->execute($sql);

        $resultSet ? null : die("SQL Failed");

        $allData = array();
        $allData['data'] = array();

        while(($row = db2_fetch_assoc($resultSet))==true){
            PhpMemoryTrace::reportPeek(__FILE__,__LINE__);
            $testJson = json_encode($row);
            if(!$testJson){
                break; // It's got invalid chars in it that will be a problem later.
            }
            $row = array_map('trim',$row);
            $this->addGlyphicons($row);
            $allData['data'][]  = $row;
        }
        $allData['sql'] = $sql;




        return $allData ;
    }


    function addGlyphicons(&$row){
        PhpMemoryTrace::reportPeek(__FILE__,__LINE__);
        $rfsId = $row['RFS_ID'];
        $resourceReference = $row['RESOURCE_REFERENCE'];
        $resourceName = $row['RESOURCE_NAME'];
        $phase = $row['PHASE'];
        $prn = $row['PRN'];
        $cio = $row['CIO'];
        $startDate4Picka = !empty($row['START_DATE']) ? Datetime::createFromFormat('Y-m-d', $row['START_DATE'])->format('Y-m-d') : null;
        $endDate4Picka = !empty($row['END_DATE'])     ? Datetime::createFromFormat('Y-m-d', $row['END_DATE'])->format('Y-m-d') : null;
        $startDate = !empty($row['START_DATE']) ? Datetime::createFromFormat('Y-m-d', $row['START_DATE'])->format('d M Y') : null;
        $endDate   = !empty($row['END_DATE'])     ? Datetime::createFromFormat('Y-m-d', $row['END_DATE'])->format('d M Y') : null;
        $description = $row['DESCRIPTION'];
        $hrsPerWeek = $row['HRS_PER_WEEK'];
        $status = !empty($row['STATUS']) ? $row['STATUS'] : resourceRequestRecord::STATUS_NEW;
        $organisation = $row['ORGANISATION'];
        $service = $row['SERVICE'];
        $row['STATUS'] =
        "<button type='button' class='btn btn-xs changeStatus accessRestrict accessAdmin accessCdi accessSupply ' aria-label='Left Align'
                    data-rfs='" .$rfsId . "'
                    data-resourcereference='" .$resourceReference . "'
                    data-prn='" .$prn . "'
                    data-cio='" .$cio . "'
                    data-phase='" . $phase. "'
                    data-status='" . $status . "'
                    data-organisation='" .$organisation .  "'
                    data-service='" . $service . "'
                    data-resourcename='" . $resourceName . "'
                    data-start='" . $startDate4Picka . "'
                    data-end='" . $endDate4Picka . "'


         >
         <span data-toggle='tooltip' title='Change Status' class='glyphicon glyphicon-tags ' aria-hidden='true' ></span>
            </button>&nbsp;" . $status;



        $row['DESCRIPTION'] =
        "<button type='button' class='btn btn-default btn-xs deleteRecord accessRestrict accessAdmin accessCdi ' aria-label='Left Align' data-reference='" .$resourceReference . "' data-platform='" .trim($row['ORGANISATION']) .  "' data-rfs='" .trim($row['RFS_ID']) . "' data-type='" . $service . "' >
            <span data-toggle='tooltip' title='Delete Resource' class='glyphicon glyphicon-trash ' aria-hidden='true' ></span>
            </button>&nbsp;" . $description;




        $editButtonColor = empty($resourceName) ? 'text-success' : 'text-warning';
        $editButtonColor = substr($resourceName,0,strlen(resourceRequestTable::DUPLICATE))==resourceRequestTable::DUPLICATE ? 'text-success' : $editButtonColor;
        $editButtonColor = substr($resourceName,0,strlen(resourceRequestTable::DELTA))==resourceRequestTable::DELTA ? 'text-success' : $editButtonColor;

        $displayedResourceName = $editButtonColor == 'text-success' ? "<i>$resourceName</i>" : $resourceName;

        $duplicatable = true; //Can clone any record.

        $canBeAmendedByDemandTeam = empty(trim($resourceName)) ? 'accessDemand' : null; // Demand can amend any Request that is yet to have resource allocated to it.

        $row['RESOURCE_NAME'] = "<span class='dataOwner' ";
        $row['RESOURCE_NAME'].= "  data-rfs='" .$rfsId . "' ";
        $row['RESOURCE_NAME'].= "  data-resourcereference='" .$resourceReference . "' ";
        $row['RESOURCE_NAME'].= "  data-prn='" .$prn . "' ";
        $row['RESOURCE_NAME'].= "  data-cio='" . $cio. "' ";
        $row['RESOURCE_NAME'].= "  data-phase='" . $phase. "' ";
        $row['RESOURCE_NAME'].= "  data-status='" . $status . "' ";
        $row['RESOURCE_NAME'].= "  data-service='" .$service .  "' ";
        $row['RESOURCE_NAME'].= "  data-subservice='" . $service . "' ";
        $row['RESOURCE_NAME'].= "  data-resourcename='" . $resourceName . "' ";
        $row['RESOURCE_NAME'].= "  data-start='" . $startDate . "' ";
        $row['RESOURCE_NAME'].= "  data-end='" . $endDate . "' ";
        $row['RESOURCE_NAME'].= "  data-hrs='" . $hrsPerWeek . "' ";
        $row['RESOURCE_NAME'].= "  >";

        $row['RESOURCE_NAME'].=
            "<button type='button' class='btn btn-xs editRecord accessRestrict accessAdmin accessCdi $canBeAmendedByDemandTeam' aria-label='Left Align' data-reference='" .$resourceReference . "' data-type='" .$service . "' >
            <span class='glyphicon glyphicon-edit ' aria-hidden='true' title='Edit Resource Name'></span>
            </button>";
        $row['RESOURCE_NAME'].=
             "<button type='button' class='btn btn-xs editResource accessRestrict accessAdmin accessCdi accessSupply' aria-label='Left Align' data-reference='" .$resourceReference . "' data-type='" .$service . "' data-resource-name='" . $resourceName . "' >
              <span class='glyphicon glyphicon-user $editButtonColor' aria-hidden='true'></span>
              </button>";
        $row['RESOURCE_NAME'] .=
            "<button type='button' class='btn btn-xs editHours accessRestrict accessAdmin accessCdi accessSupply $canBeAmendedByDemandTeam ' aria-label='Left Align' data-reference='" . $resourceReference . "'  data-startDate='" . $startDate . "' >
             <span class=' glyphicon glyphicon-time text-primary' aria-hidden='true'></span>
             </button>";
        $row['RESOURCE_NAME'] .= $duplicatable ?
              "<button type='button' class='btn btn-xs requestDuplication accessRestrict accessAdmin accessCdi accessSupply $canBeAmendedByDemandTeam' aria-label='Left Align'
                    data-reference='" . $resourceReference . "'
                    data-rfs='" . $row['RFS_ID'] . "'
                    data-type='" . $row['SERVICE'] . "'
                    data-start='" . $row['START_DATE'] . "'
                  >
              <span class='glyphicon glyphicon-duplicate text-primary' aria-hidden='true'></span>
              </button>" : null;
        $displayedResourceName = empty(trim($resourceName)) ? "<i>Unallocated</i>" : $displayedResourceName;

        $row['RESOURCE_NAME'].= "&nbsp;" . $displayedResourceName ;
        $row['RESOURCE_NAME']." </span>";


        $displayRfsId =  $rfsId . " : " . $row['RESOURCE_REFERENCE'];
        $displayRfsId.= $row['CLONED_FROM']> 0 ? "&nbsp;<i>(" . $row['CLONED_FROM'] . ")</i>" : null;

        $row['RFS']        = array('display'=> $displayRfsId, 'sort'=>$rfsId);
        $row['START_DATE'] = array('display'=> $startDate . " to " . $endDate . "<br/>Avg Hrs/Week:" . $row['HRS_PER_WEEK'], 'sort'=>$startDate);
        $row['ORGANISATION']=array('display'=>$row['ORGANISATION'] . "<br/><small>" . $row['SERVICE'] . "</small>", 'sort'=>$organisation);

    }


    static function setEndDate($resourceReference, $endDate){
        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . \rest\allTables::$RESOURCE_REQUESTS;
        $sql .= "  SET END_DATE = DATE('" . db2_escape_string($endDate) ."') ";
        $sql .= " WHERE RESOURCE_REFERENCE=" . db2_escape_string($resourceReference) ." ";

        echo $sql;


        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        return true;
    }


    static function getVbacActiveResourcesForSelect2(){

        if(isset($_SESSION['vbacEmployees'])){
            return $_SESSION['vbacEmployees'];
        } else {
             $vbacEmployees = array();
             $url = $_ENV['vbac_url'] . '/api/squadTribePlus.php?token=soEkCfj8zGNDLZ8yXH2YJjpehd8ijzlS&plus=P.ROLE_ON_THE_ACCOUNT,P.EMAIL_ADDRESS';

             $ch = curl_init();
             curl_setopt($ch, CURLOPT_HEADER,         1);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
             curl_setopt($ch, CURLOPT_HEADER,        FALSE);
             curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  // it doesn't like the self signed certs on Cirrus
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
             curl_setopt($ch, CURLOPT_URL, $url);

             $allEmployeesJson = curl_exec($ch);
             $allEmployees = json_decode($allEmployeesJson);

             $tribeMembers = array();
             $vbacEmployees = array();

             foreach ($allEmployees as $employeeDetails) {
                 $vbacEmployees[] = array('id'=>trim($employeeDetails->NOTES_ID), 'text'=>trim($employeeDetails->NOTES_ID), 'role'=>trim($employeeDetails->ROLE_ON_THE_ACCOUNT),'tribe'=>trim($employeeDetails->TRIBE_NUMBER),'distance'=>'remote');
//                  $tribeMembers[trim($employeeDetails->EMAIL_ADDRESS)] = trim($employeeDetails->TRIBE_NUMBER);
             }

             $localTribe = $tribeMembers[$_SESSION['ssoEmail']];

             foreach ($vbacEmployees as $key=>$value) {
                 if($value['tribe']==$localTribe){
                     $vbacEmployees[$key]['distance']='local';
                 }
             }

             $_SESSION['vbacEmployees'] = $vbacEmployees;
//              $_SESSION['tribeMembers'] = $tribeMembers;
        }
        return $_SESSION['vbacEmployees'];
    }



}