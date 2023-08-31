<?php
namespace itdq;
use itdq\AllItdqTables;
/**
 * Interfaces to the DIARY table, basically by inserting entries.
 *
 * @author GB001399
 * @package esoft
 *
 */
class DiaryTable  extends DbTable {

	static function insertEntry( $entry) {

		// --- temporary walkaround ---

		$latestDiaryIdSql = "SELECT MAX(DIARY_REFERENCE) as LATEST_DIARY_REF "; 
        $latestDiaryIdSql.= " FROM " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$DIARY;

		$rs = DB2_EXEC ( $GLOBALS['conn'], $latestDiaryIdSql );
		if (! $rs) {		
			echo "<BR/>" . print_r(sqlsrv_errors());
			echo "<BR/>" . print_r(sqlsrv_errors()) . "<BR/>";
			exit ( "Error in: " . __METHOD__ . " running: " . $sql );
		}

		while(($row = sqlsrv_fetch_array($rs))==true){
			$latestDiaryId = $row['LATEST_DIARY_REF'];
		}
		$latestDiaryId++;

		$sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$DIARY . " ( DIARY_REFERENCE, ENTRY, CREATOR ) ";
		$sql .= " Values ('" . htmlspecialchars(trim($latestDiaryId)) . "', '" . htmlspecialchars(trim($entry)) . "','" . htmlspecialchars($_SESSION['ssoEmail']) . "' ) ";
		
		// --- temporary walkaround ---

		// $sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$DIARY . " ( ENTRY, CREATOR) ";
		// $sql .= " Values ('" . htmlspecialchars(trim($entry)) . "','" . htmlspecialchars($_SESSION['ssoEmail']) . "' ) ";

		$rs = DB2_EXEC ( $GLOBALS['conn'], $sql );
		if (! $rs) {		
			echo "<BR/>" . print_r(sqlsrv_errors());
			echo "<BR/>" . print_r(sqlsrv_errors()) . "<BR/>";
			exit ( "Error in: " . __METHOD__ . " running: " . $sql );
		}
		return	db2_last_insert_id($GLOBALS['conn']);

	}
}

?>