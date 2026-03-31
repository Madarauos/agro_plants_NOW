<?php
require_once '../../INCLUDE/verificarLogin.php';
include "../../INCLUDE/Menu_vend.php";
include "../../INCLUDE/vlibras.php";
require_once '../../CONTROLLER/CatalogoController.php';

$catalogoController = new CatalogoController();
$dados = $catalogoController->carregarCatalogoProdutos();

$successMessage = $_GET['success'] ?? '';
$errorMessage = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Produtos</title>
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/catalogo-vendedor.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

    <main class="jp_main-content">
        
        <section class="ym_sectionProdutos">

            <h1 class="ym_titulo">Catálogo - Produtos</h1>

            <?php if ($successMessage): ?>
                <div class="ym-alert ym-alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>
            
            <?php if ($errorMessage): ?>
                <div class="ym-alert ym-alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <div class="ym_categorias">
                
                <div class="ym_area-input-pesquisa">
                    <a href="" class="ym_lupa"><i class="fa-solid fa-magnifying-glass"></i></a>
                    <input id="inputPesquisa" type="text" placeholder="Pesquise por algo no catálogo" class="ym_produtoPesquisa">    
                </div>  
                
                <div class="ym_area-select-catalogo">
                    <div class="ym_select-catalogo" onclick="mostrar_categorias()">
                        <p class="ym_categoria-select">Produto</p>
                        <p class="ym_seta-categoria">></p>
                    </div>
                    
                    <div class="ym_options">
                        <a href="catalogo-tudo.php" class="ym_link-option"><i class="fa-solid fa-cube"></i> Todos</a>
                        <a href="catalogo-servicos.php" class="ym_link-option"><i class="fa-solid fa-users-gear"></i> Serviço</a>
                    </div>
                </div>
            </div>
            
            <?php if (!$dados['errorCategorias'] && is_array($dados['categorias']) && count($dados['categorias']) > 0): ?>
                <?php foreach ($dados['categorias'] as $categoria): ?>
                <div class="ym_titulo-produtos">
                    <p class="ym_textoArea"><?php echo htmlspecialchars($categoria['nome']); ?></p>
                </div>
                
                <div class="ym_areaProdutos">
                    <div class="ym_todos-produtos">
                        <?php 
                        $produtosCategoria = array_filter($dados['produtos'], function($produto) use ($categoria) {
                            return $produto['id_cat'] == $categoria['id'];
                        });
                        ?>
                        
                        <?php if (count($produtosCategoria) > 0): ?>
                            <?php foreach ($produtosCategoria as $produto): ?>
                                <div class="ym_cardProduto">
                                    <div class="ym_img-placeholder">
                                        <img src="../../PUBLIC/img/<?php echo !empty($produto['foto']) ? $produto['foto'] : 'img_produto.webp'; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="ym_img">
                                        <div class="ym_img-label">
                                            <span><?php echo htmlspecialchars($categoria['nome']); ?></span>
                                        </div>
                                    </div>
                                    <div class="ym_card-content">
                                        <p class="ym_nomeProduto"><?php echo htmlspecialchars($produto['nome']); ?></p>
                                        <p class="ym_preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                                        <p class="ym_descricao"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                                        <a href="sobre_prod.php?id=<?php echo $produto['id']; ?>" class="ym_linkProduto ym_btn-padrao">Veja mais</a>
                                    </div>
                                </div>
                                

                                    <?php endforeach; ?>
                        <?php else: ?>
                            <p class="ym-sem-registros">Nenhum produto nesta categoria.</p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (count($produtosCategoria) > 3): ?>
                    <div class="ym_btn-slide-area">
                        <button class="ym_btn-slide ym_slideBack" onclick="slideBack(<?php echo count($produtosCategoria); ?>,0)"> < </button>
                        <button class="ym_btn-slide ym_slideGo" onclick="slideGo(<?php echo count($produtosCategoria); ?>,0)"> > </button>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="ym-sem-registros">
                    <?php 
                    if ($dados['errorCategorias']) {
                        echo "Erro ao carregar categorias: " . htmlspecialchars($dados['categorias']['error']);
                    } else {
                        echo "Nenhuma categoria com produtos encontrada.";
                    }
                    ?>
                </p>
            <?php endif; ?>

        </section>
    </main>

</body>
</html>

<script src="../../PUBLIC/JS/script-select.js"></script>
<script src="../../PUBLIC/JS/script-catalogo.js"></script>
<!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->