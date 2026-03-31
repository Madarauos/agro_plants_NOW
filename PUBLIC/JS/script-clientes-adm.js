// ==============================
// DROPDOWN DOS 3 PONTINHOS
// ==============================
function toggleDropdown(btn) {
    const dropdown = btn.nextElementSibling;
    const isVisible = dropdown.style.display === "block";

    document.querySelectorAll(".jv_dropdown").forEach(d => d.style.display = "none");

    dropdown.style.display = isVisible ? "none" : "block";
}

document.addEventListener("click", (e) => {
    if (!e.target.closest(".jv_menu-btn") && !e.target.closest(".jv_dropdown")) {
        document.querySelectorAll(".jv_dropdown").forEach(d => d.style.display = "none");
    }
});


// ==============================
// SELECT PERSONALIZADO (STATUS)
// ==============================
function inicializarCustomSelect() {
    const customSelect = document.getElementById("customSelect");
    const nativeSelect = document.getElementById("nativeSelect");

    if (!customSelect || !nativeSelect) return;

    const trigger = customSelect.querySelector(".select-trigger");
    const optionsBox = customSelect.querySelector(".select-options");
    const valueSpan = customSelect.querySelector(".select-value");
    const options = customSelect.querySelectorAll(".select-option");

    // Abrir/fechar select (IMPEDIR FECHAR IMEDIATO)
    trigger.addEventListener("click", (e) => {
        e.stopPropagation(); // <-- ESSENCIAL
        trigger.classList.toggle("active");
        optionsBox.classList.toggle("active");
    });

    // Selecionar opção
    options.forEach(opt => {
        opt.addEventListener("click", () => {
            options.forEach(o => o.classList.remove("selected"));
            opt.classList.add("selected");

            const value = opt.dataset.value;
            valueSpan.textContent = opt.textContent;

            nativeSelect.value = value;

            trigger.classList.remove("active");
            optionsBox.classList.remove("active");

            atualizarURL(value);
        });
    });

    // Fechar ao clicar fora
    document.addEventListener("click", (e) => {
        if (!customSelect.contains(e.target)) {
            trigger.classList.remove("active");
            optionsBox.classList.remove("active");
        }
    });

    // Select nativo (mobile)
    nativeSelect.addEventListener("change", function () {
        atualizarURL(this.value);
    });
}


// ==============================
// ATUALIZA A URL
// ==============================
function atualizarURL(value) {
    const url = new URL(window.location.href);

    if (value === "") url.searchParams.delete("status");
    else url.searchParams.set("status", value);

    url.searchParams.delete("pagina");

    window.location.href = url.toString();
}


// ==============================
// FILTRO DINÂMICO (nome/email)
// ==============================
function inicializarFiltroClientes() {
    const input = document.getElementById("jv_searchInput");
    const tabela = document.getElementById("jv_customerTableBody");

    if (!input || !tabela) return;

    input.addEventListener("input", function () {
        const termo = this.value.toLowerCase().trim();
        const linhas = tabela.querySelectorAll("tr");

        let count = 0;

        linhas.forEach(linha => {
            const nome = linha.querySelector(".jv_customer-details h4")?.textContent.toLowerCase() || "";
            const email = linha.querySelector(".jv_customer-details p")?.textContent.toLowerCase() || "";

            const match = nome.includes(termo) || email.includes(termo);

            linha.style.display = match ? "" : "none";

            if (match) count++;
        });

        const contador = document.getElementById("jv_customerCount");
        if (contador) contador.textContent = `${count} clientes encontrados`;
    });
}


// ==============================
// INICIALIZA TUDO
// ==============================
document.addEventListener("DOMContentLoaded", () => {
    inicializarCustomSelect();
    inicializarFiltroClientes();
});
