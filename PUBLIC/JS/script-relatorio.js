const tabButtons = document.querySelectorAll(".po-tab-btn[data-tab]");
const tabContents = document.querySelectorAll(".tab-content");

function showTab(tabName) {
tabContents.forEach(c => {
    c.style.display = (c.id === `${tabName}-tab-content`) ? "block" : "none";
});

tabButtons.forEach(btn => {
    btn.classList.toggle("po-active", btn.dataset.tab === tabName);
});
}

parametros = new URLSearchParams(window.location.search);

if(parametros.has("area")){
    showTab(parametros.get("area"));
}else{
    showTab("sales");
}




function GerarTabela_comissao(){
    tabela = document.getElementsByClassName("ym_tabela-comissao")[0];
    html="";
    
    limite = 4;
    
    const url = new URLSearchParams(window.location.search);
    if (url.has('pagina')) {
        pagina = url.get('pagina');;
    } else {
        pagina = 1;
    }
    
    total_pag = Math.ceil(dados_comissoes.length/limite);
    area_pags = document.getElementsByClassName('jv_page-navigation')[1];
    
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

    if(pagina != total_pag & total_pag > 1){
        html+=` <a href="?pagina=${parseInt(pagina, 10)+1}" class="jv_page-arrow"><i class="fas fa-arrow-right"></i></a>`;
    }
    

    area_pags.innerHTML=html;
    html="";

    vendas = dados_vendas.slice(((pagina-1)*4), (pagina*limite));    
    comissoes = dados_comissoes.slice(((pagina-1)*4), (pagina*limite));    
    
    if(dados_comissoes.length > 0){
        vendas.forEach(venda => {
        comissoes.forEach(comissao => {
            if(comissao["id_venda"] == venda["id"]){
            html += `<tr><td>${formatarData(venda['data_venda'])}</td>`
            html +=`
            <td>
                <div class="jv_customer-info">
                    <div class="jv_avatar">
                        ${venda['nome_vendedor'].substring(0, 2).toUpperCase()}
                    </div>
                    <div class="jv_customer-details">
                        <h4>${venda['nome_vendedor']}</h4>
                        <p>${venda['email_vendedor']}</p>
                    </div>
                </div>
            
            </td>`;
            html += `<td>${venda['nome_cliente']}</td>`;
            html += `<td>R$ ${venda['total']}</td>`;
            html += `<td>
                        <span class="badge-comissao">
                            ${comissao['percentual']}%
                        </span>
                    </td>`;
            html += `<td>R$ ${comissao['valor']}</td>`;
            }
        })});
    }else{
        html += '<tr><td class="ym_td" colspan="6">Nenhum resultado encontrada</td></tr>';
    }
    tabela.innerHTML = html;
}



function Pesquisar_comissao(){
    inputPesquisa = document.getElementsByClassName("jv_search-input")[1];
    pesquisa = inputPesquisa.value;
    if(pesquisa == ""){
        GerarTabela_comissao();
        return null;
    }
    info_tabela = document.getElementsByClassName("ym_tabela-comissao")[0];
    info_tabela.innerHTML = '';
    html="";
    vendas_filtrado=[];
    dados_vendas.forEach(dado => {
        if (dado["nome_vendedor"].toLowerCase().includes(pesquisa.toLowerCase()) || dado["nome_cliente"].toLowerCase().includes(pesquisa.toLowerCase())) {
            vendas_filtrado.push(dado);
        }
    });


    area_pags = document.getElementsByClassName('jv_page-navigation')[1];
    area_pags.innerHTML="";

    if(dados_comissoes.length > 0){
        vendas_filtrado.forEach(venda => {
        dados_comissoes.forEach(comissao => {
            if(comissao["id_venda"] == venda["id"]){
            html += `<tr><td>${formatarData(venda['data_venda'])}</td>`
            html +=`
            <td>
                <div class="jv_customer-info">
                    <div class="jv_avatar">
                        ${venda['nome_vendedor'].substring(0, 2).toUpperCase()}
                    </div>
                    <div class="jv_customer-details">
                        <h4>${venda['nome_vendedor']}</h4>
                        <p>${venda['email_vendedor']}</p>
                    </div>
                </div>
            
            </td>`;
            html += `<td>${venda['nome_cliente']}</td>`;
            html += `<td>R$ ${venda['total']}</td>`;
            html += `<td>
                        <span class="badge-comissao">
                            ${comissao['percentual']}%
                        </span>
                    </td>`;
            html += `<td>R$ ${comissao['valor']}</td>`;
            }
        })});
    }else{
        html += '<tr><td class="ym_td" colspan="6">Nenhum resultado encontrada</td></tr>';
    }

    info_tabela.innerHTML = html;
    
}

GerarTabela_comissao();



