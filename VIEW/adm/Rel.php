<?php
include "../../INCLUDE/Menu_adm.php";
include "../../CONTROLLER/UsuarioController.php";
include "../../CONTROLLER/ProdutoController.php";
include "../../CONTROLLER/VendaController.php";  
include "../../CONTROLLER/ClienteController.php";
include "../../CONTROLLER/ComissaoController.php";
include "../../INCLUDE/vlibras.php";
include "../../INCLUDE/verificarLogin.php";

if(!isset($_POST["categoria_graf-col"]) & !isset($_POST['categoria_tab-vend']) & !isset($_POST['categoria_graf-linha']) & !isset($_POST['categoria_tab-comis']) ){
    if(isset($_SESSION["categoria_graf-col"])){
        $_POST['categoria_graf-col'] = $_SESSION["categoria_graf-col"];
    }
    
    if(isset($_SESSION['categoria_tab-vend'])){
        $_POST['categoria_tab-vend'] = $_SESSION['categoria_tab-vend'];
    }

    if(isset($_SESSION['categoria_graf-linha'])){
        $_POST['categoria_graf-linha'] = $_SESSION['categoria_graf-linha'];
    }

    if(isset($_SESSION['categoria_tab-comis'])){
        $_POST['categoria_tab-comis'] = $_SESSION['categoria_tab-comis'];
    }
}

$controler_user = new UsuarioController();
$produto_item   = new ProdutoController();
$usuario_control = new UsuarioController();
$cliente_control = new ClienteController();
$venda_control   = new VendaController();  
$comissao_control = new ComissaoController();

$comissoes = $comissao_control->index(); 
$vendas = $venda_control->index();

$vendas_filtradas = [];
$vendas_comissao = [];


$total_vendas = count($vendas);
$total_comissoes = count($comissoes);

$pdo = new PDO("mysql:host=192.168.22.9;dbname=143p2;charset=utf8", "turma143p2", "sucesso@143");

$stmt = $pdo->query("SELECT status, COUNT(*) as total FROM pedidos GROUP BY status");
$status_pedidos_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);


 
if(!isset($_POST['categoria_tab-vend'])){
    $categoria_tab_vend = "Todos";
    $opcao1_tab_vend = "Este mês";
    $opcao2_tab_vend = "Este ano";
}

else{    
    if($_POST['categoria_tab-vend'] != $_SESSION['categoria_tab-vend']){
        if(isset($_GET['pagina'])){
            header('Location: Rel.php');
        }
    }

    if($_POST['categoria_tab-vend'] == "Este mês"){
        $categoria_tab_vend = $_POST['categoria_tab-vend'];
        $opcao1_tab_vend = "Todos";
        $opcao2_tab_vend = "Este ano";
        
    }elseif($_POST['categoria_tab-vend'] == "Este ano"){
        $categoria_tab_vend = $_POST['categoria_tab-vend'];
        $opcao1_tab_vend = "Este mês";
        $opcao2_tab_vend = "Todos";

    }else{
        $categoria_tab_vend = $_POST['categoria_tab-vend'];
        $opcao1_tab_vend = "Este mês";
        $opcao2_tab_vend = "Este ano";
    }
    $_SESSION['categoria_tab-vend'] = $_POST['categoria_tab-vend'];
}

if(!isset($_POST['categoria_tab-comis'])){
    $categoria_tab_comis = "Todos";
    $opcao1_tab_comis = "Este mês";
    $opcao2_tab_comis = "Este ano";

} else{    
    if($_POST['categoria_tab-comis'] != $_SESSION['categoria_tab-comis']){
        if(isset($_GET['pagina'])){
            header('Location: Rel.php');
        }
    }

    if($_POST['categoria_tab-comis'] == "Este mês"){
        $categoria_tab_comis = $_POST['categoria_tab-comis'];
        $opcao1_tab_comis = "Todos";
        $opcao2_tab_comis = "Este ano";
        
    }elseif($_POST['categoria_tab-comis'] == "Este ano"){
        $categoria_tab_comis = $_POST['categoria_tab-comis'];
        $opcao1_tab_comis = "Este mês";
        $opcao2_tab_comis = "Todos";

    }else{
        $categoria_tab_comis = $_POST['categoria_tab-comis'];
        $opcao1_tab_comis = "Este mês";
        $opcao2_tab_comis = "Este ano";
    }

    $_SESSION['categoria_tab-comis'] = $_POST['categoria_tab-comis'];
}

