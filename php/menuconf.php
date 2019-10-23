<?php
$navBarImage = ""; //a small image to displayed at the top left of the nav bar
$navBarBrand = array(strtoupper($_SERVER['environment']),"index.php");
$navBarSearch = false;


$navBar_data = array(
    array("ITDQ Admin",'dropDown'),
    array("View Trace", "pi_trace.php"),
    array("Trace Control", "pi_traceControl.php"),
    array("Trace Deletion", "pi_traceDelete.php"),
    array("",'endOfDropDown'),

    array("REST Admin",'dropDown'),
    array('Upload','pa_upload.php'),
    array("",'endOfDropDown'),

    array("Request",'dropDown'),
    array('New RFS','pa_newRfs.php'),
    array('New Resource Request','pa_newResourceRequest.php'),
    array("",'endOfDropDown'),

    array("Assign",'dropDown'),
    array('RFS','pr_RFS.php'),
    array('Resources','pr_resourceRequired.php'),
    array("",'endOfDropDown'),

    array('Planned Outages','ppo_PlannedOutages.php')

);
