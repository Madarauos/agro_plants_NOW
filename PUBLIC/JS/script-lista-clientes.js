document.querySelector(".jp_hamburger-menu").addEventListener("click", function () {
    this.classList.toggle("active")
    document.querySelector(".jp_sidebar").classList.toggle("active")
  })
  
  const addModal = document.getElementById("addClienteModal")
  const addBtn = document.getElementById("cadastrarClienteBtn")
  const addClienteForm = document.getElementById("addClienteForm")
  
  addBtn.addEventListener("click", () => {
    addModal.style.display = "block"
  })
  
  addClienteForm.addEventListener("submit", (e) => {
    e.preventDefault()
    alert("Cliente cadastrado com sucesso!")
    addModal.style.display = "none"
    addClienteForm.reset()
  })
  
  window.addEventListener("click", (e) => {
    if (e.target === addModal) {
      addModal.style.display = "none"
    }
  })
  
  