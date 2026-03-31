<?php
require_once __DIR__ . '/../DB/Database.php';

class CupomModel {
    private $conn;
    private $table_name = "cupom";

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
        $query = "INSERT INTO " . $this->table_name . " 
                 SET codigo=:codigo, descricao=:descricao,tipo=:tipo, valor=:valor,  data_validade=:data_validade, data_emissao=:data_emissao";
        $stmt = $this->conn->prepare($query);

        // sanitização dos dados
        $this->codigo = htmlspecialchars(strip_tags($this->codigo));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->valor = htmlspecialchars(strip_tags($this->valor));
        $this->data_validade = htmlspecialchars(strip_tags($this->data_validade));
        $this->data_emissao = htmlspecialchars(strip_tags($this->data_emissao));

        $stmt->bindParam(":codigo", $this->codigo);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":valor", $this->valor);
        $stmt->bindParam(":data_validade", $this->data_validade);
        $stmt->bindParam(":data_emissao", $this->data_emissao);

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
    public function lerTodos() {
        $query = "SELECT id, codigo, data_emissao, data_validade, valor 
                FROM " . $this->table_name .  
                " WHERE ativo = 1 
                ORDER BY data_emissao DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET nome=:nome, email=:email, telefone=:telefone, CPF=:CPF, CNPJ=:CNPJ,data_nasc=:data_nasc WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // sanitização
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->CPF = htmlspecialchars(strip_tags($this->CPF));
        $this->CNPJ = htmlspecialchars(strip_tags($this->CNPJ));
        $this->data_nasc = htmlspecialchars(strip_tags($this->data_nasc));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telefone", $this->telefone);
        $stmt->bindParam(":CPF", $this->CPF);
        $stmt->bindParam(":CNPJ", $this->CNPJ);
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
        $query = "UPDATE " . $this->table_name . " 
                SET ativo = 0 
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar cupom: " . $e->getMessage());
            throw new Exception("Erro ao deletar cupom: " . $e->getMessage());
        }
    }
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
