document.getElementById('clientefisico-tab').addEventListener('click', function() {
    document.getElementById('fisico-content').style.display = 'block';
    document.getElementById('juridico-content').style.display = 'none';
    document.getElementById('clientefisico-tab').classList.add('sab-active');
    document.getElementById('juridico-tab').classList.remove('sab-active');
});

document.getElementById('juridico-tab').addEventListener('click', function() {
    document.getElementById('fisico-content').style.display = 'none';
    document.getElementById('juridico-content').style.display = 'block';
    document.getElementById('clientefisico-tab').classList.remove('sab-active');
    document.getElementById('juridico-tab').classList.add('sab-active');
});


{/* <div id="imagem-content" class="sab-form-section">
            <div class="sab-image-placeholder">
                <div class="sab-placeholder-icon">
                    <img src="../../PUBLIC/img/SVGRepo.png" alt="">
                </div>
                <span>Imagem</span>
            </div>
            
            <div class="sab-button-container">
                <button type="button" class="sab-add-button sab-add-button2 sab-add-imagem">Adicionar imagem</button>
            </div>
        </div> */}