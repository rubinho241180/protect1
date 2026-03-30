<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style type="text/css">
	html {
		font-family: "lucida console";
	}
	table {
		border-collapse: collapse; 
		width: 100%;
	}

	td.col1,
	td.col2 {
		width: 120px;
	}
		td.col1.blocked {
			color: silver;
			-font-style: italic;
		}
	td.col4,
	td.col5 {
		width: 160px;
	}

	thead td {
		background-color: #F7F7F7;
		border-top: 1px solid #cdcdcd;
		border-left: 1px solid #cdcdcd;
		border-bottom: 1px solid #cdcdcd;
	}
	thead td:last-child {
		border-right: 1px solid #cdcdcd;
	}

		tbody tr {
			--border-top: 1px solid #ededed;
		}
		tbody td {
			border-bottom: 1px solid #ededed;
		}

	td {
		padding: 8px;
		font-size: 12px;
		vertical-align: top;
	}

	tbody td:nth-child(3) {
		display: flex;
	}

	td.value {
		text-align: right;
	}
	td.type2, 
	td.type3,
	td.neg {
		color: red;
	}

	td.sum1 {
		border: 1px solid transparent;
	}
	td.sum2 {
		background: silver;
		font-weight: bold;
	}
	div.time {
		font-size: 10px;
		color: gray;
		padding-top: 4px;
	}
		.blocked div.time {
			color: silver;
		}

	.icon, .detail {
		display: inline;
		float: left;
  		flex-direction: column;
	}

	.icon {
	    height: 24px;
	    width: 24px;
	    background-size: 100%;
	    margin: 0 8px 0 0;
	    opacity: .75;
	}

	.icon101 {
		background-image: url(http://r2.rfidle.com/protect_v2/assets/img/101b.png);
	}
	.icon201 {
		background-image: url(http://r2.rfidle.com/protect_v2/assets/img/TrueSMS_TUGo_Icon.png);
	    background-size: 96%;
	    background-repeat: no-repeat;
	    background-position: center;
	}
	.icon501 {
		background-image: url(http://r2.rfidle.com/protect_v2/assets/img/501.png);
	    background-size: 96%;
	    background-repeat: no-repeat;
	    background-position: center;
	}

	.hist-type-2,
	.hist-type-3 {
		-color: red;
	}


	.tr.hidden {
		display: none;
	}
</style>

<?php foreach ($movements as $key => $records) : ?>

	<h1><span><?=$key?></span>
		 <?php if ($key == 'D')  { ?>
		 	<span style="font-size:12px; color:#fff; vertical-align:middle;">-<?php echo $antecipado; ?></span>
		 <?php } ?>
	</h1>
	 

	<table>
		<thead>
			<tr>
				<td>Data</td>
				<td>Disponível</td>
				<td>Nº da operação</td>
				<td>Histórico</td>
				<td>Valor</td>
				<td>Saldo</td>
			</tr>
	  	</thead>
		<tbody>

			<?php 

				//echo '<pre>';
				//var_dump($movements);
				//echo '</pre>';
				$i = 0;
			 ?>



				<?php foreach ($records as $move) :  $class = ($i > 10) ? 'hidden' : 'nop'; ?>
					<tr class="<?= $class; ?>">
						<td class="col1">
							<div>
								<?= date('d/m/Y', strtotime($move["date"])); ?>
							</div>
							<div class="time">
								<?= date('H:i', strtotime($move["date"])); ?>
							</div>
						</td>
						<td class="col1 <?php if ($key == 'B') echo 'blocked'; ?>">
							<?php if ($move["free"] != NULL) : ?>
								<div>
									<?= date('d/m/Y H:i', strtotime($move["free"])); ?>
								</div>

							<?php //if ($key == 'D') : ?>
								<div class="time">
									<?= date('H:i', strtotime($move["free"])); ?>
								</div>
							<?php //endif; ?>
							<?php endif; ?>
						</td>

						<td class="col2"><?= $move["id"]; ?></td>
						<td>
							<?php if ($move["type"] == 1) : ?>
								
								<?php if ($move['appl'] == 101) : ?>
									<img class="icon" src="http://r2.rfidle.com/protect_v2/assets/img/101b.png">
								<?php endif; ?>

								<?php if ($move['appl'] == 105) : ?>
									<img class="icon" src="http://r2.rfidle.com/protect_v2/assets/img/105.png">
								<?php endif; ?>

								<?php if ($move['appl'] == 107) : ?>
									<img class="icon" src="http://r2.rfidle.com/protect_v2/assets/img/TrueWhats_HUB.png">
								<?php endif; ?>

								<?php if ($move['appl'] == 124) : ?>
									<img class="icon" src="http://r2.rfidle.com/protect_v2/assets/img/TrueWAppService.png">
								<?php endif; ?>

								<?php if ($move['appl'] == 201) : ?>
									<img class="icon" src="http://r2.rfidle.com/protect_v2/assets/img/TrueSMS_TUGo_Icon.png">
								<?php endif; ?>

								<?php if ($move['appl'] == 501) : ?>
									<img class="icon" src="http://r2.rfidle.com/protect_v2/assets/img/501.png">
								<?php endif; ?>

								<?php if ($move['appl'] == 601) : ?>
									<img class="icon" src="http://r2.rfidle.com/protect_v2/assets/img/601.png">
								<?php endif; ?>

								<?php if ($move['appl'] == 901) : ?>
									<img class="icon" src="http://r2.rfidle.com/protect_v2/assets/img/901.png">
								<?php endif; ?>

								<!--div class="icon icon<?= $move['appl']; ?>"></div-->
							<?php endif; ?>

							<div class="detail">
								<div class="hist-type-<?= $move['type']; ?>">
									<?= $move["hist"]; ?>
								</div>
								<div class="time">
									<?= $move["subh"]; ?>
								</div>
							</div>
						</td>
						<td class="col4 value type<?= $move['type']; ?>">R$ <?= number_format($move["valu"], 2, ',', '.'); ?></td>
						<td class="col5 value <?php if ($move['bala'] < 0) echo 'neg'; else echo 'pos'; ?>">R$ <?= number_format($move["bala"], 2, ',', '.'); ?></td>
					</tr>
					<?php //$i++; ?>
				<?php endforeach; ?>


	  	</tbody>
	</table>

<?php endforeach; ?>

