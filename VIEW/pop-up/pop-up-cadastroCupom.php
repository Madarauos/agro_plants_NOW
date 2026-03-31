<!DOCTYPE html>
<html lang="ptbr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cupom</title>
    <link rel="stylesheet" href="../../PUBLIC/css/pop-up-cadastroCupom.css">
    <link rel="stylesheet" href="../../PUBLIC/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="eze-container">
        <div class="eze-tab-header">
            <button class="eze-tab-button eze-active" id="cliente-tab">Cadastrar Cupom</button>
        </div>

        <form action="" method="post" class="ym_form-pop-up">
            <div id="cliente-content" class="eze-form-section active">
                <div class="eze-form-row">
                    <div class="eze-form-group">
                        <div class="eze-form-label-group">
                            <label class="eze-label-text">Desconto</label>
                            <span class="eze-required">*</span>
                        </div>
                        <input type="number" class="ym_input-padrao" name="valor" placeholder="Valor do Desconto" required>
                    </div>
                </div>

                <!-- <div class="eze-form-row">
                    <div class="eze-form-group">
                        <div class="eze-form-label-group">
                            <label class="eze-label-text">Email</label>
                            <span class="eze-required">*</span>
                        </div>
                        <input type="email" class="ym_input-padrao" name="email" placeholder="Email" required>
                    </div>
                </div> -->

                <div class="eze-form-row">
                    <div class="eze-form-group">
                        <div class="eze-form-label-group">
                            <label class="eze-label-text">Data de Validade</label>
                            <span class="eze-required">*</span>
                        </div>
                        <input type="date" class="ym_input-padrao" name="data_validade" required>
                    </div>

                    <div class="eze-form-group">
                        <div class="eze-form-label-group">
                            <label class="eze-label-text" id="labelCodigo">Código</label>
                            <span class="eze-required" id="spanCodigo">*</span>
                        </div>

                        <button type="button" id="btnGerarCodigo" class="eze-add-button" style="cursor:pointer;">
                            Gerar código
                        </button>
                        <input type="text" id="codigoExibido" class="ym_input-padrao" readonly style="display:none;">
                        <input type="hidden" name="codigo" id="inputCodigo" value="">
                    </div>


                </div>

                <div class="eze-form-row">
                    <div class="eze-form-group">
                        <div class="eze-form-label-group">
                            <label class="eze-label-text">Descrição</label>
                            <span class="eze-required">*</span>
                        </div>
                        <textarea style="resize: none;" class="ym_input-padrao" name="descricao" placeholder="Descrição" required></textarea>
                    </div>
                </div>



                <div class="eze-button-container">
                    <button type="submit" class="eze-add-button" name="adicionar">Cadastrar Cupom</button>
                    <p class="eze-help-text"><span class="eze-required">*</span>Campos obrigatórios</p>
                </div>
            </div>

        </form>
    </div>

    <script>
        function gerarCodigo(tamanho = 8) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let codigo = '';
            for (let i = 0; i < tamanho; i++) {
                codigo += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return codigo;
        }

        document.getElementById('btnGerarCodigo').addEventListener('click', () => {
            const codigoGerado = gerarCodigo(8);

            document.getElementById('btnGerarCodigo').style.display = 'none';

            const inputExibido = document.getElementById('codigoExibido');
            inputExibido.value = codigoGerado;
            inputExibido.style.display = 'block';

            document.getElementById('inputCodigo').value = codigoGerado;

            const label = document.getElementById('labelCodigo');
            const span = document.getElementById('spanCodigo');

            const textoOriginalLabel = label.textContent;
            const textoOriginalSpan = span.textContent;

            label.textContent = "Código gerado!";
            label.style.color = "green";

            span.innerHTML = '<i class="fa-regular fa-circle-check"></i>';
            span.style.color = "green";

            setTimeout(() => {
                label.textContent = textoOriginalLabel;
                label.style.color = "";

                span.textContent = textoOriginalSpan;
                span.style.color = "";
            }, 2000);
        });

    </script>


    <script>
        function switchTab(activeTabId, activeContentId) {
            document.querySelectorAll('.eze-tab-button').forEach(btn => {
                btn.classList.remove('eze-active');
            });
            
            document.querySelectorAll('.eze-form-section').forEach(section => {
                section.classList.remove('active');
            });
            
            document.getElementById(activeTabId).classList.add('eze-active');
            document.getElementById(activeContentId).classList.add('active');
        }

        document.getElementById('cliente-tab').addEventListener('click', () => {
            switchTab('cliente-tab', 'cliente-content');
        });

    </script>
</body>
</html>
