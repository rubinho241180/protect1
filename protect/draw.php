<?php 

$timestamp = date('Y-m-d H:i:s');

require_once "db.php";

$id = 2428;

$draw = $ndb->draw()->where("id", $id)->fetch();

if ($draw)
{
    $draw["timestamp"] = "2020-08-18 22:00:00";
    $draw->update();
    echo "updated";
} else 
{
    echo "fail";
}

