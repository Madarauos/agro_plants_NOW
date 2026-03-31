<?php
include "../../CONTROLLER/UsuarioController.php";
session_start();

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<script>alert('ID não informado'); window.close();</script>";
    exit;
}
?>

<div class="ym_popup-confirmacao">
    <h3>Ativar Vendedor</h3>
    <p>Deseja realmente ativar este vendedor?</p>
    
    <form method="POST" action="../lista-vendedores-adm.php" class="ym_form-confirmacao">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        
        <div class="ym_area-senha">
            <label for="senha">Confirme sua senha:</label>
            <input type="password" name="alter_status" id="senha" required>
        </div>
        
        <div class="ym_area-botoes">
            <button type="button" class="ym_btn-cancelar" onclick="window.close()">Cancelar</button>
            <button type="submit" class="ym_btn-confirmar">Ativar</button>
        </div>
    </form>
</div>

<style>
.ym_popup-confirmacao {
    padding: 20px;
    max-width: 400px;
}

.ym_popup-confirmacao h3 {
    margin-bottom: 10px;
    color: #333;
}

.ym_popup-confirmacao p {
    margin-bottom: 20px;
    color: #666;
}

.ym_area-senha {
    margin-bottom: 20px;
}

.ym_area-senha label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.ym_area-senha input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.ym_area-botoes {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.ym_btn-cancelar, .ym_btn-confirmar {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.ym_btn-cancelar {
    background: #6c757d;
    color: white;
}

.ym_btn-confirmar {
    background: #28a745;
    color: white;
}
</style>