if(!isset($_POST['categoria_graf-col'])){
    $categoria_col = "Este ano";
    $opcao_col = "Últimos anos";
}
else{    
    if($_POST['categoria_graf-col'] == "Últimos anos"){
        $categoria_col = $_POST['categoria_graf-col'];
        $opcao_col = "Este ano";

    }else{
        $categoria_col = $_POST['categoria_graf-col'];
        $opcao_col = "Últimos anos";
    }
    $_SESSION["categoria_graf-col"] = $_POST['categoria_graf-col'];
}


if(!isset($_POST['categoria_graf-linha'])){
    $categoria_linha = "Este ano";
    $opcao_linha = "Últimos anos";
}
else{    
    if($_POST['categoria_graf-linha'] == "Últimos anos"){
        $categoria_linha = $_POST['categoria_graf-linha'];
        $opcao_linha = "Este ano";

    }else{
        $categoria_linha = $_POST['categoria_graf-linha'];
        $opcao_linha = "Últimos anos";
    }
    $_SESSION["categoria_graf-linha"] = $_POST['categoria_graf-linha'];
}



if($categoria_tab_vend == "Este ano"){
    foreach($vendas as $venda){
        $ano_venda = date("Y",strtotime($venda['data_venda']));

        $total = isset($venda['total']) ? (float)$venda['total'] : 0;

        if($ano_venda == date("Y")){
            array_push($vendas_filtradas,$venda);
        }
    }

}elseif($categoria_tab_vend == "Este mês"){
    foreach($vendas as $venda){
        $ano_venda = date("m",strtotime($venda['data_venda']));

        $total = isset($venda['total']) ? (float)$venda['total'] : 0;

        if($ano_venda == date("m")){
            array_push($vendas_filtradas,$venda);
        }
    }
}else{
    $vendas_filtradas = $vendas;
}


if($categoria_tab_comis == "Este ano"){
    foreach($vendas as $venda){
        $ano_venda = date("Y",strtotime($venda['data_venda']));

        $total = isset($venda['total']) ? (float)$venda['total'] : 0;

        if($ano_venda == date("Y")){
            array_push($vendas_comissao,$venda);
        }
    }

}elseif($categoria_tab_comis == "Este mês"){
    foreach($vendas as $venda){
        $ano_venda = date("m",strtotime($venda['data_venda']));

        $total = isset($venda['total']) ? (float)$venda['total'] : 0;

        if($ano_venda == date("m")){
            array_push($vendas_comissao,$venda);
        }
    }
}else{
    $vendas_comissao = $vendas;
}



$action_handled = false;

if(!empty($_GET)){
    if (isset($_GET['visualizar'])){
        $id = $_GET['visualizar'];
        $usuario = $controler_user->mostrar($id);
        $action_handled = true;
        header('Location: info-edit-adm.php?id=' . $id . "&usuario=" . $usuario['tipo']);

    } elseif (isset($_GET['remover'])){
        $id = $_GET['remover'];
        $usuario = $controler_user->deletar($id);
        $action_handled = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
    }
}

$usuarios = $controler_user->index();

$produto_item = new ProdutoController(); 

$action_handled = false;


$produtos = $produto_item->index();

$total_vendas = count($vendas_filtradas);
?>
 
 
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Vendas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/select.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/lista-vendedores-adm.css">
    <link rel="stylesheet" href="../../PUBLIC/css/relatorio.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    .po-tab-btn.po-active:hover {
      background: #52945bff;
      color: white; 
    }

    .po-tab-btn:hover {
      background: #555555;
      color: white;
    }
    </style>

