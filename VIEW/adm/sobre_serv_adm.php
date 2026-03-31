<?php
require_once '../../INCLUDE/verificarLogin.php';
include "../../INCLUDE/Menu_adm.php";
include "../../INCLUDE/vlibras.php";
require_once '../../CONTROLLER/SobreServicoController.php';

$sobreServicoController = new SobreServicoController();
$dados = [];

if (isset($_GET['id'])) {
    $dados = $sobreServicoController->carregarServico($_GET['id']);
} else {
    $dados = ['success' => false, 'error' => 'Nenhum serviço selecionado.'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informações do Serviço</title>
    <link rel="stylesheet" href="../../PUBLIC/css/sobre_prod.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<main class="jp_main-content">
    <?php if (!$dados['success']): ?>
        <div class="ym-alert ym-alert-error"><?php echo $dados['error']; ?></div>
        <a href="catalogo-tudo.php" class="ym_btn-padrao">Voltar</a>
    <?php else:
        $servico = $dados['servico'];
        $categoria_nome = $dados['categoria_nome'];
    ?>

    <section class="gs_product-container">

        <!-- IMAGEM DO SERVIÇO -->
        <div class="gs_area-img">
            <div class="gs_main-image-wrapper">
                <img src="../../PUBLIC/img/<?php echo !empty($servico['foto']) ? $servico['foto'] : 'img_servico.webp'; ?>"
                     alt="<?php echo htmlspecialchars($servico['nome']); ?>"
                     class="gs_product-image" id="mainImage">

                <div class="gs_image-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    <span><?php echo htmlspecialchars($servico['status'] ?? 'Disponível'); ?></span>
                </div>
            </div>
        </div>

        <!-- INFORMAÇÕES -->
        <div class="gs_product-info">
            <div class="gs_header-section">
                <div class="gs_breadcrumb">
                    <span>Catálogo</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <span><?php echo htmlspecialchars($categoria_nome); ?></span>
                </div>
                <h1 class="gs_product-title"><?php echo htmlspecialchars($servico['nome']); ?></h1>
            </div>

            <!-- CARDS DE INFORMAÇÃO -->
            <div class="gs_info-grid">

                <div class="gs_info-card">
                    <div class="gs_info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                    </div>
                    <div class="gs_info-content">
                        <p class="gs_label">Categoria</p>
                        <p class="gs_value"><?php echo htmlspecialchars($categoria_nome); ?></p>
                    </div>
                </div>

                <div class="gs_info-card gs_price-card">
                    <div class="gs_info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <div class="gs_info-content">
                        <p class="gs_label">Preço</p>
                        <p class="gs_value gs_price">
                            R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?>
                        </p>
                    </div>
                </div>

            </div>

            <!-- DESCRIÇÃO -->
            <div class="gs_description-section">
                <div class="gs_section-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <h2>Descrição do Serviço</h2>
                </div>
                <p class="gs_description-text">
                    <?php echo nl2br(htmlspecialchars($servico['descricao'])); ?>
                </p>
            </div>

            <div class="ym_area-btn">
                <a href="catalogo-tudo.php" class="ym_btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<script src="../../PUBLIC/JS/script-sobre-prod.js"></script>
</body>
</html>
