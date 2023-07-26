<?php
use itdq\Trace;
use itdq\Loader;
use rest\allTables;
use rest\rfsTable;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);

// $loader = new Loader();
// $allRfs = $loader->load('RFS_ID',allTables::$RFS, " ARCHIVE is null ");
// $allValueStream = $loader->load('VALUE_STREAM',allTables::$RFS, " ARCHIVE is null ");
// $allBusinessUnits = $loader->load('BUSINESS_UNIT',allTables::$RFS, " ARCHIVE is null ");
// $allRequestor = $loader->load('REQUESTOR_EMAIL',allTables::$RFS, " ARCHIVE is null ");

// $defaultForPipelineLive = $_SESSION['isRfs'] ? null : ' checked ';
// $canSeeLive = $_SESSION['isRfs'] ? ' disabled ' : null;
?>
<div class='container'>
<h3>Rest Requests Report (Hrs/Mth)</h3>

<form id='reportDates' class="form-horizontal">
    <div class='form-group text-right' >
        <label for='selectRfs' class='col-md-1 control-label text-right'>RFS</label>
        <div class='col-md-2 text-left'>
            <select class='form-control select' id='selectRfs'
                name='selectRfs'
                data-placeholder="Select RFS" data-allow-clear="true"
                >
                <option value=''>Select RFS</option>
                <option value='All'>All</option>
            </select>
        </div>
        <label for='selectValueStream' class='col-md-1 control-label text-right'>Value Stream</label>
        <div class='col-md-2 text-left'>
            <select class='form-control select' id='selectValueStream'
                name='selectValueStream'
                data-placeholder="select Value Stream" data-allow-clear="true"
                >
                <option value=''>Select Value Stream</option>
                <option value='All'>All</option>
            </select>
        </div>
        <label for='selectBusinessUnit' class='col-md-1 control-label text-right'>Business Unit</label>
        <div class='col-md-2 text-left'>
            <select class='form-control select' id='selectBusinessUnit'
                name='selectValueStream'
                data-placeholder="select Business Unit" data-allow-clear="true"
                >
                <option value=''>Select Business Unit</option>
                <option value='All'>All</option>
            </select>
        </div>  
        <label for='selectRequestor' class='col-md-1 control-label text-right'>Requestor</label>
        <div class='col-md-2 text-left'>
            <select class='form-control select' id='selectRequestor'
                name='selectRequestor'
                data-placeholder="Select Requestor" data-allow-clear="true"
                >
                <option value=''>Select Requestor</option>
                <option value='All'>All</option>
            </select>
        </div>
    </div>
</form>
</div>

<hr/>

<div class='container-fluid'>
<div id='claimTableDiv'>
<?php
    rfsTable::buildHTMLRequestsTable('claim');
?>
</div>
</div>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>

<style type="text/css">

.dataTables_wrapper .dataTables_processing {
    background-color:#006699;
    height: 60px;
}
td.dataTables_empty {
    color:white;
	text-align: center;
	font-size: 20px;
	background-color:#006699;
}

</style>