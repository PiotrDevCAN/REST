<?php
$conn_parms = "connect_" . $_SERVER['environment'] . ".php";

include "$conn_parms"; // include the connect_$country.php file (relevant connection file)

//$conn = db2_connect($system, $userid, $pwd); // connect to DB2 itself
$conn=true;
if ($conn) {
    // echo "Connection succeeded.<BR>";
    // if connection ok then connect to the relevant app schema (prefix is defined in the relevant connect_$country.php file

//     $Statement = "SET CURRENT SCHEMA='" . strtoupper($_SESSION['Db2Schema']) . "';";
//     $rs = db2_exec($conn, $Statement);
//     if (! $rs) {
//         echo "<br/>" . $Statement . "<br/>";
//         echo "<BR>" . db2_stmt_errormsg() . "<BR>";
//         echo "<BR>" . db2_stmt_error() . "<BR>";
//         exit("Set current schema failed");
//     }
    $_SESSION['conn'] = $conn;
} else {
    echo "<BR>" . "Connection Error Message :";
    echo db2_conn_errormsg();
    echo "<BR>" . "Connection Error Code :";
    echo db2_conn_error();
    echo "Connection failed.<BR>";
    exit();
}
// db2_autocommit($conn, TRUE); // This is how it was on the Wintel Box - so the code has no/few commit points.

?>