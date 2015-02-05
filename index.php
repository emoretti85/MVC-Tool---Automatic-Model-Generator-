<?php
require_once 'modelGenerator.class.php';

$db = new PDO("mysql:host=localhost;dbname=timebarter", 'root', '', null);
$table='old_log_table';
$outPath='ModelFolder/';    

$myModelGenerator= new ModelGenerator($db, $outPath);
$myModelGenerator->createModelFromTableOrView($table);