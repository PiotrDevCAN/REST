<?php
use itdq\PlannedOutages;
do_auth();
echo "<div class='container'>";

echo "<div id='messagePlaceholder'>";
echo "</div>";

$plannedOutages = new PlannedOutages();
include ('../UserComms/responsiveOutages_V2.php');
$plannedOutages->displayOutages();
echo "</div>";