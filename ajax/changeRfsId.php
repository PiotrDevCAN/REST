<?php

use itdq\Trace;
use rest\allTables;
use rest\resourceRequestDiaryTable;
use rest\resourceRequestTable;
use rest\rfsTable;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

// originalRFS_ID=LBOP-PLD-000120&switchRFS_ID=LBOP-PLD-000120'
$oldRfsId = !empty($_POST['originalRFS_ID']) ? trim($_POST['originalRFS_ID']) : null;
$newRfsId = !empty($_POST['switchRFS_ID']) ? trim($_POST['switchRFS_ID']) : null;

$success = false;
if (!empty($oldRfsId) && !empty($newRfsId)) {
    if ($oldRfsId != $newRfsId) {
        $validIds = rfsTable::validateRfsIds();
        if ($validIds){
            $switchRfsUpdate = rfsTable::updateRfsId($oldRfsId, $newRfsId);
            if ($switchRfsUpdate) {
                
                // $allocatorNotesid = BluePages::getNotesidFromIntranetId($_SESSION['ssoEmail']);
                $allocatorNotesid = $_SESSION['ssoEmail'];
                $allRequests = $loader->load('RESOURCE_REFERENCE',allTables::$RESOURCE_REQUESTS," RFS='" . htmlspecialchars($oldRfsId) . "' ");

                if (count($allRequests) > 0) {
                    foreach ($allRequests as $resourceReference) {
                        $diaryEntry =  $resourceReference . " was re-assigned to new RFS " . $newRfsId . " by " . $allocatorNotesid;
                        resourceRequestDiaryTable::insertEntry($diaryEntry, $resourceReference);  
                    }
                    $switchRRUpdate = resourceRequestTable::updateRfsId($oldRfsId, $newRfsId);
                } else {
                    $switchRRUpdate = true;
                }
            } else {
                $switchRRUpdate = false;
            }
            if ($switchRfsUpdate == true && $switchRRUpdate == true) {
                $success = true;
            }
        }
    }
}

$messages = ob_get_clean();
ob_start();

$response = array('success'=>$success,'messages'=>$messages);

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);