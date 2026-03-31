<?php
require_once "../../CONTROLLER/CarrinhoController.php";
require_once "../../CONTROLLER/ServicoController.php";
require_once "../../CONTROLLER/CupomController.php";
require_once "../../CONTROLLER/VendaController.php";
require_once "../../INCLUDE/alertas.php";
require_once "../../INCLUDE/verificarLogin.php"; 
include "../../INCLUDE/vlibras.php";
include "../../INCLUDE/Menu_vend.php";
require_once "../../INCLUDE/verificarLogin.php"; 

$carrinhoCtrl = new CarrinhoController();
$servicoCtrl  = new ServicoController();
$cupom  = new CupomController();
$vendaCtrl = new VendaController();

if (!isset($_GET['id_cliente']) && !isset($_GET['nome'])) {
    die("Cliente não informado");
}

$id_cliente = $_GET['id_cliente'];
$nome_cliente = $_GET['nome'];

$carrinho = $carrinhoCtrl->obterCarrinho($id_cliente);
$id_carrinho = $carrinho['id'] ?? null;

if (!$id_carrinho) {
    die("Erro ao obter carrinho");
}

// Conexão PDO para gerenciar pedidos de serviços
$pdo = new PDO("mysql:host=192.168.22.9;dbname=143p2;charset=utf8", "turma143p2", "sucesso@143");

// Função para obter ID do vendedor logado
function getIdVendedor() {
    $id_vendedor = $_SESSION['id_vendedor'] ?? $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? null;
    
    if (!$id_vendedor) {
        $_SESSION['alerta'] = '<script>exibirAlerta("Erro: Vendedor não identificado. Faça login novamente.","error");</script>';
        return false;
    }
    
    return $id_vendedor;
}

// Criar novo pedido de serviço
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_pedido'])) {
    $id_vendedor = getIdVendedor();
    if (!$id_vendedor) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?id_cliente=$id_cliente&nome=" . urlencode($nome_cliente));
        exit;
    }
    
    // Calcular total do carrinho de serviços
    $itens_temp = []; // Aqui você precisará buscar itens de serviços do carrinho
    
    if (empty($itens_temp)) {
        $_SESSION['alerta'] = '<script>exibirAlerta("Adicione serviços ao carrinho antes de criar um pedido!","error");</script>';
        header("Location: " . $_SERVER['PHP_SELF'] . "?id_cliente=$id_cliente&nome=" . urlencode($nome_cliente));
        exit;
    }
    
    $total_pedido = 0;
    foreach ($itens_temp as $item_temp) {
        $total_pedido += $item_temp['preco_unitario'] * $item_temp['quantidade'];
    }
    
    if ($total_pedido <= 0) {
        $total_pedido = 0.01;
    }
    
    try {
        // Criar novo pedido de serviço
        $stmt = $pdo->prepare("INSERT INTO servicos (id_cliente, id_vendedor, status, data_pedido, total) VALUES (?, ?, 'PENDENTE', NOW(), ?)");
        if ($stmt->execute([$id_cliente, $id_vendedor, $total_pedido])) {
            $novo_id_pedido = $pdo->lastInsertId();
            $_SESSION['alerta'] = '<script>exibirAlerta("Novo pedido de serviço criado com sucesso!","sucesso");</script>';
            header("Location: " . $_SERVER['PHP_SELF'] . "?id_cliente=$id_cliente&nome=" . urlencode($nome_cliente));
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['alerta'] = '<script>exibirAlerta("Erro ao criar pedido: ' . addslashes($e->getMessage()) . '","error");</script>';
    }
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?id_cliente=$id_cliente&nome=" . urlencode($nome_cliente));
    exit;
}

// Buscar pedido de serviço do cliente (SEM filtro de tipo)
$stmt = $pdo->prepare("SELECT id, status FROM pedidos WHERE id_cliente = ? ORDER BY data_pedido DESC LIMIT 1");
$stmt->execute([$id_cliente]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);
if($pedido){
    $status_pedido = $pedido['status'] ?? 'PENDENTE';
}else{
    $status_pedido = "nenhum";
}

$id_pedido = $pedido['id'] ?? null;


