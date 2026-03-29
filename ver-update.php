<?php 

//header("Content-Type: text/html; charset=ISO-8859-1",true);

//header("Content-type: text/html;charset=utf-8");

//usleep(50000);

//sleep(5);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');





$app_id = $_GET["app_id"];

$ver_id = $_GET["ver_id"];



$cmds = array();

$ldir = array(); //logical dir

$idir = array(); //final dir







require_once "functions.php";

require_once "db.php";
require_once "NotORM.php";
$pdo = connect_pdo();
$orm = new NotORM($pdo);




// require 'predis-1.1.10/autoload.php';
// Predis\Autoloader::register();
// $options = [
//     'host'     => 'redis-14330.c308.sa-east-1-1.ec2.cloud.redislabs.com',
//     'port'     => 14330,
//     'password' => 'yiXLwRds7cnggJYRtCTyxwYZNYdAyMBX',
// ];
// $redis = new Predis\Client($options);




$sky = isset($_GET["skey"]) ? $_GET["skey"] : "";

$ser = $orm->seri("skey = ?", $sky)->fetch();



//if (!$ser) {echo 'yyy: '.$sky;} else {echo 'nnn: '.$sky;}



$ins = $orm->ins("id = ?", $ser["ins_id"])->fetch();

$cus = $orm->cus("id = ?", $ins["cus_id"])->fetch();



$cus_beta = $cus["beta"] == 1;





if ($cus_beta) {

	//$qry = $pdo->prepare("SELECT c.*, v.stop from cmd c inner join ver v on v.id = c.ver_id where c.app_id = :app_id order by v.id, c.id");

	// $qry = $pdo->prepare("SELECT c.*, 0 as stop from cmd c where c.app_id = :app_id order by c.ver_id, c.id");
	// $qry->execute(array(':app_id' => $app_id));

} else {

	//$qry = $pdo->prepare("SELECT c.*, v.stop from cmd c inner join ver v on v.id = c.ver_id where c.app_id = :app_id and v.beta = 0 order by v.id, c.id");

	// $qry = $pdo->prepare("SELECT c.*, 0 as stop from cmd c where c.app_id = :app_id order by c.ver_id, c.id");
	// $qry->execute(array(':app_id' => $app_id));



}

$qry = $pdo->prepare("SELECT c.*, 0 as stop, v.active from cmd c inner join ver v on v.id = c.ver_id where c.app_id = :app_id AND v.active = 1 AND v.minimal <= :ver_id order by v.id, c.id");
$qry->execute(array(':app_id' => $app_id, ':ver_id' => $ver_id));





$stop = false;



while ($row = $qry->fetch()) {



	if (!$stop) {



		$major    = intval(substr($row->ver_id, 0, strlen($row->ver_id)-4));

		$minor    = intval(substr($row->ver_id, -4, 2));

		$revision = intval(substr($row->ver_id, -2));

		

		$intver   = intval(

						sprintf('%02d', intval($major)).

						sprintf('%02d', intval($minor)).

						sprintf('%02d', intval($revision))

					);



		array_push(

			$cmds, 

			array(

				"ver" => $major.".".$minor.".".$revision,

				"int" => $intver,

				"cmd" => $row->cmd,

				"par1" => $row->par1,

				"par2" => $row->par2,

				"md5"  => $row->md5,

			)

		);

	}



	if (($row->stop == 1) && ($row->ver_id > $ver_id)) {

		$stop = true;

	}



}



//echo json_encode($cmds);

//exit;



foreach ($cmds as $cmd) {



	if (($cmd["int"] > $ver_id) && ($cmd["cmd"] == "ADD"))

	{

		$ldir[$cmd["par1"]]["add"][] =

		array(

			//"id" => $cmd["id"],

			//"app" => $cmd["app"],

			"int" => $cmd["int"],

			"ver" => $cmd["ver"],

			"cmd" => $cmd["cmd"], 

			"par1"=> $cmd["par1"], 

			"par2"=> $cmd["par2"],

			"md5" => $cmd["md5"],

		);

	} else



	if (($cmd["int"] > $ver_id) && ($cmd["cmd"] == "REN"))

	{

		$ldir[$cmd["par1"]]["ren"][] =

		array(

			"int" => $cmd["int"],

			"ver" => $cmd["ver"],

			"cmd" => $cmd["cmd"], 

			"par1"=> $cmd["par1"], 

			"par2"=> $cmd["par2"],

		);



		change_key($ldir, $cmd["par1"], $cmd["par2"]);

	} else



	if ($cmd["cmd"] == "DEL") {

		$ldir[$cmd["par1"]]["deleted"] = true;

		//change_key($ldir, $cmd["par1"], $cmd["par1"]."_"."DELETED_on_".$cmd["ver"]);

		unset($ldir[$cmd["par1"]]);

	}

}



