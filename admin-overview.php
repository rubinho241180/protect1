<?php 


require_once "db.php";

$pdo = connect_pdo();





$qry = 
$pdo->query("
	select 
	     concat(MONTH(r.timestamp), '-', YEAR(r.timestamp)) as month,
	     COUNT(*) as count,
	     SUM(r.price-r.discount) as total
	from 
	     seri r
	where
	     r.ser_id is null and 
	     r.price-r.discount > 10 and
	     DATEDIFF(r.dlimit, r.timestamp) > 10
	     AND r.__settled_at is not null
	GROUP BY
	     MONTH(r.timestamp),
	     YEAR(r.timestamp)
	ORDER BY
	     YEAR(r.timestamp),
	     MONTH(r.timestamp)   
");

$json = 
array(
	"sal" => array(),
	"tot" => array()
);

$count = 0;
$total = 0;

while ($row = $qry->fetch()) { 

	$count=$count+$row->count;
	$total=$total+$row->total;

	array_push(
		$json["sal"], 
		$row
	);

}

array_push(
	$json["tot"], 
	array(
		"count" => $count,
		"total" => $total,
	)
);


//if (isset($_GET["formated"]))
//	echo "<pre>";
//echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);



//if (isset($_GET["formated"]))
//	echo "</pre>";

















$qry = 
$pdo->query("
	select 
	     concat(MONTH(r.date), '-', YEAR(r.date)) as month,
	     COUNT(*) as count,
	     SUM(r.value-r.discount) as total
	from 
	     rechist r 
	WHERE 
		 r.confirmed is not null
	GROUP BY
	     MONTH(r.date),
	     YEAR(r.date)
	ORDER BY
	     YEAR(r.date),
	     MONTH(r.date)
");

$json = 
array(
	"rec" => array(),
	"tot" => array()
);

$count = 0;
$total = 0;

while ($row = $qry->fetch()) { 

	$count=$count+$row->count;
	$total=$total+$row->total;

	array_push(
		$json["rec"], 
		$row
	);

}

array_push(
	$json["tot"], 
	array(
		"count" => $count,
		"total" => $total,
	)
);


//if (isset($_GET["formated"]))
	echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);



//if (isset($_GET["formated"]))
	echo "</pre>";


