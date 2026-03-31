function abrirPopup(link, fixar = false) {
    const popup = document.getElementsByClassName('ym_popup-content')[0];
    const cont_popup = document.getElementsByClassName('ym_conteudo-popup')[0];
    const popup_overlay = document.getElementsByClassName('ym_popup-overlay')[0];
    const area_superior = document.getElementsByClassName('ym_area-superior-popup')[0];

    if (fixar){
        fixarTela();
    }

    fetch(link)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao carregar o pop-up');
            }
            return response.text();
        })
        .then(html => {
            // Limpar conteúdo anterior
            cont_popup.innerHTML = html;
            area_superior.innerHTML = `<p class="ym_icon-fechar" onclick="fecharPopup()">✖</p>`;

            // Processar scripts do conteúdo carregado
            processarScripts(cont_popup);

            popup_overlay.style.display = 'flex';
            popup.style.display = 'block';

            // Inicializar o pop-up após um pequeno delay para garantir que o DOM esteja pronto
            setTimeout(() => {
                if (typeof initializePopup === 'function') {
                    initializePopup();
                } else {
                    console.warn('Função initializePopup não encontrada');
                    // Tentar encontrar e executar scripts manualmente
                    const scripts = cont_popup.getElementsByTagName('script');
                    for (let script of scripts) {
                        if (script.src) {
                            // É um script externo, carregar dinamicamente
                            loadScript(script.src);
                        } else {
                            // Script inline, executar diretamente
                            try {
                                eval(script.innerHTML);
                            } catch (e) {
                                console.error('Erro ao executar script inline:', e);
                            }
                        }
                    }
                }
            }, 100);
        })
        .catch(error => {
            console.error('Erro ao carregar pop-up:', error);
            cont_popup.innerHTML = `<div class="ym-alert ym-alert-error">Erro ao carregar: ${error.message}</div>`;
            area_superior.innerHTML = `<p class="ym_icon-fechar" onclick="fecharPopup()">✖</p>`;
            popup_overlay.style.display = 'flex';
            popup.style.display = 'block';
        });
}

function processarScripts(container) {
    const scripts = container.querySelectorAll('script');
    scripts.forEach(script => {
        const newScript = document.createElement('script');

        if (script.src) {
            // Script externo
            newScript.src = script.src;
            newScript.onload = () => console.log('Script carregado:', script.src);
            newScript.onerror = () => console.error('Erro ao carregar script:', script.src);
        } else {
            // Script inline
            newScript.textContent = script.textContent;
        }

        // Copiar outros atributos
        Array.from(script.attributes).forEach(attr => {
            if (attr.name !== 'src' && attr.name !== 'textContent') {
                newScript.setAttribute(attr.name, attr.value);
            }
        });

        document.head.appendChild(newScript);
    });
}

function loadScript(src) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

function fecharPopup() {
    const popup_overlay = document.getElementsByClassName('ym_popup-overlay')[0];
    popup_overlay.style.display = 'none';
    liberarTela(); 
}

function fixarTela() {
    document.body.style.overflow = "hidden"; 
}

function liberarTela() {
    document.body.style.overflow = ""; 
}
