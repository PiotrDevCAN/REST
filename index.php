<?php
// do_auth($_SESSION['userBg']);
$auth = new Auth();
if(!$auth->ensureAuthorized()){
    var_dump($auth);
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


