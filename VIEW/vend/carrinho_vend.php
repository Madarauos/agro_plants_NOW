<?php
require_once "../../CONTROLLER/CarrinhoController.php";
require_once '../../INCLUDE/verificarLogin.php';
require_once "../../CONTROLLER/CatalogoController.php";
require_once "../../CONTROLLER/ProdutoController.php";
require_once "../../CONTROLLER/CupomController.php";
require_once "../../CONTROLLER/VendaController.php";
require_once "../../INCLUDE/alertas.php";
include "../../INCLUDE/vlibras.php";
include "../../INCLUDE/Menu_vend.php";
require_once "../../INCLUDE/verificarLogin.php"; 

$carrinhoCtrl = new CarrinhoController();
$catalogoCtrl = new CatalogoController();
$produtoCtrl  = new ProdutoController();
$cupom  = new CupomController();
$vendaCtrl = new VendaController();


if (!isset($_GET['id_cliente']) && !isset($_GET['nome'])) {
    die("Cliente não informado");
}

$id_cliente = $_GET['id_cliente'];
$nome_cliente = $_GET['nome'];

$carrinho = $carrinhoCtrl->obterCarrinho($id_cliente);
$id_carrinho = $carrinho['id'] ?? null;
$carrinhoItens = new CarrinhoItensModel();
$carrinhoItens->removerDuplicatas($id_carrinho);

if (!$id_carrinho) {
    die("Erro ao obter carrinho");
}

// Conexão PDO para gerenciar pedidos
$pdo = new PDO("mysql:host=192.168.22.9;dbname=143p2;charset=utf8", "turma143p2", "sucesso@143");

// Criar novo pedido (quando o anterior foi finalizado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_pedido'])) {
    error_log("=== INICIANDO CRIAÇÃO DE NOVO PEDIDO ===");
    
    $id_vendedor = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? null;
    error_log("ID Vendedor da sessão: " . ($id_vendedor ?? 'NULL'));
    
    if (!$id_vendedor) {
        $stmt_vendedor = $pdo->prepare("SELECT id_vendedor FROM pedidos WHERE id_cliente = ? ORDER BY data_pedido DESC LIMIT 1");
        $stmt_vendedor->execute([$id_cliente]);
        $pedido_anterior = $stmt_vendedor->fetch(PDO::FETCH_ASSOC);
        $id_vendedor = $pedido_anterior['id_vendedor'] ?? 1;
        error_log("ID Vendedor do pedido anterior: " . $id_vendedor);
    }
    
    // Calcular total atual do carrinho
    $itens_temp = $carrinhoCtrl->listarItens($id_carrinho);
    error_log("Quantidade de itens no carrinho: " . count($itens_temp));
    
    // Verificar se há itens no carrinho
    if (empty($itens_temp)) {
        error_log("ERRO: Carrinho vazio!");
        $_SESSION['alerta'] = '<script>exibirAlerta("Adicione produtos ao carrinho antes de criar um pedido!","error");</script>';
        header("Location: " . $_SERVER['PHP_SELF'] . "?id_cliente=$id_cliente&nome=" . urlencode($nome_cliente));
        exit;
    }
    
    $catalogo_temp = $catalogoCtrl->carregarCatalogoProdutos();
    $produtos_temp = $catalogo_temp['produtos'] ?? [];
    
    $produtosIndexados_temp = [];
    foreach ($produtos_temp as $p) {
        $produtosIndexados_temp[$p['id']] = $p;
    }
    
    $total_pedido = 0;
    foreach ($itens_temp as $item_temp) {
        $produto_temp = $produtosIndexados_temp[$item_temp['id_produto']] ?? null;
        $preco = $item_temp['preco_unitario'] ?? ($produto_temp['preco'] ?? 0);
        $total_pedido += $preco * $item_temp['quantidade'];
        error_log("Item: " . $item_temp['id_produto'] . " - Preço: " . $preco . " - Qtd: " . $item_temp['quantidade']);
    }
    
    error_log("Total calculado: " . $total_pedido);
    
    // Garantir que o total seja no mínimo 0.01 se houver constraint
    if ($total_pedido <= 0) {
        $total_pedido = 0.01;
        error_log("Total ajustado para: " . $total_pedido);
    }
    
    try {
        // Criar novo pedido
        $stmt = $pdo->prepare("INSERT INTO pedidos (id_cliente, id_vendedor, status, data_pedido, total) VALUES (?, ?, 'PENDENTE', NOW(), ?)");
        if ($stmt->execute([$id_cliente, $id_vendedor, $total_pedido])) {
            $novo_id_pedido = $pdo->lastInsertId();
            error_log("Novo pedido criado com ID: " . $novo_id_pedido);
            $_SESSION['alerta'] = '<script>exibirAlerta("Novo pedido criado com sucesso!","sucesso");</script>';
            header("Location: " . $_SERVER['PHP_SELF'] . "?id_cliente=$id_cliente&nome=" . urlencode($nome_cliente));
            exit;
        } else {
            error_log("ERRO: Falha ao executar INSERT do pedido");
            $_SESSION['alerta'] = '<script>exibirAlerta("Erro ao criar pedido!","error");</script>';
        }
    } catch (Exception $e) {
        error_log("EXCEÇÃO ao criar pedido: " . $e->getMessage());
        $_SESSION['alerta'] = '<script>exibirAlerta("Erro ao criar pedido: ' . addslashes($e->getMessage()) . '","error");</script>';
    }
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?id_cliente=$id_cliente&nome=" . urlencode($nome_cliente));
    exit;
}

