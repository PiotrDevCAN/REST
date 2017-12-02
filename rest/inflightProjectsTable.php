<?php
namespace rest;

use itdq\DbTable;

class inflightProjectsTable extends DbTable
{

    function populateFromWorksheet(\PHPExcel_Worksheet $objWorksheet){
        $firstRow = true;
        $columnHeadings = null;
        $rowCounter = 0;
        foreach ($objWorksheet->getRowIterator() as $row) {
            set_time_limit(120);
            if($firstRow){
                $headerRow = $row;
                $columnRef = 1;
                $cellIterator = $headerRow->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $columnHeadings[$columnRef++] = DbTable::toColumnName($cell->getValue());
                }
                $firstRow = false;
                $this->clear(false);
            } else {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $record = null;
                $columnRef = 1;
                foreach ($cellIterator as $cell) {
                    $rowDataAsArray[$columnHeadings[$columnRef++]] = $cell->getValue();
                }
                $inflightProjectsRecord = new inflightProjectsRecord();
                $inflightProjectsRecord->setFromArray($rowDataAsArray);
                $this->insert($inflightProjectsRecord);
                $rowCounter++;
            }
        }
        echo "<h4>Total Rows processed : $rowCounter</h4>";
    }


    function buildHTMLTable(){
        ?>
        <table id='inflightProjectsTable_id' class="table table-striped table-bordered" cellspacing="0" width="100%">
		<thead>
   		<?php
    		inflightProjectsRecord::htmlHeaderRow();
    		?>
    	</thead>
    	<tbody>
    		<tr><td>IT Retail & Consumer Finance CIO</td><td>Direct Recharge</td><td>Consumer Finance</td><td>Clissold, Nicholas</td><td>AC089</td><td>Foxtrot</td><td>NIF0174</td><td>Non-Investment Funded</td><td>Gold</td><td>-- --</td><td>Conrad Murkitt</td><td>CFU631</td><td>Foxtrot</td><td>IT Infrastructure Technology Services</td><td>ITS - Infrastructure Delivery</td><td>ID - PM & Architects</td><td>Named Resource</td><td>Contractor</td><td>YES</td><td>8721572</td><td>Conrad Murkitt</td><td></td><td>Sanderson PLC</td><td>_Infrastructure Delivery Manager</td><td>40</td><td>0</td><td>8</td><td>0</td><td>8</td><td>8</td><td>0</td><td>8</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>16</td><td>0</td><td>16</td><td>16</td><td>IBM<td></tr>
    	</tbody>
		</table>
        <?php
    }

    function returnAsArray($predicate=null,$selectableColumns='*'){
        $sql = " SELECT " . $selectableColumns;
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= !empty($predicate) ? " WHERE $predicate " : null;

        $resultSet = $this->execute($sql);

        $resultSet ? null : die("SQL Failed");

        $allData = null;

        while(($row = db2_fetch_array($resultSet))==true){
            $allData[]  = $row;
        }

        echo $sql;

        return $allData;
    }



}