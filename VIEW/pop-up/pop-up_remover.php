<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Confirmação de Remoção</title>
<link rel="stylesheet" href="../../PUBLIC/css/pop-up_remover.css">
<link rel="stylesheet" href="../../PUBLIC/css/pop-up-cadastrar_vendedor.css">
<style>
  .hidden { display: none; }
</style>
</head>
<body>

<div class="eze-form-row">
  <div id="confirm-step">
    <h3>Você tem certeza?</h3>
    <p>Esta ação irá processar sua solicitação. Deseja continuar?</p>
    <div class="eze-button-container">
      <button type="button" class="eze-add-button" id="btn-sim">Sim</button>
      <button type="button" class="eze-add-button eze-add-button2" onclick="fecharPopup()" class="eze-add-button eze-add-button2" id="btn-nao">Não</button>
    </div>
  </div>
</div>

<form method="post" id="password-step" class="ym_form-pop-up hidden">
  <h3>Confirme sua senha</h3>
  <p>Insira sua senha para confirmar a remoção</p>

  <div class="ym_input-area">
    <input type="password" name="alter_status" class="ym_input-padrao" placeholder="Digite sua senha" required>
    <input type="hidden" name="id" value="<?= ($_GET['id'])?>">
  </div>

  <div class="eze-button-container">
    <button type="submit" class="eze-add-button" id="btn-confirmar">Confirmar</button>
    <button type="button" onclick="fecharPopup()" class="eze-add-button eze-add-button2" id="btn-cancelar-senha">Cancelar</button>
  </div>
</form>


<script>
const btnSim = document.getElementById('btn-sim');
const btnCancelarSenha = document.getElementById('btn-cancelar-senha');
const btnConfirmar = document.getElementById('btn-confirmar');
const confirmStep = document.getElementById('confirm-step');
const passwordStep = document.getElementById('password-step');

btnSim.addEventListener('click', () => {
  confirmStep.classList.add('hidden');
  passwordStep.classList.remove('hidden');
});

btnCancelarSenha.addEventListener('click', () => {
  passwordStep.classList.add('hidden');
  confirmStep.classList.remove('hidden');
});

btnConfirmar.addEventListener('click', () => {
  const senha = passwordStep.querySelector('input').value.trim();
  if (!senha) {
    return;
  }
  window.close();
});
</script>

</body>
</html>

<script src="../../PUBLIC/JS/script-pop-up.js"></script>