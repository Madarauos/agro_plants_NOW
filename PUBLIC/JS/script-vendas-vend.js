function toggleDropdown(btn) {
    const dropdown = btn.nextElementSibling;
    const isVisible = dropdown.style.display === "block";

    // Fecha todos os outros dropdowns
    document.querySelectorAll(".jv_dropdown").forEach(d => d.style.display = "none");

    // Abre apenas o clicado
    if (!isVisible) {
        dropdown.style.display = "block";
    }
}

// Fecha ao clicar fora
document.addEventListener("click", e => {
    if (!e.target.closest(".jv_menu-btn") && !e.target.closest(".jv_dropdown")) {
        document.querySelectorAll(".jv_dropdown").forEach(d => d.style.display = "none");
    }
});

function formatarData(dataStr) {
    const [ano, mes, dia] = dataStr.split('-');
    return `${dia.padStart(2, '0')}/${mes.padStart(2, '0')}/${ano}`;
}

function GerarTabela() {
    const tabela = document.getElementById("jv_customerTableBody");
    let html = "";
    const limite = 4;

    const url = new URLSearchParams(window.location.search);
    const pagina = url.has('pagina') ? parseInt(url.get('pagina')) : 1;

    const total_pag = Math.ceil(dados.length / limite);
    const area_pags = document.getElementsByClassName('jv_page-navigation')[0];

    // Montar paginação
    let html_pag = "";
    if (pagina > 1) {
        html_pag += ` <a href="?pagina=${pagina - 1}" class="jv_page-arrow"><i class="fas fa-arrow-left"></i></a>`;
    }

    for (let i = 1; i <= total_pag; i++) {
        html_pag += `<a href='?pagina=${i}' class='jv_page-number ${i === pagina ? "active" : ""}'>${i}</a>`;
    }

    if (pagina < total_pag) {
        html_pag += ` <a href="?pagina=${pagina + 1}" class="jv_page-arrow"><i class="fas fa-arrow-right"></i></a>`;
    }

    area_pags.innerHTML = html_pag;

    const vendas = dados.slice((pagina - 1) * limite, pagina * limite);

    vendas.forEach(venda => {
        const iniciais = venda['nome_cliente']
            ? venda['nome_cliente'].substring(0, 2).toUpperCase()
            : 'CL';

        html += `
        <tr>
            <td>
                <div class="jv_customer-info">
                    <div class="jv_avatar">${iniciais}</div>
                    <div class="jv_customer-details">
                        <h4>Venda #${venda['id']}</h4>
                    </div>
                </div>
            </td>
            <td>${venda['data_venda'] || '-'}</td>
            <td>${venda['nome_cliente'] || '-'}</td>
            <td>R$ ${parseFloat(venda['total']).toFixed(2).replace('.', ',')}</td>
            <td class="jv_table-action">
                <button class="jv_menu-btn" onclick="toggleDropdown(this)">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
                <div class="jv_dropdown">
                    <a href="venda-info-vend.php?id=${venda['id']}" class="jv_dropdown-item">
                        <i class="fas fa-eye"></i> Visualizar
                    </a>
                    <div class="jv_dropdown-separator"></div>
                    <button type="button" class="jv_dropdown-item jv_danger" onclick="confirmarRemocao(${venda['id']})">
                        <i class="fas fa-trash"></i> Remover
                    </button>
                </div>
            </td>
        </tr>`;
    });

    tabela.innerHTML = html;

    // ✅ Contador total de vendas (sem limitar a 4)
    document.getElementById('jv_customerCount').textContent =
        `${dados.length} ${dados.length === 1 ? 'venda encontrada' : 'vendas encontradas'}`;
}

function Pesquisar() {
    const inputPesquisa = document.getElementById("jv_searchInput");
    const pesquisa = inputPesquisa.value.trim().toLowerCase();

    const info_tabela = document.getElementById("jv_customerTableBody");
    let html = "";

    if (pesquisa === "") {
        GerarTabela();
        return;
    }

    const dados_filtrado = dados.filter(dado => {
        const vendaId = `Venda #${dado['id']}`.toLowerCase();
        const nomeCliente = dado["nome_cliente"] ? dado["nome_cliente"].toLowerCase() : "";
        return vendaId.includes(pesquisa) || nomeCliente.includes(pesquisa);
    });

    document.getElementsByClassName('jv_page-navigation')[0].innerHTML = "";

    dados_filtrado.forEach(venda => {
        const iniciais = venda['nome_cliente']
            ? venda['nome_cliente'].substring(0, 2).toUpperCase()
            : 'CL';

        html += `
        <tr>
            <td>
                <div class="jv_customer-info">
                    <div class="jv_avatar">${iniciais}</div>
                    <div class="jv_customer-details">
                        <h4>Venda #${venda['id']}</h4>
                    </div>
                </div>
            </td>
            <td>${venda['data_venda'] || '-'}</td>
            <td>${venda['nome_cliente'] || '-'}</td>
            <td>R$ ${parseFloat(venda['total']).toFixed(2).replace('.', ',')}</td>
            <td class="jv_table-action">
                <button class="jv_menu-btn" onclick="toggleDropdown(this)">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
                <div class="jv_dropdown">
                    <a href="venda-info-vend.php?id=${venda['id']}" class="jv_dropdown-item">
                        <i class="fas fa-eye"></i> Visualizar
                    </a>
                    <div class="jv_dropdown-separator"></div>
                    <button type="button" class="jv_dropdown-item jv_danger" onclick="confirmarRemocao(${venda['id']})">
                        <i class="fas fa-trash"></i> Remover
                    </button>
                </div>
            </td>
        </tr>`;
    });

    info_tabela.innerHTML = html;

    // Atualiza o contador durante a pesquisa
    document.getElementById('jv_customerCount').textContent =
        `${dados_filtrado.length} ${dados_filtrado.length === 1 ? 'venda encontrada' : 'vendas encontradas'}`;
}

function confirmarRemocao(id) {
    if (confirm('Tem certeza que deseja remover esta venda?')) {
        window.location.href = `?remover=${id}`;
    }
}

// Inicializar tabela
document.addEventListener('DOMContentLoaded', GerarTabela);
