<?php
use itdq\PlannedOutages;
use itdq\Navbar;
use itdq\NavbarMenu;
use itdq\NavbarOption;
use vbac\personTable;
use itdq\NavbarDivider;
include ('itdq/PlannedOutages.php');
include ('itdq/DbTable.php');
$plannedOutagesLabel = "Planned Outages";
$plannedOutages = new PlannedOutages();
include ('UserComms/responsiveOutages_V2.php');

$navBarImage = ""; //a small image to displayed at the top left of the nav bar
$navBarBrand = array(strtoupper($_SERVER['environment']),"index.php");
$navBarSearch = false;

$pageDetails = explode("/", $_SERVER['PHP_SELF']);
$page = isset($pageDetails[2]) ? $pageDetails[2] : $pageDetails[1];

$navbar = new Navbar($navBarImage, $navBarBrand,$navBarSearch);

$cdiAdmin       = new NavbarMenu("CDI Admin");
$trace          = new NavbarOption('View Trace','pi_trace.php','accessCdi');
$traceControl   = new NavbarOption('Trace Control','pi_traceControl.php','accessCdi');
$traceDelete    = new NavbarOption('Trace Deletion', 'pi_traceDelete.php','accessCdi');

$cdiAdmin->addOption($trace);
$cdiAdmin->addOption($traceControl);
$cdiAdmin->addOption($traceDelete);

$adminMenu      = new NavbarMenu('REST Admin','accessCdi accessAdmin');
$service        = new NavbarOption('CTB Service','pa_ctbService.php'                 ,'accessCdi accessAdmin');
$adminMenu->addOption($service);

$request        = new NavbarMenu(  'Request'                                          ,'accessCdi accessAdmin accessDemand accessRfs');
$newRfs         = new NavbarOption('New RFS','pr_newRfs.php'                          ,'accessCdi accessAdmin accessDemand accessRfs');
$newResReq      = new NavbarOption('New Resource Request', 'pr_newResourceRequest.php','accessCdi accessAdmin accessDemand accessRfs');
$request->addOption($newRfs);
$request->addOption($newResReq);

$supply         = new NavbarMenu(  'Supply');
$rfs            = new NavbarOption('RFS', 'ps_rfs.php'                           ,'accessCdi accessAdmin accessDemand accessSupply accessRfs');
$resRequest     = new NavbarOption('Resource Requests', 'ps_resourceRequests.php','accessCdi accessAdmin accessDemand accessSupply accessRfs');
$supply->addOption($rfs);
$supply->addOption($resRequest);

// $reports        = new NavbarMenu('Reports');
// $dummy          = new NavbarOption('Dummy entry', 'pr_dummyReport.php','accessUser accessReports');

$navbar->addMenu($cdiAdmin);
$navbar->addMenu($adminMenu);
$navbar->addMenu($request);
$navbar->addMenu($supply);
// $navbar->addMenu($reports);

$outages = new NavbarOption($plannedOutagesLabel, 'ppo_PlannedOutages.php','accessCdi accessPmo accessFm accessUser accessReports');
$navbar->addOption($outages);

$privacy = new NavbarOption('Privacy','https://w3.ibm.com/w3publisher/w3-privacy-notice','accessCdi accessUser accessReports ');
$navbar->addOption($privacy);

$navbar->createNavbar($page);

$isCdi    = employee_in_group($_SESSION['cdiBg'],     $_SESSION['ssoEmail']) || strstr($_SERVER['environment'], 'dev') ? ".not('.accessCdi')" : null;
$isAdmin  = employee_in_group($_SESSION['adminBg'],   $_SESSION['ssoEmail']) ? ".not('.accessAdmin')" : null;
$isDemand = employee_in_group($_SESSION['demandBg'],  $_SESSION['ssoEmail']) ? ".not('.accessDemand')" : null;
$isSupply = employee_in_group($_SESSION['supplyBg'],  $_SESSION['ssoEmail']) || strstr($_SERVER['environment'], 'dev') ? ".not('.accessSupply')" : null;
$isRfs    = employee_in_group($_SESSION['rfsBg'],     $_SESSION['ssoEmail']) ? ".not('.accessRfs')" : null;


$isReports= employee_in_group($_SESSION['reportsBg'],     $_SESSION['ssoEmail']) ? ".not('.accessReports')" : null;

