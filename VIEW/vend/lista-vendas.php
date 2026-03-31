<?php
include "../../INCLUDE/Menu_vend.php";
include "../../CONTROLLER/VendaController.php";
include "../../CONTROLLER/UsuarioController.php";
include "../../CONTROLLER/ClienteController.php";
include "../../INCLUDE/vlibras.php";
require_once "../../INCLUDE/verificarLogin.php"; 

$vendedor_logado = $_SESSION['id'] ?? null; // pega o id do vendedor logado

$venda_control = new VendaController(); 
$vendas = $venda_control->index($vendedor_logado); // filtra pelas vendas do vendedor logado

$cliente_control = new ClienteController();

$total_vendas = count($vendas);

// Verificar ações GET
if(!empty($_GET)){
    if (isset($_GET['visualizar'])){
        $id = $_GET['visualizar'];
        header('Location: venda-info-vend.php?id=' . $id);
        exit;
    } elseif (isset($_GET['remover'])){
        $id = $_GET['remover'];
        $venda_control->deletar($id);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Vendas</title>
    <link rel="stylesheet" href="../../PUBLIC/css/lista-vendas-vend.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <!-- pop-up -->
    <div class="ym_popup-overlay">
        <div class="ym_popup-content">
            <div class="ym_area-superior-popup"></div>
            <div class="ym_conteudo-popup"></div>
        </div>
    </div>

    <main class="jp_main-content">
        <h1 class="ym_titulo">Vendas</h1>

        <div class="jv_container">
            <div class="jv_card">
                <!-- Header -->
                <div class="jv_card-header">
                    <div class="jv_header-content">
                        <form method="POST" action="#" class="jv_search-section">
                            <div class="jv_search-container">
                                <button type="submit" class="ym_area-icon-pesquisa" name="pesquisar">
                                    <i class="fas fa-search search-icon"></i>
                                </button>
                                <input type="text" name="pesquisa" id="jv_searchInput" placeholder="Pesquisar por venda ou cliente..." class="jv_search-input" oninput="Pesquisar()">
                            </div>
                        </form>
                    </div>
                    
                    <p class="jv_subtitle" id="jv_customerCount">
                        <?= $total_vendas ?> <?= $total_vendas == 1 ? 'venda encontrada' : 'vendas encontradas' ?>
                    </p>
                </div>

                <!-- Table -->
                <div class="jv_card-content">  
                    <div class="jv_table-container">
                        <table class="jv_table">
                            <thead>
                                <tr class="jv_table-header">
                                    <th><p class="jv_name">Vendedor</p></th>
                                    <th class="jv_date">Cliente</th>
                                    <th class="jv_date">Valor</th>
                                    <th class="jv_actions-col"></th>
                                </tr>
                            </thead>
                            <tbody id="jv_customerTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="jv_page-navigation">
        </div>

        <script>
            const dados = <?php echo json_encode($vendas); ?>;
        </script>   
        <script src="../../PUBLIC/JS/script-vendas.js"></script>
        <script src="../../PUBLIC/JS/script-pop-up.js"></script>
        <!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->
    </main>
</body>
</html>