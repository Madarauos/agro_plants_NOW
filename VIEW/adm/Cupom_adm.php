<?php
include "../../INCLUDE/Menu_adm.php";
include "../../CONTROLLER/CupomController.php";
include "../../INCLUDE/vlibras.php";
require_once "../../INCLUDE/verificarLogin.php"; 
include "../../INCLUDE/alertas.php";


$cupom_control = new CupomController();
$cupons = $cupom_control->index();
$total_cupons = count($cupons);


// Criação de cupom via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $criar_cupom = $cupom_control->criarCupom();

    if($criar_cupom == 1){
        $_SESSION['alerta'] =  '<script> exibirAlerta("Cupom cadastrado com sucesso","sucesso"); </script>';
    }else{
        $_SESSION['alerta'] = '<script> exibirAlerta("Não foi possível cadastrar o Cupom","error"); </script>';
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if(!empty($_GET)){
    if(isset($_GET['remover'])){
        $id = $_GET['remover'];
        $cupom = $cupom_control->deletar($id);

        if($cupom == 1){
            $_SESSION['alerta'] = '<script> exibirAlerta("Cupom deletado com sucesso","sucesso"); </script>';
        }else{
            $_SESSION['alerta'] = '<script> exibirAlerta("Não foi possível deletar o cupom","error"); </script>';
        }

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

if(isset($_SESSION['alerta'])){
    echo($_SESSION['alerta']);
    unset($_SESSION['alerta']);
}

?>

<?php $mostrar_acoes = true; ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Cupons</title>
    <link rel="stylesheet" href="../../PUBLIC/css/vendas-cupom-adm.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

<!-- Pop-up -->
<div class="ym_popup-overlay">
    <div class="ym_popup-content">
        <div class="ym_area-superior-popup"></div>
        <div class="ym_conteudo-popup"></div>
    </div>
</div>

<main class="jp_main-content">
    <h1 class="ym_titulo">Cupons</h1>

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
                            <input type="text" name="pesquisa" id="jv_searchInput" placeholder="Pesquisar por código..." class="jv_search-input"  oninput="Pesquisar()">
                        </div>
                    </form>

                    <div class="jv_actions">
                 
                        <div>
                            <button class="ym_btn-padrao" onclick="abrirPopup('../../VIEW/pop-up/pop-up-cadastroCupom.php','Cadastro de Cupom')">
                                <i class="fas fa-plus"></i>
                                <a>Cadastrar Cupom</a>
                            </button>
                        </div>
                    </div>
                </div>

                <p class="jv_subtitle" id="jv_customerCount">
                    <?= $total_cupons ?> <?= $total_cupons == 1 ? 'cupom encontrado' : 'cupons encontrados' ?>
                </p>
            </div>

            <!-- Table -->
            <div class="jv_card-content">
                <div class="jv_table-container">
                    <table class="jv_table">
                        <thead>
                            <tr class="jv_table-header">
                                
                                <th class="jv_codigo">Código</th>
                                <th class="jv_desconto">Desconto</th>
                                <th class="jv_cadastro">Data de Cadastro</th>
                                <th class="jv_validade">Validade</th>
                                <th></th>
                                </tr>
                        </thead>
                        <tbody id="jv_customerTableBody">

                          <td class="jv_table-action">
                                     <button class="jv_menu-btn" onclick="toggleDropdown(this)">
                                        <i class="fas fa-ellipsis-h"></i>
                                     </button>
                                     <div class="jv_dropdown-separator"></div>
                                     <button class="jv_dropdown-item jv_danger" type="submit" name="remover" value="<?= htmlspecialchars($cliente['id'])?>">
                                          <i class="fas fa-trash"></i> Remover
                                     </button>
                                 </form>
                             </td>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Paginação -->
    <div class="jv_page-navigation">
    </div>

        <script>
            const dados = <?php echo json_encode($cupons); ?>;
        </script>
       <script>
         const MOSTRAR_ACOES = true;
       </script>

        <script src="../../PUBLIC/JS/script-cupom.js"></script>
        <script src="../../PUBLIC/JS/script-pop-up.js"></script>
        <!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->
</main>
</body>
</html>