//echo json_encode($ldir);

//exit;

foreach ($ldir as $ldr => $val) {

	if (!isset($val["deleted"])) {

		$pa1 = end($val["add"])["ver"]."\\".$val["add"][0]["par1"];


		$result = $orm->redis('sy_key = ?', [$pa1])->fetch();

		//$md5 = $redis->get($pa1);

		if ($result) {
			$md5 = $result['sy_value'];
		} else {

			$md5 = md5_file( getcwd()."/repository/".$app_id."/".str_replace("\\", "/", $pa1) );

			$orm->redis()->insert([
					'sy_key'   => $pa1,
					'sy_value' => $md5,
				]);
		}


		//$md5 = gettype($result);
		//$md5 = end($val["add"])["ver"]."\\".$val["add"][0]["md5"];

		array_push(

			$idir, 

			array(

				"pa1" => $pa1,
				"pa2" => $ldr,
				"md5" =>  $md5, //*/md5_file( getcwd()."/repository/".$app_id."/".str_replace("\\", "/", $pa1) ),

			)

		);

	}

}



//echo json_encode($idir);

//exit;





$json =

array(

	"version" => 

		array(

			"int" => end($cmds)["int"],

			"str" => end($cmds)["ver"],

			//"url" => "http://r2.rfidle.com/protect/repository/",

			//"url" => ($app_id == 901) ? "http://18.216.171.146/protect/repository/" : "http://r2.rfidle.com/protect/repository/",
			//"url" => ($app_id == 901) ? "http://13.58.145.26/protect/repository/" : "http://r2.rfidle.com/protect/repository/",
			"url" => ($app_id == 901) ? "http://54.160.66.249/protect/repository/" : "http://r2.rfidle.com/protect/repository/",

		),

	//--------"actions" => $cmds,

	//--------"logical" => $ldir,

	"cus" => array(

			"beta" => $cus_beta,

			"type" => 295, //295=desenvolvedor, 1=normal, 2=beta

		),

	"updates" => $idir,

	"errors" => array(),

	//"get" => $_GET,

);









/*HISTORY*************************************************************************/

if (isset($_GET["history"])) {



	//---PDO $qry = $pdo->query("select major, minor, revision, fixed, added, changed, removed, timestamp from ver where app_id = $app_id and id > $ver_id order by id desc");

	$qry = $pdo->prepare("SELECT id, major, minor, revision, fixed, added, changed, removed, timestamp, stop from ver where app_id = :app_id and active = 1 and id > :ver_id order by id #desc");

	$qry->execute(

		array(

			":app_id" => $app_id, 

			":ver_id" => $ver_id

		)

	);



	$history = array();



	$stop = false;



	while ($row = $qry->fetch()) {



		if (!$stop) {



			$formated_version = $row->major.".".$row->minor.".".$row->revision;



			array_push(

				$history,



				array(

					"version" => $formated_version,

					"date" => date("d-m-Y", strtotime($row->timestamp)),

					"fixed" => $row->fixed,

					"stop" => $row->stop,

				)

			);

		}



		if (($row->stop == 1) && ($row->id > $ver_id)) {

			$stop = true;

		}



	}



	$history = array_reverse($history);



	$json["history"] = $history;



}

/*-------------------------------------------------------------------------------*/







$json["resources"] = array();









if (isset($_GET["ikey"])) {





	$ikey = explode("-", $_GET["ikey"])[0];



	//UPDATE CURRENT VERSION

	$sql  = "update ins set ver_id = :ver_id where mac_id = :mac_id and appl_id = :app_id";

	$qry  = $pdo->prepare($sql);

	$res  = 

	$qry->execute(

		array(

			"mac_id" => $ikey,

			"app_id" => $app_id,

			"ver_id" => $ver_id,

		)

	);



	if (!$res) {

		array_push(

			$json["errors"],

			array("msg" =>'MUTTED ERROR!')

		);

		//$json["errors"] = $qry->errorInfo();

	}

}







//SERIAL

