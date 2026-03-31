<?php
require_once __DIR__ . '/../DB/Database.php';

class ProdutoModel {
    private $conn;
    private $table_name = "produtos";

    public $id;
    public $nome;
    public $preco;
    public $descricao;
    public $quantidade;
    public $reservado;
    public $id_cat;
    public $foto;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConexao();
    }

    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET nome=:nome, preco=:preco, descricao=:descricao, 
                     quantidade=:quantidade, reservado=:reservado, id_cat=:id_cat, foto=:foto";

        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->preco = htmlspecialchars(strip_tags($this->preco));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->quantidade = htmlspecialchars(strip_tags($this->quantidade));
        $this->reservado = htmlspecialchars(strip_tags($this->reservado));
        $this->id_cat = htmlspecialchars(strip_tags($this->id_cat));
        $this->foto = htmlspecialchars(strip_tags($this->foto));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":preco", $this->preco);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":quantidade", $this->quantidade);
        $stmt->bindParam(":reservado", $this->reservado);
        $stmt->bindParam(":id_cat", $this->id_cat);
        $stmt->bindParam(":foto", $this->foto);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar produto: " . $e->getMessage());
            throw new Exception("Erro ao criar produto: " . $e->getMessage());
        }

        return false;
    }

    public function lerUm() {
        $query = "SELECT p.*, c.nome as categoria_nome FROM " . $this->table_name . " p 
                  LEFT JOIN categoria c ON p.id_cat = c.id 
                  WHERE p.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->nome = $row['nome'];
            $this->preco = $row['preco'];
            $this->descricao = $row['descricao'];
            $this->quantidade = $row['quantidade'];
            $this->reservado = $row['reservado'];
            $this->id_cat = $row['id_cat'];
            $this->foto = $row['foto'];
        }

        return $row;
    }

    public function lerTodos() {
        $query = "SELECT p.*, c.nome as categoria_nome FROM " . $this->table_name . " p 
                LEFT JOIN categoria c ON p.id_cat = c.id 
                WHERE p.status = 'ATIVADO'
                ORDER BY p.nome";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function lerPorCategoria($id_categoria) {
        $query = "SELECT p.*, c.nome as categoria_nome FROM " . $this->table_name . " p 
                LEFT JOIN categoria c ON p.id_cat = c.id 
                WHERE p.id_cat = ? AND p.status = 'ATIVADO'
                ORDER BY p.nome";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_categoria);
        $stmt->execute();

        return $stmt;
    }

    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                 SET nome=:nome, preco=:preco, descricao=:descricao, 
                     quantidade=:quantidade, reservado=:reservado, id_cat=:id_cat 
                 WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->preco = htmlspecialchars(strip_tags($this->preco));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->quantidade = htmlspecialchars(strip_tags($this->quantidade));
        $this->reservado = htmlspecialchars(strip_tags($this->reservado));
        $this->id_cat = htmlspecialchars(strip_tags($this->id_cat));
        $this->foto = htmlspecialchars(strip_tags($this->foto));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":preco", $this->preco);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":quantidade", $this->quantidade);
        $stmt->bindParam(":reservado", $this->reservado);
        $stmt->bindParam(":id_cat", $this->id_cat);
        $stmt->bindParam(":foto", $this->foto);
        $stmt->bindParam(":id", $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar produto: " . $e->getMessage());
            throw new Exception("Erro ao atualizar produto: " . $e->getMessage());
        }
    }

    public function deletar() {
        try {
            $this->lerUm();
            
            $query = "UPDATE " . $this->table_name . " SET status = 'DESATIVADO' WHERE id = ?";
            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(1, $this->id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao desativar: " . $e->getMessage());
            throw new Exception("Erro ao desativar: " . $e->getMessage());
        }
    }

    public function atualizarEstoq_Preco() {
        $query = "UPDATE " . $this->table_name . " SET quantidade = ?, preco = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(1, $this->quantidade);
        $stmt->bindParam(2, $this->preco);
        $stmt->bindParam(3, $this->id);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar estoque: " . $e->getMessage());
            throw new Exception("Erro ao atualizar estoque: " . $e->getMessage());
        }
    }

    public function atualizarReserva($id, $reservado) {
        $query = "UPDATE " . $this->table_name . " SET reservado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $reservado = htmlspecialchars(strip_tags($reservado));
        $id = htmlspecialchars(strip_tags($id));
        
        $stmt->bindParam(1, $reservado);
        $stmt->bindParam(2, $id);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar reserva: " . $e->getMessage());
            throw new Exception("Erro ao atualizar reserva: " . $e->getMessage());
        }
    }

    public function buscarPorNome($nome) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE nome LIKE ? ORDER BY nome";
        $stmt = $this->conn->prepare($query);
        
        $nome = htmlspecialchars(strip_tags($nome));
        $param = "%$nome%";
        $stmt->bindParam(1, $param);
        $stmt->execute();

        return $stmt;
    }
}
?>