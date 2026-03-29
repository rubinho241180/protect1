<?php 

$app_id = $_GET["app_id"];
$ver_id = $_GET["ver_id"];

$cmds = array();
$ldir = array(); //logical dir
$fdir = array(); //final dir



require_once "functions.php";
require_once "db.php";

$pdo = connect_pdo();
$qry = $pdo->query("select * from cmd where app_id = $app_id order by ver_id, id");
while ($row = $qry->fetch()) {

	$major    = intval(substr($row->ver_id, 0, strlen($row->ver_id)-4));
	$minor    = intval(substr($row->ver_id, -4, 2));
	$revision = intval(substr($row->ver_id, -2));
	
	array_push(
		$cmds, 
		array(
			"ver" => $major.".".$minor.".".$revision,
			"cmd" => $row->cmd,
			"par1" => $row->par1,
			"par2" => $row->par2,
		)
	);
}


foreach ($cmds as $cmd) {

	if ($cmd["cmd"] == "ADD") {
		//$ldir[$cmd["par1"]] = array("ver"=>$cmd["ver"], "src"=>"\\".$cmd["ver"]."\\".$cmd["par1"]);
		$ldir[$cmd["par1"]]["history"][] =
		array(
			"ver" => $cmd["ver"],
			"cmd" => $cmd["cmd"], 
			"par1"=> $cmd["par1"], 
			"par2"=> $cmd["par2"],
		);
	} else

	if ($cmd["cmd"] == "REN") {
		$ldir[$cmd["par1"]]["history"][] =
		array(
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


foreach ($ldir as $ldr => $val) {
	if (!isset($val["deleted"])) {
		$fdir[$ldr] = 
		array(
			//"ver" => $val["history"][0]["ver"],
			"src" => "\\".$val["history"][0]["ver"]."\\".$val["history"][0]["par1"],
		);
	}
}


/*require_once "db.php";
$pdo = connect_pdo();
$sql = "insert into cmd set cus_id = :cus_id, mac_id = :mac_id, app_id = :app_id, type = :type, dlimit = :dlimit_sql, ilimit = :ilimit, skey = :skey, info = :info";

$params = array(
				"cus_id" => $cus_id,
				"mac_id" => $mac_id,
				"app_id" => $app_id,
				"type" 	 => $type,
				"dlimit_sql" => $dlimit_sql,
				"ilimit" => $ilimit,
				"skey" 	 => $skey,
				"info" 	 => $info,
			);

$stmt = $pdo->prepare($sql);
$qry  = $stmt->execute(
			$params
		);


//$qry = $pdo->exec($sql);
*/


$json =
array(
	"cmd" => $cmds,
	"ldr" => $ldir,
	"fdr" => $fdir,
	//"get" => $_GET,
);


if (isset($_POST["formated"]))
	echo "<pre>";
echo json_encode($fdir, JSON_PRETTY_PRINT);

if (isset($_POST["formated"]))
	echo "</pre>";

