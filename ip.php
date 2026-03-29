<?php 

header('Content-Type: application/json');

include "functions.php";


$json = ip_to_geo("177.79.84.167");


var_dump($json);