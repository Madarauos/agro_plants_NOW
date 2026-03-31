<?php
require_once __DIR__ . '/ProdutoController.php';
require_once __DIR__ . '/ServicoController.php';
require_once __DIR__ . '/CategoriaController.php';

class CatalogoController {
    private $produtoController;
    private $servicoController;
    private $categoriaController;

    public function __construct() {
        $this->produtoController = new ProdutoController();
        $this->servicoController = new ServicoController();
        $this->categoriaController = new CategoriaController();
    }

    public function carregarCatalogo() {
        $resultado = [];
        
        try {
            $resultado['produtos'] = $this->produtoController->index();
            $resultado['servicos'] = $this->servicoController->index();
            
            $resultado['errorProdutos'] = isset($resultado['produtos']['error']);
            $resultado['errorServicos'] = isset($resultado['servicos']['error']);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $resultado['postResult'] = $this->processarPost();
            }
            
            if (isset($_GET['remover'])) {
                $resultado['remocaoResult'] = $this->processarRemocao();
            }
            
            $resultado['success'] = true;
            
        } catch (Exception $e) {
            $resultado['success'] = false;
            $resultado['error'] = $e->getMessage();
        }
        
        return $resultado;
    }

    public function carregarCatalogoProdutos() {
        $resultado = [];
        
        try {
            $resultado['produtos'] = $this->produtoController->index();
            $resultado['categorias'] = $this->categoriaController->indexComProdutos();
            
            $resultado['errorProdutos'] = isset($resultado['produtos']['error']);
            $resultado['errorCategorias'] = isset($resultado['categorias']['error']);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $resultado['postResult'] = $this->processarPost();
            }
            
            if (isset($_GET['remover'])) {
                $resultado['remocaoResult'] = $this->processarRemocao();
            }
            
            $resultado['success'] = true;
            
        } catch (Exception $e) {
            $resultado['success'] = false;
            $resultado['error'] = $e->getMessage();
        }
        
        return $resultado;
    }

    public function carregarCatalogoServicos() {
        $resultado = [];
        
        try {
            $resultado['servicos'] = $this->servicoController->index();
            $resultado['categorias'] = $this->categoriaController->indexComServicos();
            
            $resultado['errorServicos'] = isset($resultado['servicos']['error']);
            $resultado['errorCategorias'] = isset($resultado['categorias']['error']);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $resultado['postResult'] = $this->processarPost();
            }
            
            if (isset($_GET['remover'])) {
                $resultado['remocaoResult'] = $this->processarRemocao();
            }
            
            $resultado['success'] = true;
            
        } catch (Exception $e) {
            $resultado['success'] = false;
            $resultado['error'] = $e->getMessage();
        }
        
        return $resultado;
    }
    
    private function processarPost() {
        if (isset($_POST['adicionar_servico'])) {
            return $this->servicoController->criar();
        } elseif (isset($_POST['adicionar'])) {
            return $this->produtoController->criar();
        }
        return null;
    }
    
    private function processarRemocao() {
        $id = $_GET['remover'];
        $tipo = $_GET['tipo'] ?? 'produto';
        
        if ($tipo === 'produto') {
            return $this->produtoController->deletar($id);
        } else {
            return $this->servicoController->deletar($id);
        }
    }
}
?>