</head>
<body>
 
   
<main class="jp_main-content">
    <div class="tab-content" id="sales-tab-content" style="display:block;">
        <div class="po-container">
            <h1 class="ym_titulo">Relatório de Vendas</h1>
 
                <form method="GET" class="tabs-nav">
                    <button class="po-tab-btn po-active" name="area" type="submit" value="sales"> <p>Vendas</p></button>
                    <button class="po-tab-btn" name="area" type="submit" value="commissions"><p>Comissões</p></button>
                </form>
           
 
                <div class="po-card">
                    <div class="jv_card">
                        <div class="jv_card-header">
                            <div class="jv_header-content">
                                <form method="POST" action="#" class="jv_search-section">
                                    <div class="jv_search-container">
                                        <button type="submit" class="ym_area-icon-pesquisa" name="pesquisar">
                                            <i class="fas fa-search search-icon"></i>
                                        </button>
                                        <input type="text" name="pesquisa" id="jv_searchInput" placeholder="Pesquisar por nome ou email..." class="jv_search-input" oninput="Pesquisar()">
                                    </div>
                                </form>
                
                                <div class="jv_actions">
                                    <div>    
                                        <div>
                                            <button type="button" class="po-btn" id="exportarCsvBtn">
                                                <span><i class="fa-regular fa-file"></i></span>
                                                Exportar CSV
                                            </button>
                                        </div>
                                            
                
                                        <form method="POST" class="ym_area-select">
                                            <div class="ym_select" onclick="mostrar_categorias()">
                                                <p class="ym_categoria-select"><?= $categoria_tab_vend?></p>
                                                <p class="ym_seta-categoria">></p>
                                            </div>
                                                
                                                
                                            <div class="ym_options">
                                                <button class="ym_link-option" onclick="trocar_categoria()" type="submit" name="categoria_tab-vend" value="<?=$opcao1_tab_vend?>"><?= $opcao1_tab_vend?></button>
                                                <button class="ym_link-option" onclick="trocar_categoria()" type="submit" name="categoria_tab-vend" value="<?=$opcao2_tab_vend?>"><?= $opcao2_tab_vend?></button>
                                            </div>
                                                
                                        </form>
                                    </div>    
                                </div>
                            </div>
                
                            <p class="jv_subtitle" id="jv_customerCount">
                                <?= $total_vendas ?> <?= $total_vendas == 1 ? 'venda encontrada' : 'vendas encontradas' ?>
                            </p>
                        </div>
                
                        <!-- Tabela de Vendas -->
                        <div class="jv_card-content">
                            <div class="jv_table-container">
                                <table class="jv_table">
                                    <thead class="vc_table-header">
                                        <tr class="jv_table-header">
                                            <th class="jv_date">Data</th>
                                            <th class="jv_name"><p>Vendedor</p></th>
                                            <th class="jv_name_cli">Cliente</th>
                                            <th class="jv_valor_gast">Valor Gasto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="jv_customerTableBody">
                                    </tbody>
                                </table>

                                <!-- Paginação -->
                                <div class="jv_page-navigation">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="po-charts-grid">
                <div class="po-card">
                    <h3>Vendas por mês</h3>
                    <form method="POST" class="ym_areaselect">
                        <div class="ym_select" onclick="mostrar_categorias(1)">
                            <p class="ym_categoria-select"><?=$categoria_col?></p>
                            <p class="ym_seta-categoria">></p>
                        </div>
                                                           
                        <div class="ym_options">
                            <button class="ym_link-option" onclick="trocar_categoria(0)" type="submit" name="categoria_graf-col" value="<?=$opcao_col?>"><?=$opcao_col?></button>
                        </div>
                    </form>
                   
                    <canvas id="sales-bar-chart"></canvas>
                </div>
                <div class="po-card">
                    <h3>Status dos pedidos</h3>
                    <canvas id="sales-pie-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
 
    <div class="tab-content" id="commissions-tab-content" style="display:none;">
        <div class="po-container">
            <h1 class="ym_titulo">Relatório de Comissões</h1>
 
            <form method="GET" class="tabs-nav">
                <button class="po-tab-btn" name="area" type="submit" value="sales"><p>Vendas</p></button>
                <button class="po-tab-btn po-active" name="area" type="submit" value="commissions"><p>Comissões</p></button>
            </form>
           
            <div class="po-card">
                <div class="jv_card">
                    <div class="jv_card-header">
                        <div class="jv_header-content">
                            <form method="POST" action="#" class="jv_search-section">
                                <div class="jv_search-container">
                                    <button type="submit" class="ym_area-icon-pesquisa" name="pesquisar">
                                        <i class="fas fa-search search-icon"></i>
                                    </button>
                                    <input type="text" name="pesquisa" id="jv_searchInput" placeholder="Pesquisar por nome ou email..." class="jv_search-input" oninput="Pesquisar_comissao()">
                                </div>
                            </form>
    
                            <div class="jv_actions">
                                <div>    
                                    <div>
                                        <button type="button" class="po-btn" id="exportarCsvComissoesBtn">
                                            <span><i class="fa-regular fa-file"></i></span>
                                            Exportar CSV
                                        </button>
                                    </div>
            
                                    <form method="POST" action="#" class="ym_area-select">
                                        <div class="ym_select" onclick="mostrar_categorias(2)">
                                            <p class="ym_categoria-select"> <?= $categoria_tab_comis?> </p>
                                            <p class="ym_seta-categoria">></p>
                                        </div>
                                            
                                        <div class="ym_options">

                                            <button type="submit" name="categoria_tab-comis" class="ym_link-option" onclick="trocar_categoria()" value="<?= $opcao1_tab_comis?>" > <?= $opcao1_tab_comis?> </button>
                                            
                                            <button type="submit" name="categoria_tab-comis" class="ym_link-option" onclick="trocar_categoria(0,1)" value="<?= $opcao2_tab_comis?>" > <?= $opcao2_tab_comis?> </button>
                                        
                                        </div>
                                            
                                    </form>
                                </div>    
                            </div>
                        </div>
 
                        <p class="jv_subtitle" id="jv_customerCount">
                            <?= $total_comissoes ?> vendas encontrados
                        </p>
                </div>
 
                <div class="jv_card-content">
                    <div class="jv_table-container">
                        <table class="jv_table">
                            <thead>
                                <tr class="jv_table-header">
                                    <th class="jv_date">Data</th>
                                    <th class="jv_banguea">Vendedor</th>
                                    <th class="jv_name_cli">Cliente</th>
                                    <th class="jv_valor_venda">Valor de Venda</th>
                                    <th class="jv_comissao"><p>Comissao</p></th>
                                    <th class="jv_banguela">Valor da Comissao</th>
                                </tr>
                            </thead>
                            <tbody class="ym_tabela-comissao" id="jv_customerTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                           
                <div class="jv_page-navigation">
                </div>
            </div>
            </div>
 
            <div class="po-charts-grid">
                <div class="po-card">
                    <h3>Gasto com Comissões</h3>

                    <form class="ym_areaselect_com" method="POST" action="#">
                        <div class="ym_select" onclick="mostrar_categorias(3)">
                            <p class="ym_categoria-select"><?=$categoria_linha?></p>
                            <p class="ym_seta-categoria">></p>
                        </div>
                                   
                                   
                        <div class="ym_options">
                            <button type="submit" class="ym_link-option" onclick="trocar_categoria(0)" name="categoria_graf-linha" value="<?=$opcao_linha?>"> <?=$opcao_linha?> </button>

                        </div>
                    </form>
                    <canvas id="comm-line-chart"></canvas>
                </div>
 
                <div class="po-card">
                    <h3>Distribuição de comissões</h3>
                    <canvas id="comm-doughnut-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
 
