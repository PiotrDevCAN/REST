<?php
use itdq\WorkerAPI;

$workerAPI = new WorkerAPI();
$workerData = json_decode($workerAPI->getworkerByEmail('piotr.tajanowicz@kyndryl.com'));

echo '<pre>';
echo 'ENVIRONMENT <br>';
var_dump($_ENV);
echo 'SESSION <br>';
var_dump($_SESSION);
echo 'WORKER DATA <br>';
var_dump($workerData);
echo '</pre>';

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


