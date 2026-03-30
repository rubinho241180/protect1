<?php 

$timestamp  = date('Y-m-d H:i:s');
error_reporting(E_ALL);


function dateDiff($start, $end) {
    $start_ts = $start;//strtotime($start);
    $end_ts = $end;//strtotime($end);
    $diff = $end_ts - $start_ts;
    return round($diff / 86400);
}


require_once "db.php";

$serials = $ndb->seri()->order('dlimit');

foreach ($serials as $seri) {

    $daysDiff = dateDiff(strtotime($timestamp), strtotime($seri['dlimit']));

    if (($seri['price'] > 10) && ($seri['dlimit'] != NULL) && ($daysDiff > -30) && ($daysDiff < 30)) {

        echo $seri->ins->cus['name'].'<br>';
        echo '+'.$seri->ins->cus['ddi'].' '.$seri->ins->cus['phone'].'<br>';
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

    }


}





