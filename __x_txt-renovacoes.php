<?php 


/*
require_once "db.php";


$customers = $ndb->cus()->where("usr_id > 0")->order('id DESC');

foreach ($customers as $cus) {

    $cus_id = $cus["id"];

    //$serials = $ndb->seri()->where("(ins.cus.id = {$cus_id}) AND (usr_id = 4)")->order('id DESC');
    $serials = $ndb->seri()->where("ins.cus.id = $cus_id AND seri.usr_id = 4")->order('id DESC');

            if ($serials->count() > 0)

            echo 
                $cus['ddi'].$cus['phone'].','.
                utf8_decode($cus['name']).//','.
                //$serials->count().
                '<br>';


}


EXIT;

*/

$timestamp  = date('Y-m-d H:i:s');
error_reporting(E_ALL);


function dateDiff($start, $end) {
    $start_ts = $start;//strtotime($start);
    $end_ts = $end;//strtotime($end);
    $diff = $end_ts - $start_ts;
    return round($diff / 86400);
}


$expired = (isset($_GET['expired'])) ? ($_GET['expired'] == 'TRUE') : FALSE;

//var_dump($expired);

require_once "db.php";

//$serials = $ndb->seri()->where("ins.cus.usr_id = 4")->order('dlimit DESC');
$serials = $ndb->seri()->/*where("usr_id = 4")->*/order('dlimit DESC');
//$serials = $ndb->seri()->where("info like '%lelo%'")->order('dlimit DESC');

//echo '<code>';
foreach ($serials as $seri) {

    $dlimit = $seri['dlimit'];
    $life   = dateDiff(strtotime($seri['timestamp']), strtotime($dlimit));
    $left   = dateDiff(strtotime($timestamp), strtotime($dlimit));

    if (

            ($seri['price'] > 10) 
            && ($dlimit != NULL) 
            //&& ($left > -30) 
            //&& ($left < 30)
            ) {

        if (($expired && ($left < 0)) || (!$expired && ($left >= 0) && ($left <= 30)))
        {

            echo 
                //$life.';'.$left.';'.
                $seri->ins->cus['ddi'].$seri->ins->cus['phone'].','.
                utf8_decode($seri->ins->cus['name']).','.
                $seri->ins->appl['name'].','.
                (($left < 0) ? 'EXPIROU,' : 'VAI EXPIRAR,').
                $dlimit.','.
                (($left < 0) ? ($left*-1) : $left).
                '<br>';


        }
            /*

        echo $seri->ins->cus['city'].'-'.$seri->ins->cus['state'].'<br>';

        //echo 'installation:';
        echo '<ul>';
        echo $seri->ins['mac_id'].'-'.$seri->ins['appl_id'].'<br>';
        echo $seri['skey'].'<br>';
        //echo 'R$ '. ($seri['price']-$seri['discount']).'<br>';
        echo date('d-m-Y', strtotime($seri['timestamp'])).'<br>';
        echo date('d-m-Y', strtotime($seri['dlimit'])). '  ('.$daysDiff. ' days)<br>';
        echo '</ul>';

        echo '<hr>';
*/
    }


}

//echo '</code>';




