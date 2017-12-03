<?php
// do_auth($_SESSION['userBg']);
include realpath(dirname(__FILE__))."/class/include.php";
$auth = new Auth();
if(!$auth->ensureAuthorized()){
    die('Invalid logon attempt');
} else {
    $GLOBALS['ltcuser']['mail'] = $_SESSION['ssoEmail'];
    var_dump($_SESSION);
}

?>


<style type="text/css" class="init">
body {
	background: url('./public/img/rawpixel-com-284723.jpg')
		no-repeat center center fixed;
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
}
</style>

<div class="container">
	<div class="jumbotron">
		<h4 id='welcomeJumotron'>Aurora Resource Tracker</h4>
	</div>
</div>
<!-- /.container-fluid -->


