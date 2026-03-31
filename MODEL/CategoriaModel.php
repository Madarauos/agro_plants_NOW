<?php
require_once __DIR__ . '/../DB/Database.php';

class CategoriaModel {
    private $conn;
    private $table_name = "categoria";

    public $id;
    public $nome;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConexao();
    }

    public function lerTodas() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function lerUm() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->nome = $row['nome'];
        }

        return $row;
    }

    public function lerCategoriasComProdutos() {
        $query = "SELECT DISTINCT c.* FROM " . $this->table_name . " c 
                 INNER JOIN produtos p ON c.id = p.id_cat 
                 ORDER BY c.nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function lerCategoriasComServicos() {
        $query = "SELECT DISTINCT c.* FROM " . $this->table_name . " c 
                 INNER JOIN servicos s ON c.id = s.id_cat 
                 ORDER BY c.nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>