// Atualizar status do pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_status'])) {
    $novo_status = $_POST['atualizar_status'];
    
    if ($id_pedido) {
        $stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        if ($stmt->execute([$novo_status, $id_pedido])) {
            
            if ($novo_status === 'FINALIZADO') {
                $id_vendedor = getIdVendedor();
                if (!$id_vendedor) {
                    header("Location: " . $_SERVER['PHP_SELF'] . "?id_cliente=$id_cliente&nome=" . urlencode($nome_cliente));
                    exit;
                }
                
                // Calcular total
                $subtotal_venda = 0;
                // Aqui você precisará buscar os itens de serviços e calcular o total
                
                $desconto_venda = 0;
                if (isset($_SESSION['cupom_aplicado']) && isset($_SESSION['cupom_valor'])) {
                    $desconto_venda = $subtotal_venda * ($_SESSION['cupom_valor'] / 100);
                }
                
                $total_venda = $subtotal_venda - $desconto_venda;
                if ($total_venda < 0) $total_venda = 0;
                
                $dados_venda = [
                    'data_venda' => date('Y-m-d H:i:s'),
                    'id_pedido' => $id_pedido,
                    'id_vendedor' => $id_vendedor,
                    'id_cliente' => $id_cliente,
                    'total' => $total_venda
                ];
                
                $resultado_venda = $vendaCtrl->criarVenda($dados_venda);
                
                if (!isset($resultado_venda['erro'])) {
                    // Limpar carrinho de serviços
                    $_SESSION['alerta'] = '<script>exibirAlerta("Venda de serviço finalizada com sucesso!","sucesso");</script>';
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

// Adicionar serviço ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_servico'])) {
    $id_servico = $_POST['id_servico'];
    $quantidade = $_POST['quantidade'] ?? 1;

    $servico = $servicoCtrl->mostrar($id_servico);
    $preco_unitario = $servico['preco'] ?? 0;

    // Você precisará criar um método para adicionar serviços ao carrinho
    // ou usar uma tabela separada para carrinho_servicos
    
    $_SESSION['alerta'] = '<script>exibirAlerta("Serviço adicionado ao carrinho!","sucesso");</script>';
}

// Remover serviço do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_item'])) {
    $id_item = $_POST['remover_item'];
    // Remover item de serviço
    $_SESSION['alerta'] = '<script>exibirAlerta("Serviço removido do carrinho!","sucesso");</script>';
}

// Por enquanto, lista de serviços vazia (você precisará implementar a busca)
$itens = []; // Buscar itens de serviços do carrinho

// Buscar todos os serviços disponíveis
$servicos = $servicoCtrl->index();
if (isset($servicos['error'])) {
    $servicos = [];
}

$servicosIndexados = [];
foreach ($servicos as $s) {
    $servicosIndexados[$s['id']] = $s;
}

$subtotal = 0;
foreach ($itens as &$item) {
    $servico = $servicosIndexados[$item['id_servico']] ?? null;
    $item['preco_unitario'] = $item['preco_unitario'] ?? ($servico['preco'] ?? 0);
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
    
    if ($cupom_selecionado) {
        $_SESSION['cupom_aplicado'] = $cupom_selecionado['id'];
        $_SESSION['cupom_valor'] = $cupom_selecionado['valor'];
    } else {
        unset($_SESSION['cupom_aplicado']);
        unset($_SESSION['cupom_valor']);
    }
}

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
    <meta name="description" content="Gerenciamento de carrinho de serviços do cliente">
    <title>Carrinho de Serviços - <?= htmlspecialchars($nome_cliente) ?></title>
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/venda-info.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
        
        body.dark-theme .P_add-product-section{
            background-color: #3a3a3a;
            color: #e0e0e0;
        }

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

<main class="jp_main-content">
    <div class="back-button">
        <a href="lista-clientes.php?id_cliente=<?= $id_cliente ?>&nome=<?= urlencode($nome_cliente) ?>" class="ym_link-volta"> 
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
                <h2>Serviços Solicitados</h2>
                <span class="P_items-count"><?= count($itens) ?> <?= count($itens) === 1 ? 'serviço' : 'serviços' ?></span>
            </div>

            <div class="P_table-header">
                <div class="P_header-id">ID</div>
                <div class="P_header-produto">Serviço</div>
                <div class="P_header-preco">Preço</div>
                <div class="P_header-quantidade">Qtd</div>
                <div class="P_header-total">Total</div>
                <div class="P_header-actions"></div>
            </div>

            <div class="P_cart-table">
                <?php if (empty($itens)): ?>
                    <div style="text-align: center; padding: 40px; color: #888;">
                        <i class="fa-solid fa-briefcase" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                        <p>Nenhum serviço adicionado ainda</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($itens as $item): 
                        $servico = $servicosIndexados[$item['id_servico']] ?? null;
                        if (!$servico) continue;
                        
                        $precoUnitario = $item['preco_unitario'];
                        $quantidade = $item['quantidade'];
                        $totalItem = $precoUnitario * $quantidade;
                    ?>
                    <div class="P_cart-item" data-id-item="<?= $item['id'] ?>">
                        <div class="P_item-id">#<?= htmlspecialchars($item['id']) ?></div>
                        <div class="P_item-produto">
                            <div class="P_product-image">
                                <div class="service-icon">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                            </div>
                            <span class="P_product-name"><?= htmlspecialchars($servico['nome']) ?></span>
                        </div>
                        <div class="P_item-preco">R$ <?= number_format($precoUnitario, 2, ',', '.') ?></div>
                        <div class="P_item-quantidade">
                            <div class="P_quantity-control">
                                <span style="font-weight: 600;"><?= $quantidade ?></span>
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
                <?php endif; ?>
            </div>

            <div class="P_add-product-section">
                <h3>Adicionar Serviço</h3>
                <form class="P_add-product-form" method="post">
                    <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($id_cliente) ?>">
                    <div class="P_form-group">
                        <label for="servico">Serviço</label>
                        <select id="servico" name="id_servico" required class="P_select-input">
                            <option value="">Selecione um serviço</option>
                            <?php foreach ($servicos as $servico): ?>
                                <option value="<?= $servico['id'] ?>">
                                    <?= htmlspecialchars($servico['nome']) ?> – R$ <?= number_format($servico['preco'], 2, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="P_form-group">
                        <label for="quantidade">Quantidade</label>
                        <input type="number" id="quantidade" name="quantidade" value="1" min="1" class="P_number-input" required>
                    </div>
                    
                    <button type="submit" class="P_add-button">
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
                    <form method="POST" style="margin-top: 20px;">
                        <label for="cupom_id" style="font-weight: 600;">Cupom de Desconto</label>

                        <select name="cupom_id" id="cupom_id" class="P_select-input" required>
                            <option value="">Selecione um cupom</option>
                            <?php foreach ($cupons as $c): ?>
                                <option value="<?= $c['id'] ?>" 
                                    <?= isset($_SESSION['cupom_aplicado']) && $_SESSION['cupom_aplicado'] == $c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nome']) ?> — <?= $c['valor'] ?>%
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit" class="P_apply-coupon-btn" style="margin-top: 10px;">
                            Aplicar
                        </button>
                    </form>

                </div>

                <div class="P_divider"></div>

                <div class="P_summary-details">
                    <div class="P_detail-row">
                        <span class="P_detail-label">Subtotal</span>
                        <span class="P_detail-value">R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                    </div>
                    <div class="P_detail-row">
                        <span class="P_detail-label">Desconto</span>
                        <span class="P_detail-value P_discount">- R$ <?= number_format($desconto, 2, ',', '.') ?></span>
                    </div>
                    <div class="P_divider-thin"></div>
                    <div class="P_detail-row P_total-row">
                        <span class="P_detail-label">Total</span>
                        <span class="P_detail-value P_total">R$ <?= number_format($total, 2, ',', '.') ?></span>
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
                                    $texto_botao = 'Marcar como Iniciado';
                                    $icone_botao = 'fa-play';
                                    break;
                                case 'ENVIADO':
                                    $proximo_status = 'FINALIZADO';
                                    $texto_botao = 'Finalizar Serviço';
                                    $icone_botao = 'fa-check-circle';
                                    break;
                                case 'FINALIZADO':
                                    $proximo_status = '';
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
                    
                    <?php if ($status_pedido === 'FINALIZADO'): ?>
                    <form method="POST" style="width: 100%;">
                        <button type="submit" name="novo_pedido" class="P_checkout-button">
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

<!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->

</body>
</html>