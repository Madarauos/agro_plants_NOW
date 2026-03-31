<?php
require_once '../../INCLUDE/verificarLogin.php';
include "../../INCLUDE/Menu_vend.php";
include "../../INCLUDE/vlibras.php";
require_once '../../CONTROLLER/CatalogoController.php';

$catalogoController = new CatalogoController();
$dados = $catalogoController->carregarCatalogo();

if (isset($dados['postResult']) && isset($dados['postResult']['success'])) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=" . urlencode($dados['postResult']['success']));
    exit;
} elseif (isset($dados['postResult']) && isset($dados['postResult']['error'])) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?error=" . urlencode($dados['postResult']['error']));
    exit;
}

if (isset($dados['remocaoResult']) && isset($dados['remocaoResult']['success'])) {
    header("Location: catalogo-tudo.php?success=" . urlencode($dados['remocaoResult']['success']));
    exit;
} elseif (isset($dados['remocaoResult']) && isset($dados['remocaoResult']['error'])) {
    header("Location: catalogo-tudo.php?error=" . urlencode($dados['remocaoResult']['error']));
    exit;
}

$successMessage = $_GET['success'] ?? '';
$errorMessage = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo</title>
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/catalogo-vendedor.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

    <div class="ym_popup-overlay">
        <div class="ym_popup-content">
            <div class="ym_area-superior-popup"></div>
            <div class="ym_conteudo-popup"></div>
        </div>
    </div>

    <main class="jp_main-content">
        
        <h1 class="ym_titulo">Catálogo - Geral</h1>

        <?php if ($successMessage): ?>
            <div class="ym-alert ym-alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span><?php echo htmlspecialchars($successMessage); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
            <div class="ym-alert ym-alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?php echo htmlspecialchars($errorMessage); ?></span>
            </div>
        <?php endif; ?>

        <div class="ym_categorias">
            
            <div class="ym_area-input-pesquisa">
                <button class="ym_lupa" aria-label="Pesquisar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <input id="inputPesquisa" type="text" placeholder="Pesquise por algo no catálogo" class="ym_produtoPesquisa">    
            </div>
                            
            <div class="ym_area-select-catalogo">
                <div class="ym_select-catalogo" onclick="mostrar_categorias()" role="button" tabindex="0">
                    <p class="ym_categoria-select">Todos</p>
                    <i class="fa-solid fa-chevron-down ym_seta-categoria"></i>
                </div>
                
                <div class="ym_options" role="menu">
                    <a href="catalogo-produtos.php" class="ym_link-option" role="menuitem">
                        <i class="fa-solid fa-building-wheat"></i> Produto
                    </a>
                    <a href="catalogo-servicos.php" class="ym_link-option" role="menuitem">
                        <i class="fa-solid fa-users-gear"></i> Serviço
                    </a>
                </div>
            </div>
        </div>
        
        <div class="ym_categoria-section">
            <h2 class="ym_textoArea">Principais produtos</h2>
            
            <div class="ym_areaProdutos">
                <div class="ym_todos-produtos" id="produtos-container">
                    <?php if (!$dados['errorProdutos'] && is_array($dados['produtos']) && count($dados['produtos']) > 0): ?>
                        <?php foreach ($dados['produtos'] as $produto):
                            $categoriaNome = $produto['categoria_nome'] ?? 'Categoria não encontrada';
                        ?>
                            <div class="ym_cardProduto">
                                <div class="ym_img-placeholder">
                                    <img src="../../PUBLIC/img/<?php echo !empty($produto['foto']) ? $produto['foto'] : 'img_produto.webp'; ?>" 
                                            alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
                                            class="ym_img">
                                    <div class="ym_img-label">
                                        <span><?php echo htmlspecialchars($categoriaNome); ?></span>
                                    </div>
                                    <a href="catalogo-tudo.php?remover=<?php echo $produto['id']; ?>&tipo=produto" 
                                        class="ym_delete-link" 
                                        onclick="return confirm('Tem certeza que deseja excluir este produto?')"
                                        aria-label="Excluir produto">
                                        <i class="fa-solid fa-trash-can ym_delete-icon"></i>
                                    </a>
                                </div>

                                <div class="ym_card-content">
                                    <p class="ym_nomeProduto"><?php echo htmlspecialchars($produto['nome']); ?></p>
                                    <p class="ym_preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                                    <p class="ym_descricao"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                                    <a href="sobre_prod.php?id=<?php echo $produto['id']; ?>" class="ym_linkProduto ym_btn-padrao">
                                        Veja mais <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="ym-sem-registros">
                            <i class="fa-solid fa-box-open"></i>
                            <p>
                                <?php 
                                if ($dados['errorProdutos']) {
                                    echo "Erro ao carregar produtos: " . htmlspecialchars($dados['produtos']['error']);
                                } else {
                                    echo "Nenhum produto cadastrado ou ativado.";
                                }
                                ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="ym_categoria-section">
            <h2 class="ym_textoArea">Principais serviços</h2>
            
            <div class="ym_areaProdutos">
                <div class="ym_todos-produtos" id="servicos-container">
                    <?php if (!$dados['errorServicos'] && is_array($dados['servicos']) && count($dados['servicos']) > 0): ?>
                        <?php foreach ($dados['servicos'] as $servico): 
                            $categoriaNome = $servico['categoria_nome'] ?? 'Categoria não encontrada';
                        ?>
                            <div class="ym_cardProduto">
                                <div class="ym_img-placeholder">
                                    <img src="../../PUBLIC/img/<?php echo !empty($servico['foto']) ? $servico['foto'] : 'img_servico.webp'; ?>" 
                                            alt="<?php echo htmlspecialchars($servico['nome']); ?>" 
                                            class="ym_img">
                                    <div class="ym_img-label">
                                        <span><?php echo htmlspecialchars($categoriaNome); ?></span>
                                    </div>
                                    <a href="catalogo-tudo.php?remover=<?php echo $servico['id']; ?>&tipo=servico" 
                                        class="ym_delete-link" 
                                        onclick="return confirm('Tem certeza que deseja excluir este serviço?')"
                                        aria-label="Excluir serviço">
                                        <i class="fa-solid fa-trash-can ym_delete-icon"></i>
                                    </a>
                                </div>

                                <div class="ym_card-content">
                                    <p class="ym_nomeProduto"><?php echo htmlspecialchars($servico['nome']); ?></p>
                                    <p class="ym_preco">R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?></p>
                                    <p class="ym_descricao"><?php echo htmlspecialchars($servico['descricao']); ?></p>
                                    <a href="sobre_serv_adm.php?id=<?php echo $servico['id']; ?>" class="ym_linkProduto ym_btn-padrao">
                                        Veja mais <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="ym-sem-registros">
                            <i class="fa-solid fa-box-open"></i>
                            <p>
                                <?php 
                                if ($dados['errorServicos']) {
                                    echo "Erro ao carregar serviços: " . htmlspecialchars($dados['servicos']['error']);
                                } else {
                                    echo "Nenhum serviço cadastrado ou ativado.";
                                }
                                ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

    <script src="../../PUBLIC/JS/script-select.js"></script>
    <script src="../../PUBLIC/JS/script-pop-up.js"></script>
    <script src="../../PUBLIC/JS/script-catalogo.js"></script>
    <!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->
</body>
</html>
