<?php
 
/* apenas dispara o envio do formulário caso exista $_POST['enviarFormulario']*/
 
//if (isset($_POST['enviarFormulario'])){
 
 
/*** INÍCIO - DADOS A SEREM ALTERADOS DE ACORDO COM SUAS CONFIGURAÇÕES DE E-MAIL ***/
 
$enviaFormularioParaNome = 'Nome do destinatário que receberá formulário';
$enviaFormularioParaEmail = 'rubinho241180@gmail.com';
 
 $caixaPostalServidorNome = 'WebSite | Formulário';
$caixaPostalServidorEmail = 'sender@truesistemas.com.br';
$caixaPostalServidorSenha = 'ws461300321';
 
/*** FIM - DADOS A SEREM ALTERADOS DE ACORDO COM SUAS CONFIGURAÇÕES DE E-MAIL ***/ 
 
 
/* abaixo as veriaveis principais, que devem conter em seu formulario*/
 
$remetenteNome  = 'remetenteNome';
$remetenteEmail = 'naoresponda@truesistemas.com.br';
$assunto  = 'MeuAssunto';
$mensagem = 'MinhaMensagem';
 
$mensagemConcatenada = 'Formulário gerado via website'.'<br/>'; 
$mensagemConcatenada .= '-------------------------------<br/><br/>'; 
$mensagemConcatenada .= 'Nome: '.$remetenteNome.'<br/>'; 
$mensagemConcatenada .= 'E-mail: '.$remetenteEmail.'<br/>'; 
$mensagemConcatenada .= 'Assunto: '.$assunto.'<br/>';
$mensagemConcatenada .= '-------------------------------<br/><br/>'; 
$mensagemConcatenada .= 'Mensagem: "'.$mensagem.'"<br/>';
 
 
/*********************************** A PARTIR DAQUI NAO ALTERAR ************************************/ 
 
require_once('PHPMailer/PHPMailerAutoload.php');
 
$mail = new PHPMailer();
 
$mail->IsSMTP();
$mail->SMTPAuth  = true;
$mail->Charset   = "UTF-8";//'utf8_decode()';
$mail->Host  = 'smtp.truesistemas.com.br';
$mail->Port  = '587';
$mail->Username  = $caixaPostalServidorEmail;
$mail->Password  = $caixaPostalServidorSenha;
$mail->From  = $caixaPostalServidorEmail;
$mail->FromName  = utf8_decode($caixaPostalServidorNome);
$mail->IsHTML(true);
$mail->Subject  = utf8_decode($assunto);
$mail->Body  = utf8_decode($mensagemConcatenada);
 
 
$mail->AddAddress($enviaFormularioParaEmail,utf8_decode($enviaFormularioParaNome));
 
if(!$mail->Send()){
$mensagemRetorno = 'Erro ao enviar formulário: '. print($mail->ErrorInfo);
}else{
$mensagemRetorno = 'Formulário enviado com sucesso!';
} 
 
 
//}
?>
 
 
 
<!DOCTYPE html>
<html lang="pt-BR">
 
<head>
    <meta charset="utf-8">
<title>Formulário Exemplo Autenticado</title>
 
 
</head>
 
<body>
 
<?php
if(isset($mensagemRetorno)){
echo $mensagemRetorno;
}
 
?>

 
</body>
</html>