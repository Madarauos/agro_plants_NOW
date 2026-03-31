<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Carregando</title>
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
body{
  background-color: transparent;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(76, 175, 80, 0.2);
  border-top: 4px solid #4caf50;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 20px;
}
.agro-icons {
  font-size: 24px;
  display: flex;
  gap: 15px;
}
.agro-icons span {
  animation: bounce 1.4s infinite;
}
.agro-icons span:nth-child(2) {
  animation-delay: 0.2s;
}
.agro-icons span:nth-child(3) {
  animation-delay: 0.4s;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
@keyframes bounce {
  0%, 20%, 50%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-8px);
  }
  60% {
    transform: translateY(-4px);
  }
}
</style>
</head>
<body>
<div>
  <div class="spinner"></div>
</div>

</body>
</html>
