<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-type: application/json');


define('REPOSITORY_PATH', getcwd().'/repository');

require "db.php";

$commands = $ndb->cmd()->where("app_id = 901")->order("ver_id");


$history = 
    [];

//MONTA
foreach ($commands as $cmd)
{
    $par1 = $cmd['par1'];

    if (!array_key_exists($par1, $history))
    {
        $history[$par1] = array(
            'versions' => []
        );
    }

    array_push(
        $history[$par1]['versions'],
        array(
            $cmd['ver_id'] => $cmd['md5'],
        )
    );
}

foreach ($history as $file => &$obj)
{
    $obj['ver'] = array_keys(end($obj['versions']))[0];
    $obj['md5'] = array_values(end($obj['versions']))[0];

    unset($obj['versions']);
}

echo json_encode($history);




exit;

foreach ($history as $file => &$hist)
{
    $ver_int  = end($hist['versions']);
    $major    = intval(substr($ver_int, 0, strlen($ver_int)-4));
    $minor    = intval(substr($ver_int, -4, 2));
    $revision = intval(substr($ver_int, -2));
    $ver_str  = $major.".".$minor.".".$revision;


    $VERSION_PATH = sprintf(REPOSITORY_PATH."/%d/%s/", 901, $ver_str);

    //REPLACE "\\" to "/"
    $file = str_replace("\\", "/", $file);

    $tar = $VERSION_PATH.$file; 


    $md5 = 'df';//md5_file( $tar );

    $hist['tar'] = $tar;
    $hist['md5'] = $md5;
}

echo json_encode($history);
