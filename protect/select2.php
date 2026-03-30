<?php 

header('Content-Type: application/json');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);


require_once "DateUtils.php";
require_once "AppProtectKey.php";
require_once "db.php";

use DateUtils\Date as DateUtils;
use DateUtils\Chronometer as Chron;
use AppProtectKey\MySerial as Serial;


$chro = new Chron();

$installations = 
	array(
	);

foreach ($ndb->ins()->where("cus_id = 1371 and app_id = 201")->order("id DESC")->limit(100) as $ins) {

	$keys =
		array(
		);

	foreach ($ndb->serial()->where("ins_id = ?", $ins["id"]) as $ser2) {

		array_push(
			$keys, 
			Serial::fetch($ser2)
		);
	}


    array_push(
    	$installations, 
    	array(
    		"id"   => $ins["id"],
    		"date" => DateUtils::date($ins["timestamp"]),
			"time" => DateUtils::time($ins["timestamp"]),
			"cust" => 
				array(
					"id"   => $ins["cus_id"],
					"name" => $ins->cus["name"],
					"emai" => $ins->cus["email"],
					"phon" => $ins->cus["phone"],
					"ddi"  => $ins->cus["ddi"],
					"ctry" => $ins->cus["country"],
					"city" => $ins->cus["city"],
					"stat" => $ins->cus["state"],
				),
			"devi" =>
				array(
					"id"   => $ins["mac_id"],
					"name" => $ins["mac_name"],
				),
			"appl" =>
				array(
					"id"   => $ins["app_id"],
					"name" => $ins->app["name"],
					"vers" => $ins["ver_id"],
				),
			
			"keys"	=> $keys,

			"cust_ins_count"	=> $ndb->ins()->where("cus_id = ? and id < ?", 				  array($ins["cus_id"], $ins["id"]				  ))->count(),
			"devi_ins_count"	=> $ndb->ins()->where("cus_id = ? and id < ? and mac_id = ?", array($ins["cus_id"], $ins["id"], $ins["mac_id"]))->count(),
			"appl_ins_count"	=> $ndb->ins()->where("cus_id = ? and id < ? and app_id = ?", array($ins["cus_id"], $ins["id"], $ins["app_id"]))->count(),
    	)
    );
}




$json = 
	array(
		"installations" => $installations,
		"metrics"   => 
			array(
				"elapsed" => $chro->seconds(),

			)
	);


echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);







