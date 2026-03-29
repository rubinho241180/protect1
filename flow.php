<style type="text/css">

td {
    border: 1px solid black;
}

.hide td {
    border: none;
}

.used td {
    border: 1px solid rgba(0,0,0,.5);
    color: rgba(0,0,0,.5);
}

svg {
    height: 18px;
    -position: absolute;
    margin: -2px 0 -4px 0;
}

table {
    width: 100%;
    font-family: monospace;
}

</style>
<?php 

    require_once "db.php";

?>


<table>

    <tr class="hide">
        <td>TIME</td>
        <td>IP</td>
        <td>DOWNLOAD</td>
    </tr>

    <!-- DOWNLOAD BEGIN -->
    <?php foreach ($ndb->download()->order("id desc") as $download) { ?>  

    <tr>
        <td><?= date('d-m-Y, H:i:s', strtotime($download['timestamp'])) ?></td>
        <td><?= $download['ip'] ?></td>
        <td><?= $download['hash'] ?></td>
    </tr>

    <?php } ?>
    <!-- DOWNLOAD END** -->


    <tr class="hide">
        <td>--</td>
        <td>--</td>
        <td><?= count($ndb->download()); ?></td>
    </tr>


</table>    
