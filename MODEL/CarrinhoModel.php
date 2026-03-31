<?php
require_once __DIR__ . '/../DB/Database.php';

class CarrinhoModel {
    private $conn;
    private $table_name = "carrinho";

    public $id;
    public $id_cliente;
    public $data_criacao;
    public $valor_total;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConexao();
    }

    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET id_cliente=:id_cliente, valor_total=:valor_total";

        $stmt = $this->conn->prepare($query);

        $this->id_cliente = htmlspecialchars(strip_tags($this->id_cliente));
        $this->valor_total = htmlspecialchars(strip_tags($this->valor_total));

        $stmt->bindParam(":id_cliente", $this->id_cliente);
        $stmt->bindParam(":valor_total", $this->valor_total);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar carrinho: " . $e->getMessage());
            throw new Exception("Erro ao criar carrinho: " . $e->getMessage());
        }

        return false;
    }

    public function lerUm() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id_cliente = $row['id_cliente'];
            $this->data_criacao = $row['data_criacao'];
            $this->valor_total = $row['valor_total'];
        }

        return $row;
    }

    public function lerPorCliente($id_cliente) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_cliente = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_cliente);
        $stmt->execute();

        return $stmt;
    }

    public function lerUmPorId($id) {
    $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt->execute();
    return $stmt;
    }

    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                 SET id_cliente=:id_cliente, valor_total=:valor_total 
                 WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->id_cliente = htmlspecialchars(strip_tags($this->id_cliente));
        $this->valor_total = htmlspecialchars(strip_tags($this->valor_total));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":id_cliente", $this->id_cliente);
        $stmt->bindParam(":valor_total", $this->valor_total);
        $stmt->bindParam(":id", $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar carrinho: " . $e->getMessage());
            throw new Exception("Erro ao atualizar carrinho: " . $e->getMessage());
        }
    }

    public function deletar() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar carrinho: " . $e->getMessage());
            throw new Exception("Erro ao deletar carrinho: " . $e->getMessage());
        }
    }

    public function calcularValorTotal($id_carrinho) {
        $query = "SELECT SUM(ci.quantidade * p.preco) as total 
                  FROM carrinho_itens ci 
                  INNER JOIN produtos p ON ci.id_produto = p.id 
                  WHERE ci.id_carrinho = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_carrinho);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function atualizarValorTotal($id_carrinho) {
        $total = $this->calcularValorTotal($id_carrinho);
        $query = "UPDATE " . $this->table_name . " SET valor_total = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $total);
        $stmt->bindParam(2, $id_carrinho);
        return $stmt->execute();
    }
}
?>