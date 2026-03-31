<?php
require_once __DIR__ . '/../DB/Database.php';

class NotificacaoModel {
    private $conn;
    private $table_name = "notificacoes";

    public $id;
    public $titulo;
    public $assunto;
    public $horario_criacao;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConexao();
    }

    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " (titulo, assunto) VALUES (:titulo, :assunto)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":assunto", $this->assunto);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar notificação: " . $e->getMessage());
            throw new Exception("Erro ao criar notificação: " . $e->getMessage());
        }

        return false;
    }

    public function lerTodas($limit = 0) {
        if(!$limit == 0){
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY horario_criacao DESC LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        }else{
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY horario_criacao DESC";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();

        return $stmt;
    }

    public function deletar($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar notificação: " . $e->getMessage());
            throw new Exception("Erro ao deletar notificação: " . $e->getMessage());
        }
    }

    public function contarNaoLidas() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function Pesquisar() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE titulo LIKE :titulo OR assunto LIKE :assunto ;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":assunto", $this->assunto);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

}
?>