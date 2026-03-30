<style type="text/css">

td {
    border: 1px solid black;
    vertical-align: baseline;
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
}

table {
    width: 100%;
    font-family: monospace;
}

    .items td:nth-child(1) {
        width: 50%;
    }

    .items tr td,
    .payments tr td {
        border: 0;
    }

        .zzitems tr:last-child td:last-child {
            border-top: 1px dotted black;
        }

</style>
<?php 

    require_once "db.php";

?>


    <table id="">

        <tr class="hide">
            <td>ID</td>
            <td>ORDER</td>
            <td>EMAIL</td>
            <td>CREATED</td>
            <td>APPROVED</td>
            <td>LINK</td>
            <td>ITEMS</td>
            <td>PAYMENTS</td>
        </tr>

        <?php foreach ($ndb->orders()->where("order_status = ?", 'paid')->order("id desc") as $order) { ?>  

            <tr>
                <td><?= $order['id'] ?></td>
                <td><?= $order['gid'] ?></td>
                <td><?= $order['email'] ?></td>
                <td><?= date('m-d-Y, H:i:s', strtotime($order['created'])) ?></td>
                <td><?= date('m-d-Y, H:i:s', strtotime($order['approved'])) ?></td>
                <td>
                    <a href="http://r2.rfidle.com/protect/mp/order/<?= $order['gid']; ?>" target="_blank">
                        <svg width="24px" height="24px" viewBox="0 0 24 24"><g id="external_link" class="icon_svg-stroke" stroke="#00f" stroke-width="1.5" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 13.5 17 19.5 5 19.5 5 7.5 11 7.5"></polyline><path d="M14,4.5 L20,4.5 L20,10.5 M20,4.5 L11,13.5"></path></g></svg>
                    </a>
                </td>
                <td>
                    <table class="items">
                    <?php foreach ($order->items() as $item) { ?>

                        <tr>
                            <td><?= $item['appl_title'] ?></td>
                            <td><?= $item['price'] ?></td>
                        </tr>
                    <?php } ?>
                        <!--tr>
                            <td></td>
                            <td><?= $item['price'] ?></td>
                        </tr-->
                    </table>


                </td>

                <td>
                    <table class="payments">
                    <?php foreach ($order->payments() as $payment) { ?>

                        <tr>
                            <td><?= $payment['gid'] ?></td>
                            <td><?= $payment['value'] ?></td>
                        </tr>
                    <?php } ?>
                        <!--tr>
                            <td></td>
                            <td><?= $item['price'] ?></td>
                        </tr-->
                    </table>


                </td>
            </tr>

        <?php } ?>



    </table>    
