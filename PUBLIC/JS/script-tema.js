document.addEventListener("DOMContentLoaded", () => {
  const body = document.body;
  const savedTheme = localStorage.getItem("theme") || "dark";

  applyTheme(savedTheme);

  function applyTheme(theme) {
    body.classList.remove("dark-theme", "light-theme");
    body.classList.add(theme === "dark" ? "dark-theme" : "light-theme");
  }
});

// script-tema.js
const btnTema = document.getElementById("toggleTheme");
const body = document.body;

// Carregar preferência anterior
if (localStorage.getItem("tema") === "escuro") {
  body.classList.add("dark-theme");
}

// Alternar modo
btnTema?.addEventListener("click", () => {
  body.classList.toggle("dark-theme");
  const temaAtual = body.classList.contains("dark-theme") ? "escuro" : "claro";
  localStorage.setItem("tema", temaAtual);
});
