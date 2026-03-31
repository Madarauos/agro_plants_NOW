<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../PUBLIC/css/style_menu.css">
</head>
<body>
    <div class="jp_hamburger-menu">
        <div class="jp_hamburger-line"></div>
        <div class="jp_hamburger-line"></div>
        <div class="jp_hamburger-line"></div>
    </div>  

    <div class="jp_overlay"></div>

    <!-- Sidebar -->
    <aside class="jp_sidebar">
        <div class="jp_logo">
            <img src="../../PUBLIC/img/img_logo.png" alt="Logo">
        </div>
        <nav>
            <ul>
                <button onclick= "window.location.href='../../VIEW/adm/dashboard-adm.php'"><li id="dashboard"><img src="https://raw.githubusercontent.com/feathericons/feather/master/icons/grid.svg"> Dashboard </li></button>
    
                <button onclick= "window.location.href='../../VIEW/adm/catalogo-tudo.php'"><li id="catalogo"><img src="https://raw.githubusercontent.com/feathericons/feather/master/icons/box.svg" alt=""> Catálogo</li></button>

                <button onclick= "window.location.href='../../VIEW/adm/clientes-adm.php'"><li id="clientes"><img src="https://raw.githubusercontent.com/feathericons/feather/master/icons/users.svg" alt=""> Clientes</button></li>

                <button onclick= "window.location.href='../../VIEW/adm/lista-vendedores-adm.php'"><li id="vendedores"> <img src="https://raw.githubusercontent.com/feathericons/feather/master/icons/user.svg" alt=""> Vendedores</li></button>

                <button onclick= "window.location.href='../../VIEW/adm/vendas-adm.php'"><li id="vendas"><img src="https://raw.githubusercontent.com/feathericons/feather/master/icons/shopping-cart.svg" alt=""> Vendas</li></button>

                <button onclick= "window.location.href='../../VIEW/adm/Rel.php'"><li id="relatorios"><img src="https://raw.githubusercontent.com/feathericons/feather/master/icons/file-text.svg" alt=""> Relatórios</li></button>

                <button onclick= "window.location.href='../../VIEW/adm/Cupom_adm.php'"><li id="cupons"><img src="https://raw.githubusercontent.com/feathericons/feather/master/icons/tag.svg" alt=""> Cupons</li></button>
            </ul>
        </nav>
        <div class="jp_bottom-menu">
            <ul>
                <button onclick= "window.location.href='../../VIEW/adm/ajustes-informaçoes-adm.php'"><li id="ajustes"><img src="https://raw.githubusercontent.com/feathericons/feather/master/icons/settings.svg" alt=""> Ajustes</li></button>
                <button onclick= "window.location.href='../../CONTROLLER/logout.php'"><li id="sair"><img src="https://raw.githubusercontent.com/feathericons/feather/master/icons/log-out.svg" alt=""> Sair</li></button>
            </ul>
        </div>
    </aside>
    
    <script src="../../PUBLIC/JS/script-menus-laterais.js"></script></body>
</html>

