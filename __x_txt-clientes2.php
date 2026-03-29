<?php 

header('Content-Type: text/html; charset=UTF8');
$timestamp  = date('Y-m-d H:i:s');
error_reporting(E_ALL);


function dateDiff($start, $end) {
    $start_ts = $start;//strtotime($start);
    $end_ts = $end;//strtotime($end);
    $diff = $end_ts - $start_ts;
    return round($diff / 86400);
}


require_once "db.php";

//$customers = $ndb->cus()->order('id desc');

$pdo = connect_pdo();

//$qry = $pdo->query("select c.ddi, c.phone, c.email, c.name, count(*) as inst_count from ins i left join cus c on c.id = i.cus_id group by c.ddi, c.phone, c.name order by 4 desc, c.id desc");
//$qry = $pdo->query("select c.ddi, c.phone, c.email, c.name, count(*) as inst_count from ins i left join cus c on c.id = i.cus_id group by c.ddi, c.phone, c.name order c.id desc");
$qry = $pdo->query("select DISTINCT c.id, c.ddi, c.phone, c.email, c.name, i.distribuition_id FROM cus c LEFT JOIN ins i ON c.id = i.cus_id AND i.distribuition_id = 'TSI901' WHERE i.distribuition_id = 'TSI901' ORDER BY c.id DESC");

while ($row = $qry->fetch())
{ 

    echo 
        $row->ddi.$row->phone.','.
        utf8_decode($row->name).','.
        $row->email.','.
        $row->id.','.
        $row->distribuition_id.'<br>';

    // echo 
    //     $row->email.'<br>';

}