if ((isset($_GET["skey"])) && (strlen($_GET["skey"]) > 0)) {



	//EXTRACT SERIAL INFO

	$skey = $_GET["skey"];





	//is payed

	/*

	$sql  = 

	"select 

		s1.*,

		(select id from seri where ser_id = s1.id) as ser_recycled,

		(select sum(value) from rechist where seri_id = s1.id) as rectota,

		(select count(id) from rechist where seri_id = s1.id and date < curdate() and confirmed is null) as recfail_count

	from

		seri s1

	where

		skey = :skey

	";



	$qry  = $pdo->prepare($sql);

	$res = 

	$qry->execute(

		array(

			"skey" => $skey,

		)

	);

	*/





	//is recycled

	$sql  = 

	"select 

		s1.*,

		(select id from seri where ser_id = s1.id) as ser_recycled,

		(select sum(value) from rechist where seri_id = IFNULL(s1.firs_id, s1.id) ) as rectota,

		(select  count(id) from rechist where seri_id = IFNULL(s1.firs_id, s1.id) and date < curdate() and confirmed is null) as recfail_count

	from

		seri s1

	where

		skey = :skey

	";



	$qry  = $pdo->prepare($sql);

	$res = 

	$qry->execute(

		array(

			"skey" => $skey,

		)

	);











	if (!$res) {

		//var_dump($qry->errorInfo());

		//exit;

	} else {

		//echo 'yesss: '+$row->timestamp;

		//exit;

	}





	$row = $qry->fetch();

	$time   = strtotime(date('Y-m-d 00:00:00', time()));



	if (($res) && ($row)) 

	{



				







		$liquid = $row->price-$row->discount;

		$dbuild = strtotime(date('Y-m-d 00:00:00', strtotime($row->timestamp)));

		$dlimit = ($row->dlimit == NULL) ? NULL : strtotime($row->dlimit);



		$found    = true; 

		$expired  = (($dlimit != NULL) && (dateDiff($time, $dlimit) < 0));

		//_v1_ $blocked  = $row->blocked == 1;

		//_v2_ $blocked  = $row->_v2_disabled == 'BLOCKED'; //_v2_

		$blocked  = $row->blocked_at != NULL; //_v2_

		$recneed  = (($liquid > 1.00) && (dateDiff($dbuild, $time) > 0) && ($row->rectota < $liquid)); 

		$recfail  = $row->recfail_count > 0;

		$reseted  = $row->ser_recycled != NULL;

		$recycled = $row->ser_id != NULL;

		$enabled  = ((!$expired) && (!$blocked) && (!$recneed) && (!$recfail) && (!$reseted));

	} else {

		$found    = false; 

		$enabled  = false;

	}









	$json["serial"] =

		array(

			//"qry" => $qry,

			//"row" => $row,

			//"err" => $err,

			//"app_id" => $app_id,

			//"ver_id" => $ver_id,

			//"ikey" => $ikey,

			//"skey" => $skey,

			"found"   => $found,

			"enabled" => $enabled,

			"tags" 	  => array(

							"expired"  => isset($expired) && $expired,

							"blocked"  => isset($blocked) && $blocked,

							"recneed"  => isset($recneed) && $recneed,

							"recfail"  => isset($recfail) && $recfail,

							"reseted"  => isset($reseted) && $reseted,

							"recycled" => isset($recycled) && $recycled,

							//"first_id" => $ser->firs_id,

							//"seria_id" => $ser['seri_id'],

							//"d1" => $dbuild,

							//"d2" => $time,

							//"d3" => dateDiff($dbuild, $time),

						)

	 	);





} else {



	$json["serial"] =

		array(

			"found"   => false,

			"enabled" => false,

			"tags" 	  => array(

							/*"expired"  => isset($expired) && $expired,

							"blocked"  => isset($blocked) && $blocked,

							"recneed"  => isset($recneed) && $recneed,

							"recfail"  => isset($recfail) && $recfail,

							"reseted"  => isset($reseted) && $reseted,

							"recycled" => isset($recycled) && $recycled,

							//"d1" => $dbuild,

							//"d2" => $time,

							//"d3" => dateDiff($dbuild, $time),*/

						)

	 	);



}





/*RESOURCES RIJNDAEL ENCRYPTED*******************************************************/	



if ($json["serial"]["enabled"]) {

	include "ver-update-resource.php";

}

/*                                                                                  */

/************************************************************************************/







if (isset($_GET["formated"]))

	echo "<pre>";

//echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
echo json_encode($json, JSON_UNESCAPED_UNICODE);



if (isset($_GET["formated"]))

	echo "</pre>";



