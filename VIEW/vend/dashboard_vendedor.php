<?php
require_once '../../INCLUDE/verificarLogin.php';
include "../../INCLUDE/Menu_vend.php";
include "../../INCLUDE/vlibras.php";
include "../../CONTROLLER/VendaController.php";
include "../../CONTROLLER/clienteController.php";

$user_id = $_SESSION['id'] ?? null;
$venda_control = new VendaController(); 
$cliente_control = new ClienteController(); 

$vendas_usuario = $venda_control->index($user_id);
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

foreach ($vendas_usuario as $venda) {
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

$total_vendas = count($vendas_usuario);

$limite = 4;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;

$offset = ($pagina_atual - 1) * $limite;

$total_paginas = ($total_vendas > 0) ? ceil($total_vendas / $limite) : 1;

$vendas_paginadas = array_slice($vendas_usuario, $offset, $limite);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Vendedor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../PUBLIC/css/dashboard-vend.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>


<body>
    <!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->
    <main class="jp_main-content">
        <h1 class="ym_titulo">Dashboard</h1> 

        <div class="jp_metrics-row">
            <div class="jp_metric-box jp_metric-green">
                <div class="jp_metric-header">
                    <div class="jp_metric-title">Total Vendido</div>
                   
                </div>
                <div class="jp_metric-value"><?="R$" . number_format($total_vendido, 2, ',', '.');?></div>
            </div>
            <div class="jp_metric-box jp_metric-blue">
                <div class="jp_metric-header">
                    <div class="jp_metric-title">Número de Vendas</div>
                  
                </div>
                <div class="jp_metric-value"><?= $numero_vendas;?></div>
            </div>
            <div class="jp_metric-box jp_metric-orange">
                <div class="jp_metric-header">
                    <div class="jp_metric-title">Total de Comissões</div>
             
                </div>
                <div class="jp_metric-value">R$<?php echo number_format(($total_vendido/10),2,',','.');?></div>
            </div>
        </div>

        <div class="jp_sales-container">
            <div class="jp_sales-header">Últimas vendas</div>
            <table class="jp_sales-list">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($vendas_paginadas as $venda) {
                        echo '
                            <tr>
                                <td>'.$cliente_control->mostrar($venda['id_cliente'])['nome'].'</td>
                                <td>'.date('d/m/Y', strtotime($venda['data_venda'])).'</td>
                                <td class="jp_sales-value">R$ '.number_format(($venda['total']/10),2,',','.').'</td>
                            </tr>';
                        }
                    
                    ?>  
                </tbody>
            </table>
            
            <div class="jv_page-navigation">
                    <?php if ($pagina_atual > 1): ?>
                        <a href="?pagina=<?= $pagina_atual - 1 ?>" class="jv_page-arrow">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php
                        $inicio = max(1, $pagina_atual - 2);
                        $fim = min($total_paginas, $pagina_atual + 2);
                        for ($i = $inicio; $i <= $fim; $i++): ?>
                            <a href="?pagina=<?= $i ?>" class="jv_page-number <?= $i == $pagina_atual ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                    <?php endfor; ?>

                    <?php if ($pagina_atual < $total_paginas): ?>
                        <a href="?pagina=<?= $pagina_atual + 1 ?>" class="jv_page-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <a class="ym_mobile-td" onclick="abrirPopup('../pop-up/informacoes_vendedor.php','Informações do vendedor')">
                    <i class="fa-solid fa-circle-info"></i>
                </a>
            </div>
          
         </div>

        <div class="jp_bottom-section">
            <div class="jp_chart-panel">
                <div class="jp_chart-header">
                    <div class="jp_chart-title-area">
                        <div class="jp_chart-title">Vendas por Mês</div>
                 
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
                <canvas id="grafico_vend" width="700" height="250"></canvas>
            </div>
        </div>
    </main>
    <script src="../../PUBLIC/JS/script-select.js"></script>
    <script>
        window.data_grafico = <?php echo json_encode($data_grafico); ?>;
    </script>
    <script src="../../PUBLIC/JS/script-dashboard-vend.js"></script>
</body>
</html>
