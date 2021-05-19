<?php
use rest\allTables;
use rest\inactivePersonRecord;
use rest\inactivePersonTable;

set_time_limit(0);

$url = $_ENV['vbac_url'] . '/api/employeesLeft.php?token=' . $_ENV['vbac_api_token'];

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

    $inactivePersonTable  = new inactivePersonTable(allTables::$INACTIVE_PERSON);
    $inactivePersonRecord = new inactivePersonRecord();

    $clear = isset($_GET['clear']) ? $_GET['clear'] : false;
    if ($clear) {
        $inactivePersonTable->clear();
    }

    $responseObj = json_decode($response);
    $loadCounter = 0;

    $load = isset($_GET['load']) ? $_GET['load'] : false;
    if ($load) {

        $loaded = new DateTime();
        echo "<BR/>Database Load Started : " . $loaded->format('Y-m-d H:i:s');

        $success = true;

        $autoCommit = db2_autocommit($GLOBALS['conn']);
        db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);   
        
        if (count($responseObj) > 0) {
            
            $sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$INACTIVE_PERSON . " ( EMAIL_ADDRESS, NOTES_ID, PES_STATUS )  Values ";
                
            foreach ($responseObj as $key => $personEntry) {
                // -------------------------------------------------------------------

                // $inactivePersonRecord->setFromArray($personEntry);
                // $db2result = $inactivePersonTable->insert($inactivePersonRecord);

                // if(!$db2result){
                //     $success = false;

                //     echo db2_stmt_error();
                //     echo db2_stmt_errormsg();
                // } else {
                //     $loadCounter++;
                // } 

                // -------------------------------------------------------------------

                // $sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$INACTIVE_PERSON . " ( EMAIL_ADDRESS, NOTES_ID, PES_STATUS ) ";
                // $sql .= " Values ('" . db2_escape_string(trim($personEntry->EMAIL_ADDRESS)) . "','" . db2_escape_string($personEntry->NOTES_ID) . "','" . db2_escape_string($personEntry->PES_STATUS) . "' ) ";
                
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
                $sql .= " ('" . db2_escape_string(trim($personEntry->EMAIL_ADDRESS)) . "','" . db2_escape_string($personEntry->NOTES_ID) . "','" . db2_escape_string($personEntry->PES_STATUS) . "' ) ";
                
                $loadCounter++;
                
                // -------------------------------------------------------------------                               
            }
            
            // echo $sql;
            // exit;

            $rs = DB2_EXEC ( $GLOBALS['conn'], $sql );
            if (! $rs) {
                $success = false;
            } else {
                // $loadCounter++;
            }
        }

        if($success){
            db2_commit($GLOBALS['conn']);
        } else {
            db2_rollback($GLOBALS['conn']);
        }

        db2_autocommit($GLOBALS['conn'],$autoCommit);

        $loaded = new DateTime();
        echo "<BR/>Database Load Finished : " . $loaded->format('Y-m-d H:i:s');
    }

    echo '<BR/><B>' . count($responseObj) . ' records read from VBAC api</B>';
    echo '<BR/><B>' . $loadCounter . ' records loaded to REST db</B>';
}