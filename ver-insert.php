<?php 



$app = intval($_POST["app_id"]);
$ver = $_POST["ver"];
$his = utf8_encode(trim($_POST["his"]));

$major    = intval(explode(".", $ver)[0]);
$minor    = intval(explode(".", $ver)[1]);
$revision = intval(explode(".", $ver)[2]);

$id = intval($major.sprintf('%02d', $minor).sprintf('%02d', $revision));

require_once "db.php";
$pdo = connect_pdo();
$sql = "insert into ver set app_id = :app_id, id = :id, major = :major, minor = :minor, revision = :revision, fixed = :fixed, beta = 0";

//echo $id;


$stmt = $pdo->prepare($sql);
$qry  = 
$stmt->execute(
		array(
				"app_id"   => $app,
				"id" 	   => $id,
				"major"    => $major,
				"minor"    => $minor,
				"revision" => $revision,
				"fixed"	   => $his,
			)
);


$json =
array(
	"sql"=>$sql,
	"get"=> $_POST,
);


$json["inserted"] = $qry;

if (!$qry) {

	$json["errors"][] = $pdo->errorInfo();

} else {

	if ($app == 901) $fileName = "{app}\sender.exe";
	if ($app == 701) $fileName = "{app}\TrueTelegram.exe";
	if ($app == 705) $fileName = "{app}\TrueTelegramFilter.exe";

	if ($app == 501) $fileName = "{app}\TrueSMS.exe";
	if ($app == 403) $fileName = "{app}\TrueFBMessenger.exe";
	if ($app == 401) $fileName = "{app}\TrueFBGroupSend.exe";
	if ($app == 301) $fileName = "{app}\TrueOLX.exe";
	if ($app == 295) $fileName = "{app}\TrueSMS_HUB.exe";
	if ($app == 207) $fileName = "{app}\TrueSMS_hub.exe";
	if ($app == 203) $fileName = "{app}\TrueANDROID_MESSAGES.exe";
	if ($app == 201) $fileName = "{app}\TrueSMS_TUGo.exe";
	if ($app == 107) $fileName = "{app}\TrueWhats_hub.exe";
	if ($app == 105) $fileName = "{app}\TrueWhatsFilter.exe";
	if ($app == 104) $fileName = "{app}\mrFilter.exe";
	if ($app == 101) $fileName = "{app}\TrueWhats.exe";
	if ($app ==  10) $fileName = "{app}\TruePortabilidade.exe";
	if ($app ==   9) $fileName = "{app}\TrueExtractor.exe";
	if ($app ==   1) $fileName = "{app}\AppProtectKey.exe";
	


	$sql = "insert into cmd set app_id = :app_id, ver_id = :ver_id, cmd = :cmd, par1 = :par1";

	$stmt = $pdo->prepare($sql);
	$qry  = 
	$stmt->execute(
			array(
					"app_id"   => $app,
					"ver_id"   => $id,
					"cmd"      => "ADD",
					"par1"     => "{$fileName}",
				)
	);


	/*array_push(
		$json,
		array(
			"debug" => 
			array(
				"app_id"   => $app,
				"ver_id"   => $id,
				"cmd"      => "ADD",
				"par1"     => "{app}\AppProtectKey.exe",
			)
		)
	);*/
}


if (isset($_POST["formated"]))
	echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);

if (isset($_POST["formated"]))
	echo "</pre>";

