<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../PUBLIC/css/pop-up-add-produto.css">
</head>
<body>
    <div class="eze-container">
        <div class="eze-tab-header">
            <button type="button" class="eze-tab-button eze-active" id="servico-tab">Adicionar serviço</button>
            <button type="button" class="eze-tab-button" id="imagem-tab">Imagem</button>
        </div>

        <form action="catalogo-tudo.php" method="post" enctype="multipart/form-data" class="ym_form-pop-up">
            <div id="servico-content" class="eze-form-section" style="display:block;">
                <div class="eze-form-row">
                    <div class="eze-form-group">
                        <div class="eze-form-label-group">
                            <label class="eze-label-text">Nome</label>
                            <span class="eze-required">*</span>
                        </div>
                        <input type="text" class="ym_input-padrao" name="nome" placeholder="Nome do serviço" required>
                    </div>
                </div>

                <div class="eze-form-row">
                    <div class="eze-form-group">
                        <div class="eze-form-label-group">
                            <label class="eze-label-text">Categoria</label>
                            <span class="eze-required">*</span>
                        </div>
                        <select class="ym_input-padrao" name="id_cat" required>
                            <option value="">Selecione uma categoria</option>
                            <?php
                            require_once '../../CONTROLLER/CategoriaController.php';
                            $categoriaController = new CategoriaController();
                            $categorias = $categoriaController->index();
                            if (is_array($categorias)) {
                                foreach ($categorias as $categoria) {
                                    echo '<option value="' . $categoria['id'] . '">' . htmlspecialchars($categoria['nome']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="eze-form-group">
                        <div class="eze-form-label-group">
                            <label class="eze-label-text">Preço</label>
                            <span class="eze-required">*</span>
                        </div>
                        <input type="text" class="ym_input-padrao" name="preco" placeholder="R$ 0,00" required>
                    </div>
                </div>

                <div class="eze-form-group">
                    <div class="eze-form-label-group">
                        <label class="eze-label-text">Descrição</label>
                        <span class="eze-required">*</span>
                    </div>
                    <textarea class="ym_input-padrao ym_textarea" name="descricao" placeholder="Escreva algo sobre o serviço" required></textarea>
                </div>
            </div>

            <div id="imagem-content" class="eze-form-section" style="display:none;">
                <input type="file" id="imageInputServico" name="foto" accept="image/*" style="display: none;">
                
                <div class="eze-image-placeholder ym_input-padrao" id="imagePreviewServico">
                    <div class="eze-placeholder-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14,2 14,8 20,8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10,9 9,9 8,9"></polyline>
                        </svg>
                    </div>
                    <span>Clique para adicionar imagem</span>
                </div>

                <div class="eze-button-container">
                    <button type="button" class="eze-add-button eze-add-button2 eze-add-imagem">Selecionar Imagem</button>
                </div>
            </div>

            <div class="eze-button-container eze-button-container2">
                <button type="submit" class="eze-add-button" name="adicionar_servico">Adicionar serviço</button>
                <p class="eze-help-text"><span class="eze-required">*</span> Campos obrigatórios</p>
            </div>
        </form>
    </div>
    <script src="../../PUBLIC/JS/script-select.js"></script>
    <script src="../../PUBLIC/JS/pop-up-add-prod-serv.js"></script>
    <script>
        function switchTab(tabId, contentId) {
            document.querySelectorAll('.eze-tab-button').forEach(t => t.classList.remove('eze-active'));
            document.querySelectorAll('.eze-form-section').forEach(c => c.style.display = 'none');

            document.getElementById(tabId).classList.add('eze-active');
            document.getElementById(contentId).style.display = 'block';
        }
        window.initializePopup = function() {
        var servicoTab = document.getElementById('servico-tab');
        if (servicoTab) {
            servicoTab.addEventListener('click', function() {
                switchTab('servico-tab', 'servico-content');
            });
        }

        var imagemTab = document.getElementById('imagem-tab');
        if (imagemTab) {
            imagemTab.addEventListener('click', function() {
                switchTab('imagem-tab', 'imagem-content');
            });
        }

        var imageInputServico = document.getElementById('imageInputServico');
        var imagePreviewServico = document.getElementById('imagePreviewServico');
        var addImageBtnServico = document.querySelector('.eze-add-imagem');

        if (imagePreviewServico && imageInputServico) {
            imagePreviewServico.addEventListener('click', function() {
                imageInputServico.click();
            });
        }

        if (addImageBtnServico && imageInputServico) {
            addImageBtnServico.addEventListener('click', function() {
                imageInputServico.click();
            });
        }

        if (imageInputServico && imagePreviewServico) {
            imageInputServico.addEventListener('change', function(e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(evt) {
                        imagePreviewServico.innerHTML =
                            '<img src="' + evt.target.result + '" style="max-width:100%;max-height:200px;">' +
                            '<span style="display:block;margin-top:10px;">Clique para trocar</span>';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
        };
    </script>
</body>
</html>