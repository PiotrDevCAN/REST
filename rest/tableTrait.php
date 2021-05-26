<?php

namespace rest;

trait tableTrait
{
    static function validateDate($date, $format = 'Y-m-d'){
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    static function prepareSearchPredicate() {
        // column filtering
        $columns = isset($_POST['columns']) ? $_POST['columns'] : false;
        $searchPredicate = "";
        if ($columns && is_array($columns)) {
            if (count($columns) > 0) {
                foreach($columns as $key => $column) {
                    // $columnName = $column['data'];
                    $columnName = $column['name'];
                    $searchable = $column['searchable'];
                    $searchValue = trim($column['search']['value']);
                    $searchRegex = $column['search']['regex']; // boolean
                    if (! strpos ( $columnName, "COMPLEX" )) {
                        if (!empty($searchValue)) {
                            // $searchPredicate .= " AND " . $columnName . " LIKE '%" . $searchValue . "%'";
                            $searchPredicate .= " AND REGEXP_LIKE(" .$columnName. ", '" .$searchValue. "', 1, 'i')";
                        }
                    }
                }
            }
        }
        return $searchPredicate;
    }

    static function prepareComplexSearchPredicate() {
        // column filtering
        $columns = isset($_POST['columns']) ? $_POST['columns'] : false;
        $complexSearchPredicate = "";
        if ($columns && is_array($columns)) {
            if (count($columns) > 0) {
                foreach($columns as $key => $column) {
                    $columnName = $column['name'];
                    $searchValue = trim($column['search']['value']);
                    if (!empty($searchValue)) {
                        switch($columnName) {
                            case 'RFS_AND_RESOURCE_REFERENCE_COMPLEX':
                                $complexSearchPredicate .= " AND REGEXP_LIKE(CONCAT(CONCAT(RFS.RFS_ID, ' : '), RR.RESOURCE_REFERENCE), '" .$searchValue. "', 1, 'i')";
                                break;
                            case 'TOTAL_HOURS_COMPLEX':
                                $complexSearchPredicate .= " AND REGEXP_LIKE(CONCAT('Total Hrs:', TOTAL_HOURS), '" .$searchValue. "', 1, 'i')";
                                break;
                            case 'START_DATE_COMPLEX':
                                // $complexSearchPredicate .= " AND REGEXP_LIKE(" .$columnName. ", '" .$searchValue. "', 1, 'i')";                            
                                break;
                            case 'END_DATE_COMPLEX':
                                // $complexSearchPredicate .= " AND REGEXP_LIKE(" .$columnName. ", '" .$searchValue. "', 1, 'i')";
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        }
        return $complexSearchPredicate;
    }

    static function prepareOrderingPredicate() {
        // column ordering
        $columns = isset($_POST['columns']) ? $_POST['columns'] : false;
        $ordering = isset($_POST['order']) ? $_POST['order'] : false;
        $orderPredicate = "";
        if ($ordering && is_array($ordering)) {
            if (count($ordering) > 0) {
                $orderPredicate .= " ORDER BY ";
                foreach($ordering as $key => $order) {
                    $column = isset($order['column']) ? $order['column'] : false;
                    $direction = isset($order['dir']) ? $order['dir'] : false;
                    if (array_key_exists($column, $columns) 
                        && $column !== false
                        && $direction !== false
                    ) {
                        // $columnName = $columns[$column]['data'];
                        $columnName = $columns[$column]['name'];
                        $orderable = $columns[$column]['orderable'];
                        if ($orderable == 'true') {
                            $orderPredicate .= " " . $columnName . " " . $direction;
                        }
                    }
                }
            }
        }
        return $orderPredicate;
    }

    static function prepareGlobalSearchPredicate() {
        // global filtering
        $globalSearch = isset($_POST['search']) ? $_POST['search'] : false;
        $globalSearchPredicate = "";
        if ($globalSearch && is_array($globalSearch)) {
            if (count($globalSearch) > 0) {
                $searchValue = trim($globalSearch['value']);
                $searchRegex = $globalSearch['regex']; // boolean
            }
        }
        return $globalSearchPredicate;
    }

}
