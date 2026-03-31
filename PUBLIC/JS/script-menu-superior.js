document.addEventListener("DOMContentLoaded", () => {
  const hamburgerMenu = document.querySelector(".jp_hamburger-menu")
  const sidebar = document.querySelector(".jp_sidebar")
  const overlay = document.querySelector(".jp_overlay")
  const navLinks = document.querySelectorAll(".jp_nav-links a")
  const loginBtn = document.querySelector(".jp_login-btn")
  const body = document.body

  hamburgerMenu.addEventListener("click", (e) => {
    e.stopPropagation()
    toggleMenu()
  })

  overlay.addEventListener("click", () => {
    closeMenu()
  })

  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      closeMenu()
    })
  })

  if (loginBtn) {
    loginBtn.addEventListener("click", () => {
      closeMenu()
    })
  }

  document.addEventListener("click", (event) => {
    const isClickInsideSidebar = sidebar.contains(event.target)
    const isClickInsideHamburger = hamburgerMenu.contains(event.target)

    if (!isClickInsideSidebar && !isClickInsideHamburger && sidebar.classList.contains("active")) {
      closeMenu()
    }
  })

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && sidebar.classList.contains("active")) {
      closeMenu()
    }
  })

  window.addEventListener("resize", () => {
    if (window.innerWidth > 992 && sidebar.classList.contains("active")) {
      closeMenu()
    }
  })

  function toggleMenu() {
    const isActive = sidebar.classList.contains("active")

    if (isActive) {
      closeMenu()
    } else {
      openMenu()
    }
  }

  function openMenu() {
    hamburgerMenu.classList.add("active")
    sidebar.classList.add("active")
    overlay.classList.add("active")
    body.classList.add("menu-open")
  }

  function closeMenu() {
    hamburgerMenu.classList.remove("active")
    sidebar.classList.remove("active")
    overlay.classList.remove("active")
    body.classList.remove("menu-open")
  }
})
