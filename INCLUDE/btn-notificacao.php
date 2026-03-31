<?php
require_once "../../DB/Database.php";
require_once "../../CONTROLLER/NotificacaoController.php";

$notificacaoCtrl = new NotificacaoController();
$notificacoes = $notificacaoCtrl->listarNotificacoes(10);
$todas_notificacoes = $notificacaoCtrl->listarNotificacoes();

$produtos = $produtoController->index();

$not_produtos = [];


foreach ($todas_notificacoes as $notificacao) {
    $nome = str_replace("Estoque Baixo - ", "", $notificacao['titulo']);
    array_push($not_produtos,$nome);
}

foreach ($produtos as $produto) {
    if (!in_array($produto['nome'], $not_produtos)) {
        if ($produto['quantidade'] <= 20) {
            echo "criou" . "<br>";
            $notificacaoCtrl->criarNotificacaoEstoque($produto['nome'], $produto['quantidade']);
        }
    }
}

$alertasContato = [];
$alertasEstoque = [];

if (!isset($notificacoes['error']) && is_array($notificacoes) && !empty($notificacoes)) {
    foreach ($notificacoes as $notificacao) {
        
        if (strpos($notificacao['titulo'], 'Contato') !== false) {
            
            
            $nomeCliente = str_replace('Novo Contato - ', '', $notificacao['titulo']);
            $nomeCliente = str_replace('Novo Contato – ', '', $nomeCliente);
            
            
            if (strlen($nomeCliente) <= 20 && !preg_match('/^[a-zA-Z]{10,}$/', $nomeCliente)) {
                // Nome válido
                $alertasContato[] = [
                    "mensagem" => "📩 <b>Você tem uma nova mensagem de:</b><br>
                                 <small style='color: #e0e0e0; font-weight: 900; font-size: 13px;'>" . htmlspecialchars($nomeCliente) . "</small>",
                    "hora"     => date("H:i", strtotime($notificacao['horario_criacao']))
                ];
            } else {
                // Nome inválido
                $alertasContato[] = [
                    "mensagem" => "📩 <b>Você tem uma nova mensagem</b>",
                    "hora"     => date("H:i", strtotime($notificacao['horario_criacao']))
                ];
            }
                                 
        } elseif (strpos($notificacao['titulo'], 'Estoque') !== false) {
            
            $alertasEstoque[] = [
                "mensagem" => "📦 <b>Alerta de Estoque</b><br>
                             <small style='color: #e0e0e0; font-weight: 900; font-size: 13px;'>" . htmlspecialchars($notificacao['titulo']) . "</small><br>
                             <small style='color: #c53030; font-weight: 900; font-size: 13px;'>Necessária reposição</small>",
                "hora"     => date("H:i", strtotime($notificacao['horario_criacao']))
            ];
        }
    }
}

$totalNotificacoes = is_array($notificacoes) ? count($notificacoes) : 0;


$alertasVisiveis = [];


if (!empty($alertasEstoque)) {
    $alertasVisiveis[] = $alertasEstoque[0];
}


if (!empty($alertasContato)) {
    $alertasVisiveis[] = $alertasContato[0];
}


if (empty($alertasVisiveis)) {
    $alertasVisiveis = [
        [
            "mensagem" => "📩 <b>Você tem uma nova mensagem de:</b><br>
                          <small style='color: #e0e0e0; font-weight: 900; font-size: 13px;'>João Silva</small>",
            "hora"     => date("H:i")
        ],
        [
            "mensagem" => "📦 <b>Alerta de Estoque</b><br>
                          <small style='color: #e0e0e0; font-weight: 900; font-size: 13px;'>Estoque Baixo - Soja</small><br>
                          <small style='color: #c53030; font-weight: 900; font-size: 13px;'>Quantidade: 2 unidades</small>",
            "hora"     => date("H:i", time() - 1800)
        ]
    ];
}
?>

<div class="ym_box-notificacao">
    <div class="ym_area-notificacao">
        <div class="ym_area-icons">
            <!-- ÍCONE DE SINO (sempre visível quando fechado) -->
            <div class="jp_notification-icon">
                <i class="fas fa-bell"></i>
            </div>
            
            <!-- ÍCONE DE X (só aparece quando aberto) -->
            <div class="jp_close-icon" style="display: none;">
                <i class="fas fa-times"></i>
            </div>

            <div class="ym_indicador-notificacoes">
                <p class="ym_p"><?= $totalNotificacoes ?: '0' ?></p>
            </div>

            <div class="ym_titulo-notficacoes">
                <p class="ym_p">Suas notificações</p>
            </div>
        </div>

        <div class="ym_notificacoes">
            <?php if (!empty($alertasVisiveis)): ?>
                <?php foreach ($alertasVisiveis as $alerta): ?>
                    <div class="ym_notificacao-item" style="padding: 12px 15px; border-bottom: 1px solid #e2e8f0;">
                        <p class="ym_p" style="margin: 0 0 5px 0; font-size: 14px; line-height: 1.4;"><?= $alerta['mensagem'] ?></p>
                        <p class="ym_p" style="margin: 0; color: #718096; font-size: 12px; font-weight: 500;"><?= $alerta['hora'] ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            
            <?php if ($totalNotificacoes > 0): ?>
                <div class="vc_ver-mais" style="padding: 10px 15px;">
                    <a class="vc_not-more" href="../../VIEW/adm/notificacao-adm.php" 
                       style="background: #28a745; color: white; text-decoration: none; font-size: 14px; font-weight: 500; padding: 8px 16px; border-radius: 4px; display: block; text-align: center; transition: background 0.3s;">
                        Ver mais notificações
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const notificacao = document.getElementsByClassName('ym_area-notificacao')[0];
const sino = document.querySelector('.jp_notification-icon');
const closeBtn = document.querySelector('.jp_close-icon');

if (notificacao && sino && closeBtn) {
    // ABRIR/FECHAR POPUP
    notificacao.addEventListener('click', () => {
        notificacao.classList.toggle('active');
        
        if (notificacao.classList.contains('active')) {
            // Popup aberto: mostra X, esconde sino
            sino.style.display = 'none';
            closeBtn.style.display = 'flex';
        } else {
            // Popup fechado: mostra sino, esconde X
            sino.style.display = 'flex';
            closeBtn.style.display = 'none';
        }
    });

    // FECHAR AO CLICAR NO X
    closeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notificacao.classList.remove('active');
        sino.style.display = 'flex';
        closeBtn.style.display = 'none';
    });
}
</script>