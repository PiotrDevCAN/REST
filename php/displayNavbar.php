<?php
use itdq\PlannedOutages;
use itdq\Navbar;
use itdq\NavbarMenu;
use itdq\NavbarOption;
use itdq\NavbarDivider;
include ('itdq/PlannedOutages.php');
include ('itdq/DbTable.php');
$plannedOutagesLabel = "Planned Outages";
$plannedOutagesId = str_replace(" ","_",$plannedOutagesLabel);
$plannedOutages = new PlannedOutages();
$plannedOutagesBadge = $plannedOutages->getBadge();
include ('UserComms/responsiveOutages_V2.php');

$navBarImage = ""; //a small image to displayed at the top left of the nav bar
$navBarBrand = array(strtoupper($_ENV['environment']) . "&nbsp; 2020","index.php");
$navBarSearch = false;

$pageDetails = explode("/", $_SERVER['PHP_SELF']);
$page = isset($pageDetails[2]) ? $pageDetails[2] : $pageDetails[1];

$navbar = new Navbar($navBarImage, $navBarBrand,$navBarSearch);

$divider 		= ( new NavbarDivider(Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN));

$cdiAdmin       = new NavbarMenu("CDI Admin");
$trace          = new NavbarOption('View Trace','pi_trace.php',Navbar::$ACCESS_CDI);
$traceControl   = new NavbarOption('Trace Control','pi_traceControl.php',Navbar::$ACCESS_CDI);
$traceDelete    = new NavbarOption('Trace Deletion', 'pi_traceDelete.php',Navbar::$ACCESS_CDI);

$cdiAdmin->addOption($trace);
$cdiAdmin->addOption($traceControl);
$cdiAdmin->addOption($traceDelete);

