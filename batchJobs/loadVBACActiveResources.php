<?php
use rest\allTables;
use rest\activeResourceRecord;
use rest\activeResourceTable;
use rest\rfsRecord;
use rest\rfsTable;

set_time_limit(0);

$_ENV['vbac_url'] = 'https://vbac.dal1a.cirrus.ibm.com';
$url = $_ENV['vbac_url'] . '/api/squadTribePlus.php?token=' . $_ENV['vbac_api_token'] . '&onlyactive=false&withProvClear=true&plus=P.EMAIL_ADDRESS,P.CNUM,P.PES_STATUS,SQUAD_NAME,TRIBE_NAME,P.WORK_STREAM,P.CIO_ALIGNMENT';

// $GLOBALS['Db2Schema'] = 'REST_DEV';
// $GLOBALS['Db2Schema'] = 'REST_UT';
// $GLOBALS['Db2Schema'] = 'REST';

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    // CURLOPT_HEADER => 1,
    CURLOPT_HEADER => FALSE,
    CURLOPT_RETURNTRANSFER => 1, 
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_FAILONERROR => true,
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); //check if 504 return.

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {

    $activeResourceTable  = new activeResourceTable(allTables::$ACTIVE_RESOURCE);
    $activeResourceRecord = new activeResourceRecord();

    $activeResourceTable->clear(false);

    /*
    $responseObj = json_decode($response);
    var_dump(count($responseObj));
    if (count($responseObj) > 0) {
        foreach ($responseObj as $personEntry) {
            $activeResourceRecord->setFromArray($personEntry);
            $db2result = $activeResourceTable->insert($activeResourceRecord);
    
            if(!$db2result){
                echo db2_stmt_error();
                echo db2_stmt_errormsg();
            }
        }
    }
    */

    $success = true;

    $autoCommit = sqlsrv_commit($GLOBALS['conn']);
    sqlsrv_commit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);   
    
    $responseObj = json_decode($response);
    if (count($responseObj) > 0) {
        
        $sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE . " ( CNUM, EMAIL_ADDRESS, NOTES_ID, PES_STATUS, WORK_STREAM, CIO_ALIGNMENT, STATUS, TRIBE_NAME, SQUAD_NAME, TRIBE_NAME_MAPPED )  Values ";
        
        foreach ($responseObj as $key => $activeResourceEntry) {
            if ($key > 0) {
                $sql .= " ,";    
            }

            $mappedTribeName = array_key_exists($activeResourceEntry->TRIBE_NAME, rfsRecord::$tribeNameMapping) ? rfsRecord::$tribeNameMapping[$activeResourceEntry->TRIBE_NAME] : $activeResourceEntry->TRIBE_NAME;

            $sql .= " ('" . 
                htmlspecialchars(trim($activeResourceEntry->CNUM)) . "','" . 
                htmlspecialchars(trim($activeResourceEntry->EMAIL_ADDRESS)) . "','" . 
                htmlspecialchars($activeResourceEntry->NOTES_ID) . "','" . 
                htmlspecialchars($activeResourceEntry->PES_STATUS) . "','" . 
                htmlspecialchars($activeResourceEntry->WORK_STREAM) . "','" . 
                htmlspecialchars($activeResourceEntry->CIO_ALIGNMENT) . "','" . 
                htmlspecialchars($activeResourceEntry->INT_STATUS) . "','" . 
                htmlspecialchars($activeResourceEntry->TRIBE_NAME) . "','" . 
                htmlspecialchars($activeResourceEntry->SQUAD_NAME) . "','" . 
                htmlspecialchars($mappedTribeName) . 
            "' ) ";
        }

        echo $sql;
        exit; 

        $rs = DB2_EXEC ( $GLOBALS['conn'], $sql );
        if (! $rs) {
            $success = false;
        }
    }
    
    if($success){
        db2_commit($GLOBALS['conn']);
    } else {
        db2_rollback($GLOBALS['conn']);
    }

    sqlsrv_commit($GLOBALS['conn'],$autoCommit);

    echo count($responseObj) . ' records read from VBAC api';
}