// Buscar pedido do cliente
$stmt = $pdo->prepare("SELECT id, status FROM pedidos WHERE id_cliente = ? ORDER BY data_pedido DESC LIMIT 1");
$stmt->execute([$id_cliente]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);
if($pedido){
    $status_pedido = $pedido['status'] ?? 'PENDENTE';
}else{
    $status_pedido = "nenhum";
}

$id_pedido = $pedido['id'] ?? null;

// Atualizar status do pedido E CRIAR VENDA QUANDO FINALIZADO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_status'])) {
    $novo_status = $_POST['atualizar_status'];
    
    if ($id_pedido) {
        $stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        if ($stmt->execute([$novo_status, $id_pedido])) {
            
            // SE O STATUS FOR "FINALIZADO", CRIAR A VENDA
            if ($novo_status === 'FINALIZADO') {
                // Obter ID do vendedor
                $id_vendedor = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? null;

                if (!$id_vendedor) {
                    // Se não encontrou o id do vendedor na sessão, não podemos criar a venda
                    $_SESSION['alerta'] = '<script>exibirAlerta("Erro: Vendedor não identificado. Faça login novamente.","error");</script>';
                    header("Location: " . $_SERVER['PHP_SELF'] . "?id_cliente=$id_cliente&nome=" . urlencode($nome_cliente));
                    exit;
                }
                
                // Calcular total do carrinho (com desconto se houver)
                $itens_venda = $carrinhoCtrl->listarItens($id_carrinho);
                $catalogo_venda = $catalogoCtrl->carregarCatalogoProdutos();
                $produtos_venda = $catalogo_venda['produtos'] ?? [];
                
                $produtosIndexados_venda = [];
                foreach ($produtos_venda as $p) {
                    $produtosIndexados_venda[$p['id']] = $p;
                }
                
                $subtotal_venda = 0;
                foreach ($itens_venda as $item_venda) {
                    $preco = $item_venda['preco_unitario'] ?? ($produtosIndexados_venda[$item_venda['id_produto']]['preco'] ?? 0);
                    $subtotal_venda += $preco * $item_venda['quantidade'];
                }
                
                // Aplicar desconto se houver cupom na sessão
                $desconto_venda = 0;
                if (isset($_SESSION['cupom_aplicado']) && isset($_SESSION['cupom_valor'])) {
                    $desconto_venda = $subtotal_venda * ($_SESSION['cupom_valor'] / 100);
                }
                
                $total_venda = $subtotal_venda - $desconto_venda;
                if ($total_venda < 0) $total_venda = 0;
                
                // Criar a venda
                $dados_venda = [
                    'data_venda' => date('Y-m-d H:i:s'),
                    'id_pedido' => $id_pedido,
                    'id_vendedor' => $id_vendedor,
                    'id_cliente' => $id_cliente,
                    'total' => $total_venda
                ];
                
                $resultado_venda = $vendaCtrl->criarVenda($dados_venda);
                
                if (!isset($resultado_venda['erro'])) {
                    // LIMPAR O CARRINHO DO CLIENTE
                    // Remover todos os itens do carrinho
                    $stmt_limpar = $pdo->prepare("DELETE FROM carrinho_itens WHERE id_carrinho = ?");
                    $stmt_limpar->execute([$id_carrinho]);
                    
                    // Resetar valores do carrinho
                    $stmt_reset = $pdo->prepare("UPDATE carrinho SET valor_total = 0 WHERE id = ?");
                    $stmt_reset->execute([$id_carrinho]);
                    
                    // Limpar cupom da sessão
                    unset($_SESSION['cupom_aplicado']);
                    unset($_SESSION['cupom_valor']);
                    
                    $_SESSION['alerta'] = '<script>exibirAlerta("Venda finalizada com sucesso! Carrinho limpo.","sucesso");</script>';
                } else {
                    $_SESSION['alerta'] = '<script>exibirAlerta("Erro ao criar venda: ' . $resultado_venda['erro'] . '","error");</script>';
                }
            } else {
                $_SESSION['alerta'] = '<script>exibirAlerta("Status atualizado para ' . $novo_status . '!","sucesso");</script>';
            }
            
            header("Location: " . $_SERVER['PHP_SELF'] . "?id_cliente=$id_cliente&nome=" . urlencode($nome_cliente));
            exit;
        }
    }
}

