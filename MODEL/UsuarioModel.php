<?php
require_once __DIR__ . '/../DB/Database.php';

class UsuarioModel {
    private $conn;
    private $table_name = "usuario";

    public $id;
    public $nome;
    public $email;
    public $senha;
    public $tipo;
    public $telefone;
    public $CPF;
    public $CEP;
    public $data_nasc;
    public $foto;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConexao();
    }

    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET nome=:nome, email=:email, senha=:senha, tipo=:tipo, 
                     telefone=:telefone, CPF=:CPF, CEP=:CEP, data_nasc=:data_nasc, foto=:foto";

        $stmt = $this->conn->prepare($query);

        // Sanitização dos dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->CPF = htmlspecialchars(strip_tags($this->CPF));
        $this->CEP = htmlspecialchars(strip_tags($this->CEP));
        $this-> _nasc = htmlspecialchars(strip_tags($this->data_nasc));
        $this->foto = htmlspecialchars(strip_tags($this->foto));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":senha", $this->senha);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":telefone", $this->telefone);
        $stmt->bindParam(":CPF", $this->CPF);
        $stmt->bindParam(":CEP", $this->CEP);
        $stmt->bindParam(":data_nasc", $this->data_nasc);
        $stmt->bindParam(":foto", $this->foto);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            
            // Verificar se é violação de email ou CPF único
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'email') !== false) {
                    throw new Exception("Já existe um usuário cadastrado com este email.");
                } elseif (strpos($e->getMessage(), 'CPF') !== false) {
                    throw new Exception("Já existe um usuário cadastrado com este CPF.");
                }
            }
            
            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        }

        return false;
    }

    public function lerUm() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    public function lerUm_email() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    public function lerTodos() {
        $query = "SELECT id, nome, email, tipo, telefone, CPF, cep, data_nasc, foto,status
                  FROM " . $this->table_name . " ORDER BY nome";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function lerEspecifico($filtro) {
        $query = "SELECT id, nome, email, tipo, telefone, CPF, cep, data_nasc, foto, status 
                FROM " . $this->table_name . " 
                WHERE tipo = '$filtro' 
                ORDER BY nome";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function atualizar() {
        // query base, sem a senha
        $query = "UPDATE " . $this->table_name . " 
                 SET nome=:nome, email=:email, 
                     telefone=:telefone, CPF=:CPF, CEP=:CEP, data_nasc=:data_nasc, foto=:foto 
                 WHERE id=:id";

        // se uma nova senha foi fornecida, adicionar a query
        if (!empty($this->senha)) {
            $query = "UPDATE " . $this->table_name . " 
                     SET nome=:nome, email=:email, senha=:senha, telefone=:telefone, CPF=:CPF, CEP=:CEP, data_nasc=:data_nasc, foto=:foto 
                     WHERE id=:id";
        }

        $stmt = $this->conn->prepare($query);

        // sanitização
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->CPF = htmlspecialchars(strip_tags($this->CPF));
        $this->CEP = htmlspecialchars(strip_tags($this->CEP));
        $this->data_nasc = htmlspecialchars(strip_tags($this->data_nasc));
        $this->foto = htmlspecialchars(strip_tags($this->foto));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telefone", $this->telefone);
        $stmt->bindParam(":CPF", $this->CPF);
        $stmt->bindParam(":CEP", $this->CEP);
        $stmt->bindParam(":data_nasc", $this->data_nasc);
        $stmt->bindParam(":foto", $this->foto);
        $stmt->bindParam(":id", $this->id);

        // se uma nova senha foi fornecida, fazer bind
        if (!empty($this->senha)) {
            $stmt->bindParam(":senha", $this->senha);
        }

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

    public function atualizar_senha() {
        $query = "UPDATE " . $this->table_name . " 
                    SET senha=:senha WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->senha = htmlspecialchars(strip_tags($this->senha));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":senha", $this->senha);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            throw new Exception("Erro ao atualizar usuário: " . $e->getMessage());
        }
    }

    public function deletar() {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(1, $this->id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar usuário: " . $e->getMessage());
            throw new Exception("Erro ao deletar usuário: " . $e->getMessage());
        }
    }

    public function login($email, $senha) {
        $query = "SELECT id, nome, email, senha, tipo FROM " . $this->table_name . " 
                  WHERE email = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
    
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if(password_verify($senha,$row['senha'])){
                $this->id = $row['id'];
                $this->nome = $row['nome'];
                $this->email = $row['email'];
                $this->tipo = $row['tipo'];

                return true;
            }
        }
        
        
        return false;
    }

    public function emailExiste($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        
        $email = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(1, $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function cpfExiste($cpf) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE CPF = ?";
        $stmt = $this->conn->prepare($query);
        
        $cpf = htmlspecialchars(strip_tags($cpf));
        $stmt->bindParam(1, $cpf);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function desativar(){
        try {
        
            $query = "UPDATE " . $this->table_name . " SET status = 'DESATIVADO'" . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(1, $this->id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao desativar usuário: " . $e->getMessage());
            throw new Exception("Erro ao desativar usuário: " . $e->getMessage());
        }
    }

    public function ativar(){
        try {
        
            $query = "UPDATE " . $this->table_name . " SET status = 'ATIVADO'" . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(1, $this->id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao desativar usuário: " . $e->getMessage());
            throw new Exception("Erro ao desativar usuário: " . $e->getMessage());
        }
    }
}
?>