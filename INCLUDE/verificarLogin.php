<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id']) || !isset($_SESSION['email']) || !isset($_SESSION['tipo'])) {
    header("Location: ../paginas-iniciais/pagina-de-login.php");
    exit();
}

$paginaAtual = $_SERVER['PHP_SELF'];

if (strpos($paginaAtual, 'adm/') !== false && $_SESSION['tipo'] !== 'admin') {
    header("Location: ../vend/dashboard_vendedor.php");
    exit();
}

if (strpos($paginaAtual, 'vend/') !== false && $_SESSION['tipo'] !== 'vendedor') {
    header("Location: ../adm/dashboard-adm.php");
    exit();
}

?>