<?php
require_once __DIR__ . '/../DB/Database.php';

class VendaModel {
    private $conn;
    private $table_name = "venda";

    public $id;
    public $data_venda;
    public $id_pedido;
    public $id_vendedor;
    public $id_cliente;
    public $total;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConexao();
    }

    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                 (data_venda, id_pedido, id_vendedor, id_cliente, total)
                 VALUES (:data_venda, :id_pedido, :id_vendedor, :id_cliente, :total)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":data_venda", $this->data_venda);
        $stmt->bindParam(":id_pedido", $this->id_pedido);
        $stmt->bindParam(":id_vendedor", $this->id_vendedor);
        $stmt->bindParam(":id_cliente", $this->id_cliente);
        $stmt->bindParam(":total", $this->total);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            throw new Exception("Erro ao criar venda: " . $e->getMessage());
        }

        return false;
    }

    public function lerUm() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function lerTodos() {
        $query = "SELECT " . $this->table_name . ".*,
                usuario.nome AS nome_vendedor, usuario.email AS email_vendedor, usuario.telefone AS telefone_vendedor, usuario.CPF AS CPF_vendedor, usuario.data_nasc AS data_nasc_vendedor,
                cliente.nome AS nome_cliente, cliente.email AS email_cliente, cliente.telefone AS telefone_cliente, cliente.CPF AS CPF_cliente, cliente.CNPJ AS CNPJ_cliente, cliente.data_nasc AS data_nasc_cliente  FROM venda
                inner join usuario on venda.id_vendedor = usuario.id 
                inner join cliente on venda.id_cliente = cliente.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function lerEspecifico($filtro) {
        $query = "SELECT " . $this->table_name . ".*,
                usuario.nome AS nome_vendedor, usuario.email AS email_vendedor, usuario.telefone AS telefone_vendedor, usuario.CPF AS CPF_vendedor, usuario.data_nasc AS data_nasc_vendedor,
                cliente.nome AS nome_cliente, cliente.email AS email_cliente, cliente.telefone AS telefone_cliente, cliente.CPF AS CPF_cliente, cliente.CNPJ AS CNPJ_cliente, cliente.data_nasc AS data_nasc_cliente  FROM venda
                inner join usuario on venda.id_vendedor = usuario.id 
                inner join cliente on venda.id_cliente = cliente.id" . " WHERE venda.id_vendedor = " .$filtro;        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function deletar() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        return $stmt->execute();
    }

    /**
     * 🔁 Buscar todos os itens de uma venda
     */
    public function getItensVenda($id_venda) {
        $query = "
            SELECT 
                iv.id_produto,
                iv.preco_unitario,
                iv.quantidade,
                p.nome AS nome_produto
            FROM item_venda iv
            INNER JOIN produtos p ON iv.id_produto = p.id
            WHERE iv.id_venda = :id_venda
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_venda', $id_venda, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
