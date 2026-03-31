<?php
    include "../../CONTROLLER/UsuarioController.php";
    include "../../INCLUDE/Menu_adm.php";
    include "../../INCLUDE/vlibras.php";
    require_once "../../INCLUDE/verificarLogin.php"; 
    include "../../INCLUDE/alertas.php";
    
    $controler_user = new UsuarioController();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['adicionar'])){
            $usuario = $controler_user->criar("vendedor");
            print_r($usuario);

            if($usuario == 1){
                $_SESSION['alerta'] =  '<script> exibirAlerta("Vendedor cadastrado com sucesso","sucesso"); </script>';
            }elseif($usuario == "Já existe um usuário cadastrado com este email."){
                $_SESSION['alerta'] = '<script> exibirAlerta("Já existe um vendedor cadastrado com este email"); </script>';
            }elseif($usuario['error'] == "Você precisa ter pelo menos 18 anos para se cadastrar."){
                $_SESSION['alerta'] = '<script> exibirAlerta("Você precisa ter pelo menos 18 anos para se cadastrar"); </script>';
            }else{
                $_SESSION['alerta'] = '<script> exibirAlerta("Não foi possível cadastrar o vendedor","error"); </script>';
            }

        }

        if (isset($_POST['alter_status'])){
            $user_id = $_SESSION['id'] ?? null;
            $usuario = $controler_user->mostrar($user_id);
            $id = $_POST['id'];
            $vendedor = $controler_user->mostrar($id);
            $senha = $_POST['alter_status'];

            if($controler_user->verificar_senha($_SESSION,$senha)){
                if($vendedor['status'] == "ATIVADO"){
                    $vendedor = $controler_user->desativar($id);
                    if($vendedor == 1){
                        $_SESSION['alerta'] = '<script> exibirAlerta("Vendedor desativado com sucesso","sucesso"); </script>';
                    }else{
                        $_SESSION['alerta'] = '<script> exibirAlerta("Não foi possível desativar o Vendedor","error"); </script>';
                    }
                }else{
                    $vendedor = $controler_user->ativar($id);
                    if($vendedor == 1){
                        $_SESSION['alerta'] = '<script> exibirAlerta("Vendedor ativado com sucesso","sucesso"); </script>';
                    }else{
                        $_SESSION['alerta'] = '<script> exibirAlerta("Não foi possível ativar o Vendedor","error"); </script>';
                    }
                }
                
            }else{
                $_SESSION['alerta'] = '<script> exibirAlerta("Senha incorreta","error"); </script>';
            }
        }

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;

    }


    if (isset($_GET['visualizar'])){
        $id = $_GET['visualizar'];
        $usuario = $controler_user->mostrar($id);
        header('Location: info-edit-adm.php?id=' . $id . "&usuario=" . $usuario['tipo']);
        exit;
    } 
    
    $usuarios = $controler_user->index("vendedor");
    
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Vendedores</title>
    <link rel="stylesheet" href="../../PUBLIC/css/lista-vendedores-adm.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

    <!-- pop-up -->
    <div class="ym_popup-overlay" >
        <div class="ym_popup-content">
            <div class="ym_area-superior-popup"></div>
            <div class="ym_conteudo-popup"></div>
        </div>
    </div>

    <main class="jp_main-content">
        <h1 class="ym_titulo">Vendedores</h1>

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
                                <input type="text" name="pesquisa" id="jv_searchInput" placeholder="Pesquisar por nome ou email..." class="jv_search-input" oninput="Pesquisar()" >
                            </div>
                        </form>

                        <div class="jv_actions">
                            <div>
                                <button class="ym_btn-remover" id="jv_removeSelected" style="display: none;">
                                    <i class="fa-solid fa-trash-can"></i>
                                    Remover (<span id="jv_selectedCount">0</span>)
                                </button>
                            </div>
                            <div>
                                <button type="button" class="ym_btn-padrao" onclick="abrirPopup('../../VIEW/pop-up/cadastrar_vendedor.php','Cadastro de Vendedores')">
                                    <i class="fas fa-plus"></i>
                                    <span>Cadastrar Vendedor</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="jv_subtitle" id="jv_customerCount">
                        <?= $total_vendedores ?> vendedores encontrados
                    </p>
                </div>

                <!-- Table -->
                <div class="jv_card-content">
                    <div class="jv_table-container">
                        <table class="jv_table">
                            <thead>
                                <tr class="jv_table-header">
                                    <!-- <th class="jv_checkbox-col">
                                        <input type="checkbox" id="jv_selectAll" class="jv_checkbox">
                                    </th> -->
                                    <th class="jv_name">Nome</th>
                                    <th class="jv_banguela">Telefone</th>
                                    <th class="jv_data">Data de Nascimento</th>
                                    <th class="jv_data">Status</th>
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


        <!-- Paginação -->
        <div class="jv_page-navigation">
        </div>

        <script>
            const dados= <?php echo json_encode($usuarios); ?>;
        </script>
        <script src="../../PUBLIC/JS/script-lista-vendedores.js"></script>
        <script src="../../PUBLIC/JS/script-pop-up.js"></script>
        <!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->
</main>
</body>
</html>
