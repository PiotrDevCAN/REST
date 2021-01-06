<?php
namespace itdq;


use rest\allTables;

class DateClass {
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

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
    
    /*
     * 
     * The basic premis is : 
     *  Get an exact number of weeks between the two dates, by rolling forward to a Monday (If it's not already a Monday) and back to a Sunday ( If it's not already a Sunday)
     *  Each Week contains 5 Mon-Fri days.
     *  But, because we "rolled FORWARD to a Monday" - we've potentially not counted a number of working days prior to the Monday.
     *      To allow for this..... Work out how many days between The "Monday" and the actual Start Date. THis could include a Sat and/or a Sunday, so we have to remove 1 or 2 days if it does
     *                             this gives us the "excess" days to add on from the "first Week"
     *      We have a similar issues caused by rolling back to a Sunday, we've potentially missed some working days in the last week, so work them out too.
     *      
     *      Our final calculation is : 
     *        Number of Weeks multiplied by 5 MINUS the "excess" days in the first week picked up by rolling forward to Monday PLUS the "missing" days from the last week caused by rolling back to Sunday
     * 
     */
    
    static function workingDaysFromStartToEnd(\DateTime $startDate, \DateTime $endDate){
  
        // Calculates the number of Mon to Fri days between 2 dates.
        $startDate->setTime(0,0);
        $endDate->setTime(0,0);
        
        $adjustedStartDate = clone $startDate;
        if($adjustedStartDate->format('N')!=1){
            // If it's not a Monday, roll back to the previous Monday
            $adjustedStartDate->modify('previous Monday');
        };        
        
        $adjustedEndDate = clone $endDate;
        if($endDate->format('N')!=7){
            // If it's not a Sunday, roll back to the previous Sunday
            $adjustedEndDate->modify('previous Sunday');
        };
        
        $dateDiff = $adjustedEndDate->diff($adjustedStartDate);
        $totalDays = ($dateDiff->days)+1;  /// 2nd to the 4th is 3 days, 2nd, 3rd, and 4th but 4-2 = 2 we're really doing 4-(2-1) or (4-2)+1;
        
        $excessFirstWeekDays = ($adjustedStartDate->diff($startDate)->days); 
        $excessFirstWeekDays = $startDate->format('N')== 7 ? ($excessFirstWeekDays - 1)  : $excessFirstWeekDays; // If the start date was a Sat or Sun we need to reduce the diff by 1 or 2 as appropriate.
        
        $missingLastWeekDays  = ($endDate->diff($adjustedEndDate)->days);
        $missingLastWeekDays  = $endDate->format('N')== 6 ? $missingLastWeekDays-1 : $missingLastWeekDays; // If they'd chosen to end on a Sat, we'll have allowed 1 too many business days, so remove it.
        
        $weeks = (int)($totalDays / 7);
        
        
//         echo "<br/>Start:" . $startDate->format('d-M-Y') . " Adjusted:" . $adjustedStartDate->format('d-M-Y');
//         echo "<br/>End:" . $endDate->format('d-M-Y') . " Adjusted:" . $adjustedEndDate->format('d-M-Y');
//         echo "<br/>Total:$totalDays Weeks:$weeks Pre:$excessFirstWeekDays Post:$missingLastWeekDays";
        
        
        return (($weeks*5)-$excessFirstWeekDays+$missingLastWeekDays);
        
    }
    
    static function weekendDaysFromStartToEnd(\DateTime $startDate, \DateTime $endDate){
        // Calculates the number of Sat & Sun days between 2 dates.
        
        $startDate->setTime(0,0);
        $endDate->setTime(0,0);        
        
        $adjustedStartDate = clone $startDate;  
        $firstWeekDays = 0;
        if($adjustedStartDate->format('N')!=1){
            // If it's not a Monday, roll back to the previous Monday
            $adjustedStartDate->modify('previous Monday');
            //  
            $firstWeekDays = $startDate->format('N')==7 ? -1 : 0;
        }
        
        $adjustedEndDate = clone $endDate;
        $lastWeekDays = 0; // default
        if($endDate->format('N')!=7){
            // If it's not a Sunday, roll back to the previous Sunday  
            $adjustedEndDate->modify('previous Sunday');
            // 
            $lastWeekDays = $endDate->format('N')>5 ?  ($endDate->format('N')-5) : 0;
        }
        $dateDiff = $adjustedEndDate->diff($adjustedStartDate);
        $totalDays = ($dateDiff->days)+1;  /// 2nd to the 4th is 3 days, 2nd, 3rd, and 4th but 4-2 = 2 we're really doing 4-(2-1)
        
      
        $weeks = (int)($totalDays / 7);
        
//         echo "<br/>Start " . $startDate->format('d-m-Y') . " Adjusted Start" . $adjustedStartDate->format('d-m-Y') . "<br/>End " . $endDate->format('d-m-Y') . " Adjusted End" . $adjustedEndDate->format('d-m-Y');
 
        
//         echo "<br/>Weeks " . $weeks;
//         echo "<br/>FirstweekDays " . $firstWeekDays;
//         echo "<br/>LastweekDays " . $lastWeekDays;
        
        
         return ($weeks*2) + $lastWeekDays + $firstWeekDays; //
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
            $data[] = $row['BH_DATE'];
        }
        
        return $data != null ? $data : false;       
        
    }

}