<?php
require_once "../../CONTROLLER/VendaController.php";
require_once "../../CONTROLLER/ClienteController.php";
require_once "../../CONTROLLER/UsuarioController.php";
require_once "../../CONTROLLER/ProdutoController.php";
require_once "../../INCLUDE/alertas.php";
include "../../INCLUDE/vlibras.php";
include "../../INCLUDE/Menu_adm.php";
require_once "../../INCLUDE/verificarLogin.php";

if (!isset($_GET['id'])) {
    $_SESSION['alerta'] = '<script>exibirAlerta("Venda não encontrada!","error");</script>';
    header("Location: lista-vendas.php");
    exit;
}

$id_venda = $_GET['id'];
$vendaCtrl = new VendaController();
$clienteCtrl = new ClienteController();
$usuarioCtrl = new UsuarioController();
$produtoCtrl = new ProdutoController();

$venda = $vendaCtrl->mostrar($id_venda);

if (!$venda || isset($venda['erro'])) {
    $_SESSION['alerta'] = '<script>exibirAlerta("Venda não encontrada!","error");</script>';
    header("Location: lista-vendas.php");
    exit;
}

$cliente = $clienteCtrl->mostrar($venda['id_cliente']);
$nome_cliente = $cliente['nome'] ?? 'Cliente não encontrado';

$vendedor = $usuarioCtrl->mostrar($venda['id_vendedor']);
$nome_vendedor = $vendedor['nome'] ?? 'Vendedor não encontrado';

$pdo = new PDO("mysql:host=192.168.22.9;dbname=143p2;charset=utf8", "turma143p2", "sucesso@143");

$stmt = $pdo->prepare("SHOW TABLES LIKE 'pedido_itens'");
$stmt->execute();
$tabela_existe = $stmt->fetch();

$itens_venda = [];

