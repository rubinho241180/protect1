<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-type: application/json');

require_once "db.php";
require_once "const.php";
require_once "functions.php";

$downloads = $ndb->downloads()->order('id DESC')->limit(50);

$json = [];

foreach ($downloads as $download) {
$json[] =
  //  array_push(
        array(
            'id' => $download['id'],
            'ip' => $download['ip'],
            'started' => $download['timestamp'],
            'finished' => $download['finished'],
        );
        
//    );
}

echo json_encode($json);
