<?php 
header("Content-Type: application/json; charset=utf-8");
//header('Content-Type: text/html; charset=utf-8');

//sleep(1);

$show_count = isset($_GET["show_count"]);

$json = 
	array(
		"version" => 
			array(
				"int"=>10000,
				"str"=>"1.0.1",
			),
			
		"applications"=>
			array(
			),

		"parameters" =>	$_GET
	);

require_once "db.php";

$pdo = connect_pdo();

$qry = $pdo->query("select a.*, (select count(*) from ins where appl_id = a.id) as ins_count from appl a");

while ($row = $qry->fetch()) { 

	$ins_count = ($show_count) ? $row->ins_count : -1;

	$japp = array(
				"id" => $row->id,
				"name" => $row->name,
				"ins_count"=> $ins_count,
				"versions" => array(),
			);

	

	//VERSIONS
	$qr2 = $pdo->query("select v.*, (select count(*) from ins where appl_id = $row->id and ver_id = v.id) as ins_count from ver v where v.app_id = $row->id order by v.id desc");
	while ($ro2 = $qr2->fetch()) { 

		$ins_count = ($show_count) ? $ro2->ins_count : -1;
		
		$jver =	array(
					"major" => $ro2->major,
					"minor" => $ro2->minor,
					"revision" => $ro2->revision,
					"date" => $ro2->timestamp,
					"ins_count" => $ins_count,
					"files" => array(),
				);


		//FILES
		$qr3 = $pdo->query("select * from cmd where app_id = $row->id and ver_id = $ro2->id order by id");
		while ($ro3 = $qr3->fetch()) { 
			array_push(
				$jver["files"], 
				array(
					"cmd" => $ro3->cmd,
					"par1" => $ro3->par1,
				)
			);
		}


		array_push(
			$japp["versions"], 
			$jver
		);
	}

	array_push(
		$json["applications"], 
		$japp
	);

}

if (isset($_GET["formated"]))
	echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT);

if (isset($_GET["formated"]))
	echo "</pre>";

