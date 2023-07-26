<?php

use rest\allTables;
use rest\rfsTable;

$totalCounter = 0;
$archivedCounter = 0;
$notArchivedCounter = 0;

$rfsTable = new rfsTable(allTables::$RFS);
$filename = "../public/data/archive/" . $_POST['filename'];

$myFile = new SplFileObject($filename);
while (!$myFile->eof()) {
    $rfsId = trim($myFile->fgets());
    if (!empty($rfsId)) {
        if ($rfsTable->archiveRfs($rfsId)) {
            $archivedCounter ++;
        } else {
            $notArchivedCounter ++;
        }
        $totalCounter ++;
    }
}

echo '<B>' . $totalCounter . ' record(s) read from file</B>';
echo '<BR/><B>' . $archivedCounter . ' record(s) archived</B>';
echo '<BR/><B>' . $notArchivedCounter . ' record(s) not archived</B>';