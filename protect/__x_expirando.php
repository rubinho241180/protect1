<?php 

$timestamp  = date('Y-m-d H:i:s');
error_reporting(E_ALL);


function dateDiff($start, $end) {
    $start_ts = $start;//strtotime($start);
    $end_ts = $end;//strtotime($end);
    $diff = $end_ts - $start_ts;
    return round($diff / 86400);
}

$app_id = (isset($_GET['app'])) ? $_GET['app'] : 0;


$d1 = $date = date('Y-m-d', strtotime("monday this week"));
$d2 = $date = date('Y-m-d', strtotime("sunday this week"));

$d1 = $date = date('Y-m-d', strtotime("- 60 days"));
$d2 = $date = date('Y-m-d', strtotime("+ 60 days"));


require_once "db.php";

$serials = $ndb->seri()->where("dlimit BETWEEN ? AND ?", array($d1, $d2))->order('dlimit');

$I = 0;

?>

<style type="text/css">

table {
    width: 100%;
}

table td {border: 1px solid silver;}

</style>


<table>
    <tr>
        <td>nome</td>
        <td>email</td>
        <td>telefone</td>
        <td>key</td>
        <td>preco</td>
        <td>data</td>
        <td>limite</td>
    </tr>



<?php 
foreach ($serials as $seri) {

    $daysDiff = dateDiff(strtotime($seri['timestamp']), strtotime($seri['dlimit']));
    $daysLeft = dateDiff(strtotime($timestamp), strtotime($seri['dlimit']));

    /*
    if (
               ($seri['price'] > 100) 
            && ($seri['dlimit'] != NULL) 
            && ($daysDiff > -30) 
            && ($daysDiff < 30)
        ) {
    */ 
    
    if (
               ($seri['price']-$seri['discount'] > 10) 
            && ($seri['dlimit'] != NULL) 
            && ($daysDiff > 30) 
            //&& ($daysLeft > 1) 
            //&& ($seri['seri_id'] == NULL) 
            //&& ($seri->ins['appl_id'] == $app_id)
        ) {
    
        ?>
        <tr>
            <td><?php echo $seri->ins->cus['name']; ?></td>
            <td><?php echo $seri->ins->cus['email']; ?></td>
            <td><?php echo $seri->ins->cus['ddi'].$seri->ins->cus['phone']; ?></td>
            <td><?php echo $seri->ins['mac_id'].'-'.$seri->ins['appl_id']; ?></td>
            <td><?php echo floatval($seri['price'])-floatval($seri['discount']); ?></td>
            <td><?php echo date('d-m-Y', strtotime($seri['timestamp'])); ?></td>
            <td><?php echo date('d-m-Y', strtotime($seri['dlimit'])). '  ('.$daysLeft. ' days)'; ?></td>
        </tr>

        <?php 

        $I++;

    }
}

?>

</table>



<?php



echo "$I registros";





