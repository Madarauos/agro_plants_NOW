<?php
include "../../INCLUDE/Menu_vend.php";
include "../../INCLUDE/alertas.php";
include "../../CONTROLLER/ClienteController.php";
include "../../CONTROLLER/CarrinhoController.php";
include "../../CONTROLLER/PedidoController.php";
include "../../CONTROLLER/VendaController.php";
include "../../INCLUDE/vlibras.php";
require_once "../../CONTROLLER/PedidoController.php";
require_once "../../INCLUDE/verificarLogin.php"; 

$cliente_control = new ClienteController();
$carrinho_control = new CarrinhoController();
$pedido_control = new PedidoController();
$venda_control = new VendaController();

$status_filtro = isset($_GET['status']) ? $_GET['status'] : '';

if ($status_filtro) {
    $clientes = $cliente_control->filtrarPorStatusPedido($status_filtro);
} else {
    $clientes = $cliente_control->indexComStatusPedidos();
}

if(isset($_GET['pesquisa'])){
    if($_GET['pesquisa']==""){
        header("Location: lista-clientes.php");
    }
    $clientes = [];
    $clientes[] = $cliente_control->pesquisar();    
    if(is_array($clientes) & $clientes[0] != 'Usuário não encontrado'){
        $total_clientes = count($clientes);
    }else{
        $total_clientes = 0;
    }
}
else{
    $total_clientes = count($clientes);
}

