<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolha o Tipo</title>
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .escolha-container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .escolha-titulo {
            text-align: center;
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .opcoes-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .opcao-card {
            background: #45734b;
            border-radius: 15px;
            padding: 35px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
            position: relative;
            overflow: hidden;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .opcao-card.produto {
            background: #45734b;
        }

        .opcao-card.servico {
            background: #45734b;
        }

        .opcao-icon {
            font-size: 3.5rem;
            color: white;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .opcao-titulo {
            font-size: 1.3rem;
            color: white;
            font-weight: 600;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .opcao-descricao {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.95);
            position: relative;
            z-index: 1;
            line-height: 1.4;
        }

        .btn-cancelar {
            width: 100%;
            padding: 14px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .btn-cancelar:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-cancelar:active {
            transform: translateY(0);
        }

        @media (max-width: 600px) {
            .opcoes-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .opcao-card {
                min-height: 160px;
                padding: 30px 20px;
            }

            .opcao-icon {
                font-size: 3rem;
            }

            .opcao-titulo {
                font-size: 1.2rem;
            }

            .escolha-titulo {
                font-size: 1.3rem;
                margin-bottom: 25px;
            }
        }

        /* Animação de entrada */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .escolha-titulo {
            animation: fadeInUp 0.3s ease forwards;
        }

        .opcao-card {
            animation: fadeInUp 0.4s ease forwards;
            opacity: 0;
        }

        .opcao-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .opcao-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .btn-cancelar {
            animation: fadeInUp 0.4s ease forwards;
            animation-delay: 0.3s;
            opacity: 0;
        }
    </style>
</head>
<body>
    <div class="escolha-container">
        <h2 class="escolha-titulo">O que você deseja cadastrar?</h2>
        
        <div class="opcoes-grid">
            <div class="opcao-card produto" onclick="selecionarTipo('produto')" role="button" tabindex="0">
                <div class="opcao-icon">
                    <i class="fa-solid fa-box"></i>
                </div>
                <h3 class="opcao-titulo">Produto</h3>
                <p class="opcao-descricao">Cadastre um novo produto no catálogo</p>
            </div>
            
            <div class="opcao-card servico" onclick="selecionarTipo('servico')" role="button" tabindex="1">
                <div class="opcao-icon">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <h3 class="opcao-titulo">Serviço</h3>
                <p class="opcao-descricao">Cadastre um novo serviço no catálogo</p>
            </div>
        </div>
    </div>

    <script>
        function selecionarTipo(tipo) {
            let popupUrl;
            
            if (tipo === 'produto') {
                popupUrl = '../../VIEW/pop-up/pop-up-add-produto.php';
            } else if (tipo === 'servico') {
                popupUrl = '../../VIEW/pop-up/pop-up-add-servico.php';
            }
            
            // Como o popup foi carregado via fetch, precisamos acessar a função do documento principal
            // A função abrirPopup está no escopo global do documento principal
            if (parent && parent.abrirPopup) {
                parent.abrirPopup(popupUrl, false);
            } else if (window.parent && window.parent.abrirPopup) {
                window.parent.abrirPopup(popupUrl, false);
            } else {
                console.error('Função abrirPopup não encontrada no parent');
            }
        }

        // Suporte para navegação por teclado
        document.querySelectorAll('.opcao-card').forEach(card => {
            card.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    </script>
</body>
</html><m