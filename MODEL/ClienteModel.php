<?php
require_once __DIR__ . '/../DB/Database.php';

class ClienteModel {
    private $conn;
    private $table_name = "cliente";

    public $id;
    public $nome;
    public $email;
    public $telefone;
    public $CPF;
    public $CNPJ;
    public $data_nasc;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConexao();
    }

    public function criar() {
        if( $_POST['CNPJ'] == null){
            $query = "INSERT INTO " . $this->table_name . "(nome, email, telefone, CPF, data_nasc) VALUES (:nome,:email,:telefone,:CPF, :data_nasc)";
            $this->CPF = htmlspecialchars(strip_tags($this->CPF));
        }
        elseif( $_POST['CPF'] == null){
            $query = "INSERT INTO " . $this->table_name . "(nome, email, telefone, CNPJ, data_nasc) VALUES (:nome, :email, :telefone, :CNPJ, :data_nasc)";
            $this->CNPJ = htmlspecialchars(strip_tags($this->CNPJ));

        }
        else{
            return false;
        }
        $stmt = $this->conn->prepare($query);

        // sanitização dos dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->data_nasc = htmlspecialchars(strip_tags($this->data_nasc));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telefone", $this->telefone);
        if( $_POST['CNPJ'] == null){
            $stmt->bindParam(":CPF", $this->CPF);
        }
        else{
            $stmt->bindParam(":CNPJ", $this->CNPJ);
        }
        $stmt->bindParam(":data_nasc", $this->data_nasc);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            // verificar se e violação de email ou CPF unico
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'email') !== false) {
                    throw new Exception("Já existe um usuário cadastrado com este email.");
                } elseif (strpos($e->getMessage(), 'CPF') !== false) {
                    throw new Exception("Já existe um usuário cadastrado com este CPF.");
                }
                } elseif (strpos($e->getMessage(), 'CNPJ') !== false) {
                    throw new Exception("Já existe um usuário cadastrado com este CNPJ.");
                }
            }
            
            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        

        return false;
    }

    public function lerUm() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
        // if ($row) {
        //     $this->nome = $row['nome'];
        //     $this->email = $row['email'];
        //     $this->tipo = $row['tipo'];
        //     $this->telefone = $row['telefone'];
        //     $this->CPF = $row['CPF'];
        //     $this->endereco = $row['endereco'];
        //     $this->cidade = $row['cidade'];
        //     $this->estado = $row['estado'];
        //     $this->data_nasc = $row['data_nasc'];
        //     $this->foto = $row['foto'];
            
        //     return true;
        // }

        // return false;
    }

    public function Pesquisar() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE nome LIKE :nome OR email LIKE :email ;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function lerTodos() {
        $query = "SELECT id, nome, email, telefone, CPF, CNPJ, data_nasc, status 
                FROM " . $this->table_name . " ORDER BY nome";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function atualizar() {

        if(!empty($this->CPF)){        
            $query = "UPDATE " . $this->table_name . " 
                     SET nome=:nome, email=:email, telefone=:telefone, CPF=:CPF,data_nasc=:data_nasc WHERE id=:id";
        }   else if(!empty($this->CNPJ)){
            $query = "UPDATE " . $this->table_name . " 
                     SET nome=:nome, email=:email, telefone=:telefone, CNPJ=:CNPJ,data_nasc=:data_nasc WHERE id=:id";
        }

        $stmt = $this->conn->prepare($query);

        // sanitização
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        if(!empty($this->CPF)){
            $this->CPF = htmlspecialchars(strip_tags($this->CPF));
        }
        else if(!empty($this->CNPJ)){
            $this->CNPJ = htmlspecialchars(strip_tags($this->CNPJ));
        }
        $this->data_nasc = htmlspecialchars(strip_tags($this->data_nasc));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telefone", $this->telefone);
        if(!empty($this->CPF)){        
            $stmt->bindParam(":CPF", $this->CPF);
        } else if(!empty($this->CNPJ)){
            $stmt->bindParam(":CNPJ", $this->CNPJ);
        }
        $stmt->bindParam(":data_nasc", $this->data_nasc);
        $stmt->bindParam(":id", $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            
            // verificar se é violação de email ou CPF unico
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'email') !== false) {
                    throw new Exception("Já existe um usuário cadastrado com este email.");
                } elseif (strpos($e->getMessage(), 'CPF') !== false) {
                    throw new Exception("Já existe um usuário cadastrado com este CPF.");
                }
            }
            
            throw new Exception("Erro ao atualizar usuário: " . $e->getMessage());
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
            error_log("Erro ao deletar usuário: " . $e->getMessage());
            throw new Exception("Erro ao deletar usuário: " . $e->getMessage());
        }
    }
    public function lerTodosComUltimoPedido() {
        $query = "
            SELECT c.id, c.nome, c.email, c.telefone, c.CPF, c.CNPJ, c.data_nasc,
                (SELECT p.status 
                    FROM pedidos p 
                    WHERE p.id_cliente = c.id 
                    ORDER BY p.data_pedido DESC 
                    LIMIT 1) AS status
            FROM cliente c
            ORDER BY c.nome
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // public function login($email, $senha) {
    //     $query = "SELECT id, nome, email, senha, tipo FROM " . $this->table_name . " 
    //               WHERE email = ? LIMIT 0,1";
        
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->bindParam(1, $email);
    //     $stmt->execute();
    
    //     if ($stmt->rowCount() == 1) {
    //         $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
    //         if ($senha === $row['senha']) {
    //             $this->id = $row['id'];
    //             $this->nome = $row['nome'];
    //             $this->email = $row['email'];
    //             $this->tipo = $row['tipo'];
                
    //             return true;
    //         }
    //     }
        
    //     return false;
    // }

    // public function emailExiste($email) {
    //     $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
    //     $stmt = $this->conn->prepare($query);
        
    //     $email = htmlspecialchars(strip_tags($email));
    //     $stmt->bindParam(1, $email);
    //     $stmt->execute();
        
    //     return $stmt->rowCount() > 0;
    // }

    // public function cpfExiste($cpf) {
    //     $query = "SELECT id FROM " . $this->table_name . " WHERE CPF = ?";
    //     $stmt = $this->conn->prepare($query);
        
    //     $cpf = htmlspecialchars(strip_tags($cpf));
    //     $stmt->bindParam(1, $cpf);
    //     $stmt->execute();
        
    //     return $stmt->rowCount() > 0;
    // }
    public function desativar($id) {
        $query = "UPDATE " . $this->table_name . " SET status = 'DESATIVADO' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao desativar cliente: " . $e->getMessage());
            throw new Exception("Erro ao desativar cliente: " . $e->getMessage());
        }
    }

    public function ativar($id) {
        $query = "UPDATE " . $this->table_name . " SET status = 'ATIVADO' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao ativar cliente: " . $e->getMessage());
            throw new Exception("Erro ao ativar cliente: " . $e->getMessage());
        }
    }

    public function lerTodosAtivos() {
        $query = "SELECT id, nome, email, telefone, CPF, CNPJ, data_nasc, status 
                FROM " . $this->table_name . " WHERE status = 'ATIVADO' ORDER BY nome";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function pesquisarAtivos($pesquisa) {
        $query = "SELECT id, nome, email, telefone, CPF, CNPJ, data_nasc, status 
                FROM " . $this->table_name . " 
                WHERE status = 'ATIVADO' 
                AND (nome LIKE :nome OR email LIKE :email)
                ORDER BY nome";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nome", $pesquisa);
        $stmt->bindParam(":email", $pesquisa);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}