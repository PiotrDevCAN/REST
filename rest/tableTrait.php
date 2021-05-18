<?php

namespace rest;

trait tableTrait
{
    function prepareSearchPredicate() {
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

                    if (!empty($column['search']['value'])) {
                        $searchPredicate .= " AND " . $columnName . " LIKE '%" . $searchValue . "%'";
                    }
                }
            }
        }
        return $searchPredicate;
    }

    function prepareOrderingPredicate() {
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

    function prepareGlobalSearchPredicate() {
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
