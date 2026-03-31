<?php
include "../../INCLUDE/Menu_superior.php";
include "../../INCLUDE/vlibras.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />         
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu_superior.css">         
    <link rel="stylesheet" href="../../PUBLIC/css/landing_pg.css">
</head>
<body>            
    <main class="jp-main-content">
        <section class="er_imagem">
            <img src="../../PUBLIC/img/img_home.png" alt="Imagem de fundo do agronegócio" class="img-lp">
            <div class="img-container">
                <h1 class="er_texto">Seja um parceiro na maior rede de distribuição de insumos agrícolas do Brasil</h1>
                <div class="er_btn-sonic">
                    <button class="er_btn-sobre-nos">
                        <a href="sobre_nos.php">Sobre-Nós</a>
                    </button>
                </div>
            </div>
        </section>
                
        <!-- Seção Carrossel -->
        <section class="er_carrosel-box">
            <div class="carousel-container">
                <h2 class="carousel-title">Mais de 100 Produtos Disponíveis</h2>
                <div class="carousel-wrapper">
                    <button class="carousel-btn prev" onclick="moveCarousel(-1)" aria-label="Anterior">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="carousel-content" id="carouselContent">
                        <div class="product-card">
                            <div class="product-image-wrapper">
                              <img src="../../PUBLIC/img/<?php echo !empty($produto['foto']) ? $produto['foto'] : 'img_produto.webp'; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="product-image">
                            </div>
                            <div class="product-info">
                                <h3>Fertilizantes</h3>
                                <p>Nutrição completa para suas culturas</p>
                            </div>
                        </div>
                        <div class="product-card">
                            <div class="product-image-wrapper">
                                <img src="../../PUBLIC/img/<?php echo !empty($produto['foto']) ? $produto['foto'] : 'img_produto.webp'; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="product-image">
                            </div>
                            <div class="product-info">
                                <h3>Defensivos</h3>
                                <p>Proteção eficaz contra pragas e doenças</p>
                            </div>
                        </div>
                        <div class="product-card">
                            <div class="product-image-wrapper">
                               <img src="../../PUBLIC/img/<?php echo !empty($produto['foto']) ? $produto['foto'] : 'img_produto.webp'; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="product-image">
                            </div>
                            <div class="product-info">
                                <h3>Sementes</h3>
                                <p>Genética de alta qualidade e produtividade</p>
                            </div>
                        </div>
                        <div class="product-card">
                            <div class="product-image-wrapper">
                                 <img src="../../PUBLIC/img/<?php echo !empty($servico['foto']) ? $servico['foto'] : 'img_servico.webp'; ?>"alt="<?php echo htmlspecialchars($servico['nome']); ?>" class="product-image">
                            </div>
                            <div class="product-info">
                                <h3>Equipamentos</h3>
                                <p>Tecnologia avançada para o campo</p>
                            </div>
                        </div>
                        <div class="product-card">
                            <div class="product-image-wrapper">
                                <img src="../../PUBLIC/img/<?php echo !empty($servico['foto']) ? $servico['foto'] : 'img_servico.webp'; ?>" alt="<?php echo htmlspecialchars($servico['nome']); ?>" class="product-image">
                            </div>
                            <div class="product-info">
                                <h3>Irrigação</h3>
                                <p>Sistemas eficientes de irrigação</p>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-btn next" onclick="moveCarousel(1)" aria-label="Próximo">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="carousel-dots" id="carouselDots"></div>
            </div>
                        
            <div class="why-section">
                <h2 class="why-title">Por que você vai amar nossos Produtos Agrícolas</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h3>Muito Fácil</h3>
                        <p>Você pode adquirir seus produtos em minutos</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h3>Super Personalizável</h3>
                        <p>Soluções sob medida para cada tipo de cultura</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-thumbs-up"></i>
                        </div>
                        <h3>Aprovado pelos Produtores</h3>
                        <p>Milhares de agricultores confiam em nossos produtos</p>
                    </div>
                </div>
                <button class="cta-button" onclick="window.location.href='contate_nos.php'">COMECE SUA PARCERIA</button>
            </div>
                        
            <div class="final-section">
                <div class="final-content">
                    <div class="final-text">
                        <h2>Organize as vendas de seus produtos</h2>
                        <p>Um método mais prático e acessível para sua organização de vendas e clientes</p>
                    </div>
                    <div class="final-image">
                        <img src="../../PUBLIC/img/dashboard-vend2.png" alt="Dashboard da fazenda" class="dashboard-image">
                    </div>
                </div>
            </div>
        </section>
    </main>
        
    <footer>
        <?php include "../../INCLUDE/footer.php"; ?>
    </footer>
    
    <script src="../../PUBLIC/JS/landing_page.js"></script>
    
    <!-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                initializeMenuFix();
            }, 100);
        });

        function initializeMenuFix() {
            const hamburgerMenu = document.querySelector(".jp_hamburger-menu");
            const sidebar = document.querySelector(".jp_sidebar");
            const overlay = document.querySelector(".jp_overlay");
            const body = document.body;

            if (!hamburgerMenu || !sidebar || !overlay) {
                console.log("Elementos do menu não encontrados, tentando novamente...");
                setTimeout(initializeMenuFix, 200);
                return;
            }

            console.log("Menu elements found, initializing...");

            hamburgerMenu.replaceWith(hamburgerMenu.cloneNode(true));
            const newHamburgerMenu = document.querySelector(".jp_hamburger-menu");

            newHamburgerMenu.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log("Hamburger clicked");
                toggleMenuFix();
            });

            overlay.addEventListener("click", function() {
                console.log("Overlay clicked");
                closeMenuFix();
            });

            const navLinks = sidebar.querySelectorAll(".jp_nav-links a");
            navLinks.forEach(function(link) {
                link.addEventListener("click", function() {
                    closeMenuFix();
                });
            });

            document.addEventListener("keydown", function(event) {
                if (event.key === "Escape" && sidebar.classList.contains("active")) {
                    closeMenuFix();
                }
            });

            window.addEventListener("resize", function() {
                if (window.innerWidth > 992 && sidebar.classList.contains("active")) {
                    closeMenuFix();
                }
            });

            function toggleMenuFix() {
                const isActive = sidebar.classList.contains("active");
                console.log("Menu is active:", isActive);
                
                if (isActive) {
                    closeMenuFix();
                } else {
                    openMenuFix();
                }
            }

            function openMenuFix() {
                console.log("Opening menu");
                newHamburgerMenu.classList.add("active");
                sidebar.classList.add("active");
                overlay.classList.add("active");
                body.classList.add("menu-open");
                
                sidebar.offsetHeight;
            }

            function closeMenuFix() {
                console.log("Closing menu");
                newHamburgerMenu.classList.remove("active");
                sidebar.classList.remove("active");
                overlay.classList.remove("active");
                body.classList.remove("menu-open");
            }
        }
    </script> -->
</body>
</html>
