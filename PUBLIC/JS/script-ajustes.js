document.addEventListener("DOMContentLoaded", () => {
  const body = document.body;
  const tabButtons = document.querySelectorAll(".tab-btn");
  const tabContents = document.querySelectorAll(".tab-content");

  tabButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      tabButtons.forEach((b) => b.classList.remove("active"));
      tabContents.forEach((c) => c.classList.remove("active"));

      btn.classList.add("active");
      document.getElementById(btn.dataset.tab).classList.add("active");
    });
  });

  document.querySelectorAll(".content-card").forEach((card) => {
    const form = card.querySelector("form");
    const btnEdit = card.querySelector(".btn-edit");
    const btnCancel = card.querySelector(".btn-cancel");
    const btnSave = card.querySelector(".btn-save");
    const actions = card.querySelector(".form-actions");

    if (btnEdit) {
      btnEdit.addEventListener("click", () => {
        if (!form) return;
        form.querySelectorAll("input").forEach((i) => i.removeAttribute("readonly"));
        form.classList.add("editing");
        if (actions) actions.style.display = "flex";
      });
    }

    if (btnCancel) {
      btnCancel.addEventListener("click", () => {
        if (!form) return;
        form.querySelectorAll("input").forEach((i) => i.setAttribute("readonly", true));
        form.classList.remove("editing");
        if (actions) actions.style.display = "none";
      });
    }

    if (btnSave) {
      btnSave.addEventListener("click", () => {
        if (!form) return;
        form.querySelectorAll("input").forEach((i) => i.setAttribute("readonly", true));
        form.classList.remove("editing");
        if (actions) actions.style.display = "none";
        showToast("Alterações salvas com sucesso!", "success");
      });
    }
  });

  document.querySelectorAll(".toggle-password").forEach((btn) => {
    btn.addEventListener("click", () => {
      const input = btn.previousElementSibling;
      input.type = input.type === "password" ? "text" : "password";
      const icon = btn.querySelector("i");
      icon.classList.toggle("fa-eye");
      icon.classList.toggle("fa-eye-slash");
    });
  });

  const newPassword = document.getElementById("newPassword");
  const strengthBar = document.querySelector(".strength-fill");
  const strengthText = document.querySelector(".strength-text");

  if (newPassword) {
    newPassword.addEventListener("input", () => {
      const val = newPassword.value;
      let strength = 0;

      if (val.length > 6) strength++;
      if (/[A-Z]/.test(val)) strength++;
      if (/[0-9]/.test(val)) strength++;
      if (/[\W]/.test(val)) strength++;

      const levels = ["Fraca", "Média", "Boa", "Forte"];
      const colors = ["#e74c3c", "#f39c12", "#27ae60", "#2ecc71"];

      strengthBar.style.width = `${(strength / 4) * 100}%`;
      strengthBar.style.background = colors[strength - 1] || "#e74c3c";
      strengthText.textContent = levels[strength - 1] || "Fraca";
    });
  }

  // const themeOptions = document.querySelectorAll(".theme-option");
  // const savedTheme = localStorage.getItem("theme") || "dark";

  // applyTheme(savedTheme);

  // themeOptions.forEach((option) => {
  //   if (option.dataset.theme === savedTheme) {
  //     option.classList.add("active");
  //   }
  //   option.addEventListener("click", () => {
  //     const selectedTheme = option.dataset.theme;

  //     body.classList.add("theme-transitioning");
  //     setTimeout(() => {
  //       applyTheme(selectedTheme);
  //       body.classList.remove("theme-transitioning");
  //     }, 50);

  //     themeOptions.forEach((opt) => opt.classList.remove("active"));
  //     option.classList.add("active");

  //     localStorage.setItem("theme", selectedTheme);
  //     showToast(`Tema ${getThemeName(selectedTheme)} aplicado com sucesso!`, "success");
  //   });
  // });

  // function applyTheme(theme) {
  //   body.classList.remove("dark-theme", "light-theme");
  //   body.classList.add(theme === "dark" ? "dark-theme" : "light-theme");
  // }

  // function getThemeName(theme) {
  //   return theme === "dark" ? "Escuro" : "Claro";
  // }

  const modal = document.getElementById("modal-overlay");
  const modalClose = document.querySelector(".modal-close");
  const btnCancel = modal?.querySelector(".btn-cancel");
  const btnDelete = modal?.querySelector(".btn-danger");

  document.querySelectorAll("[data-action='delete']").forEach((btn) => {
    btn.addEventListener("click", () => modal.classList.add("open"));
  });

  modalClose?.addEventListener("click", () => modal.classList.remove("open"));
  btnCancel?.addEventListener("click", () => modal.classList.remove("open"));

  btnDelete?.addEventListener("click", () => {
    const confirmation = document.getElementById("delete-confirmation").value.trim();
    if (confirmation === "EXCLUIR") {
      showToast("Conta excluída com sucesso!", "success");
      modal.classList.remove("open");
    } else {
      showToast("Digite EXCLUIR para confirmar", "error");
    }
  });

  function showToast(message, type = "info") {
    const container = document.getElementById("toast-container");
    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => toast.classList.add("show"), 100);
    setTimeout(() => {
      toast.classList.remove("show");
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }
});
