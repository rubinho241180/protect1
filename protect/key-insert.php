<?php 

require_once "const.php";
require_once "functions.php";
require_once "rijndael.php";
	require_once "db.php";
	require_once "NotORM.php";


$timestamp = date('Y-m-d H:i:s');


$ref_mac_id = $_POST["ref_mac_id"];
$cus_id = $_POST["cus_id"];
$mac_id = $_POST["mac_id"];
$app_id = $_POST["app_id"];
$inskey = strtolower($mac_id."-".$app_id);
$ins_id = $_POST["ins_id"];


$session_id = isset($_POST["session_id"]) ? $_POST["session_id"] : "xxx";
$sal = $ndb->usr()->where("session_id = ?", $session_id)->fetch();



$ins = $ndb->ins()->where("mac_id = ?", $ref_mac_id)->fetch();
$cus = $ndb->cus()->where("id = ?", $ins["cus_id"])->fetch();
$usr = $ndb->usr()->where("id = ?", $cus["ref_usr_id"])->fetch();


if (($usr) && ($sal)) {
} else {
	$json = 
		array(
			"session" => false,
			//"mac"=>$ref_mac_id,
			//"ins"=>$ins["cus_id"],
			//"cus"=>$cus["ref_usr_id"],
			//"usr"=>$usr["id"],
			//"session_id" => $session_id,
		);
	echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
	exit;
}






$pkey = $_POST["pkey"];
$info = utf8_encode($_POST["info"]);

$par_id = $_POST["par_id"];


//$meth = $_POST["meth"];

	$gtw_id = isset($_POST["gtw_id"]) ? $_POST["gtw_id"] : NULL; //GATEWAY_PAYMENT_ID
	$pric 	= isset($_POST["pric"]) ? floatval($_POST["pric"]) : NULL;
	$disc 	= isset($_POST["disc"]) ? floatval($_POST["disc"]) : NULL;
	$liqu 	= isset($_POST["liqu"]) ? floatval($_POST["liqu"]) : NULL;
	$prev 	= isset($_POST["prev"]) ? date('Y-m-d H:i:s', strtotime($_POST["prev"])) : NULL;


if (isset($_POST["gtw_id"])) {
	$payment = curl_get_contents("https://api.mercadopago.com/v1/payments/$gtw_id?access_token=".MP_ACCESS_TOKEN);
	$payment = json_decode($payment);
	$payment_status = $payment->status;


	if (!in_array($payment_status, array("approved", "pending"))) {
		echo json_encode(array("errors"=>array("payment->status = ".$payment_status)), JSON_PRETTY_PRINT);
		exit;
	}


	$payment_fee = $payment->transaction_amount-$payment->transaction_details->net_received_amount;
	$proportional_fee_perc = ($payment_fee * 100) / $payment->transaction_amount;

	$pdo = connect_pdo();
	$orm = new NotORM($pdo);
	$ser = $orm->seri()->select("IFNULL(sum(price-discount), 0) as has_total")->where("gtw_id = '$gtw_id'")[0];

	$has_total = floatval($ser["has_total"]);

	//SE JÁ FORAM GERADAS AS LICENÇAS...
	if (($has_total+$liqu) > $payment->transaction_amount) {
		echo json_encode(array("errors"=>array("payment_total = ".$payment->transaction_amount." AND "."has_total = ".$has_total)), JSON_PRETTY_PRINT);
		exit;
	}

	$payment_date_approved = date('Y-m-d', strtotime($payment->date_approved));
	$payment_date_rettired = strtotime("next tuesday", strtotime($payment_date_approved));
	//$money_release_date    = date('Y-m-d', strtotime($payment->money_release_date));
	$money_release_date    = date('Y-m-d', strtotime($payment->money_release_date.' +1 day'));



} else {
	$payment_status = 0;
	$payment_fee = 0;
	$proportional_fee_perc = 0;
}



$flag 	 = FLAG_NEW;
$type 	 = hexdec(substr($pkey,  0, 2));
$subtype = hexdec(substr($pkey,  12, 2));
$dbuild	 = hexdec(substr($pkey,  2, 5));
$dbuild	 = str_pad($dbuild, 6, "0", STR_PAD_LEFT);

$dlimit = hexdec(substr($pkey,  7, 5));
if ($dlimit > 0) {
	$dlimit	= str_pad($dlimit, 6, "0", STR_PAD_LEFT);
	$day   	= substr($dlimit,  0, 2);
	$month 	= substr($dlimit,  2, 2);
	$year  	= substr($dlimit,  4, 2);
	$dlimit = date("Y-m-d", strtotime($year."-".$month."-".$day));
	$dlimit_sql = $dlimit;

	$dlimit = date("d-m-Y", strtotime($year."-".$month."-".$day));

} else {
	$dlimit_sql = null;
}

$ilimit = hexdec(substr($pkey, 14, 2));


$enc = AES_Rijndael_Encrypt($pkey, $inskey.":1", $inskey.":2");
$skey = strtoupper(bin2hex($enc));
$skey = substr($skey, 0, 32);

require_once "functions.php";