$adminMenu      = new NavbarMenu('REST Admin',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
$organisation   = new NavbarOption('Organisation','pa_organisation.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
$adminMenu->addOption($organisation);
$service   		= new NavbarOption('Service','pa_service.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
$adminMenu->addOption($service);
$pa_organisationServices   = new NavbarOption('Service to Organisation','pa_organisationServices.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
$adminMenu->addOption($pa_organisationServices);
// $adminMenu->addOption( new NavbarDivider(Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN));
// $businessUnit   = new NavbarOption('Business Unit','pa_businessUnit.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
// $adminMenu->addOption($businessUnit);
// $valueStream   = new NavbarOption('Value Stream','pa_valueStream.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
// $adminMenu->addOption($valueStream);
// $businessUnitValueStream   = new NavbarOption('Value Stream to Business Unit','pa_businessUnitValueStreams.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
// $adminMenu->addOption($businessUnitValueStream);
$adminMenu->addOption( new NavbarDivider(Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN));
$resType 		= new NavbarOption('Resource Type - <b>NEW!</b>','pa_resourceType.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_RFS_AD);
$adminMenu->addOption($resType);
$psBand 		= new NavbarOption('PS Band - <b>NEW!</b>','pa_PSBand.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_RFS_AD);
$adminMenu->addOption($psBand);
// $band 			= new NavbarOption('Band - <b>NEW!</b>','pa_band.php',Navbar::$ACCESS_CDI' '.Navbar::$ACCESS_RFS_AD);
// $adminMenu->addOption($band);
$resRate 		= new NavbarOption('Resource Rates - <b>NEW!</b>','pa_resourceRate.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_RFS_AD);
$adminMenu->addOption($resRate);
$adminMenu->addOption( new NavbarDivider(Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_RFS_AD));
$resTraits 	= new NavbarOption('Resource Traits (Assignment) - <b>NEW!</b>','pa_resourceTraits.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_RFS_AD);
$adminMenu->addOption($resTraits);
$bespokeRate 	= new NavbarOption('Bespoke Rates (Assignment) - <b>NEW!</b>','pa_bespokeRate.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_RFS_AD);
$adminMenu->addOption($bespokeRate);
$adminMenu->addOption( new NavbarDivider(Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN));
$VBACactiveResources = new NavbarOption('VBAC Active Resources','pa_activeResources.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
$adminMenu->addOption($VBACactiveResources);
$RFSToArchiveUpload  = new NavbarOption('RFS To Archive Upload','pc_RFSToArchiveUpload.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
$adminMenu->addOption($RFSToArchiveUpload);

$request        = new NavbarMenu(  'Request'                                          ,Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_DEMAND.' '.Navbar::$ACCESS_RFS);
$newRfs         = new NavbarOption('New RFS','pr_newRfs.php'                          ,Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_DEMAND.' '.Navbar::$ACCESS_RFS);
$newRfsPcr      = new NavbarOption('New RFS PCR - <b>NEW!</b>','pr_newRfsPcr.php'     ,Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_DEMAND.' '.Navbar::$ACCESS_RFS);
$newResReq      = new NavbarOption('New Resource Request', 'pr_newResourceRequest.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_DEMAND.' '.Navbar::$ACCESS_RFS);
// $managePipeline = new NavbarOption('Manage Pipeline', 'pr_managePipeline.php'	  ,Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_DEMAND.' '.Navbar::$ACCESS_RFS);

$request->addOption($newRfs);
$request->addOption($newRfsPcr);
$request->addOption($newResReq);
// $request->addOption(new NavbarDivider(Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_DEMAND.' '.Navbar::$ACCESS_SUPPLY.' '.Navbar::$ACCESS_SUPPLY_X.' '.Navbar::$ACCESS_RFS.' '.Navbar::$ACCESS_REPORTS));
// $request->addOption(new NavbarDivider(Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_DEMAND.''));
// $request->addOption($managePipeline);

$assign         = new NavbarMenu('Assign');
$resRequest     = new NavbarOption('Resource Requests', 'ps_resourceRequests.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_DEMAND.' '.Navbar::$ACCESS_SUPPLY.' '.Navbar::$ACCESS_SUPPLY_X.' '.Navbar::$ACCESS_RFS.' '.Navbar::$ACCESS_REPORTS);
// $info           = new NavbarOption('PHP Info', 'phpinfo.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_DEMAND.' '.Navbar::$ACCESS_SUPPLY.' '.Navbar::$ACCESS_SUPPLY_X.' '.Navbar::$ACCESS_RFS.' '.Navbar::$ACCESS_REPORTS);
$assign->addOption($resRequest);
// $supply->addOption($info);

$reports        = new NavbarMenu('Report');
$listRfs        = new NavbarOption('RFS Report', 'ps_rfs.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_DEMAND.' '.Navbar::$ACCESS_SUPPLY.' '.Navbar::$ACCESS_SUPPLY_X.' '.Navbar::$ACCESS_RFS.' '.Navbar::$ACCESS_REPORTS);
$listRfsPcr     = new NavbarOption('RFS PCR Report - <b>NEW!</b>', 'ps_rfsPcr.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_DEMAND.' '.Navbar::$ACCESS_SUPPLY.' '.Navbar::$ACCESS_SUPPLY_X.' '.Navbar::$ACCESS_RFS.' '.Navbar::$ACCESS_REPORTS);
$claim          = new NavbarOption('Requests Report', 'ps_ClaimMonthly.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN.' '.Navbar::$ACCESS_DEMAND.' '.Navbar::$ACCESS_SUPPLY.' '.Navbar::$ACCESS_SUPPLY_X.' '.Navbar::$ACCESS_RFS.' '.Navbar::$ACCESS_REPORTS);
$hrsPerWeek     = new NavbarOption('HrsPerWeek', 'ps_HoursPerWeekPerResource.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
$noResReq     	= new NavbarOption('Requests Assigned To Leavers', 'ps_NoResourceRequests.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
$noMatchBUReq   = new NavbarOption('Cross BU assignments', 'ps_CrossBUAssignments.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
// $incorWeeksReq  = new NavbarOption('Requests with Incorrect Weekends', 'ps_IncorrectWeekendsRequests.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
$rateCard   	= new NavbarOption('Rate Card Report - <b>NEW!</b>', 'ps_rateCard.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
$bespokeRates   = new NavbarOption('Bespoke Rates Report - <b>NEW!</b>', 'ps_bespokeRates.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);
$forecastedHours   = new NavbarOption('Forecasted Hours - <b>In development!</b>', 'ps_forecastedHours.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_ADMIN);

$reports->addOption($listRfs);
$reports->addOption($listRfsPcr);
$reports->addOption($claim);
$reports->addOption($hrsPerWeek);
$reports->addOption($noResReq);
$reports->addOption($noMatchBUReq);
// $reports->addOption($incorWeeksReq);
$reports->addOption($divider);
$reports->addOption($rateCard);
$reports->addOption($bespokeRates);
$reports->addOption($divider);
$reports->addOption($forecastedHours);
// $dummy = new NavbarOption('Dummy entry', 'pr_dummyReport.php',Navbar::$ACCESS_USER.' '.Navbar::$ACCESS_REPORTS);

$navbar->addMenu($cdiAdmin);
$navbar->addMenu($adminMenu);
$navbar->addMenu($request);
$navbar->addMenu($assign);
$navbar->addMenu($reports);

$outages = new NavbarOption($plannedOutagesLabel, 'ppo_PlannedOutages.php',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_PMO.' '.Navbar::$ACCESS_FM.' '.Navbar::$ACCESS_USER.' '.Navbar::$ACCESS_REPORTS);
$navbar->addOption($outages);

$privacy = new NavbarOption('Privacy','https://w3.ibm.com/w3publisher/w3-privacy-notice',Navbar::$ACCESS_CDI.' '.Navbar::$ACCESS_USER.' '.Navbar::$ACCESS_REPORTS);
$navbar->addOption($privacy);

$navbar->createNavbar($page);
$isCdi    	 = employee_in_group($_SESSION['cdiBg'],     $_SESSION['ssoEmail']) || strstr($_ENV['environment'], 'dev')  ? true : null;
$isAdmin  	 = employee_in_group($_SESSION['adminBg'],   $_SESSION['ssoEmail']) || strstr($_ENV['environment'], 'dev')  ? true : null;
$isDemand 	 = employee_in_group($_SESSION['demandBg'],  $_SESSION['ssoEmail']) || strstr($_ENV['environment'], 'dev')  ? true : null;
$isSupply 	 = employee_in_group($_SESSION['supplyBg'],  $_SESSION['ssoEmail']) || strstr($_ENV['environment'], 'dev')  ? true : null;
$isSupplyX	 = employee_in_group($_SESSION['supplyXBg'], $_SESSION['ssoEmail']) || strstr($_ENV['environment'], 'dev')  ? true : null;
$isRfs    	 = employee_in_group($_SESSION['rfsBg'],     $_SESSION['ssoEmail']) || strstr($_ENV['environment'], 'dev')  ? true : null;
$isRfsADTeam = employee_in_group($_SESSION['rfsADBg'], 	 $_SESSION['ssoEmail']) || strstr($_ENV['environment'], 'dev')  ? true : null;
$isReports	 = employee_in_group($_SESSION['reportsBg'], $_SESSION['ssoEmail']) || strstr($_ENV['environment'], 'dev')  ? true : null;

// For Testing only
// $isCdi = null;
// $isAdmin = null;
// $isDemand = null;
// $isSupply = null;
// $isSupplyX = true;
// $isRfs = null;
// $isReports = null;

$isUser = (
	!empty($isCdi) 
	|| !empty($isAdmin) 
	|| !empty($isDemand) 
	|| !empty($isSupply)  
	|| !empty($isSupplyX)  
	|| !empty($isRfs) 
	|| !empty($isRfsADTeam)
	|| !empty($isReports) 
) ? true : null;

$_SESSION['isCdi']     	 = !empty($isCdi);
$_SESSION['isAdmin']   	 = !empty($isAdmin);
$_SESSION['isDemand']  	 = !empty($isDemand);
$_SESSION['isSupply']  	 = !empty($isSupply);
$_SESSION['isSupplyX'] 	 = !empty($isSupplyX);
$_SESSION['isRfs']     	 = !empty($isRfs);
$_SESSION['isRfsADTeam'] = !empty($isRfsADTeam);
$_SESSION['isReports'] 	 = !empty($isReports);
$_SESSION['isUser']      = !empty($isUser);

?>
<script type='text/javascript'>

var ACCESS_RESTRICT = '<?=Navbar::$ACCESS_RESTRICT?>';

var ACCESS_CDI 		= '<?=Navbar::$ACCESS_CDI?>';
var ACCESS_ADMIN 	= '<?=Navbar::$ACCESS_ADMIN?>';
var ACCESS_DEMAND 	= '<?=Navbar::$ACCESS_DEMAND?>';
var ACCESS_SUPPLY 	= '<?=Navbar::$ACCESS_SUPPLY?>';
var ACCESS_SUPPLY_X = '<?=Navbar::$ACCESS_SUPPLY_X?>';
var ACCESS_RFS 		= '<?=Navbar::$ACCESS_RFS?>';
var ACCESS_RFS_AD 	= '<?=Navbar::$ACCESS_RFS_AD?>';
var ACCESS_REPORTS 	= '<?=Navbar::$ACCESS_REPORTS?>';
var ACCESS_USER 	= '<?=Navbar::$ACCESS_USER?>';

var isCdi     	= '<?=$_SESSION['isCdi'];?>'; 
var isAdmin   	= '<?=$_SESSION['isAdmin'];?>';
var isDemand  	= '<?=$_SESSION['isDemand'];?>';
var isSupply  	= '<?=$_SESSION['isSupply'];?>'; 
var isSupplyX 	= '<?=$_SESSION['isSupplyX'];?>';
var isRfs 	  	= '<?=$_SESSION['isRfs'];?>'; 
var isRfsADTeam = '<?=$_SESSION['isRfsADTeam'];?>'; 
var isReports 	= '<?=$_SESSION['isReports'];?>';
var isUser 	  	= '<?=$_SESSION['isUser'];?>';

var plannedOutagesId 	= '<?=$plannedOutagesId?>';
var plannedOutagesLabel = '<?=$plannedOutagesLabel?>';
var plannedOutagesBadge = "<?=$plannedOutagesBadge?>";

</script>