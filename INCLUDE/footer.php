<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agro Plants Now - Footer Elegante</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
     <link rel="stylesheet" href="cdnjs.cloudflare.com" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .footer {
            background: linear-gradient(135deg, #2c4934ff 0%, #2b723dff 50%, #2b4532ff 100%);
            color: white;
            padding: 1.5rem 0 0.4rem;
            position: relative;
            overflow: hidden;
    
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 25% 25%, rgba(76, 175, 80, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(139, 195, 74, 0.08) 0%, transparent 50%);
            pointer-events: none;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            position: relative;
            z-index: 1;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1.5fr;
            gap: 3.5rem;
            /* margin-bottom: 2.5rem; */
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 15px;
        }

        .footer-logo h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .company-description {
            color: #b8e6c1;
            /* line-height: 1.8; */
            font-size: 1.05rem;
            margin-bottom: 15px;
            font-weight: 400;
        }

        .social-links {
            display: flex;
            gap: 1.2rem;
            margin-bottom:0.4rem;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
            border-radius: 15px;
            color: white;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: 1.3rem;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .social-links a:hover {
            background: linear-gradient(135deg, #66bb6a 0%, #81c784 100%);
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
        }

        .footer-section h4 {
            font-size: 1.4rem;
            margin-bottom: 1.8rem;
            color: #4caf50;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }

        .footer-section h4::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, #4caf50 0%, #66bb6a 100%);
            border-radius: 2px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 1rem;
        }

        .footer-links a {
            color: #b8e6c1;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.05rem;
            font-weight: 400;
            position: relative;
            display: inline-block;
        }

        .footer-links a::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #4caf50;
            transition: width 0.3s ease;
        }

        .footer-links a:hover {
            color: #4caf50;
            transform: translateX(8px);
        }

        .footer-links a:hover::before {
            width: 100%;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            margin-bottom: 9px;
            color: #b8e6c1;
            font-size: 1.05rem;
        }

        .contact-item i {
            color: #4caf50;
            width: 22px;
            text-align: center;
            font-size: 1.2rem;
            filter: drop-shadow(0 1px 2px rgba(76, 175, 80, 0.3));
        }

        .footer-bottom {
            border-top: 1px solid rgba(76, 175, 80, 0.3);
            padding-top: 5px;
            text-align: center;
        }

        .footer-bottom p {
            color: #b8e6c1;
            font-size: 1rem;
            font-weight: 400;
        }

        .back-to-top {
            position: fixed;
            bottom: 2.5rem;
            right: 2.5rem;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
            color: white;
            border: none;
            border-radius: 18px;
            cursor: pointer;
            font-size: 1.4rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0;
            visibility: hidden;
            z-index: 1000;
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 10px 30px rgba(46, 125, 50, 0.5);
        }

        .logo-img{
            width: 80px;
            height: 80px;

        }

        @media (max-width: 768px) {
            .footer {
                padding: 3rem 0 2rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 3rem;
                text-align: center;
            }

            .footer-logo {
                justify-content: center;
            }

            .footer-logo h3 {
                font-size: 1.8rem;
            }

            .footer-logo i {
                font-size: 2.5rem;
            }

            .social-links {
                justify-content: center;
            }

            .contact-item {
                justify-content: center;
            }

            .footer-section h4::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .back-to-top {
                bottom: 2rem;
                right: 2rem;
                width: 55px;
                height: 55px;
                font-size: 1.3rem;
            }
        }

        @media (max-width: 480px) {
            .footer-container {
                padding: 0 1rem;
            }

            .footer-content {
                gap: 2.5rem;
            }

            .footer-logo h3 {
                font-size: 1.6rem;
            }

            .footer-logo i {
                font-size: 2.2rem;
            }

            .social-links a {
                width: 45px;
                height: 45px;
                font-size: 1.2rem;
            }

            .social-links {
                gap: 1rem;
            }

            .back-to-top {
                bottom: 1.5rem;
                right: 1.5rem;
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .social-links a,
            .footer-links a,
            .back-to-top {
                transition: none;
            }
        }
    </style>
</head>
<body>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section company-info">
                    <div class="footer-logo">
                        <img src="../../PUBLIC/img/img_logo.png" alt="Logo" class="logo-img">
                        <h3>AGRO PLANTS NOW</h3>
                    </div>
                    <p class="company-description">
                        A maior distribuidora de insumos e serviços do agronegócio. 
                        Conectando produtores rurais com as melhores soluções para o campo.
                    </p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="../../VIEW/paginas-iniciais/colaboradores.php" aria-label="WhatsApp"><i class="fa-solid fa-code"></i></a>
                    </div>
                </div>

                <div class="footer-section">
                    <h4>Navegação</h4>
                    <ul class="footer-links">
                        <li><a href="../../VIEW/paginas-iniciais/landing_page.php">Home</a></li>
                        <li><a href="../../VIEW/paginas-iniciais/sobre_nos.php">Sobre Nós</a></li>
                        <li><a href="../../VIEW/paginas-iniciais/contate_nos.php">Contate-nos</a></li>
                        <li><a href="../../VIEW/paginas-iniciais/colaboradores.php">Colaboradores</a></li>
                    </ul>
                </div>

                <div class="footer-section contact-info">
                    <h4>Contato</h4>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Campo Grande - MS</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>(11) 9999-9999</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>contato@agroplantsnow.com</span>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2024 Agro Plants Now. Todos os direitos reservados.</p>
            </div>
        </div>

        <button class="back-to-top" id="backToTop" aria-label="Voltar ao topo">
            <i class="fas fa-chevron-up"></i>
        </button>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const backToTopBtn = document.getElementById('backToTop');
            
            function toggleBackToTopButton() {
                if (window.pageYOffset > 300) {
                    backToTopBtn.classList.add('visible');
                } else {
                    backToTopBtn.classList.remove('visible');
                }
            }
            
            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
            
            window.addEventListener('scroll', toggleBackToTopButton);
            backToTopBtn.addEventListener('click', scrollToTop);
            
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>