<?php

$link = 'https://whatsapp.linkbacana.com';

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Copiar Link</title>
    <link rel="stylesheet" href="../../PUBLIC/css/pop-up-link.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body> 

    <div class="ym_inferior-pop-up" style="margin-left: 5px;">
        <p id="ym_link"><?php echo $link; ?></p>
        <button onclick="copiarLink()" class='ym_btn-pop-up'><i class="fa-solid fa-copy"></i></button>
    </div>
            
    <script>
        function copiarLink() {
            var texto = document.getElementById("ym_link").innerText;
            navigator.clipboard.writeText(texto)
            
        }
    </script>
</body>

</html>
