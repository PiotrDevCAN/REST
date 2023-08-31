<?php
use rest\allTables;
use rest\activeResourceRecord;
use rest\activeResourceTable;

set_time_limit(0);

$url = $_ENV['vbac_url'] . '/api/squadTribePlus.php?token=' . $_ENV['vbac_api_token'] . '&withProvClear=true&plus=P.EMAIL_ADDRESS,P.PES_STATUS,SQUAD_NAME,TRIBE_NAME';

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => 1, 
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {

    $activeResourceTable  = new activeResourceTable(allTables::$ACTIVE_RESOURCE);
    $activeResourceRecord = new activeResourceRecord();

    $clear = isset($_GET['clear']) ? $_GET['clear'] : false;
    if ($clear) {
        $activeResourceTable->clear();
    }

    $responseObj = json_decode($response);
    $loadCounter = 0;

    $load = isset($_GET['load']) ? $_GET['load'] : false;
    if ($load) {

        $loaded = new DateTime();
        echo "<BR/>Database Load Started : " . $loaded->format('Y-m-d H:i:s');

        $success = true;

        $autoCommit = sqlsrv_commit($GLOBALS['conn']);
        sqlsrv_commit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);   
        
        if (count($responseObj) > 0) {
            
            $sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE . " ( EMAIL_ADDRESS, NOTES_ID, PES_STATUS )  Values ";
                
            foreach ($responseObj as $key => $personEntry) {

                // -------------------------------------------------------------------

                // $activeResourceRecord->setFromArray($personEntry);
                // $db2result = $activeResourceTable->insert($activeResourceRecord);

                // if(!$db2result){
                //     $success = false;

                //     echo print_r(sqlsrv_errors());
                //     echo print_r(sqlsrv_errors());
                // } else {
                //     $loadCounter++;
                // } 

                // -------------------------------------------------------------------

                // $sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE . " ( EMAIL_ADDRESS, NOTES_ID, PES_STATUS ) ";
                // $sql .= " Values ('" . htmlspecialchars(trim($personEntry->EMAIL_ADDRESS)) . "','" . htmlspecialchars($personEntry->NOTES_ID) . "','" . htmlspecialchars($personEntry->PES_STATUS) . "' ) ";
                
                // $rs = DB2_EXEC ( $GLOBALS['conn'], $sql );
                // if (! $rs) {
                //     $success = false;
                // } else {
                //     $loadCounter++;
                // }
                
                // Database Load Started : 2021-05-19 08:52:37
                // Database Load Finished : 2021-05-19 08:59:06

                // -------------------------------------------------------------------
                
                if ($key > 0) {
                    $sql .= " ,";    
                }
                $sql .= " ('" . htmlspecialchars(trim($personEntry->EMAIL_ADDRESS)) . "','" . htmlspecialchars($personEntry->NOTES_ID) . "','" . htmlspecialchars($personEntry->PES_STATUS) . "' ) ";
                
                $loadCounter++;
                
                // -------------------------------------------------------------------

            }

            $rs = DB2_EXEC ( $GLOBALS['conn'], $sql );
            if (! $rs) {
                $success = false;
            } else {
                // $loadCounter++;
            }
        }

        if($success){
            sqlsrv_commit($GLOBALS['conn']);
        } else {
            sqlsrv_rollback($GLOBALS['conn']);
        }

        sqlsrv_commit($GLOBALS['conn'],$autoCommit);

        $loaded = new DateTime();
        echo "<BR/>Database Load Finished : " . $loaded->format('Y-m-d H:i:s');
    }

    echo '<BR/><B>' . count($responseObj) . ' records read from VBAC api</B>';
    echo '<BR/><B>' . $loadCounter . ' records loaded to REST db</B>';
}