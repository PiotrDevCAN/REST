<?php
namespace rest;

use itdq\DbRecord;

/**
 *
 * @author gb001399
 *
 * ALTER TABLE "ROB_DEV"."PERSON" ADD COLUMN "SQUAD_NUMBER" NUMERIC(5);
 *
 */
class activeResourceRecord extends DbRecord
{
    protected $CNUM;
    protected $OPEN_SEAT_NUMBER;
    protected $FIRST_NAME;
    protected $LAST_NAME;

    protected $EMAIL_ADDRESS;
    protected $NOTES_ID;
    protected $LBG_EMAIL;

    protected $EMPLOYEE_TYPE;

    protected $FM_CNUM;
    protected $FM_MANAGER_FLAG;

    protected $CTB_RTB;
    protected $TT_BAU;
    protected $LOB;
    protected $ROLE_ON_THE_ACCOUNT;
    protected $ROLE_TECHNOLOGY;

    protected $START_DATE;
    protected $PROJECTED_END_DATE;

    protected $COUNTRY;
    protected $IBM_BASE_LOCATION;
    protected $LBG_LOCATION;

    protected $OFFBOARDED_DATE;

    protected $PES_DATE_REQUESTED;
    protected $PES_REQUESTOR;
    protected $PES_DATE_RESPONDED;
    protected $PES_STATUS_DETAILS;
    protected $PES_STATUS;

    protected $REVALIDATION_DATE_FIELD;
    protected $REVALIDATION_STATUS;

    protected $CBN_DATE_FIELD;
    protected $CBN_STATUS;

    protected $WORK_STREAM;
    protected $CT_ID_REQUIRED;
    protected $CT_ID;
    protected $CIO_ALIGNMENT;
    protected $PRE_BOARDED;

    protected $SECURITY_EDUCATION;
    protected $RF_Flag;
    protected $RF_Start;
    protected $RF_End;

    protected $PMO_STATUS;
    protected $PES_DATE_EVIDENCE;

    protected $RSA_TOKEN;
    protected $CALLSIGN_ID;

    protected $PROCESSING_STATUS;
    protected $PROCESSING_STATUS_CHANGED;
    
    protected $PES_LEVEL;
    protected $PES_RECHECK_DATE;
    protected $PES_CLEARED_DATE;
    
    protected $SQUAD_NUMBER;

    protected $person_bio;

    // Fields to be edited in the DataTables Reports. Need to know their position in the array $row;
    const FIELD_CNUM = 0;
    const FIELD_NOTES_ID = 5;
    const FIELD_FM_MANAGER_FLAG = 9;
    const FIELD_LOB = 12;
    const FIELD_ROLE_ON_THE_ACCOUNT = 13;
    const FIELD_COUNTRY = 17;
    const FIELD_PES_DATE_REQUESTED = 21;
    const FIELD_PES_REQUESTOR = 22;
    const FIELD_PES_DATE_RESPONDED = 23;
    const FIELD_PES_STATUS_DETAILS = 24;
    const FIELD_PES_STATUS = 25;

    const REVALIDATED_FOUND = 'found';
    const REVALIDATED_VENDOR = 'vendor';
    const REVALIDATED_LEAVER = 'leaver';
    const REVALIDATED_POTENTIAL = 'potentialLeaver';
    const REVALIDATED_PREBOARDER = 'preboarder';
    const REVALIDATED_OFFBOARDING = 'offboarding';
    const REVALIDATED_OFFBOARDED = 'offboarded';

    const SECURITY_EDUCATION_COMPLETED = 'Yes';
    const SECURITY_EDUCATION_NOT_COMPLETED = 'No';

    const PMO_STATUS_CONFIRMED = 'Confirmed';
    const PMO_STATUS_AWARE     = 'Aware';

    public static $cio = array('CTB Leadership','CTB Central BU','CTB PMO','Commercial & Business Banking','Insurance & Enterprise Programmes','Cyber & TRP','Enterprise Transformation','Retail & Community Banking Transformation','Cross Platform','Product & Engineering');

    public static $pesTaskId = array('lbgvetpr@uk.ibm.com'); // Only first entry will be used as the "contact" in the PES status emails.
    public static $pmoTaskId = array('aurora.central.pmo@uk.ibm.com');
    public static $orderITCtbTaskId = array('jeemohan@in.ibm.com');
    public static $orderITNonCtbTaskId = array('aurora.central.pmo@uk.ibm.com');
    public static $orderITBauTaskId = array('aurora.central.pmo@uk.ibm.com');
    public static $orderITNonBauTaskId = array('aurora.central.pmo@uk.ibm.com');
    public static $smCdiAuditEmail = 'e3h3j0u9u6l2q3a3@ventusdelivery.slack.com';
    public static $securityOps = array('IBM.LBG.Security.Operations@uk.ibm.com');
    const PES_STATUS_NOT_REQUESTED = 'Not Requested';
    const PES_STATUS_CLEARED       = 'Cleared';
    const PES_STATUS_CLEARED_PERSONAL   = 'Cleared - Personal Reference';
    const PES_STATUS_DECLINED      = 'Declined';
    const PES_STATUS_EXCEPTION     = 'Exception';
    const PES_STATUS_PROVISIONAL   = 'Provisional Clearance';
    const PES_STATUS_FAILED        = 'Failed';
    const PES_STATUS_INITIATED     = 'Initiated';
    const PES_STATUS_REQUESTED     = 'Evidence Requested';
    const PES_STATUS_RESTART       = 'Restart Requested';
    const PES_STATUS_REMOVED       = 'Removed';
    const PES_STATUS_REVOKED       = 'Revoked';
    const PES_STATUS_CANCEL_REQ     = 'Cancel Requested';
    const PES_STATUS_CANCEL_CONFIRMED = 'Cancel Confirmed';
    const PES_STATUS_TBD           = 'TBD';
    const PES_STATUS_RECHECK_REQ   = 'Recheck Req';
    const PES_STATUS_RECHECK_PROGRESSING   = 'Recheck Progressing';
    const PES_STATUS_MOVER         = 'Mover';
    const PES_STATUS_LEFT_IBM      = 'Left IBM';

    function __construct($pwd=null){
        $this->headerTitles['FM_CNUM'] = 'FUNCTIONAL MGR';
        $this->headerTitles['SQUAD_NUMBER'] = 'SQUAD NAME';
        $this->headerTitles['OLD_SQUAD_NUMBER'] = 'OLD SQUAD NAME';
        parent::__construct();
    }
    
    function htmlHeaderCells(){
        $headerCells = parent::htmlHeaderCells();
        $headerCells.= "<th>Has Delegates</th>";
        return $headerCells;
    }
}