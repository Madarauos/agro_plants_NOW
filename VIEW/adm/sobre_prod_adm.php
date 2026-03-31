<?php
include "../../INCLUDE/Menu_adm.php";
require_once '../../INCLUDE/verificarLogin.php';
include "../../INCLUDE/vlibras.php";
include "../../INCLUDE/alertas.php";
require_once '../../CONTROLLER/SobreProdutoController.php';

$sobreProdutoController = new SobreProdutoController();
$dados = [];



if (isset($_POST['salvar'])) {
    $sobreProdutoController->atualizarProduto();
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $_GET['id']);
}

if (isset($_GET['id'])) {
    $dados = $sobreProdutoController->carregarProduto($_GET['id']);
} else {
    $dados = ['success' => false, 'error' => 'Nenhum produto selecionado.'];
}


if(isset($_SESSION['alerta'])){
    echo($_SESSION['alerta']);
    unset($_SESSION['alerta']);
}


?>

<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
  new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informações do Produto</title>
    <link rel="stylesheet" href="../../PUBLIC/css/sobre_prod.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

    <main class="jp_main-content">
        <?php if (!$dados['success']): ?>
            <div class="ym-alert ym-alert-error"><?php echo $dados['error']; ?></div>
            <a href="catalogo-tudo.php" class="ym_btn-padrao">Voltar</a>
        <?php else: 
            $produto = $dados['produto'];
            $categoria_nome = $dados['categoria_nome'];
        ?>
        <section class="gs_product-container">

            <div class="gs_area-img">
                <div class="gs_main-image-wrapper">
                    <img src="../../PUBLIC/img/<?php echo !empty($produto['foto']) ? htmlspecialchars($produto['foto']) : 'img_produto.webp'; ?>" 
                         alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
                         class="gs_product-image" id="mainImage">
                    <div class="gs_image-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                        <span><?php echo ($produto['status'] > 0) ? 'Em Estoque' : 'Fora de Estoque'; ?></span>
                    </div>
                </div>
            </div>

            <form method="POST" class="gs_product-info">
                <div class="gs_header-section">
                    <div class="gs_breadcrumb">
                        <span>Catálogo</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                        <span><?php echo htmlspecialchars($categoria_nome); ?></span>
                    </div>
                    <div class="ym_area-titulo-edit-btn">
                        <h1 class="gs_product-title"><?php echo htmlspecialchars($produto['nome']); ?></h1>
                        <div class="ym_area-btns">
                            <button class="ym_edit-button" type="button" onclick="editar()"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button id="ym_save-button" class="ym_edit-button" type="submit" name="salvar" ><i class="fa-solid fa-floppy-disk"></i></button>
                        </div>
                    </div>
                </div>

                <div class="gs_info-grid">
                    <div class="gs_info-card">
                        <div class="gs_info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                        </div>
                        <div class="gs_info-content">
                            <p class="gs_label">Categoria</p>
                            <h1 class="gs_product-title gs_value" title="<?php echo htmlspecialchars($produto['nome']); ?>">
                                <?php echo htmlspecialchars($produto['nome']); ?>
                            </h1>
                        </div>
                    </div>

                    <div class="gs_info-card gs_price-card">
                        <div class="gs_info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div class="gs_info-content">
                            <p class="gs_label">Preço</p>
                            <p class="gs_value">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                        </div>
                    </div>

                    <div class="gs_info-card">
                        <div class="gs_info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                        </div>
                        <div class="gs_info-content">
                            <p class="gs_label">Estoque</p>
                            <div class="ym_info-qtd">
                                <p class="gs_value"><?php echo (int)$produto['quantidade']; ?></p>
                                <p>unidades</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="gs_description-section">
                    <div class="gs_section-header">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <h2>Descrição do Produto</h2>
                    </div>
                    <p class="gs_description-text" title="<?php echo htmlspecialchars($produto['descricao']); ?>">
                    <?php echo nl2br(htmlspecialchars($produto['descricao'])); ?>
                    </p>
                </div>

                <div class="ym_area-btn">
                    <a href="catalogo-tudo.php" class="ym_btn-secondary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Voltar
                    </a>
                </div>
            </form>
        </section>
        <?php endif; ?>
    </main>
    <script src="../../PUBLIC/JS/script-sobre-prod.js"></script>
    <!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->
</body>
</html>