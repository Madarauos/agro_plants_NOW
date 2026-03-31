<?php
require_once __DIR__ . '/../DB/Database.php';

class PedidoItensModel {
    private $conn;
    private $table_name = "pedido_itens";

    public $id;
    public $id_pedido;
    public $id_produto;
    public $quantidade;
    public $preco_unitario;
    public $total_item;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConexao();
    }

    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET id_pedido=:id_pedido, id_produto=:id_produto, quantidade=:quantidade, 
                     preco_unitario=:preco_unitario, total_item=:total_item";

        $stmt = $this->conn->prepare($query);

        $this->id_pedido = htmlspecialchars(strip_tags($this->id_pedido));
        $this->id_produto = htmlspecialchars(strip_tags($this->id_produto));
        $this->quantidade = htmlspecialchars(strip_tags($this->quantidade));
        $this->preco_unitario = htmlspecialchars(strip_tags($this->preco_unitario));
        $this->total_item = htmlspecialchars(strip_tags($this->total_item));

        $stmt->bindParam(":id_pedido", $this->id_pedido);
        $stmt->bindParam(":id_produto", $this->id_produto);
        $stmt->bindParam(":quantidade", $this->quantidade);
        $stmt->bindParam(":preco_unitario", $this->preco_unitario);
        $stmt->bindParam(":total_item", $this->total_item);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar item do pedido: " . $e->getMessage());
            throw new Exception("Erro ao criar item do pedido: " . $e->getMessage());
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
            $this->id_pedido = $row['id_pedido'];
            $this->id_produto = $row['id_produto'];
            $this->quantidade = $row['quantidade'];
            $this->preco_unitario = $row['preco_unitario'];
            $this->total_item = $row['total_item'];
        }

        return $row;
    }

    public function lerTodosPorPedido($id_pedido) {
        $query = "SELECT pi.*, p.nome as produto_nome, p.foto as produto_foto
                  FROM " . $this->table_name . " pi 
                  INNER JOIN produtos p ON pi.id_produto = p.id 
                  WHERE pi.id_pedido = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_pedido);
        $stmt->execute();

        return $stmt;
    }

    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                 SET quantidade=:quantidade, preco_unitario=:preco_unitario, total_item=:total_item 
                 WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->quantidade = htmlspecialchars(strip_tags($this->quantidade));
        $this->preco_unitario = htmlspecialchars(strip_tags($this->preco_unitario));
        $this->total_item = htmlspecialchars(strip_tags($this->total_item));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":quantidade", $this->quantidade);
        $stmt->bindParam(":preco_unitario", $this->preco_unitario);
        $stmt->bindParam(":total_item", $this->total_item);
        $stmt->bindParam(":id", $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar item do pedido: " . $e->getMessage());
            throw new Exception("Erro ao atualizar item do pedido: " . $e->getMessage());
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
            error_log("Erro ao deletar item do pedido: " . $e->getMessage());
            throw new Exception("Erro ao deletar item do pedido: " . $e->getMessage());
        }
    }

    public function deletarPorPedido($id_pedido) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_pedido = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_pedido);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar itens do pedido: " . $e->getMessage());
            throw new Exception("Erro ao deletar itens do pedido: " . $e->getMessage());
        }
    }
}
?>