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
        
        if(empty($resourceNotesid)){
            // No one to notify, so don't send to anyone.
            return false;            
        }
        
        $resourceEmail = !empty($resourceNotesid) ? BluePages::getIntranetIdFromNotesId($resourceNotesid) : null ;
        
        $to = !empty($requestorEmail) ? array($resourceEmail) : array($requestorEmail); // If we have a RESOURCE_NAME send it to them, else just to the REQUESTOR
        $cc = substr(trim(strtolower($requestorEmail)),-7 ) === 'ibm.com' ?  array($requestorEmail) : array(); // CC the requestor, if we have an IBM email address for them.
        
        $replacements = array();
        foreach ($emailPattern as $field => $pattern) {
            $replacements[] = $resourceRequestData[$field];
        }
        
        
        $thStyle = 'font-size:10.0pt;font-weight:700;text-decoration:none;font-family:Tahoma, sans-serif;padding:5px;text-align:right;background-color: #4eb1ea;';
        $tdStyle = 'mso-style-parent:style0;
	padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:locked visible;
	white-space:nowrap;
	mso-rotate:0;
    padding:5px';
        
        $pStyle  = 'font-size:9.0pt;font-family:Tahoma, sans-serif;';
        
        $emailBody = "<p style='$pStyle'>" . preg_replace($emailPattern, $replacements, $emailEntry) . "</p>";
        $emailBody.= "<br/>";
        $emailBody.= "<p style='$pStyle'>Resource Request Details:</p>";
        $emailBody.= "<table>";
        $emailBody.= "<tbody>";
        $emailBody.= "<tr ><th style='$thStyle'>RFS</th><td style='$tdStyle'>" . $resourceRequestData['RFS'] . "</td></tr>";
        $emailBody.= "<tr ><th style='$thStyle'>Request</th><td style='$tdStyle''>" . $resourceRequestData['RESOURCE_REFERENCE'] . "</td></tr>";
        $emailBody.= "<tr ><th style='$thStyle'>Service</th><td style='$tdStyle'>" . $resourceRequestData['SERVICE'] . "</td></tr>";
        $emailBody.= "<tr ><th style='$thStyle'>Total Hours</th><td style='$tdStyle'>" . $resourceRequestData['TOTAL_HOURS'] . "</td></tr>";
        $emailBody.= "<tr ><th style='$thStyle'>Hours Type</th><td style='$tdStyle'>" . $resourceRequestData['HOURS_TYPE'] . "</td></tr>";
        $emailBody.= "<tr ><th style='$thStyle'>Starting</th><td style='$tdStyle'>" . $startDate->format('d M Y') . "</td></tr>";
        $emailBody.= "<tr ><th style='$thStyle'>Ending</th><td style='$tdStyle'>" . $endDate->format('d M Y') . "</td></tr>";
        $emailBody.= "</tbody>";
        $emailBody.= "</table>";
        
        BlueMail::send_mail($to, "Update to: " . $resourceRequestData['RFS'] . " - " . $resourceRequestData['RESOURCE_REFERENCE'], $emailBody, 'REST@noreply.ibm.com',$cc);        

    }
    
    static function sendRfsNotification($rfsid,$emailEntry, $emailPattern){
        
             
        $rfsTable = new rfsTable(allTables::$RFS);
        $rfsData = $rfsTable->getPredicate(" RFS_ID ='" . db2_escape_string($rfsid) . "' ");
        $endDate   = new \DateTime($rfsData['RFS_END_DATE']);
        
        $requestorEmail = rfsTable::getRequestorEmail($rfsid);
        
        if(empty($requestorEmail)){
            // No one to notify, so don't send to anyone.
            return false;
        }
        
        $to = array($requestorEmail); 
        $cc = array($_SESSION['ssoEmail']);
      
        $replacements = array();
        foreach ($emailPattern as $field => $pattern) {
            $replacements[] = $rfsData[$field];
        }
        
        
        $thStyle = 'font-size:10.0pt;font-weight:700;text-decoration:none;font-family:Tahoma, sans-serif;padding:5px;text-align:right;background-color: #4eb1ea;';
        $tdStyle = 'mso-style-parent:style0;
	padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:locked visible;
	white-space:nowrap;
	mso-rotate:0;
    padding:5px';
        
        $pStyle  = 'font-size:9.0pt;font-family:Tahoma, sans-serif;';
        
        $emailBody = "<p style='$pStyle'>" . preg_replace($emailPattern, $replacements, $emailEntry) . "</p>";
        $emailBody.= "<br/>";
        $emailBody.= "<p style='$pStyle'>RFS Details:</p>";
        $emailBody.= "<table>";
        $emailBody.= "<tbody>";
        $emailBody.= "<tr ><th style='$thStyle'>RFS</th><td style='$tdStyle'>" . $rfsData['RFS_ID'] . "</td></tr>";
        $emailBody.= "<tr ><th style='$thStyle'>Title</th><td style='$tdStyle''>" . $rfsData['PROJECT_TITLE'] . "</td></tr>";
        $emailBody.= "<tr ><th style='$thStyle'>Value Stream</th><td style='$tdStyle'>" . $rfsData['VALUE_STREAM'] . "</td></tr>";           
        $emailBody.= "<tr ><th style='$thStyle'>End Date</th><td style='$tdStyle'>" . $endDate->format('d M Y') . "</td></tr>";
        $emailBody.= "</tbody>";
        $emailBody.= "</table>";
        
        BlueMail::send_mail($to, "Update to: " . $rfsid , $emailBody, 'REST@noreply.ibm.com',$cc);
        
    }
    

    
}