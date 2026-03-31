function toggleDropdown(btn) {
    const dropdown = btn.nextElementSibling;
    const isVisible = dropdown.style.display === "block";

    // Fecha os outros menus
    document.querySelectorAll(".jv_dropdown").forEach(d => d.style.display = "none");

    if (!isVisible) dropdown.style.display = "block";
}

function formatarData(dataStr) {
    const [ano, mes, dia] = dataStr.split('-');
    return `${dia.padStart(2,'0')}/${mes.padStart(2,'0')}/${ano}`;
}

function GerarTabela() {
    const tabela = document.getElementById("jv_customerTableBody");
    let html = "";

    const hoje = new Date();
    const cuponsValidos = dados.filter(cupom =>
        new Date(cupom['data_validade']) >= hoje
    );

    const limite = 4;
    const url = new URLSearchParams(window.location.search);
    const pagina = url.has('pagina') ? parseInt(url.get('pagina'), 10) : 1;

    const total_pag = Math.ceil(cuponsValidos.length / limite);
    const area_pags = document.getElementsByClassName('jv_page-navigation')[0];
    let pagHtml = "";

    if (pagina > 1) {
        pagHtml += `<a href="?pagina=${pagina-1}" class="jv_page-arrow"><i class="fas fa-arrow-left"></i></a>`;
    }

    for (let i = 1; i <= total_pag; i++) {
        pagHtml += `<a href='?pagina=${i}' class='jv_page-number ${i === pagina ? "active" : ""}'>${i}</a>`;
    }

    if (pagina < total_pag) {
        pagHtml += `<a href="?pagina=${pagina+1}" class="jv_page-arrow"><i class="fas fa-arrow-right"></i></a>`;
    }

    area_pags.innerHTML = pagHtml;

    const cuponsPagina = cuponsValidos.slice((pagina-1)*limite, pagina*limite);

    cuponsPagina.forEach(cupom => {
        html += `<tr>`;
        html += `<td>${cupom['codigo']}</td>`;
        html += `<td>${cupom['valor']}%</td>`;
        html += `<td>${formatarData(cupom['data_emissao'])}</td>`;
        html += `<td>${formatarData(cupom['data_validade'])}</td>`;

        // Só mostra se for ADM
        if (typeof MOSTRAR_ACOES !== "undefined" && MOSTRAR_ACOES === true) {
            html += `
                <td class="jv_table-action">
                    <button class="jv_menu-btn" onclick="toggleDropdown(this)">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>

                    <form class="jv_dropdown" method="GET" action="">
                        <button type="submit" name="remover" value="${cupom['id']}" 
                                class="jv_dropdown-item jv_danger">
                            <i class="fa-solid fa-trash"></i> Remover
                        </button>
                    </form>
                </td>
            `;
        }

        html += `</tr>`;
    });

    tabela.innerHTML = html;

    const contador = document.getElementById("jv_customerCount");
    contador.textContent = `Total de Cupons: ${cuponsValidos.length}`;
}

function Pesquisar() {
    const inputPesquisa = document.getElementById("jv_searchInput");
    const pesquisa = inputPesquisa.value.toLowerCase();
    const tabela = document.getElementById("jv_customerTableBody");
    const paginacao = document.getElementsByClassName('jv_page-navigation')[0];

    // Filtra cupons válidos e pesquisa
    const hoje = new Date();
    let cuponsFiltrados = dados.filter(cupom => new Date(cupom['data_validade']) >= hoje);

    if (pesquisa !== "") {
        cuponsFiltrados = cuponsFiltrados.filter(cupom => cupom['codigo'].toLowerCase().includes(pesquisa));
    }

    // Paginação
    const limite = 4;
    const pagina = 1;
    const total_pag = Math.ceil(cuponsFiltrados.length / limite);

    let pagHtml = "";
    for (let i = 1; i <= total_pag; i++) {
        pagHtml += `<a href="#" onclick="paginaPesquisa(${i})" class='jv_page-number ${i === pagina ? 'active' : ''}'>${i}</a>`;
    }
    paginacao.innerHTML = pagHtml;

    // Mostra os cupons da primeira página
    const cuponsPagina = cuponsFiltrados.slice(0, limite);
    let html = "";
    cuponsPagina.forEach(cupom => {
        html += `<tr>`;
        html += `<td>${cupom['codigo']}</td>`;
        html += `<td>${cupom['valor']}%</td>`;
        html += `<td>${formatarData(cupom['data_emissao'])}</td>`;
        html += `<td>${formatarData(cupom['data_validade'])}</td>`;
        html += `</tr>`;
    });

    tabela.innerHTML = html;
    const contador = document.getElementById("jv_customerCount");
    contador.textContent = `Total de Cupons: ${cuponsFiltrados.length}`;
}

// Inicializa tabela ao carregar
GerarTabela();