<?php

use itdq\WorkerAPI;

if(!isset($_SESSION['uid'])) {
	$_SESSION['uid'] = 'piotr.tajanowicz@kyndryl.com';
}

$start = microtime(true);

// $workerAPI = new WorkerAPI();
// $workerData = $workerAPI->getworkerByEmail($_SESSION['ssoEmail']);

// $elapsed = microtime(true);
// echo ("WorkerAPI call took:" . (float)($elapsed-$start));
// echo '</br>';

// echo '<pre>';
// echo 'ENVIRONMENT <br>';
// var_dump($_ENV);
// echo 'SESSION <br>';
// var_dump($_SESSION);
// echo 'WORKER DATA <br>';
// var_dump($workerData);
// echo '</pre>';

echo 'Emails status: '.trim($_ENV['email']);

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


