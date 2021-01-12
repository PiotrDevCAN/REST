<?php  
use itdq\DateClass;
use rest\resourceRequestHoursTable;
use rest\resourceRequestHoursRecord;


$resourceRequestHours = new resourceRequestHoursRecord();
$startDate = DateTime::createFromFormat('d-m-y', '12-01-21');
$endDate = DateTime::createFromFormat('d-m-y','20-01-21');

$weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
$weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);
echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

$complimentaryDate = resourceRequestHoursTable::getDateComplimentaryFields($startDate, $resourceRequestHours);


var_dump($complimentaryDate);

$businessDaysInWeek = DateClass::businessDaysForWeekEndingFriday($complimentaryDate['WEEK_ENDING_FRIDAY'], $weekDays['bankHolidays'] ,$startDate, $endDate);

var_dump($businessDaysInWeek);

echo "<hr/>";

$complimentaryDate = resourceRequestHoursTable::getDateComplimentaryFields($endDate, $resourceRequestHours);

var_dump($complimentaryDate);

$businessDaysInWeek = DateClass::businessDaysForWeekEndingFriday($complimentaryDate['WEEK_ENDING_FRIDAY'], $weekDays['bankHolidays'] ,$startDate, $endDate);

var_dump($businessDaysInWeek);

echo "<hr/>";

$startDate->modify('+1 week');

$complimentaryDate = resourceRequestHoursTable::getDateComplimentaryFields($startDate, $resourceRequestHours);

var_dump($complimentaryDate);

$businessDaysInWeek = DateClass::businessDaysForWeekEndingFriday($complimentaryDate['WEEK_ENDING_FRIDAY'], $weekDays['bankHolidays'] ,$startDate, $endDate);

var_dump($businessDaysInWeek);


// $startDate = DateTime::createFromFormat('d-m-y', '25-12-20');
// $endDate = DateTime::createFromFormat('d-m-y','03-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);
// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

// $startDate = DateTime::createFromFormat('d-m-y', '26-12-20');
// $endDate = DateTime::createFromFormat('d-m-y','03-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

// $startDate = DateTime::createFromFormat('d-m-y', '27-12-20');
// $endDate = DateTime::createFromFormat('d-m-y','03-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);
// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";


// $startDate = DateTime::createFromFormat('d-m-y', '28-12-20');
// $endDate = DateTime::createFromFormat('d-m-y','03-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

// $startDate = DateTime::createFromFormat('d-m-y', '29-12-20');
// $endDate = DateTime::createFromFormat('d-m-y','03-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

// $startDate = DateTime::createFromFormat('d-m-y', '30-12-20');
// $endDate = DateTime::createFromFormat('d-m-y','03-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

// $startDate = DateTime::createFromFormat('d-m-y', '31-12-20');
// $endDate = DateTime::createFromFormat('d-m-y','03-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

// $startDate = DateTime::createFromFormat('d-m-y', '01-01-21');
// $endDate = DateTime::createFromFormat('d-m-y','03-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";
 
// $startDate = DateTime::createFromFormat('d-m-y', '02-01-21');
// $endDate = DateTime::createFromFormat('d-m-y','03-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

// $startDate = DateTime::createFromFormat('d-m-y', '01-01-21');
// $endDate = DateTime::createFromFormat('d-m-y','09-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

// $startDate = DateTime::createFromFormat('d-m-y', '01-01-21');
// $endDate = DateTime::createFromFormat('d-m-y','10-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

// $startDate = DateTime::createFromFormat('d-m-y', '01-01-21');
// $endDate = DateTime::createFromFormat('d-m-y','11-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";


// $startDate = DateTime::createFromFormat('d-m-y', '01-01-21');
// $endDate = DateTime::createFromFormat('d-m-y','12-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

// $startDate = DateTime::createFromFormat('d-m-y', '02-01-21');
// $endDate = DateTime::createFromFormat('d-m-y','11-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

// $startDate = DateTime::createFromFormat('d-m-y', '03-01-21');
// $endDate = DateTime::createFromFormat('d-m-y','11-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

// $startDate = DateTime::createFromFormat('d-m-y', '04-01-21');
// $endDate = DateTime::createFromFormat('d-m-y','10-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";


// $startDate = DateTime::createFromFormat('d-m-y', '04-01-21');
// $endDate = DateTime::createFromFormat('d-m-y','08-01-21');

// $weekendDays = DateClass::weekendDaysFromStartToEnd($startDate,$endDate);
// $weekDays    = DateClass::businessDaysFromStartToEnd($startDate, $endDate);

// echo "<h4> From : " . $startDate->format('d-M-Y') . " To: " . $endDate->format('d-M-Y') . " Weekend Days:" . $weekendDays .  " Business Days:" . $weekDays['businessDays'] . "</h4>";

