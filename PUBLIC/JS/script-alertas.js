function exibirAlerta(mensagem, tipo = "info") {
  const area_alerta = document.getElementsByClassName("ym_area-alertas")[0];
  const alerta = document.createElement("span");
 
  alerta.className = `ym_alerta     ${tipo}`
  alerta.textContent = mensagem
 
  area_alerta.appendChild(alerta)
 
  setTimeout(() => {
    alerta.classList.add("show")
  }, 100)
 
  setTimeout(() => {
    alerta.classList.remove("show")
    setTimeout(() => {
      if (area_alerta.contains(alerta)) {
        area_alerta.removeChild(alerta)
      }
    }, 300)
  }, 3500)
}