if ($tabela_existe) {
    $stmt = $pdo->prepare("
        SELECT 
            pi.id,
            pi.id_produto,
            pi.quantidade,
            pi.preco_unitario,
            p.nome as produto_nome,
            p.foto as produto_foto
        FROM pedido_itens pi
        INNER JOIN produtos p ON pi.id_produto = p.id
        WHERE pi.id_pedido = ?
    ");
    $stmt->execute([$venda['id_pedido']]);
    $itens_venda = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (empty($itens_venda)) {
    $stmt = $pdo->prepare("
        SELECT 
            ci.id,
            ci.id_produto,
            ci.quantidade,
            p.preco as preco_unitario,
            p.nome as produto_nome,
            p.foto as produto_foto
        FROM carrinho_itens ci
        INNER JOIN carrinho c ON ci.id_carrinho = c.id
        INNER JOIN produtos p ON ci.id_produto = p.id
        WHERE c.id_cliente = ?
        ORDER BY ci.id DESC
        LIMIT 20
    ");
    $stmt->execute([$venda['id_cliente']]);
    $itens_venda = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$subtotal = 0;
foreach ($itens_venda as $item) {
    $subtotal += $item['preco_unitario'] * $item['quantidade'];
}

$desconto = $subtotal - $venda['total'];
if ($desconto < 0) $desconto = 0;

$porcentagem_desconto = $subtotal > 0 ? ($desconto / $subtotal) * 100 : 0;

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
    <meta name="description" content="Detalhes da venda realizada">
    <title>Detalhes da Venda #<?= htmlspecialchars($id_venda) ?></title>
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/venda-info-adm.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>

<main class="jp_main-content">
    <div class="back-button">
        <a href="vendas-adm.php" class="ym_link-volta"> 
            <i class="fa-solid fa-arrow-left"></i>
            <span>Voltar</span>
        </a>
    </div>

    <div class="P_customer-info">
        <div class="P_customer-card">
            <div class="P_customer-icon">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div class="P_customer-details">
                <div class="P_customer-label">Venda #<?= htmlspecialchars($id_venda) ?></div>
                <div class="P_customer-name"><?= htmlspecialchars($nome_cliente) ?></div>
                <div class="info-badge">
                    <i class="fa-solid fa-calendar"></i>
                    <?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?>
                </div>
            </div>
            <div class="vendedor-info">
                <i class="fa-solid fa-user-tie"></i>
                <div>
                    <div class="nomezinho1">Vendedor</div>
                    <div class="nomezinho2"><?= htmlspecialchars($nome_vendedor) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="readonly-message">
        <i class="fa-solid fa-info-circle"></i>
        <p>Esta é uma visualização de uma venda já finalizada. As informações não podem ser alteradas.</p>
    </div>

    <div class="P_main-container">
        <div class="P_cart-section">
            <div class="P_section-header">
                <h2>Itens da Venda</h2>
                <span class="P_items-count"><?= count($itens_venda) ?> <?= count($itens_venda) === 1 ? 'item' : 'itens' ?></span>
            </div>
            
            <div class="P_table-header">
                <div class="P_header-id">ID</div>
                <div class="P_header-produto">Produto</div>
                <div class="P_header-preco">Preço Unit.</div>
                <div class="P_header-quantidade">Quantidade</div>
                <div class="P_header-total">Total</div>
            </div>

            <div class="P_cart-table">
                <?php if (!empty($itens_venda)): ?>
                    <?php foreach ($itens_venda as $item): 
                        $nomeProduto = $item['produto_nome'] ?? 'Produto';
                        $fotoProduto = !empty($item['produto_foto']) ? $item['produto_foto'] : 'img_produto.webp';
                        $caminhoImagem = "../../PUBLIC/img/" . htmlspecialchars($fotoProduto);
                        
                        $precoUnitario = $item['preco_unitario'];
                        $quantidade = $item['quantidade'];
                        $totalItem = $precoUnitario * $quantidade;
                    ?>
                    <div class="P_cart-item item-disabled">
                        <div class="P_item-id">#<?= htmlspecialchars($item['id']) ?></div>
                        <div class="P_item-produto">
                            <div class="P_product-image">
                                <img src="<?= $caminhoImagem ?>" alt="Imagem de <?= htmlspecialchars($nomeProduto) ?>">
                            </div>
                            <span class="P_product-name"><?= htmlspecialchars($nomeProduto) ?></span>
                        </div>
                        <div class="P_item-preco">R$ <?= number_format($precoUnitario, 2, ',', '.') ?></div>
                        <div class="P_item-quantidade">
                            <div class="P_quantity-control" style="display: flex; align-items: center; justify-content: center;">
                                <span style="font-weight: 600; font-size: 16px;"><?= $quantidade ?></span>
                            </div>
                        </div>
                        <div class="P_item-total">R$ <?= number_format($totalItem, 2, ',', '.') ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: black;">
                        <i class="fa-solid fa-box-open" style="font-size: 48px; margin-bottom: 15px; color: #9ca3af;"></i>
                        <p>Nenhum item encontrado para esta venda</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="P_summary-section">
            <div class="P_summary-card">
                <h3>Resumo da Venda</h3>
                
                <?php if ($desconto > 0): ?>
                <div class="P_coupon-section">  
                    <label style="color: #166534; font-weight: 600;">
                        <i class="fa-solid fa-ticket" style="margin-left: 5px;"></i> Cupom Aplicado
                    </label>
                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                        <span style="background: #3e704c; color: white; padding: 8px 16px; border-radius: 6px; font-weight: 600; margin-left: 5px; margin-bottom: 5px;">
                            <?= number_format($porcentagem_desconto, 0) ?>% OFF
                        </span>
                        <span style="color: #166534;">Desconto de R$ <?= number_format($desconto, 2, ',', '.') ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <div class="P_divider"></div>

                <div class="P_summary-details">
                    <div class="P_detail-row">
                        <span class="P_detail-label">Subtotal</span>
                        <span class="P_detail-value">R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                    </div>
                    <?php if ($desconto > 0): ?>
                    <div class="P_detail-row">
                        <span class="P_detail-label">Desconto (<?= number_format($porcentagem_desconto, 0) ?>%)</span>
                        <span class="P_detail-value P_discount">- R$ <?= number_format($desconto, 2, ',', '.') ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="P_divider-thin"></div>
                    <div class="P_detail-row P_total-row">
                        <span class="P_detail-label">Total Pago</span>
                        <span class="P_detail-value P_total">R$ <?= number_format($venda['total'], 2, ',', '.') ?></span>
                    </div>
                </div>

                <div style="margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: #6b7280;"><i class="fa-solid fa-hashtag"></i> ID do Pedido:</span>
                        <span style="color: #6b7280; font-weight: 600;">#<?= htmlspecialchars($venda['id_pedido']) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #6b7280;"><i class="fa-solid fa-check-circle"></i> Status:</span>
                        <span style="color: #22c55e; font-weight: 600;">FINALIZADO</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->

</body>
</html>