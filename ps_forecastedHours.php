<?php
use itdq\Trace;
use rest\forecastedHoursTable;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);

?>

<div class='container'>
<h3>Forecasted Hours</h3>
<form id='reportDates' class='form-horizontal'>
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
  </div>
</form>
</div>

<div class='container-fluid'>
<div id='messageArea'></div>
<div id='forecastedHoursTableDiv'>
<?php
  forecastedHoursTable::buildHTMLTable();
?>
</div>
</div>

<?php
	include_once 'includes/modalSaveResultModal.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