$limite = 4;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $limite;
$total_paginas = ceil($total_clientes / $limite);
$clientes_paginados = array_slice($clientes, $offset, $limite);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nome']) && !isset($_POST['finalizar_pedido']) && !isset($_POST['criar_pedido'])) {
    $criar_cliente = $cliente_control->criarCliente();

    if ($criar_cliente == 1) {
        $_SESSION['alerta'] = '<script> exibirAlerta("Cliente cadastrado com sucesso","sucesso"); </script>';
    } elseif ($criar_cliente != "") {
        $_SESSION['alerta'] = '<script> exibirAlerta("' . $criar_cliente . '","error"); </script>';
    } else {
        $_SESSION['alerta'] = '<script> exibirAlerta("Não foi possível cadastrar o cliente","error"); </script>';
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}   


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar_pedido'])) {
    $id_pedido = $_POST['finalizar_pedido'];
    
    $pedido_info = $pedido_control->mostrar($id_pedido);
    
    if (isset($pedido_info['error'])) {
        $_SESSION['alerta'] = '<script> exibirAlerta("Pedido não encontrado","error"); </script>';
    } else {
        $resultado = $pedido_control->atualizarStatus($id_pedido, 'FINALIZADO');
        
        if (isset($resultado['success'])) {
            $dados_venda = [
                'data_venda' => date('Y-m-d H:i:s'),
                'id_pedido' => $id_pedido,
                'id_vendedor' => $_SESSION['id'],
                'id_cliente' => $pedido_info['id_cliente'],
                'total' => $pedido_info['total']
            ];
            
            $resultado_venda = $venda_control->criarVenda($dados_venda);
            
            if ($resultado_venda) {
                $_SESSION['alerta'] = '<script> exibirAlerta("Pedido finalizado e venda registrada com sucesso!","sucesso"); </script>';
            } else {
                $_SESSION['alerta'] = '<script> exibirAlerta("Pedido finalizado, mas erro ao registrar venda","error"); </script>';
            }
        } else {
            $_SESSION['alerta'] = '<script> exibirAlerta("Erro ao finalizar pedido","error"); </script>';
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['criar_pedido'])) {
    $id_cliente = $_POST['criar_pedido'];
    $id_vendedor = $_SESSION['id'];
    
    $status_inicial = 'PENDENTE';
    
    $resultado = $carrinho_control->criarPedidoDoCarrinho($id_cliente, $id_vendedor, $status_inicial);
    
    if (isset($resultado['success'])) {
        $_SESSION['alerta'] = '<script> exibirAlerta("' . $resultado['success'] . '","sucesso"); </script>';
    } else {
        $mensagem_erro = $resultado['error'];
        if (strpos($mensagem_erro, 'Estoque insuficiente') !== false) {
            $mensagem_erro = "Erro: Estoque insuficiente para alguns produtos. Verifique as quantidades disponíveis.";
        }
        $_SESSION['alerta'] = '<script> exibirAlerta("' . $mensagem_erro . '","error"); </script>';
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar_pedido'])) {
    $id_pedido = $_POST['finalizar_pedido'];
    
    $pedido_info = $pedido_control->mostrar($id_pedido);
    
    if (isset($pedido_info['error'])) {
        $_SESSION['alerta'] = '<script> exibirAlerta("Pedido não encontrado","error"); </script>';
    } else {
        $resultado = $pedido_control->atualizarStatus($id_pedido, 'FINALIZADO');
        
        if (isset($resultado['success'])) {
            $_SESSION['alerta'] = '<script> exibirAlerta("Pedido finalizado com sucesso!","sucesso"); </script>';
        } else {
            $_SESSION['alerta'] = '<script> exibirAlerta("Erro ao finalizar pedido","error"); </script>';
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


if(!empty($_GET)){
    if (isset($_GET['visualizar'])){
        $id = $_GET['visualizar'];
        header('Location: info-edit-vend.php?id=' . $id . '&usuario=cliente');
        exit;
    } elseif (isset($_GET['remover'])){
        $id = $_GET['remover'];
        $cliente = $cliente_control->deletar($id);
        if($cliente == 1){
            $_SESSION['alerta'] = '<script> exibirAlerta("Cliente deletado com sucesso","sucesso"); </script>';
        } else {
            $_SESSION['alerta'] = '<script> exibirAlerta("Não foi possível deletar o cliente","error"); </script>';
        }
        header("Location: lista-clientes.php");
        exit;
    }
}

if(isset($_SESSION['alerta'])){
    echo($_SESSION['alerta']);
    unset($_SESSION['alerta']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Clientes</title>
    <link rel="stylesheet" href="../../PUBLIC/css/clientes-adm.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

<div class="ym_popup-overlay">
    <div class="ym_popup-content">
        <div class="ym_area-superior-popup"></div>
        <div class="ym_conteudo-popup"></div>
    </div>
</div>

<main class="jp_main-content">
    <h1 class="ym_titulo">Clientes</h1>

    <div class="jv_container">
        <div class="jv_card">
            <div class="jv_card-header">
                <div class="jv_header-content">
                    <div class="jv_search-section">
                        <form class="jv_search-container" method="GET">
                            <button class="ym_area-icon-pesquisa" type="submit">
                                <i class="fas fa-search search-icon"></i>
                            </button>
                            <?php
                            if(isset($_GET['pesquisa'])){
                                echo'<input type="text" name="pesquisa" id="jv_searchInput" placeholder="Pesquisar por nome ou email..." class="jv_search-input" value='. $_GET['pesquisa'] .'>';
                            }else{
                                echo'<input type="text" name="pesquisa" id="jv_searchInput" placeholder="Pesquisar por nome ou email..." class="jv_search-input">';
                            }
                            ?>
                        </form>
                    </div>

                    <div class="jv_actions">
                        <div>
                            <button class="ym_btn-remover" id="jv_removeSelected" style="display: none;">
                                <i class="fa-solid fa-trash-can"></i>
                                Remover (<span id="jv_selectedCount">0</span>)
                            </button>
                        </div>
                        <div>
                            <button class="ym_btn-padrao" onclick="abrirPopup('../../VIEW/pop-up/cadastroPessoas.php','Cadastro de Pessoas')">
                                <i class="fas fa-plus"></i>
                                <a>Cadastrar Cliente</a>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="separar">
                    <p class="jv_subtitle" id="jv_customerCount">
                        <?= $total_clientes ?> clientes encontrados
                    </p>

                </div>

            </div>

            <div class="jv_card-content">
                <div class="jv_table-container">
                    <table class="jv_table">
                        <thead>
                            <tr class="jv_table-header">
                                <!-- <th class="jv_checkbox-col">
                                    <input type="checkbox" id="jv_selectAll" class="jv_checkbox">
                                </th> -->
                                <th class="jv_name">Nome</th>
                                <th class="jv_date">Data</th>
                                <th class="jv_total_comp">Status do Pedido</th>
                                <th class="jv_valor_gast">Carrinho</th>
                                <th class="jv_actions-col"></th>
                            </tr>
                        </thead>
                        <tbody id="jv_customerTableBody">
                            <?php if ($total_clientes > 0): ?>
                                <?php foreach ($clientes_paginados as $cliente): ?>
                                    <?php
                                        $ultimoPedido = null;
                                        $pedidoStmt = $pedido_control->indexPorCliente($cliente['id']);
                                        $pedidos = is_array($pedidoStmt) ? $pedidoStmt : [];
                                        
                                        if (!empty($pedidos) && !isset($pedidos['error'])) {
                                            usort($pedidos, function($a, $b) {
                                                return strtotime($b['data_pedido']) - strtotime($a['data_pedido']);
                                            });
                                            $ultimoPedido = $pedidos[0];
                                        }
                                        
                                        $status = $ultimoPedido ? $ultimoPedido['status'] : 'SEM PEDIDOS';
                                        $id_pedido = $ultimoPedido ? $ultimoPedido['id'] : null;
                                        
                                        $carrinhoTemItens = $carrinho_control->carrinhoTemItens($cliente['id']);

                                        switch ($status) {
                                            case 'PAGO':
                                                $progress = 40;
                                                $icone = '<i class="fas fa-dollar-sign"></i>';
                                                break;
                                            case 'ENVIADO':
                                                $progress = 50;
                                                $icone = '<i class="fas fa-truck"></i>';
                                                break;
                                            case 'ENTREGUE':
                                                $progress = 75;
                                                $icone = '<i class="fas fa-box-open"></i>';
                                                break;
                                            case 'FINALIZADO':
                                                $progress = 100;
                                                $icone = '<i class="fas fa-check-circle"></i>';
                                                break;
                                            case 'PENDENTE':
                                                $progress = 25;
                                                $icone = '<i class="fas fa-clock"></i>';
                                        }
                                    ?>
                                    <tr>
                                        <!-- <td>
                                            <input type="checkbox" class="jv_checkbox customer-checkbox" data-customer-id="<?= $cliente['id'] ?>">
                                        </td> -->
                                        <td>
                                            <div class="jv_customer-info">
                                                <div class="jv_avatar">
                                                    <?= strtoupper(substr($cliente['nome'], 0, 2)) ?>
                                                </div>
                                                <div class="jv_customer-details">
                                                    <h4><?= htmlspecialchars($cliente['nome']) ?></h4>
                                                    <p><?= htmlspecialchars($cliente['email']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($cliente['data_nasc'] ?? '-') ?></td>
                                        <td>
                                            <?php if ($status !== 'SEM PEDIDOS'): ?>
                                                <div class="jv_status-wrapper">
                                                    <div class="jv_progress-bar">
                                                        <div class="jv_progress <?= strtolower($status) ?>" 
                                                            style="width: <?= $progress ?>%;"></div>
                                                    </div>
                                                    <span class="jv_status-label">
                                                        <?= $icone ?>
                                                        <?= $status ?>
                                                    </span>
                                                    
                                                    <?php if ($status !== 'FINALIZADO' && $status !== 'CANCELADO'): ?>
                                                        <form method="POST" style="display:inline; margin-left: 10px;">
                                                            <input type="hidden" name="finalizar_pedido" value="<?= $id_pedido ?>">
                                                            <button type="submit" class="ym_btn-padrao3" style="font-size: 11px; padding: 4px 8px;">
                                                                <i class="fas fa-check"></i> Finalizar
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <small style="color:#888">Nenhum pedido</small>
                                            <?php endif; ?>
                                        </td>
                                            <td>
                                                <div class="td">
                                                    <a href="selecao_tipo_venda.php?id_cliente=<?= $cliente['id'] ?>&id_vendedor=<?= $_SESSION['id'] ?>&nome=<?= urlencode($cliente['nome'])?>" 
                                                    class="ym_btn-padrao2" title="Selecionar tipo de venda">
                                                        <i class="fas fa-shopping-cart"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        <td class="jv_table-action">
                                            <button class="jv_menu-btn" onclick="toggleDropdown(this)">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <form class="jv_dropdown" method="GET" action="">
                                                <button type="submit" name="visualizar" value="<?= htmlspecialchars($cliente['id']) ?>" class="jv_dropdown-item">
                                                    <i class="fas fa-eye"></i> Visualizar
                                                </button>
                                                <div class="jv_dropdown-separator"></div>
                                                <button class="jv_dropdown-item jv_danger" type="submit" name="remover" value="<?= htmlspecialchars($cliente['id'])?>">
                                                    <i class="fas fa-trash"></i> Remover
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align: center; height: 49.7vh;">Nenhum cliente encontrado</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if ($total_paginas > 1): ?>
        <div class="jv_page-navigation">
            <?php if($pagina_atual > 1): ?>
                <a href="?pagina=<?= $pagina_atual - 1 ?><?= $status_filtro ? '&status=' . $status_filtro : '' ?>" class="jv_page-arrow">
                    <i class="fas fa-arrow-left"></i>
                </a>
            <?php endif; ?>

            <?php
            $inicio = max(1, $pagina_atual - 2);
            $fim = min($total_paginas, $pagina_atual + 2);
            for ($i = $inicio; $i <= $fim; $i++): ?>
                <a href="?pagina=<?= $i ?><?= $status_filtro ? '&status=' . $status_filtro : '' ?>" 
                   class="jv_page-number <?= $i == $pagina_atual ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if($pagina_atual < $total_paginas): ?>
                <a href="?pagina=<?= $pagina_atual + 1 ?><?= $status_filtro ? '&status=' . $status_filtro : '' ?>" class="jv_page-arrow">
                    <i class="fas fa-arrow-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</main>
<script src="../../PUBLIC/JS/script-clientes-adm.js"></script>
<script src="../../PUBLIC/JS/script.js"></script>
<script src="../../PUBLIC/JS/script-pop-up.js"></script>
<!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->


</main>
</body>
</html>