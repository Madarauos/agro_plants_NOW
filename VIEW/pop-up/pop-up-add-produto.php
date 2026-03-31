
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
            <button type="button" class="eze-tab-button eze-active" id="cliente-tab">Adicionar produto</button>
            <button type="button" class="eze-tab-button" id="documento-tab">Imagem</button>
        </div>

        <form action="catalogo-tudo.php" method="post" enctype="multipart/form-data" class="ym_form-pop-up">
            <div id="cliente-content" class="eze-form-section active">
                <div class="eze-form-row">
                    <div class="eze-form-group">
                        <div class="eze-form-label-group">
                            <label class="eze-label-text">Nome</label>
                            <span class="eze-required">*</span>
                        </div>
                        <input type="text" id="nomeProduto" class="ym_input-padrao" name="nome" placeholder="Nome do produto">
                   
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

                <div class="eze-form-row">
                    <div class="eze-form-group">
                        <div class="eze-form-label-group">
                            <label class="eze-label-text">Quantidade</label>
                            <span class="eze-required">*</span>
                        </div>
                        <input type="number" class="ym_input-padrao" name="quantidade" placeholder="Quantidade em estoque" required min="0">

                    </div>
                </div>

                <div class="eze-form-group">
                    <div class="eze-form-label-group">
                        <label class="eze-label-text">Descrição</label>
                        <span class="eze-required">*</span>
                    </div>
                    <textarea type="text" id="descricaoProduto" class="ym_input-padrao" name="descricao" placeholder="Escreva algo sobre o produto" required maxlength="256"></textarea>
                    <span id="contadorDescricao" class="contador-texto2">0/256</span>
                </div>
            </div>

            <div id="documento-content" class="eze-form-section">
                <input type="file" id="imageInput" name="foto" accept="image/*" style="display: none;">
                
                <div class="eze-image-placeholder ym_input-padrao" id="imagePreview">
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
                    <button type="button" class="eze-add-button eze-add-button2 eze-add-documento">Selecionar Imagem</button>
                </div>
            </div>

            <div class="eze-button-container eze-button-container2">
                <button type="submit" class="eze-add-button" name="adicionar">Adicionar produto</button>
                <p class="eze-help-text"><span class="eze-required">*</span> Campos obrigatórios</p>
            </div>
        </form>
    </div>
    
    <script src="../../PUBLIC/JS/script-select.js"></script>
    <script src="../../PUBLIC/JS/pop-up-add-produto.js"></script>

    <script>
    const inputNome = document.getElementById('nomeProduto');
    const textareaDescricao = document.getElementById('descricaoProduto');
    const contador2 = document.getElementById('contadorDescricao');
    const form = document.querySelector('.ym_form-pop-up');


    inputNome.addEventListener('input', () => {
        const semEspacos = inputNome.value.replace(/\s/g, '');
        contador.textContent = `${semEspacos.length}/256`;
    });


    textareaDescricao.addEventListener('input', () => {
        const semEspacosDesc = textareaDescricao.value.replace(/\s/g, '');
        contador2.textContent = `${semEspacosDesc.length}/256`;
    });


    form.addEventListener('submit', (e) => {
        const nomeValido = inputNome.value.trim().length > 0;
        const descricaoValida = textareaDescricao.value.trim().length > 0;

        if (!nomeValido || !descricaoValida) {
            e.preventDefault();
            alert('Os campos "Nome" e "Descrição" não podem conter apenas espaços.');
        }
    });
    </script>
</body>
</html>