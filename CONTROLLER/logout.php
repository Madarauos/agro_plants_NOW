<?php
require_once 'UsuarioController.php';

$controller = new UsuarioController();
$result = $controller->logout();

if (isset($result['redirect'])) {
    header('Location: ' . $result['redirect']);
    exit;
}
?>