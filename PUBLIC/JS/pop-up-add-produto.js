function switchTab(activeTabId, activeContentId) {
    console.log('Alternando para aba:', activeTabId);
    
    document.querySelectorAll('.eze-tab-button').forEach(btn => {
        btn.classList.remove('eze-active');
    });
    
    document.querySelectorAll('.eze-form-section').forEach(section => {
        section.classList.remove('active');
        section.style.display = 'none';
    });
    
    const activeTab = document.getElementById(activeTabId);
    const activeContent = document.getElementById(activeContentId);
    
    if (activeTab) {
        activeTab.classList.add('eze-active');
    }
    
    if (activeContent) {
        activeContent.classList.add('active');
        activeContent.style.display = 'block';
    }
}

function initializePopup() {
    console.log('Inicializando pop-up de produto...');
    
    const clienteTab = document.getElementById('cliente-tab');
    const documentoTab = document.getElementById('documento-tab');
    
    if (clienteTab) {
        clienteTab.onclick = function(e) {
            e.preventDefault();
            console.log('Clicou na aba Cliente');
            switchTab('cliente-tab', 'cliente-content');
        };
    }
    
    if (documentoTab) {
        documentoTab.onclick = function(e) {
            e.preventDefault();
            console.log('Clicou na aba Imagem');
            switchTab('documento-tab', 'documento-content');
        };
    }

    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const addImageBtn = document.querySelector('.eze-add-documento');

    if (imagePreview && imageInput) {
        imagePreview.onclick = function() {
            imageInput.click();
        };
    }

    if (addImageBtn && imageInput) {
        addImageBtn.onclick = function() {
            imageInput.click();
        };
    }

    if (imageInput) {
        imageInput.onchange = function(e) {
            const file = e.target.files[0];
            if (file && imagePreview) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 200px; object-fit: contain; border-radius: 8px;">
                        <span style="margin-top: 10px; display: block;">Clique para trocar a imagem</span>
                    `;
                    imagePreview.style.cursor = 'pointer';
                };
                reader.readAsDataURL(file);
            }
        };
    }

    const priceInput = document.querySelector('input[name="preco"]');
    if (priceInput) {
        priceInput.oninput = function(e) {
            formatarPreco(e.target);
        };

        priceInput.onfocus = function(e) {
            let valor = e.target.value.replace(/\D/g, '');
            e.target.value = valor;
        };

        priceInput.onblur = function(e) {
            formatarPreco(e.target);
        };
    }

    const form = document.querySelector('.ym_form-pop-up');
    if (form) {
        form.onsubmit = function(e) {
            let priceInput = document.querySelector('input[name="preco"]');
            if (priceInput && priceInput.value) {
                let rawValue = priceInput.value.replace('R$', '')
                                            .replace(/\./g, '')
                                            .replace(',', '.');
                priceInput.value = parseFloat(rawValue).toFixed(2);
            }
            
            const imageInput = document.getElementById('imageInput');
            if (imageInput && imageInput.files.length === 0) {
                alert('Por favor, selecione uma imagem para o produto.');
                e.preventDefault();
                return false;
            }
            return true;
        };
    }
    
    switchTab('cliente-tab', 'cliente-content');
    
    console.log('Pop-up inicializado com sucesso');
}

function formatarPreco(input) {
    let valor = input.value.replace(/\D/g, '');
    
    if (valor.length === 0) {
        input.value = '';
        return;
    }
    
    while (valor.length < 3) {
        valor = '0' + valor;
    }
    
    const inteiros = valor.slice(0, -2) || '0';
    const centavos = valor.slice(-2);
    
    let inteirosFormatados = inteiros.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    
    input.value = `R$ ${inteirosFormatados},${centavos}`;
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePopup);
} else {
    initializePopup();
}

window.initializePopup = initializePopup;