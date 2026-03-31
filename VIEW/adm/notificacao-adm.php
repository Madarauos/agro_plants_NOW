<?php
include "../../INCLUDE/Menu_adm.php";
require_once "../../INCLUDE/verificarLogin.php"; 
require_once "../../CONTROLLER/NotificacaoController.php";
include "../../INCLUDE/vlibras.php";

$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'estoque';

try {
    
    $notificacaoCtrl = new NotificacaoController();
    
    $todas_notificacoes = $notificacaoCtrl->listarNotificacoes();
    
    $notificacoes_estoque = [];
    $notificacoes_contato = [];

    if(isset($_POST['pesquisa'])){
        $todas_notificacoes = $notificacaoCtrl->pesquisar();   
    }

    if (is_array($todas_notificacoes)){
        foreach ($todas_notificacoes as $notificacao) {
            if (strpos($notificacao['titulo'], 'Estoque Baixo') !== false) {
                $notificacoes_estoque[] = [
                    'titulo' => $notificacao['titulo'],
                    'setor' => "Produtos",
                    'data' => date("d/m/Y", strtotime($notificacao['horario_criacao'])),
                    'id' => $notificacao['id'],
                    'assunto' => $notificacao['assunto']
                ];
            } else if (strpos($notificacao['titulo'], 'Novo Contato') !== false) {
                $dados = json_decode($notificacao['assunto'], true);
                
                if ($dados && isset($dados['tipo']) && $dados['tipo'] === 'contato') {
                    $notificacoes_contato[] = [
                        'titulo' => $notificacao['titulo'],
                        'data' => date("d/m/Y H:i", strtotime($notificacao['horario_criacao'])),
                        'mensagem' => $dados['mensagem'] ?? '',
                        'nome' => $dados['nome'] ?? '',
                        'email' => $dados['email'] ?? '',
                        'id' => $notificacao['id']
                    ];
                } else {
                    $assunto = $notificacao['assunto'];
                    $linhas = explode("\n", $assunto);
                    $nome = '';
                    $email = '';
                    $mensagem = '';
                    $capturando_mensagem = false;
                    
                    foreach ($linhas as $linha) {
                        $linha = trim($linha);
                        
                        if (strpos($linha, 'De:') !== false) {
                            if (preg_match('/De:\s*([^(]+)\s*\(([^)]+)\)/', $linha, $matches)) {
                                $nome = trim($matches[1]);
                                $email = trim($matches[2]);
                            }
                        } else if (strpos($linha, 'Mensagem:') !== false) {
                            $capturando_mensagem = true;
                        } else if ($capturando_mensagem && !empty($linha)) {
                            $mensagem .= $linha . "\n";
                        }
                    }
                    
                    $notificacoes_contato[] = [
                        'titulo' => $notificacao['titulo'],
                        'data' => date("d/m/Y H:i", strtotime($notificacao['horario_criacao'])),
                        'mensagem' => trim($mensagem),
                        'nome' => $nome,
                        'email' => $email,
                        'id' => $notificacao['id']
                    ];
                }
            }
        }
    }

    
    if ($filtro === 'mensagens') {
        $dados =  $notificacoes_contato;
    } else {
        $dados = $notificacoes_estoque;
    }

    $total_itens = count($dados);

} catch (Exception $e) {
    error_log("Erro: " . $e->getMessage());
    $dados = [];
    $total_itens = 0;
}

