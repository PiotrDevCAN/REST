<?php


use rest\resourceRequestDiaryTable;

$filename = "data/Set Resource Name to Unallocated.csv";
$file = fopen($filename,'r');

while(( $row = fgetcsv($file))==true){
   $ref = $row[0];
   $entry = "Resource Name was : " . $row[1];
   
   echo "\n" . $ref . ":" . $entry;
}






// resourceRequestDiaryTable::insertEntry($entry, $resourceRef)