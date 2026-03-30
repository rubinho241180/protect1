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
        <td>#</td>
        <td>ORDER</td>
        <td>EMAIL</td>
        <td>APP</td>
        <td>PRICE</td>
        <td>CREATED</td>
        <td>APPROVED</td>
        <td>REPORT</td>
    </tr>

<?php foreach ($ndb->items()->where("serial is not null and seri_id is null")->order("id desc") as $item) { ?>  

    <tr>
        <td><?= $item->orders['id'] ?></td>
        <td>
            <?php 

                $payment = $item->orders->payments()->fetch()
            ?>
            
            <?= $payment['gid'] ?>
            <a href="http://r2.rfidle.com/protect/mp/payment/<?= $payment['gid']; ?>" target="_blank">
                <svg width="24px" height="24px" viewBox="0 0 24 24"><g id="external_link" class="icon_svg-stroke" stroke="#00f" stroke-width="1.5" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 13.5 17 19.5 5 19.5 5 7.5 11 7.5"></polyline><path d="M14,4.5 L20,4.5 L20,10.5 M20,4.5 L11,13.5"></path></g></svg>
            </a>
        </td>
        <td><?= $item->orders['email'] ?></td>
        <td><?= $item['appl_title'] ?></td>
        <td><?= $item['price'] ?></td>
        <td><?= date('d-m-Y, H:i:s', strtotime($item->orders['created'])  ) ?></td>
        <td><?= date('d-m-Y, H:i:s', strtotime($item->orders['approved']) ) ?></td>
        <td></td>
    </tr>

<?php } ?>


    <tr class="hide">
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
    </tr>

<?php foreach ($ndb->items()->where("orders.order_status = ? and seri_id is not null", 'paid')->order("id desc") as $item) { ?>  

    <?php //if ($item->orders['order_status'] == 'paid') { ?>

    <tr class="used">
        <td><?= $item->orders['id'] ?></td>
        <td>
            <?php 

                $payment = $item->orders->payments()->fetch()
            ?>

            <?= $payment['gid'] ?>
            <a href="http://r2.rfidle.com/protect/mp/order/<?= $payment['gid']; ?>" target="_blank">
                <svg width="24px" height="24px" viewBox="0 0 24 24"><g id="external_link" class="icon_svg-stroke" stroke="#00f" stroke-width="1.5" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 13.5 17 19.5 5 19.5 5 7.5 11 7.5"></polyline><path d="M14,4.5 L20,4.5 L20,10.5 M20,4.5 L11,13.5"></path></g></svg>
            </a>
        </td>
        <td><?= $item->orders['email'] ?></td>
        <td><?= $item['appl_title'] ?></td>
        <td><?= $item['price'] ?></td>
        <td><?= date('d-m-Y, H:i:s', strtotime($item->orders['created'])) ?></td>
        <td><?= date('d-m-Y, H:i:s', strtotime($item->orders['approved'])) ?></td>
        <td><?= count($item->seri->seri_prod()) ?></td>
    </tr>

    <?php //} ?>

<?php } ?>

    </table>    
