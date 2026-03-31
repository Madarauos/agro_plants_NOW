const notificacao = document.getElementsByClassName('ym_area-notificacao')[0];
notificacao.addEventListener('click', () => {
    notificacao.classList.toggle('active');
    
    const icon = document.querySelector('.jp_notification-icon i');
    if (icon) {
        if (notificacao.classList.contains('active')) {
            // Popup aberto: troca sino por X
            icon.classList.remove('fa-bell');
            icon.classList.add('fa-xmark');
        } else {
            // Popup fechado: troca X por sino
            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bell');
        }
    }
});