if (isset($_GET['remover'])) {
    $id_remover = $_GET['remover'];
    $notificacaoCtrl->deletarNotificacao($id_remover);
    header("Location: notificacao-adm.php?filtro=" . $filtro);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Notificações</title>
    <link rel="stylesheet" href="../../PUBLIC/css/lista-vendedores-adm.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="../../PUBLIC/css/notificacao_adm.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   
    <style>
    <?php if ($filtro === 'mensagens'): ?>
    .setor-col {
        display: none;
    }
    <?php endif; ?>
</style>
</head>
<body>
    <main class="jp_main-content">
        <h1 class="ym_titulo">Lista de Notificações</h1>

        <div class="jv_container">
            <div class="jv_card">
                <div class="jv_card-header">
                    <div class="jv_header-content">
                        <form method="POST" action="#" class="jv_search-section">
                            <div class="jv_search-container">
                                <button type="submit" class="ym_area-icon-pesquisa">
                                    <i class="fas fa-search search-icon"></i>
                                </button>
                                <?php
                                if(isset($_POST["pesquisa"])){
                                    echo'<input type="text" name="pesquisa" id="jv_searchInput" placeholder="Pesquisar..." class="jv_search-input" value = '. $_POST["pesquisa"] .'>';
                                }else{
                                    echo'<input type="text" name="pesquisa" id="jv_searchInput" placeholder="Pesquisar..." class="jv_search-input">';
                                }
                                
                                ?>
                                
                            </div>
                        </form>

                        <div class="jv_actions">
                            <div>
                                <select class="jv_filter-select" onchange="window.location.href = '?filtro=' + this.value">
                                    <option value="estoque" <?= $filtro === 'estoque' ? 'selected' : '' ?>>Estoque</option>
                                    <option value="mensagens" <?= $filtro === 'mensagens' ? 'selected' : '' ?>>Mensagens</option>
                                </select>
                                
                                <button class="ym_btn-remover" id="jv_removeSelected" style="display: none;">
                                    <i class="fa-solid fa-trash-can"></i>
                                    Remover (<span id="jv_selectedCount">0</span>)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="jv_card-content">
                    <div class="jv_table-container">
                        <table class="jv_table">
                            <thead>
                                <tr class="jv_table-header">
                                    <th class="jv_date">Título</th>
                                    <?php if ($filtro === 'estoque'): ?>
                                        <th class="jv_total_comp setor-col">Setor</th>
                                    <?php endif; ?>
                                    <th class="jv_valor_gast">Data</th>
                                    <th class="jv_actions-col"></th> 
                                </tr>
                            </thead>
                            <tbody id="jv_customerTableBody">
                            <?php if ($total_itens > 0): ?>
                                <?php foreach ($dados as $index => $item): ?>
                                    <tr>
                                        <td>
                                            <div class="jv_customer-info">
                                                <div class="jv_avatar <?= $filtro === 'mensagens' ? 'email-avatar' : '' ?>">
                                                    <?= $filtro === 'estoque' ? '<i class="fa-solid fa-triangle-exclamation"></i> ' : '<i class="fa-solid fa-message"></i>' ?>
                                                </div>
                                                <div class="jv_customer-details">
                                                    <?php if ($filtro === 'mensagens'): ?>
                                                        <div class="mensagem-completa mensagem-clicavel" 
                                                             onclick="abrirPopupEmail(
                                                                '<?= htmlspecialchars(addslashes($item['email']), ENT_QUOTES) ?>', 
                                                                '<?= htmlspecialchars(addslashes($item['data']), ENT_QUOTES) ?>', 
                                                                `<?= htmlspecialchars(addslashes($item['mensagem']), ENT_QUOTES) ?>`,
                                                                '<?= htmlspecialchars(addslashes($item['nome']), ENT_QUOTES) ?>'
                                                             )">
                                                            <h4><?= htmlspecialchars($item['titulo']) ?></h4>
                                                            <div class="conteudo-mensagem">
                                                                <?php
                                                                $mensagem_abreviada = $item['mensagem'];
                                                                if (strlen($mensagem_abreviada) > 100) {
                                                                    $mensagem_abreviada = substr($mensagem_abreviada, 0, 100) . '...';
                                                                }
                                                                echo nl2br(htmlspecialchars($mensagem_abreviada));
                                                                ?>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <h4><?= htmlspecialchars($item['titulo']) ?></h4>
                                                        <p><?= htmlspecialchars($item['assunto'] ?? 'Produto com estoque baixo') ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <?php if ($filtro === 'estoque'): ?>
                                            <td class="setor-col"><?= htmlspecialchars($item['setor']) ?></td>
                                        <?php endif; ?>
                                        <td><?= $item['data'] ?></td>
                                        <td class="jv_table-action">
                                            <button class="jv_menu-btn" onclick="toggleDropdown(this)">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <div class="jv_dropdown">
                                                <button class="jv_dropdown-item remover-btn" onclick="removerNotificacao(<?= is_numeric($item['id']) ? $item['id'] : "'{$item['id']}'" ?>)">
                                                    <i class="fas fa-trash"></i> Remover
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= $filtro === 'estoque' ? '5' : '4' ?>" style="text-align: center; height: 49.7vh;">
                                        Nenhum item encontrado
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="popupEmail" class="popup-email">
            <div class="popup-email-content">
                <div class="popup-email-header">
                    <h3><i class="fa-solid fa-message"></i> Detalhes da Mensagem</h3>
                    <button class="popup-email-close" onclick="fecharPopupEmail()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="popup-email-body">
                    <div class="email-info-grid">
                        <div class="info-item">
                            <span class="info-label">Nome do Remetente</span>
                            <span class="info-value" id="popupNome"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value" id="popupRemetente"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Data e Hora</span>
                            <span class="info-value" id="popupData"></span>
                        </div>
                    </div>
                    
                    <div class="mensagem-container">
                        <span class="mensagem-label">Mensagem Recebida</span>
                        <div class="mensagem-conteudo" id="popupMensagem"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../../PUBLIC/JS/script-pop-up.js"></script>
    <script src="../../PUBLIC/JS/script.js"></script>
    <script src="../../PUBLIC/JS/script-clientes-adm.js"></script>
    
    <script>
        function removerNotificacao(id) {
            if (confirm('Tem certeza que deseja remover esta notificação?')) {
                // Se for um ID de exemplo, apenas recarrega a página
                if (typeof id === 'string' && id.startsWith('exemplo')) {
                    alert('Esta é uma mensagem de exemplo e não pode ser removida.');
                    return;
                }
                window.location.href = '?remover=' + id + '&filtro=<?= $filtro ?>';
            }
        }
        
        function abrirPopupEmail(email, data, mensagem, nome) {
            const decodeHTML = (html) => {
                const txt = document.createElement('textarea');
                txt.innerHTML = html;
                return txt.value;
            };

            document.getElementById('popupNome').textContent = decodeHTML(nome);
            document.getElementById('popupRemetente').textContent = decodeHTML(email);
            document.getElementById('popupData').textContent = decodeHTML(data);
            document.getElementById('popupMensagem').textContent = decodeHTML(mensagem);
            document.getElementById('popupEmail').style.display = 'flex';
            
            document.body.style.overflow = 'hidden';
        }

        function fecharPopupEmail() {
            document.getElementById('popupEmail').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        document.getElementById('popupEmail').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharPopupEmail();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fecharPopupEmail();
            }
        });

        function toggleDropdown(button) {
            const dropdown = button.nextElementSibling;
            const allDropdowns = document.querySelectorAll('.jv_dropdown');
            
            allDropdowns.forEach(d => {
                if (d !== dropdown) {
                    d.style.display = 'none';
                }
            });
            
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.jv_menu-btn') && !e.target.closest('.jv_dropdown')) {
                document.querySelectorAll('.jv_dropdown').forEach(d => {
                    d.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>