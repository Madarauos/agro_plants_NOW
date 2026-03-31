document.addEventListener('DOMContentLoaded', function() {
    const imagemPrincipal = document.querySelector('.gs_product-image');
    const miniaturas = document.querySelectorAll('.gs_product-image-select');
    
    miniaturas.forEach(miniatura => {
        miniatura.addEventListener('click', function() {
            imagemPrincipal.src = this.src;
            imagemPrincipal.alt = this.alt;
            
            miniaturas.forEach(img => img.classList.remove('active'));
            
            this.classList.add('active');
        });
    });
    
    if (miniaturas.length > 0) {
        miniaturas[0].classList.add('active');
    }
});


valores = []

function editar(){
    button = document.getElementsByClassName("ym_edit-button")[0];
    icon = button.children[0];
    values = document.getElementsByClassName("gs_value");

    if(icon.className.includes("fa-pen-to-square")){
        icon.classList.remove("fa-pen-to-square")
        icon.classList.add("fa-xmark")

        document.getElementById("ym_save-button").style.display = "flex";
        
        for (index = 1; index < values.length; index++) {
            valor = values[index];
            valores.push(valor.textContent);
            input = document.createElement("input");
            
            if(index == 1){
                string_formatada = valor.textContent.slice(3).replaceAll(",", ".");
                input.value = string_formatada;
            }else{
                input.value = valor.textContent;
            }
            input.type = 'number';
            if(index==1){
                input.name = 'preco'; 
            }else{
                input.name = 'estoque'; 
            }
            input.classList.add("gs_value", "ym_input-edit");

            valor.parentNode.replaceChild(input, valor);
        }
    }else{
        icon.classList.remove("fa-xmark")
        icon.classList.add("fa-pen-to-square")

        document.getElementById("ym_save-button").style.display = "none";

        for (index = 1; index < values.length; index++) {
            valor = values[index];
            p = document.createElement("p");
            p.textContent = valores[index-1];
            p.classList.add("gs_value");

            valor.parentNode.replaceChild(p, valor);
        }
    }
}