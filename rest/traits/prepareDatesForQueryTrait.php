<?php

namespace rest\traits;

use itdq\DateClass;

trait prepareDatesForQueryTrait
{

    static function prepareDatesForQuery(){
        
        // The first month we need to show them is the CLAIM month they are currently in. 
        // So start with today, and get the next Claim Cut off - th
        
        $startMonthObj = new \DateTime();
        $thisMonthObj = new \DateTime();

        $thisYear = $thisMonthObj->format('Y');
        $thisMonth = $thisMonthObj->format('m');
        $thisDay = '01';
        $thisMonthObj->setDate($thisYear, $thisMonth, $thisDay);
        $thisMonthsClaimCutoff = DateClass::getThisMonthsClaimCutoff($thisMonthObj->format('d-m-Y'));

        $startMonthObj >= $thisMonthsClaimCutoff ? $startMonthObj = DateClass::addTime($startMonthObj, 0, 1, 0) : null;
        $startYear  = $startMonthObj->format('Y');
        $startMonth = $startMonthObj->format('m');
        
        $lastMonthObj = clone $startMonthObj;
        $lastMonthObj = DateClass::addTime($lastMonthObj, 0, 6, 0);
        $lastYear = $lastMonthObj->format('Y');
        $lastMonth = $lastMonthObj->format('m');
                 
        $nextMonthObj = clone $startMonthObj;
        $monthLabels = array();
        $monthDetails = array();
        
        for ($i = 0; $i < 6; $i++) {
            $monthLabels[] = $nextMonthObj->format('M_y');
            $monthDetails[$i]['year'] = $nextMonthObj->format('Y');
            $monthDetails[$i]['month'] = $nextMonthObj->format('m');
            $nextMonthObj = DateClass::addTime($nextMonthObj, 0, 1, 0);
        }
        
        $dates = array(            
            'monthLabels' => $monthLabels,
            'monthDetails' => $monthDetails,
            'startYear' => $startYear,
            'startMonth' => $startMonth,
            'lastYear' => $lastYear,
            'lastMonth' => $lastMonth
        );

        return $dates;
    }
}