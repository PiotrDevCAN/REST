<?php
namespace rest;


use itdq\BlueMail;
use itdq\BluePages;

class emailNotifications
{
    static function sendNotification($resourceReference,$emailEntry, $emailPattern){
    
        $resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
        $resourceRequestData = $resourceTable->getPredicate(" RESOURCE_REFERENCE='" . db2_escape_string($resourceReference) . "' ");
        
        $startDate = new \DateTime($resourceRequestData['START_DATE']);
        $endDate   = new \DateTime($resourceRequestData['END_DATE']);
        
        $resourceNotesid = isset($resourceRequestData['RESOURCE_NAME']) ? $resourceRequestData['RESOURCE_NAME']  : null ;
        $requestorEmail = rfsTable::getRequestorEmail($resourceRequestData['RFS']);
        
        $resourceEmail = !empty($resourceNotesid) ? BluePages::getIntranetIdFromNotesId($resourceNotesid) : null ;
        
        $to = !empty($requestorEmail) ? array($resourceEmail) : array($requestorEmail); // If we have a RESOURCE_NAME send it to them, else just to the REQUESTOR
        $cc = !empty($requestorEmail) ? array($requestorEmail) : null; // If we have a RESOURCE_NAME, then CC the requestor, else it's going to the requestor so CC nobody.
        
        $replacements = array();
        foreach ($emailPattern as $field => $pattern) {
            $replacements[] = $resourceRequestData[$field];
        }
        
        $emailBody = preg_replace($emailPattern, $replacements, $emailEntry);
        $emailBody.= "<br/>";
        $emailBody.= "<p>Details of the specific Resource Request : " . $resourceRequestData['RESOURCE_REFERENCE'] . "</p>";
        $emailBody.= "<table>";
        $emailBody.= "<tbody>";
        $emailBody.= "<tr style='background-color: #4eb1ea;'><th style='text-align:right;padding:5px;'>RFS</th><td>" . $resourceRequestData['RFS'] . "</td></tr>";
        $emailBody.= "<tr style='background-color: #fff;'><th style='text-align:right;padding:5px;'>Request</th><td>" . $resourceRequestData['RESOURCE_REFERENCE'] . "</td></tr>";
        $emailBody.= "<tr style='background-color: #4eb1ea;'><th style='text-align:right;padding:5px;'>Service</th><td>" . $resourceRequestData['SERVICE'] . "</td></tr>";
        $emailBody.= "<tr style='background-color: #fff;'><th style='text-align:right;padding:5px;'>Hours</th><td>" . $resourceRequestData['HRS_PER_WEEK'] . "</td></tr>";
        $emailBody.= "<tr style='background-color: #4eb1ea;'><th style='text-align:right;padding:5px;'>Starting</th><td>" . $startDate->format('d M Y') . "</td></tr>";
        $emailBody.= "<tr style='background-color: #fff;'><th style='text-align:right;padding:5px;'>Ending</th><td>" . $endDate->format('d M Y') . "</td></tr>";
        $emailBody.= "</tbody>";
        $emailBody.= "</table>";
        
        BlueMail::send_mail($to, "Update to " . $resourceRequestData['RFS'] . ":" . $resourceRequestData['RESOURCE_REFERENCE'], $emailBody, 'REST@noreply.ibm.com',$cc);        

    }
    

    
}