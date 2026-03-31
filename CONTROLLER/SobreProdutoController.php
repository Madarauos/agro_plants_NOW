<?php
require_once __DIR__ . '/../MODEL/ProdutoModel.php';
require_once __DIR__ . '/../MODEL/CategoriaModel.php';

class SobreProdutoController {
    private $produtoModel;
    private $categoriaModel;

    public function __construct() {
        $this->produtoModel = new ProdutoModel();
        $this->categoriaModel = new CategoriaModel();
    }

    public function carregarProduto($id) {
        try {
            if (!$id) {
                throw new Exception("Nenhum produto selecionado.");
            }

            $this->produtoModel->id = $id;
            $produto = $this->produtoModel->lerUm();
            
            if (!$produto) {
                throw new Exception("Produto não encontrado");
            }

            $this->categoriaModel->id = $produto['id_cat'];
            $categoria = $this->categoriaModel->lerUm();
            $categoria_nome = $categoria ? $categoria['nome'] : 'Categoria não encontrada';

            return [
                'success' => true,
                'produto' => $produto,
                'categoria_nome' => $categoria_nome
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function atualizarProduto(){
        try {
            $this->produtoModel->id = $_GET['id'];
            $this->produtoModel->quantidade = $_POST['estoque'];
            $this->produtoModel->preco = $_POST['preco'];
            $atualizar = $this->produtoModel->atualizarEstoq_Preco(); 
            return $atualizar;
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

}
?>