<?php
require_once __DIR__ . '/../DB/Database.php';

class ComissaoModel {
    private $conn;
    private $table_name = "comissao";

    public $id;
    public $id_venda;
    public $percentual;
    public $valor;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConexao();
    }

    public function lerUm() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function lerTodos() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}