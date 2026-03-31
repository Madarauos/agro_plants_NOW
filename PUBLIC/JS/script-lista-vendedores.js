function abreviarNome(nomeCompleto) {
    const partes = nomeCompleto.split(' ');
    if (partes.length <= 3) {
        return nomeCompleto;
    }
    // Mantém os 3 primeiros nomes e adiciona "..."
    return partes.slice(0, 3).join(' ') + '...';
}

function toggleDropdown(btn) {
    const dropdown = btn.nextElementSibling;
    const isVisible = dropdown.style.display === "block";

    document.querySelectorAll(".jv_dropdown").forEach(d => d.style.display = "none");

    if (!isVisible) {
        dropdown.style.display = "block";
    }
}

document.addEventListener("click", e => {
    if (!e.target.closest(".jv_menu-btn") && !e.target.closest(".jv_dropdown")) {
        document.querySelectorAll(".jv_dropdown").forEach(d => d.style.display = "none");
    }
});

// PESQUISA
formatarData = (dataStr) => {
    const [ano, mes, dia] = dataStr.split('-');
    return `${dia.padStart(2,'0')}/${mes.padStart(2,'0')}/${ano}`;
}

function GerarTabela(){
    tabela = document.getElementById("jv_customerTableBody");
    html="";
    limite = 4;
    const url = new URLSearchParams(window.location.search);
    if (url.has('pagina')) {
        pagina = url.get('pagina');
    } else {
        pagina = 1;
    }
    total_pag = Math.ceil(dados.length/limite);
    area_pags = document.getElementsByClassName('jv_page-navigation')[0];
    if(pagina != 1){
        html+=` <a href="?pagina=${pagina-1}" class="jv_page-arrow"><i class="fas fa-arrow-left"></i></a>`;
    }

    for (let i = 1; i <= total_pag; i++) {    
        if(i == pagina){
            html+=`<a href='?pagina=${i}' class='jv_page-number active'>${i}</a>`;            
        }else{
            html+=`<a href='?pagina=${i}' class='jv_page-number'>${i}</a>`;
        }
    }

    if(pagina != total_pag){
        html+=` <a href="?pagina=${parseInt(pagina, 10)+1}" class="jv_page-arrow"><i class="fas fa-arrow-right"></i></a>`;
    }

    area_pags.innerHTML=html;
    html="";

    usuarios = dados.slice(((pagina-1)*4), (pagina*limite));

    if(usuarios.length > 0){
        usuarios.forEach(usuario => {
            // Definir foto de perfil
            const fotoPath = usuario['foto'] && usuario['foto'] !== '' ? 
                `../../PUBLIC/img/${usuario['foto']}` : 
                '../../PUBLIC/img/default_user.jpg';
            
            const iniciais = usuario['nome'].substring(0, 2).toUpperCase();

            // html += `<tr><td>
            //             <input type="checkbox" class="jv_checkbox customer-checkbox" data-customer-id="${usuario['id']}">
            //         </td>`;
                    
            html += `<td>
                <div class="jv_customer-info">
                    <div class="jv_avatar-container">
                        ${usuario['foto'] && usuario['foto'] !== '' ? 
                            `<img src="${fotoPath}" alt="${usuario['nome']}" class="jv_avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="jv_avatar-fallback" style="display:none;">${iniciais}</div>` :
                            `<div class="jv_avatar-fallback">${iniciais}</div>`
                        }
                    </div>
                    <div class="jv_customer-details">
                        <h4>${abreviarNome(usuario['nome'])}</h4>
                        <p>${usuario['email']}</p>
                    </div>
                </div>
            </td>`;
            html += `<td>${usuario['telefone']}</td>`;
            html += `<td>${formatarData(usuario['data_nasc'])}</td>`;
            html += `<td>${usuario['status']}</td>`;
            html += `<td class="jv_table-action">
                        <button class="jv_menu-btn" onclick="toggleDropdown(this)">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <form class="jv_dropdown" method="GET" action="">
                            <button type="submit" name="visualizar" value="${usuario['id']}" class="jv_dropdown-item">
                                <i class="fas fa-eye"></i> Visualizar
                            </button>
                            <div class="jv_dropdown-separator"></div>`;
            
            if(usuario['status'] == "ATIVADO"){
                html += `<button type="button" onclick="abrirPopup('../../VIEW/pop-up/pop-up_remover.php?id=${usuario['id']}')" class="jv_dropdown-item jv_danger">
                    <i class="fa-solid fa-ban"></i> Desativar
                </button>
                </form>
            </td>
        </tr>`;
            } else {
                html += `<button type="button" onclick="abrirPopup('../../VIEW/pop-up/pop-up_remover.php?id=${usuario['id']}')" class="jv_dropdown-item jv_acess">
                    <i class="fa-solid fa-power-off"></i> Ativar
                </button>
                </form>
            </td>
        </tr>`;
            }
        });
    }else{
        html += '<tr><td class="ym_td" colspan="6">Nenhum vendedor encontrado</td></tr>';
    }

    tabela.innerHTML = html;
    const contador = document.getElementById("jv_customerCount");
    contador.textContent = ` ${dados.length} vendedores encontrados`;
}

function Pesquisar(){
    inputPesquisa = document.getElementById("jv_searchInput");
    pesquisa = inputPesquisa.value;
    if(pesquisa == ""){
        GerarTabela();
        return null;
    }

    info_tabela = document.getElementById("jv_customerTableBody");
    info_tabela.innerHTML = '';
    html="";
    dados_filtrado=[];



    dados.forEach(dado => {
        if (dado["nome"].toLowerCase().includes(pesquisa.toLowerCase()) || 
            dado["email"].toLowerCase().includes(pesquisa.toLowerCase())) {
            dados_filtrado.push(dado);
        }
    });

    area_pags = document.getElementsByClassName('jv_page-navigation')[0];
    area_pags.innerHTML="";
    
    if(dados_filtrado.length > 0){
        dados_filtrado.forEach(usuario => {
            const fotoPath = usuario['foto'] && usuario['foto'] !== '' ? 
                `../../PUBLIC/img/${usuario['foto']}` : 
                '../../PUBLIC/img/default_user.jpg';
            
            const iniciais = usuario['nome'].substring(0, 2).toUpperCase();
            
            html += `<tr><td>
                <div class="jv_customer-info">
                    <div class="jv_avatar-container">
                        ${usuario['foto'] && usuario['foto'] !== '' ? 
                            `<img src="${fotoPath}" alt="${usuario['nome']}" class="jv_avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                             <div class="jv_avatar-fallback" style="display:none;">${iniciais}</div>` :
                            `<div class="jv_avatar-fallback">${iniciais}</div>`
                        }
                    </div>
                    <div class="jv_customer-details">
                        <h4>${abreviarNome(usuario['nome'])}</h4>
                        <p>${usuario['email']}</p>
                    </div>
                </div>
            </td>`;
            html += `<td>${usuario['telefone']}</td>`;
            html += `<td>${formatarData(usuario['data_nasc'])}</td>`;
            html += `<td>${usuario['status']}</td>`;
            html += `<td class="jv_table-action">
                        <button class="jv_menu-btn" onclick="toggleDropdown(this)">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <form class="jv_dropdown" method="GET" action="">
                            <button type="submit" name="visualizar" value="${usuario['id']}" class="jv_dropdown-item">
                                <i class="fas fa-eye"></i> Visualizar
                            </button>
                            <div class="jv_dropdown-separator"></div>`;
            
            if(usuario['status'] == "ATIVADO"){
                html += `<button type="button" onclick="abrirPopup('../../VIEW/pop-up/pop-up_remover.php?id=${usuario['id']}')" class="jv_dropdown-item jv_danger">
                    <i class="fa-solid fa-ban"></i> Desativar
                </button>
                </form>
            </td>
        </tr>`;
            } else {
                html += `<button type="button" onclick="abrirPopup('../../VIEW/pop-up/pop-up_remover.php?id=${usuario['id']}')" class="jv_dropdown-item jv_acess">
                    <i class="fa-solid fa-power-off"></i> Ativar
                </button>
                </form>
            </td>
        </tr>`;
            }
        });
    }else{
        html += '<tr><td class="ym_td" colspan="6">Nenhum vendedor encontrado</td></tr>';
    }

    info_tabela.innerHTML = html;
    const contador = document.getElementById("jv_customerCount");
    contador.textContent = ` ${dados_filtrado.length} vendedores encontrados`;
}

GerarTabela();