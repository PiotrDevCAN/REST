<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

include realpath(dirname(__FILE__))."/class/include.php";
$auth = new Auth();
if($auth->ensureAuthorized())
{
    echo "Logged in. Welcome " . htmlentities($_SESSION['firstName']) . " " . $_SESSION['lastName'] . ".";
    if(isset($_SESSION['somethingChanged']))
    {
        echo "<br/><br/><span style='font-weight:bold;'>Warning: </span> The values that are returned from w3ID/IBMID has probably been changed.<br/><br/>No need to panic, this is very easy to fix.<br/>This is your session currently:<br/><br/><code>";
        var_dump($_SESSION);
        echo "</code><br/><br/>" . "Everything you see there except <span style='font-weight:bold;'>somethingChanged</span> is coming from w3ID/IBMID service.";
        echo '<br/>You now need to look into <span style="font-weight:bold;">private function processOpenIDConnectCallback($data)</span> in <span style="font-weight:bold;">class/auth.class.php</span> and read the comments.';
        echo "<br/>Please keep in mind, that even if sign in is technically working now, you should not use the code in production without strict checking of those values.";
        echo "<br/><br/>";
        echo "What you can do now is:";
        echo "<br/>a) Paste this warning message to <a href='https://github.ibm.com/CWT/auth-openidconnect-w3/issues/' target='_blank'>GitHub Issues</a> and wait for it to be fixed.";
        echo '<br/>b) Very easily adjust the code in private function processOpenIDConnectCallback($data) with new and correct values and <a href="https://github.ibm.com/CWT/auth-openidconnect-w3/issues/" target="_blank">open a new issue</a> or <a href="https://github.ibm.com/CWT/auth-openidconnect-w3/pulls" target="_blank">create a new pull request</a>.';
        echo '<br/><br/>Note: When trying to fix this yourself, do remember to always clear cookies when refreshing the page.';
    }

    ?><pre><?php
    print_r($_SESSION);
    ?></pre><?php

}

echo "<h2>Try to connect</h2>";

echo $_SESSION['VCAP_SERVICES'];

$conn = db2_connect("dashdb-txn-flex-yp-dal09-541.services.dal.bluemix.net", "bluadmin", "NmE1MzFlNDI0NTI3"); // connect to DB2 itself

if ($conn) {
    echo "Connection succeeded.<BR>";
    //if connection ok then connect to the relevant app schema (prefix is defined in the relevant connect_$country.php file

//         $Statement = "SET CURRENT SCHEMA='" . strtoupper($_SESSION['Db2Schema']) . "';";
//         $rs = db2_exec($conn, $Statement);
//         if (! $rs) {
//             echo "<br/>" . $Statement . "<br/>";
//             echo "<BR>" . db2_stmt_errormsg() . "<BR>";
//             echo "<BR>" . db2_stmt_error() . "<BR>";
//             exit("Set current schema failed");
//         }
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


?>