</main>
<?php
$status_pedidos = [
    "PAGO" => 0,
    "ENVIADO" => 0,
    "FINALIZADO" => 0
];

$total_pedidos = 0;
foreach ($status_pedidos_raw as $status) {
    $status_nome = strtoupper(trim($status['status']));
    $quantidade = (int)$status['total'];
    
    if (isset($status_pedidos[$status_nome])) {
        $status_pedidos[$status_nome] = $quantidade;
    }
    $total_pedidos += $quantidade;
}

if ($total_pedidos == 0) {
    $status_pedidos = [
        "PAGO" => 0.001,
        "ENVIADO" => 0.001,
        "FINALIZADO" => 0.001
    ];
} else {
    $status_pedidos = array_filter($status_pedidos, function($value) {
        return $value > 0;
    });
}

$colors_status = [];
$status_colors_map = [
    "PAGO" => "#107a10ff",
    "ENVIADO" => "#125d12ff",
    "FINALIZADO" => "#27db36ff",
];

foreach (array_keys($status_pedidos) as $status) {
    $colors_status[] = $status_colors_map[$status] ?? "rgba(69,115,75,0.5)";
}



if($categoria_col == "Este ano"){
    $graf_col = ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"];
    $vendas_total = array_fill(0, 12, 0);
    
    foreach ($vendas as $venda) {
            $data = $venda['data_venda'];
            $total = isset($venda['total']) ? (float)$venda['total'] : 0;
    
            if ($data) {
                $mes = (int) date("n", strtotime($data)) - 1;
                $vendas_total[$mes] += $total;
            }
    }

}elseif($categoria_col == "Últimos anos"){
    $ano = date("Y");
    $graf_col = [];

    for ($i=0; $i < 10; $i++) { 
        array_push($graf_col,intval($ano) -$i);
    }

    $vendas_total = array_fill(0, 10, 0);
    $graf_col = array_reverse($graf_col);

    foreach ($vendas as $venda) {
            $ano_venda = date("Y",strtotime($venda['data_venda']));

            $total = isset($venda['total']) ? (float)$venda['total'] : 0;
    
            foreach($graf_col as $ano){
                if($ano_venda == $ano){
                    $vendas_total[$ano_venda - $graf_col[0]] += $total;
                }
            }
    }
}

