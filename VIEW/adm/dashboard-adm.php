<?php
    include "../../INCLUDE/verificarLogin.php";
    include "../../INCLUDE/Menu_adm.php";
    include "../../CONTROLLER/ProdutoController.php";
    include "../../INCLUDE/vlibras.php";
    include "../../CONTROLLER/VendaController.php";
    include "../../CONTROLLER/ClienteController.php";
    include "../../CONTROLLER/UsuarioController.php";
    
    $produtoController = new ProdutoController();
    $produtos = $produtoController->index();
    
    include "../../INCLUDE/btn-notificacao.php";
    $limite = 5;
    $alertas = [];
    
    if (!isset($produtos['error'])) {
        foreach ($produtos as $produto) {
            if ($produto['quantidade'] <= $limite) {
                $alertas[] = "O produto <b>{$produto['nome']}</b> está com apenas <b>{$produto['quantidade']}</b> unidades restantes!";
            }
        }
    }

    $venda_control = new VendaController();
    $vendas_totais = $venda_control->index();
    $total_vendido = 0;
    $numero_vendas = 0;

    $data_grafico = [0,0,0,0,0,0,0,0,0,0,0,0];

    if(!isset($_POST['categoria'])){
        $categoria = "Produtos";
        $opcao = "Serviços";
    }
    else{    
        $opcao = $_POST['opcao'];
        $categoria = $_POST['categoria'];
    }

    if($categoria == "Produtos"){
        $filtro="produto";
    }else{
        $filtro="servico";
    }

    foreach ($vendas_totais as $venda) {
        $total_vendido += $venda['total'];
        $numero_vendas += 1;
        if($venda['tipo'] == $filtro){
            $data_venda = new DateTime($venda['data_venda']);
            for ($i=0; $i <= 12; $i++) { 
                if($data_venda->format("m") == $i){
                    $data_grafico[$i-1] = $data_grafico[$i-1] + 1;
                }
            }
        }
    } 

    $cliente_control = new ClienteController();
    $clientes_totais = $cliente_control->index();
    $total_de_clientes = count($clientes_totais);

    
    $vendedores_control = new UsuarioController();
    $vendedores_totais = $vendedores_control->index('vendedor');
    $TotalVendedor = count($vendedores_totais);

    $total_vendas = count($vendas_totais);
    
    $limite = 4;
    $pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    if ($pagina_atual < 1) $pagina_atual = 1;

    $offset = ($pagina_atual - 1) * $limite;

    $total_paginas = ($total_vendas > 0) ? ceil($total_vendas / $limite) : 1;

    $vendas_paginadas = array_slice($vendas_totais, $offset, $limite);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Vendas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../PUBLIC/css/dashboard-adm.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <main class="jp_main-content">
      
        <h1 class="ym_titulo">Dashboard</h1> 


        <div class="jp_cards-container">
            <div class="jp_card">
                <div class="jp_card-header">
                    <div class="jp_card-title">Total Vendido</div>
                </div>
                <div class="jp_card-value"><?="R$" . number_format($total_vendido, 2, ',', '.');?></div>
            </div>
            <div class="jp_card">
                <div class="jp_card-header">
                    <div class="jp_card-title">Total de Vendas</div>
                </div>
                <div class="jp_card-value"><?= $numero_vendas;?></div>
            </div>
            <div class="jp_card">
                <div class="jp_card-header">
                    <div class="jp_card-title">Vendedores</div>
                </div>
                <div class="jp_card-value"><?=$TotalVendedor?></div>
            </div>
            <div class="jp_card">
                <div class="jp_card-header">
                    <div class="jp_card-title">Cliente Cadastrado</div>
                </div>
                <div class="jp_card-value"><?=$total_de_clientes?></div>
            </div>
        </div>

        <div class="jp_sales-section">
            <div class="jp_sales-title">Últimas vendas</div>
            <table class="jp_sales-table">
                <thead>
                    <tr class="vc_header-planilha">
                        <th>Clientes</th>
                        <th>Vendedor</th>
                        <th>Tipo</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Comissão</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($vendas_paginadas as $venda) {
                        echo '
                            <tr>
                                <td>'.$cliente_control->mostrar($venda['id_cliente'])['nome'].'</td>
                                <td>'.$vendedores_control->mostrar($venda['id_vendedor'])['nome'].'</td>
                                <td>'.$venda['tipo'].'</td>
                                <td>'.date('d/m/Y', strtotime($venda['data_venda'])).'</td>
                                <td class="jp_sales-value">R$ '. number_format($venda['total'], 2, ',', '.') .'</td>
                                <td class="jp_sales-value">R$ '. number_format($venda['total']/100 * 5, 2, ',', '.')  .'</td>
                            </tr>';
                        }
                    
                    ?>  
                </tbody>
            </table>

            <div class="jp_pagination">
                    <?php if ($pagina_atual > 1): ?>
                        <a href="?pagina=<?= $pagina_atual - 1 ?>" class="jp_pagination-arrow">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php
                        $inicio = max(1, $pagina_atual - 2);
                        $fim = min($total_paginas, $pagina_atual + 2);
                        for ($i = $inicio; $i <= $fim; $i++): ?>
                            <a href="?pagina=<?= $i ?>" class="jp_pagination-item <?= $i == $pagina_atual ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                    <?php endfor; ?>

                    <?php if ($pagina_atual < $total_paginas): ?>
                        <a href="?pagina=<?= $pagina_atual + 1 ?>" class="jp_pagination-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <div class="jp_chart-section">
            <div class="jp_chart-header">

                <div class="jp_chart-title-container">
                    <div class="jp_chart-title">Vendas deste ano</div>
                </div>

                <div class="jp_chart-filters">

                    <form method="POST" class="ym_area-select">
                        <div class="ym_select" onclick="mostrar_categorias()">
                            <p class="ym_categoria-select"><?=$categoria?> </p>
                            <p class="ym_seta-categoria">></p>
                        </div>
                        
                        <input type="hidden" name="opcao" value="<?=$categoria?>">
                        
                        <button class="ym_options" type="submit" name="categoria" value="<?=$opcao?>">
                            <a class="ym_link-option" onclick="trocar_categoria()"><?=$opcao?></a>
                        </button>
                        
                    </form>
                    

                </div>
            </div>
            <canvas id="grafico_adm"></canvas>
        </div>
    </main>

    <script src="../../PUBLIC/JS/script-select.js"></script>    
    <script>
        window.data_grafico = <?php echo json_encode($data_grafico); ?>;
    </script>
    <script src="../../PUBLIC/JS/script-dashboard-adm-vcl.js"></script>
    <!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->
</body>
</html>
