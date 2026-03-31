<?php
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviar_email($email,$codigo,$nome){
    $mail = new PHPMailer(true);
    try{
    
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = "agroplantsnow@gmail.com";
        $mail->Password = "vmlx xuhy qxbs darh";
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
    
        $mail->setFrom("agroplantsnow@gmail.com","Suporte - Agro Plants Now");
        $mail->addAddress($email,$nome);
        
        $mail->isHTML(true);
        $mail->Subject = 'Recuperar de senha';
        $mail->Body = "Olá <b>$nome</b>, <br><br> 
        Seu código para recuperar a senha é: <b>$codigo</b><br><br>";
        $mail->AltBody = "Olá $nome =, \n\n O código de recuperação é: $codigo";
    
        $mail->send();
    }
    catch (Exception $e){
        echo "Erro ao enviar o e-mail: {$mail->ErrorInfo}";
    }
}

?>

<!-- CONTA GOOGLE DO PI -->
<!-- Email: agroplantsnow@gmail.com -->
<!-- Senha: agroplantsnow123 -->