<?php
require_once __DIR__ . '/../DB/Database.php';

class ServicoModel {
    private $conn;
    private $table_name = "servicos";

    public $id;
    public $nome;
    public $preco;
    public $descricao;
    public $id_cat;
    public $foto;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConexao();
    }

    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET nome=:nome, preco=:preco, descricao=:descricao, id_cat=:id_cat, foto=:foto";

        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->preco = htmlspecialchars(strip_tags($this->preco));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->id_cat = htmlspecialchars(strip_tags($this->id_cat));
        $this->foto = htmlspecialchars(strip_tags($this->foto));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":preco", $this->preco);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":id_cat", $this->id_cat);
        $stmt->bindParam(":foto", $this->foto);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar serviço: " . $e->getMessage());
            throw new Exception("Erro ao criar serviço: " . $e->getMessage());
        }

        return false;
    }

    public function lerUm() {
        $query = "SELECT s.*, c.nome as categoria_nome FROM " . $this->table_name . " s 
                  LEFT JOIN categoria c ON s.id_cat = c.id 
                  WHERE s.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->nome = $row['nome'];
            $this->preco = $row['preco'];
            $this->descricao = $row['descricao'];
            $this->id_cat = $row['id_cat'];
            $this->foto = $row['foto'];
        }

        return $row;
    }

    public function lerTodos() {
        $query = "SELECT s.*, c.nome as categoria_nome FROM " . $this->table_name . " s 
                LEFT JOIN categoria c ON s.id_cat = c.id 
                WHERE s.status = 'ATIVADO'
                ORDER BY s.nome";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function lerPorCategoria($id_categoria) {
        $query = "SELECT s.*, c.nome as categoria_nome FROM " . $this->table_name . " s 
                LEFT JOIN categoria c ON s.id_cat = c.id 
                WHERE s.id_cat = ? AND s.status = 'ATIVADO'
                ORDER BY s.nome";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_categoria);
        $stmt->execute();

        return $stmt;
    }

    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                 SET nome=:nome, preco=:preco, descricao=:descricao, id_cat=:id_cat, foto=:foto 
                 WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->preco = htmlspecialchars(strip_tags($this->preco));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->id_cat = htmlspecialchars(strip_tags($this->id_cat));
        $this->foto = htmlspecialchars(strip_tags($this->foto));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":preco", $this->preco);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":id_cat", $this->id_cat);
        $stmt->bindParam(":foto", $this->foto);
        $stmt->bindParam(":id", $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar serviço: " . $e->getMessage());
            throw new Exception("Erro ao atualizar serviço: " . $e->getMessage());
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