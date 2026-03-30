<?php session_start(); ?>
<!DOCTYPE html>
<html  lang="pt-br">
<head charset="UTF-8">
    <link rel="stylesheet" href="assets/send-navbar.css">
    <link rel="stylesheet" href="assets/send.css">
    <link rel="stylesheet" href="assets/send-login.css">
    <script src="assets/jquery-3.2.1.min.js"></script>
    <script>



</script>
</head>
<body>

    <ul id="navbar">
        <li class="navbar-item navbar-item-brand">
            <span id="brand">BULKFY</span>
        </li>
        <li class="navbar-item navbar-item-credit">
            <span>SMS BALANCE: </span>
            <span id="credit">100.000</span>
        </li>
        <!--li class="navbar-item navbar-item-phone">
            <form id="form-phone">
                <input id="phone" type="text" placeholder="Phone..." autocomplete="off" autofocus>
                <button id="add-chat">OPEN</button>
            </form>
        </li-->
    </ul>

    <?php if (isset($_SESSION['user_id'])) : ?>

        <ul class="hul empty">
            <span id="empty">
                <div>EMPTY</div>
            </span>
        </ul>


    <?php else : ?>

        <ul class="hul empty">
               <?php include "send-login.php"; ?>
        </ul>
        
    <?php endif ?>

    <ul id="bottom">
        <li class="bottom-item">
            <span>100.000</span>
        </li>
    </ul>


    <script src='assets/send.js'></script>
    <script src='assets/send-navbar.js'></script>

</body>
</html>
  