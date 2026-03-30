<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');



$timestamp  = date('Y-m-d H:i:s');
$date       = date('Y-m-d');


$iso = (isset($_GET['iso'])) ? $_GET['iso'] : "BR";
$iso = "BR";


require_once "db.php";


$mccs = [
];

$_mccs = $ndb2->mccs()->where('country_id = ?', $iso);

foreach ($_mccs as $mcc)
{
    array_push(
        $mccs, 
        array(
            'id' => $mcc['id'],
            'carriers' => []
        )
    );
}



foreach ($mccs as &$mcc)
{

    $carriers  = [
    ];

    $_mncs = $ndb2->mncs()->where('mcc_id = ?', [$mcc['id']])->/*where('carriers.operational = 1')->*/select('min(mncs.id) as id, carriers.brand, carriers.operator, carriers.thumbnail')->group('carriers.brand, carriers.operator, carriers.thumbnail')->order('CHAR_LENGTH(carriers.brand)');

    foreach ($_mncs as $mnc)
    {

        
        $_mncs = $ndb2->mncs()->where('carriers_id = ?', md5(strtoupper($mnc['brand'].'_'.$mnc['operator'])));
        $mncs   = [];

        foreach ($_mncs as $mnc2)
        {
            array_push(
                $mncs,
                array(
                    'id' => $mnc2['id'],
                    'status' => (int)$mnc2['operational'],
                )
                
            );
        }





        $display = ($mnc['brand'] == '') ? explode(' ', $mnc['operator'])[0] : $mnc['brand'];


        $pattern = 
            $ndb2->patterns()->where('mnc_id = ?', $mnc['id'])->fetch();    


        array_push(
            $mcc['carriers'],
            array(
                'brand' => $display, 
                //'brand' => $mnc['brand'],
                'operator' => $mnc['operator'],
                'thumbnail' => $mnc['thumbnail'],
                'mnc' => $mncs,
                'patterns' => [
                    'interval1'        => (!!$pattern) ? (int)$pattern['interval1'     ] :  30,
                    'interval2'        => (!!$pattern) ? (int)$pattern['interval2'     ] :  45,
                    'small_limit'      => (!!$pattern) ? (int)$pattern['small_limit'   ] :   5,
                    'small_interval'   => (!!$pattern) ? (int)$pattern['small_interval'] :  60,
                    'daily_limit'      => (!!$pattern) ? (int)$pattern['daily_limit'   ] : 100,
                    'max_attemps'      => 1,
                    'max_targets_fail' => 0,
                ],
            )
        );

    }
    
}

$json = [
    'iso' => $iso,
    'mcc' => $mccs,
];


echo json_encode($json/*, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE*/);
