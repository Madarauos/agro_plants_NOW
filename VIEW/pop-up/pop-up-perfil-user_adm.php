<?php
$nome = 'José Farmer';
$email = 'josefarmer@gmail.com';
$link = 'https://github.com';
?>


<!DOCTYPE html>
<html lang="ptbr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User</title>
    <link rel="stylesheet" href="../../PUBLIC/css/pop-up-perfil-user_adm.css">
</head>
<body>
    <section class="ym_area-pop-up">

        <div class="ym_pop-up">

            <div class="ym_texto-btn-pop-up">
                <div class="ym_area-texto">
                    <?php 
                    echo "<h1 class='ym_nome-usuario'>" . $nome ."</h1>
                    <p class='ym_email-usuario'>" . $email . "</p>";
                    ?>
                    
                </div>
                <a href="<?= $link ?>" class="ym_btn-acessar-perfil" style="margin-top: 25px;">Acessar perfil</a>
            </div>

            <div class="ym_area-img">
                <img src="../../PUBLIC/img/img_user.png" alt="user" class="ym_img-pop-up" style="margin-top: -5px; margin-left: 50px; width: 50px; height: 50px;">
            </div>
        
        </div>
        
    </section>
</body>
</html>