<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Whatsapp Web</title>
	<style>
		html, body {
			background-color: #eee!important;
			height: 100%;
			font-family: Tahoma;
			margin: 0;
			padding: 0;
		}
		* {
			color: #4b4b4b;
		}

		.popup {
		    box-shadow: 0 17px 50px 0 rgba(0,0,0,.19),0 12px 15px 0 rgba(0,0,0,.24);
		    position: absolute;
		    height: 140px;
		    width: 400px;
		    background-color: #fff;
		    left: 50%;
		    top: 50%;
		    margin-left: -200px;
		    margin-top: -70px;
		    padding: 22px 24px 20px;
		    box-sizing: border-box;
		    border-radius: 3px;
		}		

		.popup-contents, .controls-container {
    		font-size: 14px;
    		line-height: 20px;
		}

		.popup-controls {
			text-align: right;
		}

		.btn-plain {
		    position: relative;
		    font-size: 14px;
		    font-weight: 300;
		    padding: 10px 24px;
		    color: #07bc4c;
		    border-radius: 3px;
		    -webkit-transition: box-shadow .18s ease-out,background .18s ease-out,color .18s ease-out;
		    transition: box-shadow .18s ease-out,background .18s ease-out,color .18s ease-out;
		}
		
		.btn-fill, .btn-plain {
		    text-transform: uppercase;
		}

		button {
		    border: 0;
		    padding: 0;
		    margin: 0;
		    background: 0 0;
		    outline: none;
		    cursor: pointer;
		    font-family: inherit;
		}

		.btn-plain:hover, .app-wrapper-web .app, .incoming-msgs, .btn-more, #window {
		    box-shadow: 0 1px 1px 0 rgba(0,0,0,.06),0 2px 5px 0 rgba(0,0,0,.2);
		}
		.btn-plain:hover {
		    color: #0cb757;
		}

	</style>
</head>
<body>

<?php 	

$app_name = ((isset($_GET["ikey"])) && (explode("-", $_GET["ikey"])[1] == "201")) ? "Vivo TU Go" : "WhatsApp Web";

 ?>


	<div class="popup">
		<div class="popup-body">
			<div class="popup-contents">Esta função não é suportada pelo seu browser. Atualize para a versão mais atual para continuar utilizando o <?php echo $app_name; ?>.</div>
		</div>
		<div class="popup-controls">
			<button class="btn-plain popup-controls-item" onClick="location.href='http://web.whatsapp.com'">OK</button>
		</div>
	</div>	

	<?php 



		if (isset($_GET["ikey"]) && isset($_GET["skey"])) {

			$mac_id = explode("-", $_GET["ikey"])[0];
			$app_id = explode("-", $_GET["ikey"])[1];
			$serial = $_GET["skey"];
			$ip     = $_SERVER['REMOTE_ADDR'];
			$timestamp = date('Y-m-d H:i:s');

			require_once "db.php";
			$pdo = connect_pdo();
			$sql = 
			"
				insert into 
					illegal 
				set 
					mac_id = :mac_id,
					app_id = :app_id,
					serial = :serial,
					ip 	   = :ip,
					timestamp = :timestamp
			";


			$stmt = $pdo->prepare($sql);
			$qry  = 
			$stmt->execute(
				array(
					"mac_id" => $mac_id,
					"app_id" => $app_id,
					"serial" => $serial,
					"ip"     => $ip,
					"timestamp"     => $timestamp,
				)
			);

		}



	 ?>
	
</body>
</html>