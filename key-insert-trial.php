<?php 

require_once "AppProtectKey.php";


use AppProtectKey\inst as inst;


require_once "db.php";
require_once "const.php";

$pdo = connect_pdo();


$mac_id = $_POST["mac_id"];
$app_id = $_POST["app_id"];
$dlimit = date('Y-m-d', strtotime("+12 hour"));



//VERIFICA QUE JÁ FORAM GERADOS SERIAIS TESTES
$sql = 
	"
		select 
			count(*) as ser_count
		from
			seri s
		inner join
			ins i on i.id = s.ins_id
		where
			i.mac_id = :mac_id and
			i.appl_id = :app_id
	";

$qry = $pdo->prepare($sql);
$qry->execute(
	array(
		":mac_id" => $mac_id, 
		":app_id" => $app_id
	)
);


$row = $qry->fetch();


if ($row->ser_count > 0) {

	$json = 
		array(
			"errors"=> 
				array(
					array(
						"msg"=>"LIMITE DE TESTES EXCEDIDO!",
					)
				)
		);

} else {

	$inst = new inst($mac_id, $app_id);
	 
	$json = $inst->serial->insert(
				array(
					"type" => 53, 
					"subt" => 1, 
					"dlim" => $dlimit,
					"ilim" => 1,
					//"ikey" => $inst->ikey(),
					"auto" => 1,
					"info" => "TRIAL 24H",
					'flag' => FLAG_NEW,
				)
			); 

}






array_push($json, array("date_default_timezone_get()"=>date_default_timezone_get()));

//echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT);
//echo "</pre>";


