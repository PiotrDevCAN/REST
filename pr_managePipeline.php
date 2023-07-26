<?php
use itdq\Trace;
use rest\rfsTable;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container-fluid'>
<h3>Pipeline Report</h3>
<div id='rfsTableDiv'>
<?php
  rfsTable::buildHTMLPipelineTable();
?>
</div>
</div>

<?php
	include_once 'includes/modalDeleteRfsModal.html';
  include_once 'includes/modalArchiveRfsModal.html';
  include_once 'includes/modalEditPcrModal.html';
  include_once 'includes/modalEditRfsModal.html';
?>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>