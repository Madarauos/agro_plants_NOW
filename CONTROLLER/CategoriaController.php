<?php
require_once __DIR__ . '/../MODEL/CategoriaModel.php';

class CategoriaController {
    private $categoria;

    public function __construct() {
        $this->categoria = new CategoriaModel();
    }

    public function index() {
        try {
            $stmt = $this->categoria->lerTodas();
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $categorias;
        } catch (Exception $e) {
            $error = $e->getMessage();
            return ['error' => $error];
        }
    }

    public function mostrar($id) {
        try {
            $this->categoria->id = $id;
            $categoria = $this->categoria->lerUm();
            
            if ($categoria) {
                return $categoria;
            } else {
                throw new Exception("Categoria não encontrada");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return ['error' => $error];
        }
    }

    public function indexComProdutos() {
        try {
            $stmt = $this->categoria->lerCategoriasComProdutos();
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $categorias;
        } catch (Exception $e) {
            $error = $e->getMessage();
            return ['error' => $error];
        }
    }

    public function indexComServicos() {
        try {
            $stmt = $this->categoria->lerCategoriasComServicos();
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $categorias;
        } catch (Exception $e) {
            $error = $e->getMessage();
            return ['error' => $error];
        }
    }
}
?>