<?php
include "../../INCLUDE/Menu_vend.php";
include "../../CONTROLLER/CupomController.php";
include "../../INCLUDE/vlibras.php";
require_once "../../INCLUDE/verificarLogin.php"; 

$cupom_control = new CupomController();
$cupons = $cupom_control->index();
$total_cupons = count($cupons);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Cupons</title>
    <link rel="stylesheet" href="../../PUBLIC/css/cupom-vend.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

<main class="jp_main-content">
    <h1 class="ym_titulo">Cupons</h1>

    <div class="jv_container">
        <div class="jv_card">
            <div class="jv_card-header">
                <div class="jv_header-content">
                    <form method="POST" action="#" class="jv_search-section">
                        <div class="jv_search-container">
                            <button type="submit" class="ym_area-icon-pesquisa" name="pesquisar">
                                <i class="fas fa-search search-icon"></i>
                            </button>
                            <input type="text" name="pesquisa" id="jv_searchInput" placeholder="Pesquisar por código..." class="jv_search-input" oninput="Pesquisar()">
                        </div>
                    </form>
                </div>
                <p class="jv_subtitle" id="jv_customerCount">
                    <?= $total_cupons ?> <?= $total_cupons == 1 ? 'cupom encontrado' : 'cupons encontrados' ?>
                </p>
            </div>

            <div class="jv_card-content">
                <div class="jv_table-container">
                    <table class="jv_table">
                        <thead>
                            <tr class="jv_table-header">
                                <th class="jv_codigo">Código</th>
                                <th class="jv_desconto">Desconto</th>
                                <th class="jv_cadastro">Data de Cadastro</th>
                                <th class="jv_validade">Validade</th>
                         
                            </tr>
                        </thead>
                        <tbody id="jv_customerTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Paginação -->
    <div class="jv_page-navigation">
    </div>

</main>
</body>
</html>

<script>
    const dados = <?php echo json_encode($cupons); ?>;
</script>
<script src="../../PUBLIC/JS/script-cupom.js"></script>