//require_once "db.php";
$pdo = connect_pdo();
$sql = 
"
	insert into 
		seri 
	set 
		ins_id = :ins_id,
		_v2_flag = :flag,
		type = :type,
		subtype = :subtype,
		dlimit = :dlimit_sql,
		ilimit = :ilimit,
		skey = :skey,
		info = :info,
		par_id = :par_id,
		price    = :pric,
		discount = :disc,
		prevision = :prev,
		gtw_id = :gtw_id,
		usr_id = :usr_id,
		sal_id = :sal_id,

		sal_perc = :sal_perc,
		sal_valu = :sal_valu
";

	if (($liqu != NULL) && ($usr["is_admin"] != 1)) {
		
		//$sal_perc = ($usr["id"] == 4) ? 15.00 : 10.00;

		switch ($usr["id"]) {
		    case 4:
		        $sal_perc = 15.00;
		        break;
		    case 8:
		        $sal_perc = 15.00;
		        break;
		    default:
		        $sal_perc = 10.00;
		        break;
		}

		$sal_valu = ($liqu*$sal_perc)/100;
	} else {
		$sal_perc = NULL;
		$sal_valu = NULL;
	}

$params = array(
				"ins_id" => $ins_id,
				//"cus_id" => $cus_id,
				//"mac_id" => $mac_id,
				//"app_id" => $app_id,
				"type" 	 => $type,
				"flag" 	 => $flag,
				"subtype" 	 => $subtype,
				"dlimit_sql" => $dlimit_sql,
				"ilimit" => $ilimit,
				"skey" 	 => $skey,
				"info" 	 => $info,
				"par_id" => ($par_id > 0) ? $par_id : NULL,
				"pric" 	 => $pric,
				"disc" 	 => $disc,
				"prev" 	 => $prev,
				"gtw_id" => $gtw_id,
				"usr_id" => $usr["id"],
				//"sal_id" => $sal["id"],
				"sal_id" => $usr["id"],

				"sal_perc" => $sal_perc,
				"sal_valu" => $sal_valu,
			);

//echo json_encode($params, JSON_PRETTY_PRINT);

//exit;

$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
$stmt = $pdo->prepare($sql);
$qry  = $stmt->execute(
			$params
		);

$inserted_seri_id = $pdo->lastInsertId();


	if (($pric-$disc) > 10)
	{
		$text  = "🔑🔑\n";
		$text .= "SERIAL GERADO!\n";
		$text .= "R$".number_format(($pric-$disc), 2, ",", ".")."\n";
		$text .= "--\n";
		$text .= $info;

		//addSMS(TELEGRAM_TSGROUP, $text);
		addSMS(TELEGRAM_RUBINHO, $text);
		addSMS(TELEGRAM_SAMARA, $text);
	}



	//SE JÁ FOI AOPROVADO, GERAR OS RECEBIMENTOS
	if (isset($_POST["gtw_id"])) {
		if ($payment->status == "approved") {
			
			$orm = new NotORM($pdo);

			//$gate_fee = ($payment_fee*$proportional_fee_perc)/100;
			$gate_fee = ($liqu*4.99)/100;


			$orm->rechist()->insert(
				array(
					"date"         => $money_release_date, //date("Y-m-d", $payment_date_rettired),
					"value" 	     => $liqu,//$pric,
					"discount" 	   => 0,//$disc,
					"gateway_fee"  => $gate_fee,
					"seri_id"      => $inserted_seri_id,
					"recmethod_id" => 22,
					"timestamp"    => $timestamp,
					"gtw_auto"     => 1,
				)
			);


		}
	}







	if ($liqu != NULL) {

		//GERA AS COMISSÕES DOS CO-PRODUTORES
		$produtores = $ndb->appl_prod()->where("appl_id = ?", $app_id);

		foreach ($produtores as $appl_prod) {
			
			$sale_valu = ($sal_valu != NULL) ? $sal_valu : 0;
			$liqu_prod = $liqu - $sale_valu - $payment_fee;

			$ndb->seri_prod()->insert(
				array(
					"seri_id" => $inserted_seri_id,
					"prod_id" => $appl_prod["prod_id"],
					"perc"		=> $appl_prod["perc"],
					"valu"		=> ($liqu_prod*$appl_prod["perc"])/100,
				)
			);

		}

	}







//$qry = $pdo->exec($sql);


$json =
array(
	"ins_id"=>$ins_id,
	"cus_id"=>$cus_id,
	"mac_id"=>$mac_id,
	"app_id"=>$app_id,
	"ref_mac_id" => $ref_mac_id,

	"pkey"=>$pkey,
	"type"=>$type,
	"dbuild"=>$dbuild,
	"dlimit"=>$dlimit,
	"ilimit"=>$ilimit,

	"skey"=>strtoupper($skey),
	"errors"=> array(),
	"sql"=>$sql,
	"payment_status" => $payment_status,
	"usr" => array("id"=>$usr["id"], "name"=>$usr["name"]),
	//"liqu" => $liqu,
	//"sale_valu" => $sale_valu,
	//"liqu_prod" => $liqu_prod,
);

if (!$qry) {

	$json["POST"] = $_POST; 
	array_push(
		$json["errors"],
		$pdo->errorInfo()
	);
} else {
	$json["inserted"] = $qry;
}


if (isset($_POST["formated"]))
	echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT);

if (isset($_POST["formated"]))
	echo "</pre>";

