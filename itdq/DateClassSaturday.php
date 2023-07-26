<?php
namespace itdq;


use rest\allTables;
use rest\bankHoliday;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;

class DateClassSaturday {

    const WORKING_WEEK_LENGTH = 5; // Mon - Fri
    // const WORKING_WEEK_LENGTH = 6; // Mon - Sat
    // const WEEKEND_LENGTH = 2; // Sat - Sun
    const WEEKEND_LENGTH = 1; // Sun

    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    const REGULAR_DAYS = [1, 2, 3 ,4, 5]; // Mon - Fri
    const WEEK_DAYS = [6]; // Saturday only
    const WEEKEND_DAYS = [7]; // Sundays only

    const DAYS_NAMES = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturnday',
        7 => 'Sunday'
    ];

    static function weekEndingFriday($date){
        $dateObj = new \DateTime($date);

        $monday = new \DateTime($dateObj->format('Y-m-d'));
        $monday->modify('next monday');
        $weekEndingFriday = new \DateTime($monday->format('Y-m-d'));
        $weekEndingFriday->modify('previous Friday');

        $oneWeek = new \DateInterval('P1W');
        if($weekEndingFriday < $dateObj){
            $weekEndingFriday->add($oneWeek);
        }
        return $weekEndingFriday;
    }

    static function claimMonth($date){
        $dateObj = new \DateTime($date);
        $month = $dateObj->format('m');
        $year = $dateObj->format('Y');

        $nextMonth = $month + 1;
        if($nextMonth>12){
            $nextMonth = 1;
            $year++;
        }

        $nextMonthString = $year . "-" . $nextMonth . "-01";
        $nextMonthObj = new \DateTime($nextMonthString);
        $lastMondayOfMonthObj = new \DateTime($nextMonthObj->format('Y-m-d'));
        $lastMondayOfMonthObj->modify('previous Monday');
        $claimCutofFriday = new \DateTime($lastMondayOfMonthObj->format('Y-m-d'));
        $claimCutofFriday->modify('previous Friday');

        if($dateObj > $claimCutofFriday){
            $claimCutofFriday = self::claimMonth($nextMonthString); // We've past the cutoff - get the next cutoff
        }

        return $claimCutofFriday;
    }

    static function setStartDate(\DateTime $startDate){
        $adjustedDate = clone $startDate;
        if($adjustedDate->format('N') != 1){
            // If it's not a Monday, roll back to the previous Monday
            $adjustedDate->modify('previous Monday');
        };
        return $adjustedDate;
    }
    
    static function setEndDate(\DateTime $endDate){
        $adjustedDate = clone $endDate;
        if($endDate->format('N') != self::SUNDAY){
            // If it's not a Sunday, roll back to the previous Sunday
            $adjustedDate->modify('previous Sunday');
        };
        return $adjustedDate;
    }

    static function calculateDates(\DateTime $startDate, \DateTime $endDate, $trace = false){

        //// Calculates the number of Mon to Fri days between 2 dates.
        // Calculates the number of Mon to Sat days between 2 dates.
        $startDate->setTime(0,0);
        $endDate->setTime(0,0);

        $adjustedStartDate = self::setStartDate($startDate);
        $adjustedEndDate = self::setEndDate($endDate);

        $dateDiff = $endDate->diff($startDate)->days;
        $totalDays = $dateDiff+1;  // 2nd to the 4th is 3 days, 2nd, 3rd, and 4th but 4-2 = 2 we're really doing 4-(2-1) or (4-2)+1;
        $weeks = (int)($totalDays / 7);

        $adjustedDateDiff = $adjustedEndDate->diff($adjustedStartDate)->days;
        $adjustedTotalDays = $adjustedDateDiff+1;  // 2nd to the 4th is 3 days, 2nd, 3rd, and 4th but 4-2 = 2 we're really doing 4-(2-1) or (4-2)+1;
        $adjustedWeeks = (int)($adjustedTotalDays / 7);

        if ($trace) {
            echo "<br/>----------------------------------------------";
            echo "<br/>Start: <b>" . $startDate->format('d-M-Y') . "</b> Adjusted Start: <b>" . $adjustedStartDate->format('d-M-Y') . "</b>";
            echo "<br/>End: <b>" . $endDate->format('d-M-Y') . "</b> Adjusted End: <b>" . $adjustedEndDate->format('d-M-Y') . "</b>";
            
            echo "<br/>Original Total: " . $totalDays;
            echo "<br/>Adjusted Total: " . $adjustedTotalDays;
            
            echo "<br/>Original Weeks: " . $weeks;
            echo "<br/>Adjusted Weeks: " . $adjustedWeeks;
        }

        return array(
            'startDate' => $startDate,
            'endDate' => $endDate,
            'adjustedStartDate' => $adjustedStartDate,
            'adjustedEndDate' => $adjustedEndDate,
            'dateDiff' => $dateDiff,
            'originalTotalDays' => $totalDays,
            'totalDays' => $adjustedTotalDays,
            'originalWeeks' => $weeks,
            'weeks' => $adjustedWeeks
        );
    }

    static function adjustStartDate(\DateTime $startDate, $hrsType = null, $trace = false){
        // If they enter Week End Overtime, then they have to start on a Saturday or Sunday
        //        
        // If they don't enter Week End OVertime, they CAN'T START on a Saturday or Sunday
        //
        $adjustedStartDate = clone $startDate;
        
        if ($trace) {
            echo '<br/>----------------------------------------------';
            echo '<br/>';
            echo ' date before adjustments '.$adjustedStartDate->format('Y-m-d');
        }

        /*
        if($hrsType == resourceRequestRecord::HOURS_TYPE_OT_WEEK_END){
            if($adjustedStartDate->format('N') < self::SATURDAY){
                // If it's not a Saturday/Sunday then roll forward to next Saturday
                $adjustedStartDate->modify('next Saturday');
            }
        } else {
            if($adjustedStartDate->format('N') > 5){
                // If it's a weekend, roll forward to the next Monday
                $adjustedStartDate->modify('next Monday');
            };
        }
        */
        
        switch ($hrsType) {
            case resourceRequestRecord::HOURS_TYPE_REGULAR:
                $dayOfWeek = 1;
                $startDay = 'Monday';
                if($adjustedStartDate->format('N') > 5){
                    // If it's a weekend, roll forward to the next Monday
                    $adjustedStartDate->modify('next Monday');
                };
                break;
            case resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY:
                $dayOfWeek = 6;
                $startDay = 'Saturday';
                if($adjustedStartDate->format('N') != self::SATURDAY){
                    // If it's not a Saturday then roll forward to next Saturday
                    $adjustedStartDate->modify('next Saturday');
                }
                break;
            case resourceRequestRecord::HOURS_TYPE_OT_WEEK_END:
                $dayOfWeek = 7;
                $startDay = 'Sunday';
                if($adjustedStartDate->format('N') != self::SUNDAY){
                    // If it's not a Sunday then roll forward to next Sunday
                    $adjustedStartDate->modify('next Sunday');
                }
                break;
            default:
                break;
        }
 
        if ($trace) {
            echo '<br/>';
            echo ' date after adjustments '.$adjustedStartDate->format('Y-m-d');
        }
        return array(
            'dayOfWeek' => $dayOfWeek,
            'startDay' => $startDay,
            'adjustedDate' => $adjustedStartDate
        );
    }

    static function otherDaysForWeekEnding(string $weekEnding, $dayOfWeek, \DateTime $startDate, \DateTime $endDate, $nextPeriod, $endPeriod){
        if($endDate > $weekEnding){
            echo ' TEST 1 '.$dayOfWeek;
            // Includes whole weekend
            $businessDaysInWeek = 1;
        } else {
            switch ($endDate->format('N')) {
                case $dayOfWeek:
                    echo ' TEST 2 '.$dayOfWeek;
                    // Ends on a Saturday/Sunday
                    $businessDaysInWeek = 1;
                    break;
                default:
                    if($nextPeriod < $endPeriod) {
                        echo ' TEST 3 '.$dayOfWeek;
                        // Ends in the next period
                        $businessDaysInWeek = 1;
                    } else {
                        echo ' TEST 4 '.$dayOfWeek;
                        // Ends before the weekend starts
                        $businessDaysInWeek = 0;
                    }
                    break;
            }
        }
        return $businessDaysInWeek;
    }

    static function businessDaysForWeekEnding(string $weekEnding, array $bankHolidays, \DateTime $startDate, \DateTime $endDate){
        $wef = \DateTime::createFromFormat('Y-m-d', $weekEnding);
        $monday = clone $wef;
        $monday->modify('-4 days');
        $day = clone $monday;
        $day->setTime(0,0);
        $endDate->setTime(0, 0);
        $startDate->setTime(0, 0);
        $wef->setTime(0, 0);
        
        $weekDays = array();
      
        while($day < $startDate ){
            $day->modify('+1 day'); // Roll forward if needs be to the Start Date which could have been between Monday and Friday
        }
        while($day <= $endDate && $day <= $wef){            
            $weekDays[] = $day->format('Y-m-d');
            $day->modify('+1 day');
        }
        
        $workingDaysArray = array_diff($weekDays, $bankHolidays); // now remove any bankholidays
    
        return count($workingDaysArray);
    }

    static function getAmountOfDaysByTypeFromStartToEnd(\DateTime $startDate, \DateTime $endDate, $dayType = '', $trace = false){

        $calculationDate = clone $startDate;
        $days = 0;
        $daysList = array();

        for ($i = $calculationDate; $i <= $endDate; $i = $i->modify('+1 day')) {
            $day = $i->format('N');  // 1 (for Monday) through 7 (for Sunday)
            if ($day == $dayType) {
                $daysList[] = $i->format('Y-m-d');
                $days++;
            }
        }
        return array(
            'amount' => $days,
            'list' => $daysList
        );
    }

    static function workingDaysFromStartToEnd(\DateTime $startDate, \DateTime $endDate, $trace = false){
        $days = 0;
        $daysList = array();
        foreach (self::REGULAR_DAYS as $dayType) {
            $daysData= self::getAmountOfDaysByTypeFromStartToEnd($startDate, $endDate, $dayType, $trace);
            list(
                'amount'=> $perType,
                'list' => $perTypeDates
            ) = $daysData;

            $daysList = array_merge($daysList, $perTypeDates);
            $days += $perType;
        }
        return array(
            'amount' => $days,
            'list' => $daysList
        );
    }    

    static function weekendDaysFromStartToEnd(\DateTime $startDate, \DateTime $endDate, $trace = true){
        // Calculates the number of Sat & Sun days between 2 dates.
        $saturdayDaysData = self::getAmountOfDaysByTypeFromStartToEnd($startDate, $endDate, DateClass::SATURDAY, $trace);
        list(
            'amount'=> $saturdayDays,
            'list' => $saturdayDates
        ) = $saturdayDaysData;
        
        $sundayDaysData = self::getAmountOfDaysByTypeFromStartToEnd($startDate, $endDate, DateClass::SUNDAY, $trace);
        list(
            'amount'=> $sundayDays,
            'list' => $sundayDates
        ) = $sundayDaysData;

        $weekendDays = $saturdayDays + $sundayDays;
        $weekendDaysDates = array_merge($saturdayDates, $sundayDates);
        return array(
            'saturdayDays'=> $saturdayDays,
            'saturdayDaysDates'=>$saturdayDates,
            'sundayDays' => $sundayDays,
            'sundayDaysDates'=>$sundayDates,
            'weekendDays' => $weekendDays,
            'weekendDaysDates' => $weekendDaysDates,
        );     
    }
    
    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return number[]|unknown[]|number[][]|unknown[][]|boolean[]|mixed[][]
     * 
     * Get the number of Monday to Friday Days from workingDaysFromStartToEnd
     * Gets the Bank Holidays from the $BANK_HOLIDAYS table
     * Deducts number of Bank Holidays from the number of workingDays and
     * Returns and array containing : 
     *      'businessDays' Integer of number of non BH Mon-Fri between $startDate and $endDate
     *      'workingDays' Integer of number of Mon-Fri between $startDate and $endDate
     *      'bankHoldidays' Array of bankHolidays between $startDate and $endDate
     * 
     */
    static function businessDaysFromStartToEnd(\DateTime $startDate, \DateTime $endDate, $trace = false){
        $workingDaysData  = self::workingDaysFromStartToEnd($startDate, $endDate, $trace);
        list(
            'amount' => $workingDays,
            'list' => $workingDaysDates
        ) = $workingDaysData;
        
        $bankHolidaysData = bankHoliday::bankHolidaysFromStartToEnd($startDate, $endDate);
        list(
            'amount' => $bankHolidays,
            'list' => $bankHolidaysDates
        ) = $bankHolidaysData;

        if ($bankHolidays) {
            $businessDays = $workingDays - $bankHolidays;
            $businessDaysDates = array_diff($workingDaysDates, $bankHolidaysDates);
        } else {
            $businessDays = $workingDays;
            $businessDaysDates = $workingDaysDates;
        }
        
        return array(
            'workingDays'=> $workingDays,
            'workingDaysDates'=>$workingDaysDates,
            'businessDays' => $businessDays,
            'businessDaysDates' => $businessDaysDates,
            'bankHolidays' => $bankHolidays,
            'bankHolidaysDates' => $bankHolidaysDates,
        );
    }

    static function getAllTypesDaysFromStartToEnd(\DateTime $startDate, \DateTime $endDate, $trace = false){
        $businessDaysData  = self::businessDaysFromStartToEnd($startDate, $endDate, $trace);
        list(
            'workingDays'=> $workingDays,
            'workingDaysDates'=> $workingDaysDates,
            'businessDays' => $businessDays,
            'businessDaysDates' => $businessDaysDates,
            'bankHolidays' => $bankHolidays,
            'bankHolidaysDates' => $bankHolidaysDates,
        ) = $businessDaysData;

        $weekendDaysData = self::weekendDaysFromStartToEnd($startDate, $endDate, $trace);
        list(
            'saturdayDays'=> $saturdayDays,
            'saturdayDaysDates' => $saturdayDaysDates,
            'sundayDays' => $sundayDays,
            'sundayDaysDates' => $sundayDaysDates,
            'weekendDays' => $weekendDays,
            'weekendDaysDates' => $weekendDaysDates
        ) = $weekendDaysData;

        if ($trace) {
            echo "<br/>----------------------------------------------";
            echo "<br/><b>getAllTypesDaysFromStartToEnd</b>";
            echo "<br/>----------------------------------------------";
            echo '<br/>Working Days: '.$workingDays;
            echo '<ul>';
            foreach($workingDaysDates as $date) {
                echo '<li>'.$date.'</li>';
            }
            echo '</ul>';

            echo '<br/>Business Days: '.$businessDays;
            echo '<ul>';
            foreach($businessDaysDates as $date) {
                echo '<li>'.$date.'</li>';
            }
            echo '</ul>';
            
            echo '<br/>Bank Holiday Days: '.$bankHolidays;
            echo '<ul>';
            foreach($bankHolidaysDates as $date) {
                echo '<li>'.$date.'</li>';
            }
            echo '</ul>';
            
            echo '<br/>Saturday (Weekday) Days: '.$saturdayDays;
            echo '<ul>';
            foreach($saturdayDaysDates as $date) {
                echo '<li>'.$date.'</li>';
            }
            echo '</ul>';
            echo '<br/>Sunday (Weekend) Days: '.$sundayDays;
            echo '<ul>';
            foreach($sundayDaysDates as $date) {
                echo '<li>'.$date.'</li>';
            }
            echo '</ul>';
            echo '<br/>Weekend Days: '.$weekendDays;
            echo '<ul>';
            foreach($weekendDaysDates as $date) {
                echo '<li>'.$date.'</li>';
            }
            echo '</ul>';
        }

        $result = array_merge($businessDaysData, $weekendDaysData);
        return $result;
    }
}