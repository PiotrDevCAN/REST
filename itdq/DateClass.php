<?php
namespace itdq;


use rest\allTables;

class DateClass {

    static function weekEnding($date){
        $dateObj = new \DateTime($date);

        $monday = new \DateTime($dateObj->format('Y-m-d'));
        $monday->modify('next monday');
        $weekEndingFriday = new \DateTime($monday->format('Y-m-d'));
        $weekEndingFriday->modify('previous friday');

        if($weekEndingFriday < $dateObj){
            $weekEndingFriday->add(new \DateInterval('P1W'));
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
        $lastMondayOfMonthObj->modify('previous monday');
        $claimCutofFriday = new \DateTime($lastMondayOfMonthObj->format('Y-m-d'));
        $claimCutofFriday->modify('previous friday');

        if($dateObj > $claimCutofFriday){
            $claimCutofFriday = self::claimMonth($nextMonthString); // We've past the cutoff - get the next cutoff
        }

        return $claimCutofFriday;
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
    static function businessDaysFromStartToEnd(\DateTime $startDate, \DateTime $endDate){
        $workingDays  = self::workingDaysFromStartToEnd($startDate, $endDate);        
        $bankHolidays = self::bankHolidaysFromStartToEnd($startDate, $endDate);        
        $businessDays = $bankHolidays ? $workingDays - count($bankHolidays) : $workingDays;        
        return array('businessDays' => $businessDays, 'workingDays'=> $workingDays, 'bankHolidays' => $bankHolidays);        
    }
    
    static function workingDaysFromStartToEnd(\DateTime $startDate, \DateTime $endDate){
        // Calculates the number of Mon to Fri days between 2 dates.
        $adjustedStartDate = clone $startDate;
        $adjustedStartDate->modify('next Monday');
        
        $adjustedEndDate = clone $endDate;
        $adjustedEndDate->modify('previous Sunday');
        
        $dateDiff = $adjustedEndDate->diff($adjustedStartDate);
        $totalDays = ($dateDiff->days)+1;  /// 2nd to the 4th is 3 days, 2nd, 3rd, and 4th but 4-2 = 2 we're really doing 4-(2-1)
        
        $firstWeekDays = $adjustedStartDate->diff($startDate)->days;
        $lastWeekDays  = $endDate->diff($adjustedEndDate)->days;
        
        $weeks = (int)($totalDays / 7);
        
        return (($weeks*5)+$firstWeekDays-2+$lastWeekDays);
        
    }

    static function bankHolidaysFromStartToEnd(\DateTime $startDate, \DateTime $endDate){       
        
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$BANK_HOLIDAYS;
        $sql.= " WHERE BH_DATE >= DATE('" . $startDate->format('Y-m-d') . "') AND BH_DATE <= DATE('" . $endDate->format('Y-m-d') . "') ";
        $sql.= " ORDER BY 1 ";
        
        $rs = db2_exec($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        
        $data = array();

        while (($row = db2_fetch_assoc($rs))==true) {
            print_r($row);
            $data[] = $row['BH_DATE'];
        }
        
        return $data != null ? $data : false;       
        
    }

}