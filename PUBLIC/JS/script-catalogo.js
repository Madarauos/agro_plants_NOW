// ==============================
// PESQUISA EM TEMPO REAL
// ==============================
function inicializarPesquisa() {
  const inputPesquisa = document.getElementById("inputPesquisa")

  if (!inputPesquisa) return

  inputPesquisa.addEventListener("input", function () {
    const termo = this.value.toLowerCase().trim()

    // Buscar em produtos e serviços
    const containers = ["produtos-container", "servicos-container"]

    containers.forEach((containerId) => {
      const container = document.getElementById(containerId)
      if (!container) return

      const cards = container.querySelectorAll(".ym_cardProduto")
      let visibleCount = 0

      cards.forEach((card) => {
        const nome = card.querySelector(".ym_nomeProduto")?.textContent.toLowerCase() || ""
        const descricao = card.querySelector(".ym_descricao")?.textContent.toLowerCase() || ""
        const categoria = card.querySelector(".ym_img-label span")?.textContent.toLowerCase() || ""

        const matches = nome.includes(termo) || descricao.includes(termo) || categoria.includes(termo)

        if (matches || termo === "") {
          card.style.display = "flex"
          visibleCount++
        } else {
          card.style.display = "none"
        }
      })

      // Ocultar/mostrar seção se não houver resultados
      const section = container.closest(".ym_categoria-section")
      if (section) {
        const titulo = section.querySelector(".ym_textoArea")
        if (visibleCount > 0 || termo === "") {
          section.style.display = "block"
        } else {
          section.style.display = "none"
        }
      }
    })
  })
}

// ==============================
// MENU DE CATEGORIAS
// ==============================
function mostrar_categorias() {
  const select = document.querySelector(".ym_select-catalogo")
  const options = document.querySelector(".ym_options")

  if (!select || !options) return

  const isOpen = options.classList.contains("show")

  if (isOpen) {
    options.classList.remove("show")
    select.classList.remove("active")
  } else {
    options.classList.add("show")
    select.classList.add("active")
  }
}

// Fechar menu ao clicar fora
document.addEventListener("click", (event) => {
  const select = document.querySelector(".ym_select-catalogo")
  const options = document.querySelector(".ym_options")

  if (!select || !options) return

  if (!select.contains(event.target) && !options.contains(event.target)) {
    options.classList.remove("show")
    select.classList.remove("active")
  }
})

// Suporte para teclado no select
document.addEventListener("keydown", (event) => {
  const select = document.querySelector(".ym_select-catalogo")
  const options = document.querySelector(".ym_options")

  if (!select || !options) return

  if (event.target === select && (event.key === "Enter" || event.key === " ")) {
    event.preventDefault()
    mostrar_categorias()
  }

  if (event.key === "Escape" && options.classList.contains("show")) {
    options.classList.remove("show")
    select.classList.remove("active")
  }
})

// ==============================

// ==============================
// INICIALIZAÇÃO
// ==============================
document.addEventListener("DOMContentLoaded", () => {
  inicializarPesquisa()

  // Garantir que o menu está oculto ao carregar
  const options = document.querySelector(".ym_options")
  if (options) {
    options.classList.remove("show")
  }

  // Adicionar animação de entrada nos cards
  const cards = document.querySelectorAll(".ym_cardProduto")
  cards.forEach((card, index) => {
    card.style.opacity = "0"
    card.style.transform = "translateY(20px)"

    setTimeout(() => {
      card.style.transition = "opacity 0.4s ease, transform 0.4s ease"
      card.style.opacity = "1"
      card.style.transform = "translateY(0)"
    }, index * 50)
  })

  // Auto-dismiss para alertas após 5 segundos
  const alertas = document.querySelectorAll(".ym-alert")
  alertas.forEach((alerta) => {
    setTimeout(() => {
      alerta.style.transition = "opacity 0.3s ease, transform 0.3s ease"
      alerta.style.opacity = "0"
      alerta.style.transform = "translateY(-10px)"

      setTimeout(() => {
        alerta.remove()
      }, 300)
    }, 5000)
  })
})

// ==============================
// UTILITÁRIOS
// ==============================

// Debounce para otimizar a pesquisa
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}