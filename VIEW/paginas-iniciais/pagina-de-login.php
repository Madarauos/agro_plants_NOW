<?php
ob_start();
include "../../CONTROLLER/UsuarioController.php";

$controler_user = new UsuarioController();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['id'], $_SESSION['email'], $_SESSION['tipo'])) {
    if ($_SESSION['tipo'] == 'admin') {  
        header("Location: ../adm/dashboard-adm.php");
        exit;
    } elseif ($_SESSION['tipo'] == 'vendedor') {
        header("Location: ../vend/dashboard_vendedor.php");
        exit;
    }
}

include "../../INCLUDE/Menu_superior.php";
include "../../INCLUDE/phpmailer.php";
include "../../INCLUDE/vlibras.php";

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de login</title>
    <link rel="stylesheet" href="../../PUBLIC/css/pagina-de-login.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu_superior.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>    
    <!-- pop-up -->
    <div class="ym_popup-overlay">
        <div class="ym_carregamento-content"></div>
        <div class="ym_popup-content">
            <div class="ym_area-superior-popup"></div>
            <div class="ym_conteudo-popup"></div>
        </div>
    </div>

    <div class="ym_area-alertas"></div>

    <section class="jc_login-section">
        <div class="jc_total">
            <div class="jc_left-side">
                <h2>Agricultura de </h2>
                <h2>qualidade é aqui</h2>   
            </div>

            <div class="jc_login-box">
                <h3>Iniciar a sessão</h3> 
                
                <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="../../CONTROLLER/login.php" onsubmit="return validarFormulario()">
                    <div class="lc_area-inputs">
                        <input type="email" class="jc_input-field" name="email" placeholder="E-mail" required>
                        <input type="password" class="jc_input-field" name="senha" placeholder="Senha" required>
                    </div>
                    <div class="lc_area-links">
                        <a onclick="abrirPopup('../pop-up/pop-up-email-recuperar-senha.php')" class="jc_forgot-password">Esqueceu sua senha?</a>
                    </div>

                    <?php include "../../INCLUDE/reCaptcha.php"; ?>

                    <input type="submit" class="jc_login-btn" value="Iniciar Sessão">
                </form>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="../../PUBLIC/JS/script-menu-superior.js"></script>
    <script src="../../PUBLIC/JS/script-pop-up.js"></script>
    <script src="../../PUBLIC/JS/script-carregamento.js"></script>
    <script src="../../PUBLIC/JS/script-alertas.js"></script>

    <script>
        function validarFormulario() {
            const response = grecaptcha.getResponse();
            if (response.length === 0) {
                exibirAlerta("Por favor, confirme que você não é um robô.", "error");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>

<?php
if(isset($_SESSION['senha_alterada'])){
    if($_SESSION['senha_alterada']){
        echo '<script>  ; </script>';
        unset($_SESSION['senha_alterada']);
    }
}

if(isset($_GET['email_enviado'])){
    echo"<script>abrirPopup('../pop-up/pop-up-recuperacao-de-senha.php')</script>";
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['email'])){
        $email = $_POST['email'];
        $usuario = $controler_user->mostrar_email($email);
        $codigo = random_int(100000, 999999);
        $_SESSION['codigo'] = $codigo;
        $_SESSION['user_id'] = $usuario['id'];
        
        if(isset($usuario['error'])){
            echo '<script>exibirAlerta("Não existe um usuário com este email","error")</script>';
        }else{
            $nome = $usuario['nome'];
            enviar_email($email,$codigo,$nome);
            header("Location: " . $_SERVER['PHP_SELF'] . "?email_enviado");
        }
    }

    if(isset($_POST['codigo'])){
        if($_POST['codigo'] == $_SESSION['codigo']){
            $_POST["alter_senha"]="";
            unset($_POST['codigo']);
        }else{
            echo '<script>exibirAlerta("O código está errado","error")</script>';
        }
    }

    if(isset($_POST['alter_senha'])){
        echo"<script>abrirPopup('../pop-up/pop-up-criar-senha.php')</script>";
    }

    if(isset($_POST['nova_senha'])){
        if($_POST['nova_senha'] == $_POST['conf_senha']){
            $alterar_senha = $controler_user->alterar_senha();
            if($alterar_senha == 1 ){
                $_SESSION['senha_alterada'] = true;
                header("Location: " . $_SERVER['PHP_SELF']);
            }
        }
        else{
            echo '<script>exibirAlerta("As senhas não são iguais","error")</script>';
        }
    }

}

if(isset($_GET['error'])){
    echo '<script>exibirAlerta("Não foi possível iniciar a sessão","error")</script>';
}

ob_end_flush();
?>
