<?php

use rest\allTables;
use rest\resourceRequestHoursTable;
use rest\resourceRequestRecord;

$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : false;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : false;

$totalHours = isset($_GET['totalHours']) ? $_GET['totalHours'] : 0;

$hrsType = isset($_GET['hrsType']) ? $_GET['hrsType'] : resourceRequestRecord::HOURS_TYPE_REGULAR;

$trace = true;

?>
<div class="container-fluid">
	<div class="row">
        <div class="col-md-4">

<?php

        echo "--------------------------------------------------------------------------------------------";
echo '<br/>Hours Type for selected period of time';
echo "<br/>--------------------------------------------------------------------------------------------";
?>
<form id="hrsType" method="GET">
    <p>
        <label for="startDate">Start Date</label>
        <input type="text" name="startDate" id="startDate" value="<?=$startDate?>"/>
    </p>
    <p>
        <label for="endDate">End Date</label>
        <input type="text" name="endDate" id="endDate" value="<?=$endDate?>"/>
    </p>
    <p>
        <label for="totalHours">Total Hours</label>
        <input type="text" name="totalHours" id="totalHours" value="<?=$totalHours?>"/>
    </p>
    <p>
    <label for="hrsType" id="hrsType">Hours Type</label>
        <select name="hrsType">
        <?php
            foreach(resourceRequestRecord::$allHourTypes as $type) {
                if ($type == $hrsType) {
                    $selected = ' selected ';
                } else {
                    $selected = null;
                }
            ?>
                <option value="<?=$type?>" <?=$selected?>><?=$type?></option>
            <?php
            }
        ?>
        </select>
    </p>
    <p>
        <button type="submit">Check</button>
    </p>
</form>

<script type='text/javascript'>
    $(document).ready(function() {
        // startDate
        var picker = new Pikaday({
            field: document.getElementById('startDate'),
            format: 'YYYY-MM-DD',
            showTime: false,
            onSelect: function() {
                console.log(this.getMoment().format('Do MMMM YYYY'));
                var db2Value = this.getMoment().format('YYYY-MM-DD')
                console.log(db2Value);
                jQuery('#startDate').val(db2Value);
            }
        });
        jQuery('#calendarIconstartDate').click(function(){
            jQuery('#startDate').click();
        });

        // endDate
        var picker = new Pikaday({
            field: document.getElementById('endDate'),
            format: 'YYYY-MM-DD',
            showTime: false,
            onSelect: function() {
                console.log(this.getMoment().format('Do MMMM YYYY'));
                var db2Value = this.getMoment().format('YYYY-MM-DD')
                console.log(db2Value);
                jQuery('#endDate').val(db2Value);
            }
        });
        jQuery('#calendarIconendDate').click(function(){
            jQuery('#endDate').click();
        });
    });
</script>
<?php

if ($startDate && $endDate) {
    
    $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);

    switch ($hrsType) {
        case 'reg':
        case resourceRequestRecord::HOURS_TYPE_REGULAR:
            
            $hoursData = $resourceHoursTable->createResourceRequestHours('TEST', $startDate, $endDate, $totalHours, false, resourceRequestRecord::HOURS_TYPE_REGULAR, $trace, false);

            break;
        case 'weekday':
        case resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY:

            $hoursData = $resourceHoursTable->createResourceRequestHours('TEST', $startDate, $endDate, $totalHours, false, resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY, $trace, false);

            break;
        case 'weekend':
        case resourceRequestRecord::HOURS_TYPE_OT_WEEK_END:

            $hoursData = $resourceHoursTable->createResourceRequestHours('TEST', $startDate, $endDate, $totalHours, false, resourceRequestRecord::HOURS_TYPE_OT_WEEK_END, $trace, false);

            break;
        default:
            break;
    }

    list(
        'messages' => $messages,
        'summary' => $summary,
        'weeksCreated' => $weeksCreated
    ) = $hoursData;
    ?>

            </div>
            <div class="col-md-4">
                <br/>----------------------------------------------
                <br/><b>Function final output</b>
                <br/>----------------------------------------------
                <br/>
                <?=$summary;?>
            </div>
            <div class="col-md-4">
                <?=$messages;?>
            </div>
        </div>
    </div>

    <?php
    if ($trace) {
        echo "<br/>----------------------------------------------";
        echo '<br/>Amount of hours records created :'.$weeksCreated;
    }

}
?>