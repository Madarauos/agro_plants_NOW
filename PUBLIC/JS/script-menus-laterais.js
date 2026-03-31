let page = window.location.pathname;

if(page.toLowerCase().includes("dashboard")){
    let area = document.getElementById("dashboard")
    area.style.backgroundColor = "#3a3a3a"
    area.style.borderRadius = "8px"

};

if(page.toLowerCase().includes("catalogo") || page.toLowerCase().includes("prod")){
    let area = document.getElementById("catalogo")
    area.style.backgroundColor = "#3a3a3a"
    area.style.borderRadius = "8px"

};

if(page.toLowerCase().includes("clientes")  || window.location.search.toLowerCase().includes("cliente")){
    let area = document.getElementById("clientes")
    area.style.backgroundColor = "#3a3a3a"
    area.style.borderRadius = "8px"

};

if(page.toLowerCase().includes("vendas") || page.toLowerCase().includes("venda")){
    let area = document.getElementById("vendas")
    area.style.backgroundColor = "#3a3a3a"
    area.style.borderRadius = "8px"

};

if(page.toLowerCase().includes("cupom")){
    let area = document.getElementById("cupons")
    area.style.backgroundColor = "#3a3a3a"
    area.style.borderRadius = "8px"

};

if(page.toLowerCase().includes("ajustes")){
    let area = document.getElementById("ajustes")
    area.style.backgroundColor = "#3a3a3a"
    area.style.borderRadius = "8px"

};

if(page.toLowerCase().includes("vendedores") || window.location.search.toLowerCase().includes("vendedor")){
    let area = document.getElementById("vendedores")
    area.style.backgroundColor = "#3a3a3a"
    area.style.borderRadius = "8px"

};

if(page.toLowerCase().includes("rel")){
    let area = document.getElementById("relatorios")
    area.style.backgroundColor = "#3a3a3a"
    area.style.borderRadius = "8px"
};

document.addEventListener('DOMContentLoaded', function () {

    const hamburgerMenu = document.querySelector('.jp_hamburger-menu');
    const sidebar = document.querySelector('.jp_sidebar');
    const overlay = document.querySelector('.jp_overlay');
    const allMenuItems = document.querySelectorAll('.jp_sidebar nav ul li, .jp_bottom-menu ul li');
    
    function setActiveMenuItem() {
        const currentPath = window.location.pathname;
        const currentPage = currentPath.split('/').pop();
        
        allMenuItems.forEach(item => {
            item.classList.remove('jp_active');
        });
        
        const pageMap = {
            'vcl-dashboard-adm.php': 'dashboard',
            'catalogo_adm.php': 'catalogo',
            'clientes_adm.php': 'clientes',
            'lista-vendedores-adm.php': 'vendedores',
            'vendas-adm.php': 'vendas',
            'Rel.php': 'relatorios',
            'ajustes-informaçoes-adm.php': 'ajustes',
            'landing_page.php': 'sair'
        };
        
        const targetPage = pageMap[currentPage];
        
        if (targetPage) {
            const activeItem = document.querySelector(`[data-page="${targetPage}"]`);
            if (activeItem) {
                activeItem.closest('li').classList.add('jp_active');
            }
        }
    }
    

    setActiveMenuItem();
    
    allMenuItems.forEach(item => {
        item.addEventListener('click', function (e) {
            const link = this.querySelector('a');
            if (link && link.getAttribute('href') !== '#') {

                const dataPage = link.getAttribute('data-page');
                if (dataPage) {
                    localStorage.setItem('activeMenuItem', dataPage);
                }
            }
        });
    });
    

    const savedActiveItem = localStorage.getItem('activeMenuItem');
    if (savedActiveItem) {
        allMenuItems.forEach(item => {
            item.classList.remove('jp_active');
        });
        
        const activeItem = document.querySelector(`[data-page="${savedActiveItem}"]`);
        if (activeItem) {
            activeItem.closest('li').classList.add('jp_active');
        }
    }
    
    if (hamburgerMenu && sidebar) {
        hamburgerMenu.addEventListener('click', function() {
            hamburgerMenu.classList.toggle('active');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
        
        overlay.addEventListener('click', function() {
            hamburgerMenu.classList.remove('active');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
        
        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickInsideHamburger = hamburgerMenu.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickInsideHamburger && sidebar.classList.contains('active')) {
                hamburgerMenu.classList.remove('active');
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        });
    }
});