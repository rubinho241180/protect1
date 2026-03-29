<?php 
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
ini_set('display_errors', 1);

//$app_id = isset($_GET["app_id"]) ? $_GET["app_id"] : 101;
//$ver_id = isset($_GET["ver_id"]) ? $_GET["ver_id"] : 40000;

require_once "lib/php-html-css-js-minifier.php";
require_once "rijndael.php";
require_once "db.php";

$pdo = connect_pdo();
$qry = $pdo->query("select id from ver where app_id = $app_id and beta = 0 order by id");


$resources = array();

//$json = 
//array();

$versions = array();

while ($row = $qry->fetch()) {
	$versions[] = $row->id;
}

if (intval($ver_id) > intval(end($versions))) {
	$versions[] = intval($ver_id);
}

$t = microtime(true);


foreach ($versions as $id) {
	$major    = intval(substr($id, 0, strlen($id)-4));
	$minor    = intval(substr($id, -4, 2));
	$revision = intval(substr($id, -2));
	
	$strver	  = $major.".".$minor.".".$revision;

	$intver   = intval(
					sprintf('%02d', intval($major)).
					sprintf('%02d', intval($minor)).
					sprintf('%02d', intval($revision))
				);

	$dir = "resource/$app_id/$strver/";

	if (($id <= $ver_id) && (is_dir($dir))) {

		$files = array_diff(scandir($dir), array('.', '..'));

		foreach ($files as $key => $file) {
			$name = str_replace(".js", "_1", $file);
			$name = str_replace(".css", "_2", $name);

			$js 		 = minify_js(file_get_contents($dir.$file));
			$rijn 	 	 = AES_Rijndael_Encrypt($js, "12345678-101:1", "12345678-101:1");
			$rijn64		 = base64_encode($rijn);

			$resources[$name] =
			array(
				"ver" => $strver,
				//"fil" => $dir.$file,
				"res" => $rijn64,
				"len" => strlen($rijn64),
			);

		}

	}
}

$json["resources"] = $resources;

$json["elapsed"] = round(microtime(true) - $t,3);

/*if (isset($_GET["formated"]))
echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
if (isset($_GET["formated"]))
echo "</pre>";*/

