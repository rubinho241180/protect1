<?php 

function getPhp($file)
{
	ob_start();
	include $file;
	return ob_get_clean();	

	ob_start(); // Start output buffer capture.
	include($file); // Include your template.
	$output = ob_get_contents(); // This contains the output of yourtemplate.php
	// Manipulate $output...
	ob_end_clean(); // Clear the buffer.
	return $output; // Print everything.	
}

function getMinified($url, $content) {
    $postdata = array('http' => array(
          'method'  => 'POST',
          'header'  => 'Content-type: application/x-www-form-urlencoded',
          'content' => http_build_query( array('input' => $content) ) ) );
    return file_get_contents($url, false, stream_context_create($postdata));
  }

$url = 'https://javascript-minifier.com/raw';



//error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
//error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE);
//error_reporting(0);
//ini_set('display_errors', 1);

$ky = dechex(  crc32(substr(date("Y-m-d H-i", time()-date("Z")), 0, -1)."x.$_GET[ikey]:1") );
$iv = dechex(  crc32(substr(date("Y-m-d H-i", time()-date("Z")), 0, -1)."x.$_GET[ikey]:2") );

if (in_array($app_id, [1, 601, 901, 104])) {
	//
	$ky = dechex(  crc32("x.$_GET[ikey]:1") );
	$iv = dechex(  crc32("x.$_GET[ikey]:2") );
}

$ky = str_pad($ky, 8, "0", STR_PAD_LEFT);
$iv = str_pad($iv, 8, "0", STR_PAD_LEFT);

require_once "lib/php-html-css-js-minifier.php";
require_once "lib/jsmin.php";
require_once "rijndael.php";
require_once "db.php";

$pdo = connect_pdo();
//---PDO $qry = $pdo->query("select id from ver where app_id = $app_id and beta = 0 order by id");

$qry = $pdo->prepare("SELECT id from ver where app_id = :app_id and beta = 0 order by id");
$qry->execute(array(":app_id" => $app_id));



$resources = 
array(
	"run" => 
	array(
		"ver" => "0.0.0",
		"val" => base64_encode(AES_Rijndael_Encrypt("TFrm".$app_id, $ky, $iv)),
	),
);

if (in_array($app_id, [101, 105, 107]))
{
	$resources["run"]["val"] = base64_encode("TFrm".$app_id);
}

$versions = 
array(

);

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

		//echo json_encode($files);
		//exit;

		foreach ($files as $key => $file) {
			$name = str_replace(".json", "_json", $file);
			$name = str_replace(".js", "_1", $name);
			$name = str_replace(".css", "_2", $name);
			$name = str_replace(".php", "_3", $name);


	

			$info = new SplFileInfo($file);

			if ($info->getExtension() == 'php') {

				$js = "http://$_SERVER[HTTP_HOST]/protect/$dir$file";
				/*
				echo $js;
				echo "<hr>";
				*/
				$js = file_get_contents($js, true);
								

				//ob_start();
				//include $dir.$file;
				//$js = ob_get_clean();

			} else {
				$js = file_get_contents($dir.$file, true);
			}

			//$js =  getMinified($url, $js);


			//$js 		 = /*JSMin::minify(file_get_contents($dir.$file));//*/minify_js(file_get_contents($dir.$file));

			if (in_array($app_id, [101, 105, 107, 124, 203, 111]))
			{
				$rijn 	 	 = $js;
			} else {
				$rijn 	 	 = AES_Rijndael_Encrypt($js, $ky, $iv);
			}

			$rijn64		 = base64_encode($rijn);
			//$decripted	 = AES_Rijndael_Decrypt($rijn, $ky, $iv);

			$resources[$name] =
			array(
				"ver" => $strver,
				//"fil" => $dir.$file,
				//"min" => $js,
				"val" => $rijn64,
				"pur" => (in_array($app_id, [101, 105, 107, 124, 203, 111])),
 				//"dec" => trim($decripted),
				"len" => strlen($rijn64),
			);

		}

	}
}

$json["resources"] = $resources;

$json["statistic"] = 
	array(
		"elapsed" =>round(microtime(true) - $t,3), 
	);

