<?php
require_once __DIR__ . '/../DB/Database.php';

class MensagemeModel {
    private $conn;
    public $id;
    public $nome;
    public $email;
    public $mensagem;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Criar nova mensagem
    public function criar() {
        try {
            $sql = "INSERT INTO mensagens (nome, email, mensagem) 
                    VALUES (:nome, :email, :mensagem)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nome', $this->nome);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':mensagem', $this->mensagem);
            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Erro ao salvar mensagem: " . $e->getMensagem());
        }
    }

    // Listar todas as mensagens
    public function lerTodas() {
        try {
            $sql = "SELECT * FROM mensagens ORDER BY data_envio DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar mensagens: " . $e->getMensagem());
        }
    }

    // Ler uma mensagem específica
    public function lerUm() {
        try {
            $sql = "SELECT * FROM mensagens WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar mensagem: " . $e->getMensagem());
        }
    }

    // Deletar uma mensagem
    public function deletar() {
        try {
            $sql = "DELETE FROM mensagens WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Erro ao excluir mensagem: " . $e->getMensagem());
        }
    }
}
