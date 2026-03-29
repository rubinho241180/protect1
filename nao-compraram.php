<?php 

require_once "db.php";

$customers = $ndb->cus()->where('id between 1 AND 1999')->order('id DESC');

$json = [
];

foreach ($customers as $cus)
{

    $comprou = false;


    //$installations = $ndb->ins()->where('cus_id', $cus['id']);

    foreach ($cus->ins() as $ins)
    {
        //echo "   - $ins[mac_id]<br>";

        foreach ($ins->seri()->where('__settled_total > 0') as $seri)
        {
          //  echo "        ---- $seri[skey]<br>";
            $comprou = true;

        }

    }

    $date = date('d/m/Y', strtotime($cus['timestamp']));

    if (!$comprou)
    {
        //$emoji = "NÃO COMPROU!!!";
        //echo "$cus[ddi]$cus[phone],$cus[name],$cus[email],$date,$emoji<br>";
        echo "$cus[ddi]$cus[phone]<br>";
    } else
    {
        $emoji = "🤑";
    }



}



// echo json_encode($json)


//SELECT sum(value-discount) FROM `rechist` WHERE recmethod_id = 11 AND confirmed > '2021-05-01'