if($categoria_linha == "Este ano"){
    $graf_linha = ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"];
    $gastos_total = array_fill(0, 12, 0);
    
    foreach ($vendas as $venda) {
        foreach($comissoes as $comissao){
            
            if($venda['id'] == $comissao['id_venda']){
                $data = $venda['data_venda'];
                $total = $comissao['valor'];
        
                if ($data) {
                    $mes = (int) date("n", strtotime($data)) - 1;
                    $gastos_total[$mes] += $total;
                }
            }

        }
    }

}elseif($categoria_linha == "Últimos anos"){
    $ano = date("Y");
    $graf_linha = [];

    for ($i=0; $i < 10; $i++) { 
        array_push($graf_linha,intval($ano) -$i);
    }

    $gastos_total = array_fill(0, 10, 0);
    $graf_linha = array_reverse($graf_linha);

    foreach ($vendas as $venda) {
        foreach($comissoes as $comissao){
            if($venda['id'] == $comissao['id_venda']){
                $ano_venda = date("Y",strtotime($venda['data_venda']));

                $total = $comissao['valor'];
        
                foreach($graf_linha as $ano){
                    if($ano_venda == $ano){
                        $gastos_total[$ano_venda - $graf_linha[0]] += $total;
                    }
                }
            }
        }
    }
}

$max_venda = max($vendas_total);
$colors_vendas = array_map(fn($v) => $v == $max_venda ? "#27db36ff" : "#107a10ff", $vendas_total);

