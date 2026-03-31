let currentOpenDropdown = null;

function showDropdown(event, customerId) {
    event.stopPropagation();
    const menu = document.getElementById('dropdownMenu');

    // Fecha se já estiver aberto
    if (currentOpenDropdown === customerId && menu.style.display === 'block') {
        menu.style.display = 'none';
        currentOpenDropdown = null;
        return;
    }

    // Pega posição do botão clicado
    const rect = event.currentTarget.getBoundingClientRect();
    menu.style.display = 'block';
    menu.style.left = rect.left + window.scrollX - 50 + 'px';
    menu.style.top = rect.bottom + window.scrollY + 5 + 'px';

    currentOpenDropdown = customerId;

    // Adiciona eventos de clique nos itens do menu
    menu.querySelectorAll('.dropdown-item').forEach(item => {
        item.onclick = () => {
            handleDropdownAction(item.dataset.action, customerId);
            menu.style.display = 'none';
            currentOpenDropdown = null;
        };
    });
}

// Fecha ao clicar fora
document.addEventListener('click', () => {
    const menu = document.getElementById('dropdownMenu');
    menu.style.display = 'none';
    currentOpenDropdown = null;
});

// Ações do menu
function handleDropdownAction(action, customerId) {
    let name = document.querySelector(`button[onclick*='${customerId}']`)
        .closest('tr')
        .querySelector('h4').innerText;

    switch (action) {
        case 'view':
            alert('Visualizando ' + name);
            break;
        case 'edit':
            alert('Editando ' + name);
            break;
        case 'delete':
            if (confirm('Deseja remover ' + name + '?'))
                alert(name + ' removido');
            break;
    }
}
