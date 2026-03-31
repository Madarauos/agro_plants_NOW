function carregar(page){
    const cont_popup = document.getElementsByClassName('ym_carregamento-content')[0];
    const popup_overlay = document.getElementsByClassName('ym_popup-overlay')[0];
    fetch('../../INCLUDE/loader.php')
        .then(response => response.text())
        .then(html => {
            cont_popup.innerHTML = html;
    
        setTimeout(() => {
            window.location.href = page;
        }, 1000);

        popup_overlay.style.display = 'flex';

        });
}