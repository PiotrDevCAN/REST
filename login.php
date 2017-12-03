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

$database = "BLUDB";        # Get these database details from
$hostname = "dashdb-txn-flex-yp-dal09-541.services.dal.bluemix.net";  # the web console
$user     = "bluadmin";   #
$password = "NmE1MzFlNDI0NTI3";   #
$port     = 50000;          #
$ssl_port = 50001;          #

# Build the connection string
#
$driver  = "DRIVER={IBM DB2 ODBC DRIVER};";
$dsn     = "DATABASE=$database; " .
"HOSTNAME=$hostname;" .
"PORT=$port; " .
"PROTOCOL=TCPIP; " .
"UID=$user;" .
"PWD=$password;";
$ssl_dsn = "DATABASE=$database; " .
"HOSTNAME=$hostname;" .
"PORT=$ssl_port; " .
"PROTOCOL=TCPIP; " .
"UID=$user;" .
"PWD=$password;" .
"SECURITY=SSL;";
$conn_string = $driver . $dsn;     # Non-SSL
$conn_string = $driver . $ssl_dsn; # SSL

# Connect
#
$conn = odbc_connect( $conn_string, "", "" );

var_dump($conn);




if( getenv( "VCAP_SERVICES" ) )
{
    # Get database details from the VCAP_SERVICES environment variable
    #
    # *This can only work if you have used the Bluemix dashboard to
    # create a connection from your dashDB service to your PHP App.
    #
    $details  = json_decode( getenv( "VCAP_SERVICES" ), true );
    $dsn      = $details [ "dashDB" ][0][ "credentials" ][ "dsn" ];
    $ssl_dsn  = $details [ "dashDB" ][0][ "credentials" ][ "ssldsn" ];

    # Build the connection string
    #
    $driver = "DRIVER={IBM DB2 ODBC DRIVER};";
    $conn_string = $driver . $dsn;     # Non-SSL
    $conn_string = $driver . $ssl_dsn; # SSL

    $conn = db2_connect( $conn_string, "", "" );
    if( $conn )
    {
        echo "<p>Connection succeeded.</p>";
        db2_close( $conn );
    }
    else
    {
        echo "<p>Connection failed.</p>";
    }
}
else
{
    echo "<p>No credentials.</p>";
}





?>