// Gerar link de pagamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gerar_link'])) {
    error_log("=== GERANDO LINK DE PAGAMENTO ===");
    
    $id_vendedor = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? null;
    error_log("ID Vendedor: " . ($id_vendedor ?? 'NULL'));
    
    if (!$id_pedido) {
        error_log("Pedido não existe, criando novo...");
        
        // Calcular o total antes de criar o pedido
        $itens_link = $carrinhoCtrl->listarItens($id_carrinho);
        error_log("Itens no carrinho: " . count($itens_link));
        
        if (empty($itens_link)) {
            error_log("ERRO: Carrinho vazio ao gerar link");
            $_SESSION['alerta'] = '<script>exibirAlerta("Adicione produtos ao carrinho antes de gerar o link!","error");</script>';
            header("Location: " . $_SERVER['PHP_SELF'] . "?id_cliente=$id_cliente&nome=" . urlencode($nome_cliente));
            exit;
        }
        
        $total_link = 0;
        
        foreach ($itens_link as $item_link) {
            $produto_link = $produtosIndexados[$item_link['id_produto']] ?? null;
            $preco_link = $item_link['preco_unitario'] ?? ($produto_link['preco'] ?? 0);
            $total_link += $preco_link * $item_link['quantidade'];
        }
        
        error_log("Total calculado: " . $total_link);
        
        // Aplicar desconto se houver
        if (isset($_SESSION['cupom_valor'])) {
            $desconto_link = $total_link * ($_SESSION['cupom_valor'] / 100);
            $total_link -= $desconto_link;
            error_log("Desconto aplicado: " . $desconto_link . ", Total final: " . $total_link);
        }
        
        // Garantir valor mínimo
        if ($total_link <= 0) {
            $total_link = 0.01;
            error_log("Total ajustado para mínimo: " . $total_link);
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO pedidos (id_cliente, id_vendedor, status, data_pedido, total) VALUES (?, ?, 'PENDENTE', NOW(), ?)");
            $stmt->execute([$id_cliente, $id_vendedor, $total_link]);
            $id_pedido = $pdo->lastInsertId();
            error_log("Pedido criado com ID: " . $id_pedido);
        } catch (Exception $e) {
            error_log("ERRO ao criar pedido: " . $e->getMessage());
            $_SESSION['alerta'] = '<script>exibirAlerta("Erro ao criar pedido: ' . addslashes($e->getMessage()) . '","error");</script>';
            header("Location: " . $_SERVER['PHP_SELF'] . "?id_cliente=$id_cliente&nome=" . urlencode($nome_cliente));
            exit;
        }
    }
    
    $link_pagamento = "https://seusite.com/pagamento?pedido=" . $id_pedido . "&cliente=" . $id_cliente;
    error_log("Link gerado: " . $link_pagamento);
    
    $_SESSION['link_pagamento'] = $link_pagamento;
    $_SESSION['mostrar_qrcode'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_quantity') {
    header('Content-Type: application/json');
    
    error_log(" Recebendo requisição de atualização de quantidade");
    error_log(" POST data: " . print_r($_POST, true));
    
    $id_item = $_POST['id_item'] ?? null;
    $nova_quantidade = $_POST['quantidade'] ?? null;
    
    error_log(" ID Item: " . $id_item);
    error_log(" Nova Quantidade: " . $nova_quantidade);
    
    if ($id_item && $nova_quantidade && $nova_quantidade > 0) {
        try {
            if (!method_exists($carrinhoCtrl, 'atualizarQuantidade')) {
                error_log(" ERRO: Método atualizarQuantidade não existe no CarrinhoController");
                echo json_encode(['success' => false, 'error' => 'Método atualizarQuantidade não implementado']);
                exit;
            }
            
            $resultado = $carrinhoCtrl->atualizarQuantidade($id_item, $nova_quantidade);
            error_log(" Resultado da atualização: " . ($resultado ? 'true' : 'false'));
            
            if ($resultado) {
                $itens = $carrinhoCtrl->listarItens($id_carrinho);
                $itemAtualizado = null;
                
                foreach ($itens as $item) {
                    if ($item['id'] == $id_item) {
                        $itemAtualizado = $item;
                        break;
                    }
                }
                
                if ($itemAtualizado) {
                    $totalItem = $itemAtualizado['preco_unitario'] * $itemAtualizado['quantidade'];
                    
                    $subtotal = 0;
                    foreach ($itens as $item) {
                        $subtotal += $item['preco_unitario'] * $item['quantidade'];
                    }
                    
                    error_log(" Sucesso! Total item: " . $totalItem . ", Subtotal: " . $subtotal);
                    
                    echo json_encode([
                        'success' => true,
                        'total_item' => number_format($totalItem, 2, ',', '.'),
                        'subtotal' => number_format($subtotal, 2, ',', '.'),
                        'total' => number_format($subtotal, 2, ',', '.')
                    ]);
                } else {
                    error_log(" ERRO: Item não encontrado após atualização");
                    echo json_encode(['success' => false, 'error' => 'Item não encontrado']);
                }
            } else {
                error_log(" ERRO: Falha ao atualizar quantidade no banco");
                echo json_encode(['success' => false, 'error' => 'Erro ao atualizar quantidade']);
            }
        } catch (Exception $e) {
            error_log(" EXCEÇÃO: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        error_log(" ERRO: Dados inválidos - ID: $id_item, Qtd: $nova_quantidade");
        echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produto'])) {
    $id_produto = (int) $_POST['id_produto'];
    $quantidade = (int) ($_POST['quantidade'] ?? 1);

    $produto = $produtoCtrl->mostrar($id_produto);
    $preco_unitario = $produto['preco'] ?? 0;
    $estoque_disponivel = $produto['quantidade'] ?? 0; // campo de estoque do produto

    // Verifica se a quantidade ultrapassa o estoque
    if ($quantidade > $estoque_disponivel) {
        echo '<script>exibirAlerta("A quantidade solicitada ultrapassa o estoque disponível (estoque: '.$estoque_disponivel.').","error");</script>';
    } else {
        // Adiciona o produto normalmente
        $resultado = $carrinhoCtrl->adicionarItem($id_carrinho, $id_produto, $quantidade);

        if (isset($resultado['success'])) {
            echo '<script>exibirAlerta("Produto adicionado ao carrinho!","sucesso");</script>';
        } else {
            echo '<script>exibirAlerta("Erro: '.$resultado['error'].'","error");</script>';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_item'])) {
    $id_item = $_POST['remover_item'];
    $carrinhoCtrl->removerItem($id_item);
    echo '<script>exibirAlerta("Item removido do carrinho!","sucesso");</script>';
}

$itens = $carrinhoCtrl->listarItens($id_carrinho);
$catalogo = $catalogoCtrl->carregarCatalogoProdutos();
$produtos = $catalogo['produtos'] ?? [];

$produtosIndexados = [];
foreach ($produtos as $p) {
    $produtosIndexados[$p['id']] = $p;
}

$subtotal = 0;
foreach ($itens as &$item) {
    $produto = $produtosIndexados[$item['id_produto']] ?? null;
    
    $item['preco_unitario'] = $item['preco_unitario'] ?? ($produto['preco'] ?? 0);
    $subtotal += $item['preco_unitario'] * $item['quantidade'];
}
unset($item);

$cupons = $cupom->index();

$cupom_selecionado = null;
$cupom_valor = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cupom_id'])) {
    $cupom_id = $_POST['cupom_id'];
    
    foreach ($cupons as $c) {
        if ($c['id'] == $cupom_id) {
            $cupom_selecionado = $c;
            break;
        }
    }
    
    // Salvar cupom na sessão para usar ao finalizar
    if ($cupom_selecionado) {
        $_SESSION['cupom_aplicado'] = $cupom_selecionado['id'];
        $_SESSION['cupom_valor'] = $cupom_selecionado['valor'];
    } else {
        unset($_SESSION['cupom_aplicado']);
        unset($_SESSION['cupom_valor']);
    }
}

// Recuperar cupom da sessão se existir
if (!$cupom_selecionado && isset($_SESSION['cupom_aplicado'])) {
    foreach ($cupons as $c) {
        if ($c['id'] == $_SESSION['cupom_aplicado']) {
            $cupom_selecionado = $c;
            break;
        }
    }
}

if ($cupom_selecionado) {
    $cupom_valor = $cupom_selecionado['valor'];
} 

$desconto = 0;
if (!empty($cupom_valor)) {
    $desconto = $subtotal * ($cupom_valor / 100);
}

$total = $subtotal - $desconto;
if ($total < 0) $total = 0;

// Exibir alertas
if(isset($_SESSION['alerta'])){
    echo($_SESSION['alerta']);
    unset($_SESSION['alerta']);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gerenciamento de carrinho de compras do cliente">
    <title>Carrinho - <?= htmlspecialchars($nome_cliente) ?></title>
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/venda-info.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .qrcode-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .qrcode-modal.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .qrcode-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        #qrcode{
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;

        }
        
        .qrcode-content h3 {
            margin-bottom: 20px;
            color: #333;

        }
        
        .qrcode-content canvas {
            margin: 20px 0;
        }
        
        .qrcode-link {
            word-break: break-all;
            background: #d0cfcfff;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            color: black;
        }
        
        .qrcode-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        
        .qrcode-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-copiar {
            background-color: #4CAF50;
            color: white;
            gap: 5px;
        }
        
        .btn-fechar {
            background-color: #c62525ff;
            color: white;
            gap: 5px;
        }
        
        .btn-nova-venda {
            background: #3e704c;
        }

        .P_status-badge {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            margin-left: 90vh;
            animation: fadeIn 0.5s ease-in;
        }

        .P_status-badge.pendente {
            background: rgba(251, 191, 36, 0.1);
            color: #d97706;
            border: 2px solid rgba(251, 191, 36, 0.3);
        }

        .P_status-badge.pago {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
            border: 2px solid rgba(34, 197, 94, 0.3);
        }

        .P_status-badge.enviado {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
            border: 2px solid rgba(59, 130, 246, 0.3);
        }

        .P_status-badge.finalizado {
            background: rgba(139, 92, 246, 0.1);
            color: #7c3aed;
            border: 2px solid rgba(139, 92, 246, 0.3);
        }

        .P_disabled-section {
            opacity: 0.5;
            pointer-events: none;
            position: relative;
        }

        .P_disabled-section::after {
            content: "🔒 Bloqueado após pagamento confirmado";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
            z-index: 10;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .P_disabled-section:hover::after {
            opacity: 1;
        }
    </style>
</head>
<body>

<!-- Modal QR Code -->
<div id="qrcodeModal" class="qrcode-modal <?= isset($_SESSION['mostrar_qrcode']) ? 'active' : '' ?>">
    <div class="qrcode-content">
        <h3>Link de Pagamento</h3>
        <div id="qrcode"></div>
        <?php if (isset($_SESSION['link_pagamento'])): ?>
            <div class="qrcode-link">
                <?= htmlspecialchars($_SESSION['link_pagamento']) ?>
            </div>
        <?php endif; ?>
        <div class="qrcode-buttons">
            <button class="btn-copiar" onclick="copiarLink()">
                <i class="fa-solid fa-copy"></i> Copiar Link
            </button>
            <button class="btn-fechar" onclick="fecharModal()">
                <i class="fa-solid fa-times"></i> Fechar
            </button>
        </div>
    </div>
</div>

<main class="jp_main-content">
        <div class="back-button">
            <a href="lista-clientes.php" class="ym_link-volta"> 
                <i class="fa-solid fa-arrow-left"></i>
                <span>Voltar</span>
            </a>
        </div>

        <div class="P_customer-info">
            <div class="P_customer-card">
            <div class="P_customer-icon">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="P_customer-details">
                <div class="P_customer-label">Cliente</div>
                <div class="P_customer-name"><?= htmlspecialchars($nome_cliente) ?></div>

            </div>
               <?php if ($id_pedido): 
                    $status_class = strtolower($status_pedido);
                    $status_icons = [
                        'PENDENTE' => 'fa-clock',
                        'PAGO' => 'fa-check-circle',
                        'ENVIADO' => 'fa-truck',
                        'FINALIZADO' => 'fa-flag-checkered'
                    ];
                    $status_icon = $status_icons[$status_pedido] ?? 'fa-info-circle';
                ?>
                <div class="P_status-badge <?= $status_class ?>">
                    <i class="fa-solid <?= $status_icon ?>"></i>
                    Status: <?= htmlspecialchars($status_pedido) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="P_main-container">
            <div class="P_cart-section">
                <div class="P_section-header">
                    <h2>Itens do Carrinho</h2>
                    <span class="P_items-count"><?= count($itens) ?> <?= count($itens) === 1 ? 'item' : 'itens' ?></span>
                </div>
                    <div class="P_table-header">
                        <div class="P_header-id">ID</div>
                        <div class="P_header-produto">Produto</div>
                        <div class="P_header-preco">Preço</div>
                        <div class="P_header-quantidade">Qtd</div>
                        <div class="P_header-total">Total</div>
                        <div class="P_header-actions"></div>
                    </div>

                <div class="P_cart-table">

                    <?php if (empty($itens)): ?>
                        <div style="text-align: center; padding: 40px; color: #888;">
                            <i class="fa-solid fa-box" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                            <p>Nenhum produto adicionado ainda</p>
                        </div>
                    <?php endif ?>

                    <?php foreach ($itens as $item): 
                        $produto = $produtosIndexados[$item['id_produto']] ?? null;
                        $nomeProduto = $produto['nome'] ?? 'Produto';
                        $fotoProduto = !empty($produto['foto']) ? $produto['foto'] : 'img_produto.webp';
                        $caminhoImagem = "../../PUBLIC/img/" . htmlspecialchars($fotoProduto);
                        if (!$produto) continue;

                        $precoUnitario = $item['preco_unitario'];
                        $quantidade = $item['quantidade'];
                        $totalItem = $precoUnitario * $quantidade;
                    ?>
                    <div class="P_cart-item" data-id-item="<?= $item['id'] ?>">
                        <div class="P_item-id">#<?= htmlspecialchars($item['id']) ?></div>
                        <div class="P_item-produto">
                            <div class="P_product-image">
                                <img src="<?= $caminhoImagem ?>" alt="Imagem de <?= htmlspecialchars($nomeProduto) ?>">
                            </div>
                            <span class="P_product-name"><?= htmlspecialchars($produto['nome']) ?></span>
                        </div>
                        <div class="P_item-preco">R$ <?= number_format($precoUnitario, 2, ',', '.') ?></div>
                        <div class="P_item-quantidade">
                            <div class="P_quantity-control">
                                <button class="P_qty-btn" onclick="decreaseQty(this)">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input type="number" value="<?= $quantidade ?>" min="1" class="P_qty-input" readonly>
                                <button class="P_qty-btn" onclick="increaseQty(this)">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="P_item-total">R$ <?= number_format($totalItem, 2, ',', '.') ?></div>
                        <div class="P_item-actions">
                            <form method="post">
                                <input type="hidden" name="remover_item" value="<?= $item['id'] ?>">
                                <button class="P_trash-button" type="submit">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>

                <?php 
                $pedido_bloqueado = in_array($status_pedido, ['PAGO', 'ENVIADO']);
                ?>

                <div class="P_add-product-section <?= $pedido_bloqueado ? 'P_disabled-section' : '' ?>">
                    <h3 style="color: #3e704c;">Adicionar Produto</h3>
                    <form class="P_add-product-form" method="post" <?= $pedido_bloqueado ? 'onsubmit="return false;"' : '' ?>>
                        <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($id_cliente) ?>">
                        <div class="P_form-group">
                            <label for="produto">Produto</label>
                            <select id="produto" name="id_produto" required class="P_select-input" <?= $pedido_bloqueado ? 'disabled' : '' ?>>
                                <option value="">Selecione um produto</option>
                                <?php foreach ($produtos as $produto): ?>
                                    <option value="<?= $produto['id'] ?>">
                                        <?= htmlspecialchars($produto['nome']) ?> – R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                 
                            <div class="P_form-group">
                            <label for="quantidade">Quantidade</label>
                            <input type="number" id="quantidade" name="quantidade" value="1" min="1" class="P_number-input" required <?= $pedido_bloqueado ? 'disabled' : '' ?>>
                        </div>
                        
                        <button type="submit" class="P_add-button" <?= $pedido_bloqueado ? 'disabled' : '' ?>>
                            <i class="fa-solid fa-plus"></i>
                            Adicionar ao Carrinho
                        </button>

                    </form>
                </div>
            </div>

            <div class="P_summary-section">
                <div class="P_summary-card">
                    <h3 style="color: #3e704c;">Resumo do Pedido</h3>
                    
                    <div class="P_coupon-section">
                        <form method="post">
                            <label for="cupom_id">Selecionar Cupom de Desconto</label>
                            <select id="cupom_id" name="cupom_id" class="P_select-input">
                                <option value="">Nenhum cupom</option>
                                <?php foreach ($cupons as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= (isset($cupom_selecionado) && $cupom_selecionado['id'] == $c['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['codigo']) ?> — <?= $c['valor'] ?>%
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="P_apply-coupon-btn">Aplicar</button>
                        </form>
                    </div>

                    <div class="P_divider"></div>

                    <div class="P_summary-details">
                        <div class="P_detail-row">
                            <span class="P_detail-label">Subtotal</span>
                            <span class="P_detail-value" id="summary-subtotal">R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                        </div>
                        <div class="P_detail-row">
                            <span class="P_detail-label">Desconto</span>
                            <span class="P_detail-value P_discount" id="summary-discount">- R$ <?= number_format($desconto, 2, ',', '.') ?></span>
                        </div>
                        <div class="P_divider-thin"></div>
                        <div class="P_detail-row P_total-row">
                            <span class="P_detail-label">Total</span>
                            <span class="P_detail-value P_total" id="summary-total">R$ <?= number_format($total, 2, ',', '.') ?></span>
                        </div>
                    </div>

                    <div class="atualizar_status">
                        <?php if ($status_pedido === 'PENDENTE'): ?>
                        <form method="POST" style="width: 100%;">
                            <button type="submit" name="gerar_link" class="P_checkout-button">
                                <i class="fa-solid fa-qrcode"></i>
                                Gerar Link de Pagamento
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <form method="POST" style="width: 100%;">
                            <?php 
                                $proximo_status = '';
                                $texto_botao = '';
                                $icone_botao = '';
                                
                                switch($status_pedido) {
                                    case 'PENDENTE':
                                        $proximo_status = 'PAGO';
                                        $texto_botao = 'Confirmar Pagamento';
                                        $icone_botao = 'fa-dollar-sign';
                                        break;
                                    case 'PAGO':
                                        $proximo_status = 'ENVIADO';
                                        $texto_botao = 'Marcar como Enviado';
                                        $icone_botao = 'fa-truck';
                                        break;
                                    case 'ENVIADO':
                                        $proximo_status = 'FINALIZADO';
                                        $texto_botao = 'Finalizar Venda';
                                        $icone_botao = 'fa-check-circle';
                                        break;
                                    case 'FINALIZADO':
                                        $proximo_status = 'PENDENTE';
                                        $texto_botao = '';
                                        $icone_botao = '';
                                        break;
                                }
                                
                                if ($proximo_status !== '') {
                                    echo '<button type="submit" name="atualizar_status" value="' . $proximo_status . '" class="P_checkout-button">';
                                    echo '<i class="fa-solid ' . $icone_botao . '"></i>';
                                    echo ' ' . $texto_botao;
                                    echo '</button>';
                                }
                            ?>
                        </form>
                        
                        <?php if ($status_pedido == 'FINALIZADO' || $status_pedido == "nenhum"): ?>
                        <form method="POST" style="width: 100%;">
                            <button type="submit" name="novo_pedido" class="P_checkout-button btn-nova-venda">
                                <i class="fa-solid fa-plus-circle"></i>
                                Nova Venda
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        <?php if (isset($_SESSION['mostrar_qrcode']) && isset($_SESSION['link_pagamento'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            new QRCode(document.getElementById("qrcode"), {
                text: "<?= $_SESSION['link_pagamento'] ?>",
                width: 256,
                height: 256,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        });
        <?php 
            unset($_SESSION['mostrar_qrcode']);
            unset($_SESSION['link_pagamento']);
        endif; 
        ?>
        
        function copiarLink() {
            const link = document.querySelector('.qrcode-link').textContent.trim();
            navigator.clipboard.writeText(link).then(function() {
                alert('Link copiado para a área de transferência!');
            });
        }
        
        function fecharModal() {
            document.getElementById('qrcodeModal').classList.remove('active');
        }
        
        document.getElementById('qrcodeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModal();
            }
        });

        const pedidoBloqueado = <?= json_encode($pedido_bloqueado) ?>;

if (pedidoBloqueado) {
    // Bloquear funções de atualização de quantidade
    window.increaseQty = function(button) {
        alert('Não é possível alterar quantidades após o pagamento ser confirmado!');
        return false;
    };
    
    window.decreaseQty = function(button) {
        alert('Não é possível alterar quantidades após o pagamento ser confirmado!');
        return false;
    };
    
    // Bloquear remoção de itens
    document.querySelectorAll('.P_trash-button').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Não é possível remover itens após o pagamento ser confirmado!');
        });
    });
    
    // Bloquear aplicação de cupom
    const applyCouponBtn = document.querySelector('.P_apply-coupon-btn');
    if (applyCouponBtn) {
        applyCouponBtn.disabled = true;
    }
    
    const couponSelect = document.querySelector('#cupom_id');
    if (couponSelect) {
        couponSelect.disabled = true;
    }
}
    </script>

    <!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->

</body>
</html>