<?php
require_once __DIR__ . '/../DB/Database.php';

class PedidoModel {
    private $conn;
    private $table_name = "pedidos";

    public $id;
    public $data_pedido;
    public $id_cliente;
    public $id_vendedor;
    public $id_cupom;
    public $status;
    public $total;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConexao();
    }

    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET data_pedido=:data_pedido, id_cliente=:id_cliente, id_vendedor=:id_vendedor, 
                     id_cupom=:id_cupom, status=:status, total=:total";

        $stmt = $this->conn->prepare($query);

        $this->data_pedido = htmlspecialchars(strip_tags($this->data_pedido));
        $this->id_cliente = htmlspecialchars(strip_tags($this->id_cliente));
        $this->id_vendedor = htmlspecialchars(strip_tags($this->id_vendedor));
        $this->id_cupom = htmlspecialchars(strip_tags($this->id_cupom));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->total = htmlspecialchars(strip_tags($this->total));

        $stmt->bindParam(":data_pedido", $this->data_pedido);
        $stmt->bindParam(":id_cliente", $this->id_cliente);
        $stmt->bindParam(":id_vendedor", $this->id_vendedor);
        $stmt->bindParam(":id_cupom", $this->id_cupom);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":total", $this->total);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar pedido: " . $e->getMessage());
            throw new Exception("Erro ao criar pedido: " . $e->getMessage());
        }

        return false;
    }

    public function lerUm() {
        $query = "SELECT p.*, 
                         c.nome as cliente_nome,
                         v.nome as vendedor_nome
                  FROM " . $this->table_name . " p 
                  LEFT JOIN usuario c ON p.id_cliente = c.id 
                  LEFT JOIN usuario v ON p.id_vendedor = v.id 
                  WHERE p.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->data_pedido = $row['data_pedido'];
            $this->id_cliente = $row['id_cliente'];
            $this->id_vendedor = $row['id_vendedor'];
            $this->id_cupom = $row['id_cupom'];
            $this->status = $row['status'];
            $this->total = $row['total'];
        }

        return $row;
    }

    public function lerTodos() {
        $query = "SELECT p.*, 
                         c.nome as cliente_nome,
                         v.nome as vendedor_nome
                  FROM " . $this->table_name . " p 
                  LEFT JOIN usuario c ON p.id_cliente = c.id 
                  LEFT JOIN usuario v ON p.id_vendedor = v.id 
                  ORDER BY p.data_pedido DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function lerPorCliente($id_cliente) {
        $query = "SELECT p.*, 
                         c.nome as cliente_nome,
                         v.nome as vendedor_nome
                  FROM " . $this->table_name . " p 
                  LEFT JOIN usuario c ON p.id_cliente = c.id 
                  LEFT JOIN usuario v ON p.id_vendedor = v.id 
                  WHERE p.id_cliente = ? 
                  ORDER BY p.data_pedido DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_cliente);
        $stmt->execute();

        return $stmt;
    }

    public function lerPorVendedor($id_vendedor) {
        $query = "SELECT p.*, 
                         c.nome as cliente_nome,
                         v.nome as vendedor_nome
                  FROM " . $this->table_name . " p 
                  LEFT JOIN usuario c ON p.id_cliente = c.id 
                  LEFT JOIN usuario v ON p.id_vendedor = v.id 
                  WHERE p.id_vendedor = ? 
                  ORDER BY p.data_pedido DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_vendedor);
        $stmt->execute();

        return $stmt;
    }

    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                 SET data_pedido=:data_pedido, id_cliente=:id_cliente, id_vendedor=:id_vendedor, 
                     id_cupom=:id_cupom, status=:status, total=:total 
                 WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->data_pedido = htmlspecialchars(strip_tags($this->data_pedido));
        $this->id_cliente = htmlspecialchars(strip_tags($this->id_cliente));
        $this->id_vendedor = htmlspecialchars(strip_tags($this->id_vendedor));
        $this->id_cupom = htmlspecialchars(strip_tags($this->id_cupom));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->total = htmlspecialchars(strip_tags($this->total));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":data_pedido", $this->data_pedido);
        $stmt->bindParam(":id_cliente", $this->id_cliente);
        $stmt->bindParam(":id_vendedor", $this->id_vendedor);
        $stmt->bindParam(":id_cupom", $this->id_cupom);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":total", $this->total);
        $stmt->bindParam(":id", $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar pedido: " . $e->getMessage());
            throw new Exception("Erro ao atualizar pedido: " . $e->getMessage());
        }
    }

    public function atualizarStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $status);
        $stmt->bindParam(2, $id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar status do pedido: " . $e->getMessage());
            throw new Exception("Erro ao atualizar status do pedido: " . $e->getMessage());
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
            error_log("Erro ao deletar pedido: " . $e->getMessage());
            throw new Exception("Erro ao deletar pedido: " . $e->getMessage());
        }
    }

    public function criarSemCupom() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET data_pedido=:data_pedido, id_cliente=:id_cliente, id_vendedor=:id_vendedor, 
                    status=:status, total=:total";

        $stmt = $this->conn->prepare($query);

        $this->data_pedido = htmlspecialchars(strip_tags($this->data_pedido));
        $this->id_cliente = htmlspecialchars(strip_tags($this->id_cliente));
        $this->id_vendedor = htmlspecialchars(strip_tags($this->id_vendedor));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->total = htmlspecialchars(strip_tags($this->total));

        $stmt->bindParam(":data_pedido", $this->data_pedido);
        $stmt->bindParam(":id_cliente", $this->id_cliente);
        $stmt->bindParam(":id_vendedor", $this->id_vendedor);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":total", $this->total);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar pedido: " . $e->getMessage());
            throw new Exception("Erro ao criar pedido: " . $e->getMessage());
        }

        return false;
    }
}
?>