$comissoes_vendedor = array_fill(0, 12, 0);
$comissoes_dist = ["Fixas" => 0, "Variáveis" => 0];

foreach ($comissoes as $comissao) {
    foreach ($vendas as $venda){
        if($comissao["id_venda"] == $venda['id']){

            $data = $venda['data_venda'];
            $mes = ($data && strtotime($data) !== false) ? ((int)date("n", strtotime($data)) - 1) : (int)date("n") - 1;
        
            $valor = 0;
            if(isset($comissao['valor'])){
                $valor = (float)$comissao['valor'];
            }elseif (isset($comissao['valor'], $comissao['percentual'])){
                $valor = (float)$comissao['valor'] * ((float)$comissao['percentual'] / 100);

            }
        
            $comissoes_vendedor[$mes] += $valor > 0 ? $valor : 0.001;
        
            $tipo = $venda['valor'] ?? 'Fixas';

            if (isset($comissoes_dist[$tipo])){
                $comissoes_dist[$tipo] += $valor > 0 ? $valor : 0.001;
            }

        }
    }
}

foreach ($comissoes_dist as $key => $value) if ($value==0) $comissoes_dist[$key]=0.001;
?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    new Chart(document.getElementById("sales-bar-chart"), {
        type: "bar",
        data: {
            labels: <?= json_encode($graf_col)?>,
            datasets: [{
                label: "Vendas (R$)",
                data: <?= json_encode($vendas_total) ?>,
                backgroundColor: <?= json_encode($colors_vendas) ?>,
                borderRadius: 8,
                borderWidth: 1,
                
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: { 
                    callbacks: { 
                        label: ctx => "R$ " + ctx.raw.toLocaleString("pt-BR", {minimumFractionDigits: 2}) 
                    } 
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    ticks: { callback: v => "R$ " + v.toLocaleString("pt-BR") }, 
                    grid: { color: "rgba(0,0,0,0.05)" } 
                },
                x: { grid: { display: false } }
            }
        }
    });

    new Chart(document.getElementById("sales-pie-chart"), {
        type: "pie",
        data: {
            labels: <?= json_encode(array_keys($status_pedidos)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($status_pedidos)) ?>,
                backgroundColor: <?= json_encode($colors_status) ?>,
                borderColor: "#fff",
                borderWidth: 2,
                hoverOffset: 12
            }]
        },
        options: {
            plugins: {
                legend: { 
                    position: "bottom", 
                    labels: { font: { size: 13 } } 
                },
                tooltip: { 
                    callbacks: { 
                        label: function(ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const value = ctx.raw;
                            const percentage = ((value / total) * 100).toFixed(1);
                            return ctx.label + ": " + value + " (" + percentage + "%)";
                        }
                    } 
                }
            }
        }
    });

    new Chart(document.getElementById("comm-line-chart"), {
        type: "line",
        data: {
            labels: <?= json_encode($graf_linha) ?>,
            datasets: [{
                label: "Gasto com Comissões (R$)",
                data: <?= json_encode(array_values($gastos_total)) ?>,
                backgroundColor: "rgba(44, 171, 54, 0.23)",
                borderColor: "#27db36ff",
                borderWidth: 3,
                fill: true,
                tension: 0.1,
                pointBackgroundColor: "#27db36ff",
                pointRadius: 6
            }]
        },
        options: {
            plugins: {
                legend: { labels: { font: { size: 14 } } },
                tooltip: { 
                    callbacks: { 
                        label: ctx => "R$ " + ctx.raw.toLocaleString("pt-BR",{minimumFractionDigits:2}) 
                    } 
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    ticks: { callback: v => "R$ " + v.toLocaleString("pt-BR",{minimumFractionDigits:2}) }, 
                    grid: { color: "rgba(0,0,0,0.05)" } 
                },
                x: { grid: { display: false } }
            }
        }
    });

    new Chart(document.getElementById("comm-doughnut-chart"), {
        type: "doughnut",
        data: {
            labels: <?= json_encode(array_keys($comissoes_dist)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($comissoes_dist)) ?>,
                backgroundColor: ["#107a10ff","#27db36ff"],
                borderColor: "#fff",
                borderWidth: 2,
                hoverOffset: 12
            }]
        },
        options: {
            plugins: {
                legend: { 
                    position: "bottom", 
                    labels: { font: { size: 13 } } 
                },
                tooltip: { 
                    callbacks: { 
                        label: ctx => ctx.label + ": R$ " + ctx.raw.toLocaleString("pt-BR") 
                    } 
                }
            }
        }
    });
});


