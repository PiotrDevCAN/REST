<?php
include "here.inc";
include "../here.inc";
include "../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php";

echo class_exists('PHPExcel_IOFactory');

include "../../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php";

echo class_exists('PHPExcel_IOFactory');

echo __DIR__;