$isUser = (!empty($isCdi) || !empty($isAdmin) || !empty($isDemand) || !empty($isSupply)  || !empty($isRfs)) ? ".not('.accessUser')" : null;

$isReports = employee_in_group($_SESSION['reportsBg'],  $_SESSION['ssoEmail']) ? ".not('.accessReports')" : null;

$_SESSION['isCdi']     = !empty($isCdi);
$_SESSION['isAdmin']   = !empty($isAdmin);
$_SESSION['isDemand']  = !empty($isDemand);
$_SESSION['isSupply']  = !empty($isSupply);
$_SESSION['isUser']    = !empty($isUser);
$_SESSION['isRfs']     = !empty($isRfs);
$_SESSION['isReports'] = !empty($isReports);



$plannedOutagesId = str_replace(" ","_",$plannedOutagesLabel);
?>
<script>
$('.navbarMenuOption')<?=$isCdi?><?=$isAdmin?><?=$isDemand?><?=$isSupply?><?=$isUser?><?=$isRfs?><?=$isReports?>.remove();
$('.navbarMenu').not(':has(li)').remove();

$('li[data-pagename="<?=$page;?>"]').addClass('active').closest('li.dropdown').addClass('active');
<?php



if($page != "index.php" && substr($page,0,3)!='cdi'){
    ?>

    console.log('<?=$page;?>');

	var pageAllowed = $('li[data-pagename="<?=$page;?>"]').length;
	if(pageAllowed==0 ){
		window.location.replace('index.php');
		alert("You do not have access to:<?=$page?>");
	}
	<?php
}

$userLevel = '';
$userLevel.= $isCdi     ? ':CDI'    : null;
$userLevel.= $isAdmin   ? ':Admin'  : null;
$userLevel.= $isDemand  ? ':Demand' : null;
$userLevel.= $isSupply  ? ':Supply' : null;
$userLevel.= $isRfs     ? ':Rfs Team' : null;
$userLevel.= $isReports ? ':Reports Only'   : null;
?>

restrictButtonAccess = function(){
	var userLevel  = $('#userLevel').text();
	var isCdi      = userLevel.indexOf('CDI')     !== -1;
	var isAdmin    = userLevel.indexOf('Admin')   !== -1;
	var isDemand   = userLevel.indexOf('Demand')  !== -1;
	var isSupply   = userLevel.indexOf('Supply')  !== -1;
	var isUser     = userLevel.indexOf('User')    !== -1;
	var isRfs      = userLevel.indexOf('Rfs Team')!== -1;
	var isReports  = userLevel.indexOf('Reports') !== -1;

	var nonPermittedButtons = $('button.accessRestrict'); // We will remove all accessRestrict buttons, unless they have the access.
	if(isCdi){
		// We remove .accessCdi buttons from the list of nonPermittedButtons as the person is CDI so those buttons are allowed.
		nonPermittedButtons = $(nonPermittedButtons).not('.accessCdi');
	}
	if(isAdmin){
		nonPermittedButtons = $(nonPermittedButtons).not('.accessAdmin');
	}
	if(isDemand){
		nonPermittedButtons = $(nonPermittedButtons).not('.accessDemand');
	}
	if(isSupply){
		nonPermittedButtons = $(nonPermittedButtons).not('.accessSupply');
	}
	if(isUser){
		nonPermittedButtons = $(nonPermittedButtons).not('.accessUser');
	}
	if(isRfs){
		nonPermittedButtons = $(nonPermittedButtons).not('.accessRfs');
	}
	if(isReports){
		nonPermittedButtons = $(nonPermittedButtons).not('.accessReports');
	}
	console.log('will remove restricted buttons');
	$(nonPermittedButtons).remove();
};

$(document).on('draw.dt',function(){
	restrictButtonAccess();
	});


$(document).ready(function () {
    $('button.accessRestrict')<?=$isCdi?><?=$isAdmin?><?=$isDemand?><?=$isSupply?><?=$isUser?><?=$isReports?><?=$isRfs?>.remove();
    $("#userLevel").html("<?=$userLevel?>");
    var poContent = $('#<?=$plannedOutagesId?> a').html();
	var badgedContent = poContent + "&nbsp;" + "<?=$plannedOutages->getBadge();?>";
	$('#<?=$plannedOutagesId?> a').html(badgedContent);

});
</script>

