<?php 

function curl_get_contents($url) {
	$ch = curl_init();
	$timeout = 5;

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

	$data = curl_exec($ch);

	curl_close($ch);

	return $data;
}


function dateDiff($start, $end) {
	$start_ts = $start;//strtotime($start);
	$end_ts = $end;//strtotime($end);
	$diff = $end_ts - $start_ts;
	return round($diff / 86400);
}

function is_email2($email){
	//verifica se e-mail esta no formato correto de escrita
	if (!ereg('^([a-zA-Z0-9.-_])*([@])([a-z0-9]).([a-z]{2,3})',$email)){
		$mensagem='E-mail Inv&aacute;lido!';
		return $mensagem;
    }
    else{
		//Valida o dominio
		$dominio=explode('@',$email);
		if(!checkdnsrr($dominio[1],'A')){
			$mensagem='E-mail Inv&aacute;lido!';
			return $mensagem;
		}
		else{return true;} // Retorno true para indicar que o e-mail é valido
	}
}

function is_mail($email){
    $er = "/^(([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}){0,1}$/";
    if ((trim($email != '')) && (preg_match($er, $email))){
	return true;
    } else {
	return false;
    }
}

function only_numbers($str) {
    return preg_replace("/[^0-9]/", "", $str);
}



function change_key( &$array, $old_key, $new_key) {

    if( ! array_key_exists( $old_key, $array ) )
        return $array;

    $keys = array_keys( $array );
    $keys[ array_search( $old_key, $keys ) ] = $new_key;

    //return array_combine( $keys, $array );
    $array = array_combine( $keys, $array );
}



function addSMS($to, $text) {
	require_once "db.php";
	$pdo = connect_pdo();
	//$qry = $pdo->exec("insert into sms (target, text) values ('$to', '$text')");

	$sql = "insert into sms (target, text) values (:to, :text)";
	$sta = $pdo->prepare($sql);

	$tgs = explode(",", $to);


	foreach ($tgs as $key => $value) {

		$qry = $sta->execute(
					array(
						"to" => $value,
						"text" => $text,
					)
			   );
	}




	if (!$qry) {
		var_dump($pdo->errorInfo());
	} 

}