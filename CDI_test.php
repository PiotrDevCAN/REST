<?php

$allBusinessUnits = array('Commercial','Cyber & TRP','Enterprise Transformation','Insurance & Enterprise Programmes','Retail');

$allTribes = array('CTB BU','Cloud Infra & Tooling','Commercial & Business Banking','Cross Platform (Managed Services)','Cross Platform (Project Services)'
    ,'Cyber & TRP','Enablement','Enterprise Transformation','Insurance & Enterprise'
    ,'Product & Architecture Management','Retail & Community Banking','SRE & Data Analyst'
    ,'Sandbox','Solutioning','Unix & Oracle','Wintel & SQL');

$results = array();
$bestMatches = array();
$start = microtime(true);
foreach ($allBusinessUnits as $businessUnit) {
    $bestMatch = 0;
    foreach ($allTribes as $tribe) {
        $match = similar_text($businessUnit, $tribe);
        if($match > $bestMatch){
            $bestMatch = $match;
            $bestMatches[$businessUnit] = $tribe . "(". $bestMatch . ")";
        }
        $results[$businessUnit][$tribe] = $match;        
    }
}
$elapsed  = microtime(true) - $start;

echo "<br/>Elapsed:" . $elapsed;


echo "<pre>";
print_r($results);
echo "</pre>";


echo "<pre>";
print_r($bestMatches);
echo "</pre>";
