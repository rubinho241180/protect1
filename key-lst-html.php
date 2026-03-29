<style type="text/css">
body {
	font-family: Tahoma;
}
table {
	width: 1300px;
}
table td {
	border: 1px solid black;
	padding: 4px;
	vertical-align: top;
}
table td:nth-child(1) {
	width: 125px;
} 
table td:nth-child(2) {
	width: 125px;
} 
table td:nth-child(3) {
	width: 250px;
} 
table td:nth-child(6) {
	width: 125px;
} 
table td:nth-child(7) {
	width: 300px;
} 
</style>

<table>
	<tr>
		<td>Key</td>
		<td>Instalação</td>
		<td>Nome</td>
		<td>Software</td>
		<td>Serial</td>
		<td>Valores</td>
		<td>Observações</td>
	</tr>

<?php 


	

foreach ($json['terminals'] as $key => $value) { 

		switch ($value['app_id']) {
		    case 101:
		        $app_name = 'WHATSAPP';
		        break;
		    case 201:
		        $app_name = 'SMS';
		        break;
		    case 301:
		        $app_name = 'OLX';
		        break;
		 }
	?>

	<tr>
		<td><?php echo $value['mac_id']."-".$value['app_id']; ?></td>
		<td><?php echo $value['date']; ?></td>
		<td><?php echo base64_decode($value['cus_name']); ?></td>
		<td><?php echo $app_name; ?></td>
		<td>
			<div><?php echo $value['keys'][0]['skey']; ?>
			<div>Data: <?php echo $value['keys'][0]['dbuild']; ?>
		</td>
		<td>
			<div><?php echo $value['keys'][0]['liquid']; ?>
			<div><?php echo $value['keys'][0]['liquid']; ?>
			<div><?php echo $value['keys'][0]['liquid']; ?>
		</td>
		<td><?php echo nl2br(base64_decode($value['keys'][0]['info'])); ?></td>
	</tr>


<?php	
}

?>

</table>

