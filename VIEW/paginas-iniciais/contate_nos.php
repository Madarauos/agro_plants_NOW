<?php
include "../../INCLUDE/Menu_superior.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once "../../CONTROLLER/NotificacaoController.php";
    
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if (!empty($name) && !empty($email) && !empty($message)) {
        $notificacaoCtrl = new NotificacaoController();
        $result = $notificacaoCtrl->criarNotificacaoContato($name, $email, $message);
        
        if (isset($result['success'])) {
            http_response_code(200);
            exit;
        } else {
            http_response_code(500);
            exit;
        }
    } else {
        http_response_code(400);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contate-nos</title>
    <link rel="stylesheet" href="../../PUBLIC/css/contate_nos.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu_superior.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    </style>
</head>
<body>  
    <main class="contact-section">
        <div class="contact-info-container">
            <h2 class="contact-title">Fale Conosco</h2>
            <p class="contact-description">
                Estamos aqui para ajudar! Entre em contato conosco para qualquer dúvida, sugestão ou suporte.
            </p>
            <div class="info-items">
                <div class="info-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                </svg>
                    <div>
                        <p class="info-label">Ligue para nós</p>
                        <p class="info-value">+55 (67) 99999-9999</p>
                    </div>
                </div>
                <div class="info-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                </svg>
                    <div>
                        <p class="info-label">Localização</p>
                        <p class="info-value">Rua Santo dos Santos, 999 Campo Grande - MS</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-container">
            <h3 class="form-title">Contate-nos</h3>
            <p id="form-message" class="form-status-message"></p>
            <form id="contact-form">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Nome" class="form-input" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="E-mail" class="form-input" required>
                </div>
                <div class="form-group">
                    <textarea name="message" placeholder="Sua Mensagem" rows="6" class="form-textarea" required></textarea>
                </div>
                <button type="submit" class="submit-button">Enviar Mensagem</button>
            </form>
        </div>
    </main>

    <footer>
        <?php include "../../INCLUDE/footer.php"; ?>
    </footer>
    <script src="../../PUBLIC/JS/contate-nos.js"></script>
    <script src="../../PUBLIC/JS/script-menu-superior.js"></script>
</body>
</html>