<?php
    if(isset($_POST['remover'])){
        header("location:../adm/lista-vendedores-adm.php");
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação para remover Vendedor</title>
    <link rel="stylesheet" href="../../PUBLIC/css/informacoes_vendedor.css">
</head>
<body>
    <div class="lc_area-infos">

        <div class="lc_img-infos">
            <img class="lc_img" src="../../PUBLIC/img/img_user.png" alt=""> 
            <div class="lc_infos">
                <p class="lc_info">Nome</p>
                <p class="lc_dado">Rafael Germinari</p>
            </div>
        </div>
        
        <div class="lc_infos">
            <p class="lc_info">CPF</p>
            <p class="lc_dado">12345678919</p>
        </div>  
        
        <div class="lc_infos">
            <p class="lc_info">Email</p>
            <p class="lc_dado">rafaelgerminari@gmail.com</p>
        </div>
        
        <div class="lc_infos">
            <p class="lc_info">Telefone</p>
            <p class="lc_dado">+99 (99) 99999-9999</p>
        </div>
        
        <div class="lc_infos">
            <p class="lc_info">Data de Nascimento</p>
            <p class="lc_dado">01/01/1992</p>
        </div>

        <form method="POST" action="">  
            <input class="lc_remove-btn" type="submit" name="remover" value="Remover">
        </form>

    </div>
</body>
</html>

<script src="../../PUBLIC/JS/script-pop-up.js"></script>