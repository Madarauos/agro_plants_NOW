<?php
include "../../INCLUDE/Menu_adm.php";
include "../../CONTROLLER/UsuarioController.php";
require_once "../../DB/Database.php"; 
require_once "../../INCLUDE/verificarLogin.php"; 
include "../../INCLUDE/vlibras.php";
include "../../INCLUDE/alertas.php";

$user_id = $_SESSION['id'] ?? null;
$controler_user = new UsuarioController();

$db = new Database();
$conn = $db->getConexao();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $stmt = $conn->prepare('
        UPDATE usuario 
        SET nome = ?, email = ?, telefone = ?, cpf = ?, cep = ?, data_nasc = ?
        WHERE id = ?
    ');
    $atualizar = $stmt->execute([
        $_POST['nome'],
        $_POST['email'],
        $_POST['telefone'],
        $_POST['cpf'],
        $_POST['cep'],
        $_POST['data_nasc'],
        $user_id
    ]);

    if($atualizar == 1){
        $_SESSION['alerta'] = '<script> exibirAlerta("Informações atualizadas com sucesso","sucesso"); </script>';
    }else{
        $_SESSION['alerta'] = '<script> exibirAlerta("Não foi possível atualizadar as informações","error"); </script>';
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    
    if($controler_user->verificar_senha($_SESSION,$_POST['senha_atual'])){
        if ($_POST['nova_senha'] === $_POST['confirmar_senha']) {
            if($controler_user->alterar_senha()){
                print_r($controler_user->alterar_senha());
                $_SESSION['alerta'] = '<script> exibirAlerta("Senha atualizada com sucesso","sucesso"); </script>';
            }else{
                $_SESSION['alerta'] = '<script> exibirAlerta("Não foi possível atualizar a senha","error"); </script>';
            }
        }else{
            $_SESSION['alerta'] = '<script> exibirAlerta("As senha não são iguais","error"); </script>';
        }
    }else{
        $_SESSION['alerta'] = '<script> exibirAlerta("Senha incorreta","error"); </script>';
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
}

$stmt = $conn->prepare('
    SELECT nome, email, tipo, telefone, cpf, cep, data_nasc, foto
    FROM usuario 
    WHERE id = ?
');
$stmt->execute([$user_id]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações do Perfil</title>
    <link rel="stylesheet" href="../../PUBLIC/css/style_menu.css">
    <link rel="stylesheet" href="../../PUBLIC/css/ajustes.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <main class="jp_main-content">
        <h1 class="page-title ym_titulo">Configurações do Perfil</h1>

        <header class="profile-header">
            <div class="profile-info">
                <div class="profile-pic-container">
                    <?php if ($user_data['foto']): ?>
                        <img src="<?= $user_data['foto'] ?>" alt="Foto de Perfil" class="profile-pic">
                    <?php else: ?>
                        <span class="profile-placeholder">Foto de Perfil</span>
                    <?php endif; ?>
                </div>
                <div class="profile-text">
                    <h2><?php echo htmlspecialchars($user_data['nome']); ?></h2>
                    <p><?php echo htmlspecialchars($user_data['email']); ?></p>
                </div>
            </div>
            <div class="profile-badges">
                <div class="role-badge"><?php echo htmlspecialchars($user_data['tipo'] === 'admin' ? 'Administrador' : $user_data['tipo']); ?></div>
                <div class="status-badge online">online</div>
            </div>
        </header>

        <nav class="tabs-nav">
            <button class="tab-btn active" data-tab="personal">
                <i class="fas fa-user"></i> Informações Pessoais
            </button>
            <button class="tab-btn" data-tab="security">
                <i class="fas fa-shield-alt"></i> Segurança
            </button>
            <!-- <button class="tab-btn" data-tab="preferences">
                <i class="fas fa-palette"></i> Preferências
            </button> -->
        </nav>

        <div class="tab-content active" id="personal">
    <div class="content-card">
        <div class="card-header">
            <h3><i class="fas fa-user-edit"></i> Informações Pessoais</h3>
            <button type="button" class="btn-edit" onclick="enableEdit('personal')">
                <i class="fas fa-edit"></i> Editar
            </button>
        </div>
        <form id="personalForm" class="form-grid" method="POST">
            <input type="hidden" name="update_info" value="1">
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($user_data['nome']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Telefone</label>
                <input type="tel" name="telefone" value="<?php echo htmlspecialchars($user_data['telefone']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Data de Nascimento</label>
                <input type="date" name="data_nasc" value="<?php echo htmlspecialchars($user_data['data_nasc']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>CPF</label>
                <input type="text" name="cpf" value="<?php echo htmlspecialchars($user_data['cpf']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Tipo (Cargo)</label>
                <input type="text" value="<?php echo htmlspecialchars($user_data['tipo'] === 'admin' ? 'Administrador' : $user_data['tipo']); ?>" readonly disabled>
            </div>
            <div class="form-group full-width">
                <label>CEP</label>
                <input type="text" name="cep" value="<?php echo htmlspecialchars($user_data['cep']); ?>" readonly>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="cancelEdit('personal')">Cancelar</button>
                <button type="submit" class="btn-save">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- Alterar Senha -->
<div class="tab-content" id="security">
    <div class="content-card">
        <div class="card-header">
            <h3><i class="fas fa-shield-alt"></i> Segurança da Conta</h3>
        </div>
        
        <div class="security-section">
            <h4>Alterar Senha</h4>
            <form id="passwordForm" class="password-form" method="POST">
                <input type="hidden" name="update_password" value="1">
                <div class="form-group">
                    <label>Senha Atual</label>
                    <div class="password-input">
                        <input type="password" id="currentPassword" class="placeholder_senha" name="senha_atual" placeholder="Digite sua senha atual" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('currentPassword')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nova Senha</label>
                    <div class="password-input">
                        <input type="password" id="newPassword" class="placeholder_senha" name="nova_senha" placeholder="Digite a nova senha" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('newPassword')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                    <span class="strength-text" id="strengthText">Força da senha</span>
                </div>
                <div class="error-message" id="newPasswordError"></div>
                <div class="form-group">
                    <label>Confirmar Nova Senha</label>
                    <div class="password-input">
                        <input type="password" id="confirmPassword" class="placeholder_senha" name="confirmar_senha" placeholder="Confirme a nova senha" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-save">Alterar Senha</button>
            </form>
        </div>
    </div>
</div>



        <!-- <div class="tab-content" id="preferences">
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-palette"></i> Preferências do Sistema</h3>
                </div> -->
                
                <!-- <div class="preference-section">
                    <h4>Aparência</h4>
                    <div class="theme-selector">
                        <div class="theme-option active" data-theme="light">
                            <div class="theme-preview light">
                                <div class="preview-header"></div>
                                <div class="preview-content"></div>
                            </div>
                            <label class="tema">Claro</label>
                        </div> -->
                        <!-- <div class="theme-option" data-theme="dark">
                            <div class="theme-preview dark">
                                <div class="preview-header"></div>
                                <div class="preview-content"></div>
                            </div>
                            <label class="tema">Escuro</label>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </main>

    <div id="toast-container"></div>


    <script src="../../PUBLIC/JS/script-ajustes.js"></script>
    <!-- <script src="../../PUBLIC/JS/script-tema.js"></script> -->
</body>
</html>
