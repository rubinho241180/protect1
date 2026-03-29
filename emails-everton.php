<?php 

require_once "db.php";

$customers = $ndb->cus()->where('id between 1 AND 10000')->order('id DESC');

$json = [
];

foreach ($customers as $cus)
{

        echo "$cus[email]<br>";


}



// echo json_encode($json)


//SELECT sum(value-discount) FROM `rechist` WHERE recmethod_id = 11 AND confirmed > '2021-05-01'