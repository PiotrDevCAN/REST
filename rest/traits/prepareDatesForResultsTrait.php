<?php

namespace rest\traits;

trait prepareDatesForResultsTrait
{
    function prepareDatesForResults($row){
        
        $startDate = !empty($row['START_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['START_DATE'])->format('d M Y') : null;
        $startDateSortable = !empty($row['START_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['START_DATE'])->format('Ymd') : null;
        $endDate = !empty($row['END_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['END_DATE'])->format('d M Y') : null;
        $endDateSortable = !empty($row['END_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['END_DATE'])->format('Ymd') : null;
        
        $dates = array(            
            'startDate' => $startDate,
            'startDateSortable' => $startDateSortable,
            'endDate' => $endDate,
            'endDateSortable' => $endDateSortable
        );

        return $dates;
    }
}