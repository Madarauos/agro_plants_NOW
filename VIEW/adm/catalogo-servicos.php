<?php
require_once '../../INCLUDE/verificarLogin.php';
include "../../INCLUDE/Menu_adm.php";
include "../../INCLUDE/vlibras.php";
require_once '../../CONTROLLER/CatalogoController.php';

$catalogoController = new CatalogoController();
$dados = $catalogoController->carregarCatalogoServicos();

if (isset($dados['postResult']) && isset($dados['postResult']['success'])) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=" . urlencode($dados['postResult']['success']));
    exit;
} elseif (isset($dados['postResult']) && isset($dados['postResult']['error'])) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?error=" . urlencode($dados['postResult']['error']));
    exit;
}

if (isset($dados['remocaoResult']) && isset($dados['remocaoResult']['success'])) {
    header("Location: catalogo-servicos.php?success=" . urlencode($dados['remocaoResult']['success']));
    exit;
} elseif (isset($dados['remocaoResult']) && isset($dados['remocaoResult']['error'])) {
    header("Location: catalogo-servicos.php?error=" . urlencode($dados['remocaoResult']['error']));
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
    <title>Catálogo - Serviços</title>
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/catalogo.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <style>
        .ym_area-input-pesquisa{
            margin-left: -1px;
        }

        .ym_area-input-pesquisa .ym_area-lupa{
            width: 50px;
            height: 100%;
        }
    </style>

    <div class="ym_popup-overlay">
        <div class="ym_popup-content">
            <div class="ym_area-superior-popup"></div>
            <div class="ym_conteudo-popup"></div>
        </div>
    </div>

    <main class="jp_main-content">
        

        <h1 class="ym_titulo">Catálogo - Serviços</h1>

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
                    <p class="ym_categoria-select">Serviço</p>
                    <p class="ym_seta-categoria">></p>
                </div>
                
                
                <div class="ym_options">
                    <a href="catalogo-tudo.php" class="ym_link-option"><i class="fa-solid fa-cube"></i> Todos</a>
                    <a href="catalogo-produtos.php" class="ym_link-option"><i class="fa-solid fa-building-wheat"></i> Produto</a>
                </div>
                
            </div>
            
            <a class="ym_btn-add" onclick="abrirPopup('../../VIEW/pop-up/pop-up-add-servico.php','Cadastro de serviço')">+</a>
        </div>
        
        <?php if (!$dados['errorCategorias'] && is_array($dados['categorias']) && count($dados['categorias']) > 0): ?>
            <?php foreach ($dados['categorias'] as $categoria): ?>
            <div class="ym_titulo-produtos">
                <p class="ym_textoArea"><?php echo htmlspecialchars($categoria['nome']); ?></p>
            </div>
            
            <div class="ym_areaProdutos">
                <div class="ym_todos-produtos">
                    <?php 
                    $servicosCategoria = array_filter($dados['servicos'], function($servico) use ($categoria) {
                        return $servico['id_cat'] == $categoria['id'];
                    });
                    ?>
                    
                    <?php if (count($servicosCategoria) > 0): ?>
                        <?php foreach ($servicosCategoria as $servico): ?>
                            <div class="ym_cardProduto">
                                <div class="ym_img-placeholder">
                                    <img src="../../PUBLIC/img/<?php echo !empty($servico['foto']) ? $servico['foto'] : 'img_servico.webp'; ?>" alt="<?php echo htmlspecialchars($servico['nome']); ?>" class="ym_img">
                                    <div class="ym_img-label">
                                        <span><?php echo htmlspecialchars($categoria['nome']); ?></span>
                                    </div>
                                    <a href="catalogo-servicos.php?remover=<?php echo $servico['id']; ?>&tipo=servico" class="ym_delete-link" onclick="return confirm('Tem certeza que deseja excluir este serviço?')">
                                        <i class="fa-solid fa-trash-can ym_delete-icon"></i>
                                    </a>
                                </div>
                                <div class="ym_card-content">
                                    <p class="ym_nomeProduto"><?php echo htmlspecialchars($servico['nome']); ?></p>
                                    <p class="ym_preco">R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?></p>
                                    <p class="ym_descricao"><?php echo htmlspecialchars($servico['descricao']); ?></p>
                                    <a href="sobre_serv_adm.php?id=<?php echo $servico['id']; ?>" class="ym_linkProduto ym_btn-padrao">Veja mais</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="ym-sem-registros">Nenhum serviço nesta categoria.</p>
                    <?php endif; ?>
                </div>
                
                <?php if (count($servicosCategoria) > 3): ?>
                <div class="ym_btn-slide-area">
                    <button class="ym_btn-slide ym_slideBack" onclick="slideBack(<?php echo count($servicosCategoria); ?>,0)"> < </button>
                    <button class="ym_btn-slide ym_slideGo" onclick="slideGo(<?php echo count($servicosCategoria); ?>,0)"> > </button>
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
                    echo "Nenhuma categoria com serviços encontrada.";
                }
                ?>
            </p>
        <?php endif; ?>

    </main>

</body>
</html>

<script src="../../PUBLIC/JS/script-select.js"></script>
<script src="../../PUBLIC/JS/script-pop-up.js"></script>
<script src="../../PUBLIC/JS/script-catalogo.js"></script>
<!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->