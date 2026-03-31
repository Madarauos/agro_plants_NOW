function toggleDropdown(btn) {
    const dropdown = btn.nextElementSibling;
    const isVisible = dropdown.style.display === "block";

    // Fecha todos os outros
    document.querySelectorAll(".jv_dropdown").forEach(d => d.style.display = "none");

    // Abre apenas o clicado
    if (!isVisible) {
        dropdown.style.display = "block";
    }
}

formatarData = (dataStr) => {
    const [ano, mes, dia] = dataStr.split('-');
    return `${dia.padStart(2,'0')}/${mes.padStart(2,'0')}/${ano}`;
}

function GerarTabela() {
    const tabela = document.getElementById("jv_customerTableBody");
    let html = "";

    const hoje = new Date();

    // Filtra apenas cupons válidos
    const cuponsValidos = dados.filter(cupom => {
        const validade = new Date(cupom['data_validade']);
        return validade >= hoje; // cupom só é válido se a data de validade não passou
    });

    // Mostra todos os cupons válidos
    cuponsValidos.forEach(cupom => {
        html += `<tr>`;
        html += `<td>${cupom['codigo']}</td>`;
        html += `<td>${cupom['valor']}%</td>`;
        html += `<td>${formatarData(cupom['data_emissao'])}</td>`;
        html += `<td>${formatarData(cupom['data_validade'])}</td>`;
        html += `</tr>`;
    });

    tabela.innerHTML = html;
}

function Pesquisar() {
    const inputPesquisa = document.getElementById("jv_searchInput");
    const pesquisa = inputPesquisa.value.toLowerCase();
    const tabela = document.getElementById("jv_customerTableBody");

    const hoje = new Date();

    // Filtra cupons válidos
    let cuponsFiltrados = dados.filter(cupom => {
        const validade = new Date(cupom['data_validade']);
        return validade >= hoje;
    });

    // Aplica pesquisa
    if (pesquisa !== "") {
        cuponsFiltrados = cuponsFiltrados.filter(cupom =>
            cupom['codigo'].toLowerCase().includes(pesquisa)
        );
    }

    // Mostra os cupons filtrados
    let html = "";
    cuponsFiltrados.forEach(cupom => {
        html += `<tr>`;
        html += `<td>${cupom['codigo']}</td>`;
        html += `<td>${cupom['valor']}%</td>`;
        html += `<td>${formatarData(cupom['data_emissao'])}</td>`;
        html += `<td>${formatarData(cupom['data_validade'])}</td>`;
        html += `</tr>`;
    });

    tabela.innerHTML = html;
}

// Inicializa tabela ao carregar
GerarTabela();