<?php
require_once "../../INCLUDE/verificarLogin.php";
include "../../INCLUDE/Menu_vend.php";
include "../../INCLUDE/vlibras.php";

if (!isset($_GET['id_cliente']) && !isset($_GET['nome'])) {
    die("Cliente não informado");
}

$id_cliente = $_GET['id_cliente'];
$nome_cliente = $_GET['nome'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Seleção do tipo de venda">
    <title>Tipo de Venda - <?= htmlspecialchars($nome_cliente) ?></title>
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/global-tema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>

        body.dark-theme .selecao-card{
            background-color: #2c2c2c;
            color: #e0e0e0;
        }

        body.dark-theme .cliente-info{
            background: #3a3a3a;
            color: #ffffffff;
        }

        .selecao-container {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .selecao-card {
            background-color: white;
            border-radius: 20px;
            padding: 50px;
            max-width: 900px;
            width: 100%;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .selecao-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .selecao-header h1 {
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .selecao-header .cliente-info {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #f8f9fa;
            padding: 12px 24px;
            border-radius: 50px;
            color: #3e704c;
            font-weight: 600;
            margin-top: 15px;
        }

        .selecao-header .cliente-info i {
            font-size: 18px;
        }

        .selecao-opcoes {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 30px;
        }

        .opcao-card {
            background: #45734b;
            border-radius: 16px;
            padding: 40px 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            color: white;
            display: block;
        }


        .opcao-card:hover::before {
            opacity: 1;
        }

        .opcao-card.produto {
            background: #45734b;
        }


        .opcao-card.servico {
            background: #45734b;
        }

        .opcao-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 36px;
            transition: all 0.3s ease;
        }

        .opcao-card:hover .opcao-icon {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1) rotate(5deg);
        }

        .opcao-titulo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .opcao-descricao {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .back-button-container {
            text-align: center;
            margin-top: 30px;
        }

        .btn-voltar {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 30px;
            background: #45734b;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .selecao-opcoes {
                grid-template-columns: 1fr;
            }

            .selecao-card {
                padding: 30px 20px;
            }

            .selecao-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<main class="jp_main-content">
    <div class="selecao-container">
        <div class="selecao-card">
            <div class="selecao-header">
                <h1>Selecione o Tipo de Venda</h1>
                <div class="cliente-info">
                    <i class="fa-solid fa-user"></i>
                    <span><?= htmlspecialchars($nome_cliente) ?></span>
                </div>
            </div>

            <div class="selecao-opcoes">
                <a href="carrinho_vend.php?id_cliente=<?= $id_cliente ?>&nome=<?= urlencode($nome_cliente) ?>&id_vendedor=<?= $_SESSION['id'] ?>&tipo=produto" class="opcao-card produto">
                    <div class="opcao-icon">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <h2 class="opcao-titulo">Produtos</h2>
                    <p class="opcao-descricao">
                        Venda de produtos físicos do catálogo com controle de estoque e gerenciamento de entregas
                    </p>
                </a>

                <a href="carrinho_servico.php?id_cliente=<?= $id_cliente ?>&nome=<?= urlencode($nome_cliente) ?>&tipo=servico" class="opcao-card servico">
                    <div class="opcao-icon">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <h2 class="opcao-titulo">Serviços</h2>
                    <p class="opcao-descricao">
                        Venda de serviços prestados com agendamento e acompanhamento de execução
                    </p>
                </a>
            </div>

            <div class="back-button-container">
                <a href="lista-clientes.php" class="btn-voltar">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Voltar para Clientes</span>
                </a>
            </div>
        </div>
    </div>
</main>

<!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->

</body>
</html>