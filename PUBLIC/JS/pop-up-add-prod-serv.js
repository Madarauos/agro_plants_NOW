document.getElementById('produto-tab').addEventListener('click', function() {
            document.getElementById('produto-content').style.display = 'block';
            document.getElementById('imagem-content').style.display = 'none';
            document.getElementById('produto-tab').classList.add('eze-active');
            document.getElementById('imagem-tab').classList.remove('eze-active');
        });
        
        document.getElementById('imagem-tab').addEventListener('click', function() {
            document.getElementById('produto-content').style.display = 'none';
            document.getElementById('imagem-content').style.display = 'block';
            document.getElementById('produto-tab').classList.remove('eze-active');
            document.getElementById('imagem-tab').classList.add('eze-active');
        });