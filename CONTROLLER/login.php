<?php
require_once 'UsuarioController.php';

$controller = new UsuarioController();
$result = $controller->login();

if (isset($result['redirect'])) {
    header('Location: ' . $result['redirect']);
    exit;
} else if (isset($result['error'])) {
    header('Location: ../VIEW/paginas-iniciais/pagina-de-login.php?error=' . urlencode($result['error']));
    exit;
}
?>