document.addEventListener("DOMContentLoaded", function() {
    const botaoVendas = document.getElementById("exportarCsvBtn");
    if (botaoVendas) {
        botaoVendas.addEventListener("click", function() {
            exportarVendasCsv(dados, "relatorio_vendas.csv");
        });
    }

    const botaoComissoes = document.getElementById("exportarCsvComissoesBtn");
    if (botaoComissoes) {
        botaoComissoes.addEventListener("click", function() {
            exportarComissoesCsv(dados_vendas, dados_comissoes, "relatorio_comissoes.csv");
        });
    }

    function exportarVendasCsv(dadosExportar, nomeArquivo) {
        const cabecalho = ["Data", "Vendedor", "Email Vendedor", "Cliente", "Valor Gasto"];
        let csv = [cabecalho];

        dadosExportar.forEach(venda => {
            const linha = [
                formatarData(venda.data_venda),
                venda.nome_vendedor,
                venda.email_vendedor,
                venda.nome_cliente,
                "R$ " + parseFloat(venda.total).toFixed(2)
            ];
            csv.push(linha);
        });

        let csvString = csv.map(row => 
            row.map(cell => `"${cell}"`).join(",")
        ).join("\n");
        
        let blob = new Blob([csvString], { type: "text/csv;charset=utf-8;" });
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = nomeArquivo;
        link.click();
    }

    function exportarComissoesCsv(vendasExportar, comissoesExportar, nomeArquivo) {
        const cabecalho = ["Data", "Vendedor", "Email Vendedor", "Cliente", "Valor de Venda", "Comissao (%)", "Valor da Comissao"];
        let csv = [cabecalho];

        vendasExportar.forEach(venda => {
            comissoesExportar.forEach(comissao => {
                if(comissao.id_venda == venda.id) {
                    const linha = [
                        formatarData(venda.data_venda),
                        venda.nome_vendedor,
                        venda.email_vendedor,
                        venda.nome_cliente,
                        "R$ " + parseFloat(venda.total).toFixed(2),
                        comissao.percentual + "%",
                        "R$ " + parseFloat(comissao.valor).toFixed(2)
                    ];
                    csv.push(linha);
                }
            });
        });

        let csvString = csv.map(row => 
            row.map(cell => `"${cell}"`).join(",")
        ).join("\n");
        
        let blob = new Blob([csvString], { type: "text/csv;charset=utf-8;" });
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = nomeArquivo;
        link.click();
    }

    function formatarData(dataStr) {
        const [ano, mes, diaHora] = dataStr.split('-');
        const dia = diaHora.split(' ')[0];
        return `${dia.padStart(2, '0')}/${mes.padStart(2, '0')}/${ano}`;
    }
});

</script>

<script>
    const dados_vendas= <?php echo json_encode($vendas_comissao); ?>;
    const dados = <?php echo json_encode($vendas_filtradas); ?>;
    const dados_comissoes = <?php echo json_encode($comissoes); ?>;
</script>
<script src="../../PUBLIC/JS/script-vendas.js"></script>
<script src="../../PUBLIC/JS/script-tabs.js"></script>
<script src="../../PUBLIC/JS/script-select.js"></script>
<script src="../../PUBLIC/JS/script-relatorio.js"></script>
<!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->



</body>
</html>