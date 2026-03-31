<?php
require_once __DIR__ . '/../MODEL/UsuarioModel.php';

class UsuarioController {
    private $user;

    public function __construct() {
        $this->user = new UsuarioModel();
    }

    // Listar usuários com filtro opcional
    public function index($filtro = null) {
        try {
            if ($filtro && in_array($filtro, ['admin', 'vendedor'])) {
                $stmt = $this->user->lerEspecifico($filtro);
            } else {
                $stmt = $this->user->lerTodos();
            }
            
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $users;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Criar um novo usuário
   public function criar($tipo) {
    try {
        $this->user->nome = $_POST['nome'];
        $this->user->email = $_POST['email'];
        $this->user->senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $this->user->tipo = $tipo;
        $this->user->telefone = $_POST['telefone'] ?? null;
        $this->user->CPF = $_POST['CPF'];
        $this->user->data_nasc = $_POST['data_nasc'] ?? null;
        $this->user->foto = $_POST['foto'] ?? null;
        $this->user->CEP = $_POST['cep'] ?? null;

        if ($this->user->data_nasc) {
            $dataNascimento = new DateTime($this->user->data_nasc);
            $hoje = new DateTime();
            $idade = $dataNascimento->diff($hoje)->y;

            if ($idade < 18) {
                throw new Exception("Você precisa ter pelo menos 18 anos para se cadastrar.");
            }
        } else {
            throw new Exception("Data de nascimento é obrigatória.");
        }

        // Criação do usuário
        if ($this->user->criar()) {
            return true;
        } else {
            throw new Exception("Erro ao criar usuário.");
        }

    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}
    // Mostrar detalhes de um usuário
    public function mostrar($id) {
        try {
            $this->user->id = $id;
            $user = $this->user->lerUm();
            
            if ($user) {
                return $user;
            } else {
                throw new Exception("Usuário não encontrado");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function mostrar_email($email) {
        try {
            $this->user->email = $email;
            $user = $this->user->lerUm_email();
            
            if ($user) {
                return $user;
            } else {
                throw new Exception("Usuário não encontrado");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Atualizar um usuário
    public function atualizar($id) {
        try {
            $this->user->id = $id;
            
            // Receber dados do formulário
            $this->user->nome = $_POST['nome'];
            $this->user->email = $_POST['email'];
            $this->user->telefone = $_POST['telefone'] ?? null;
            $this->user->CPF = $_POST['CPF'];
            $this->user->CEP = $_POST['cep'] ?? null;
            $this->user->data_nasc = $_POST['data_nasc'] ?? null;
            $this->user->foto = $_POST['foto'] ?? null;
            
            // Se uma nova senha foi fornecida
            if (!empty($_POST['senha'])) {
                $this->user->senha = $_POST['senha'];
            }

            if ($this->user->atualizar()) {
                return True;
                exit();
            } else {
                throw new Exception("Erro ao atualizar usuário");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Deletar um usuário
    public function deletar($id) {
        try {
            $this->user->id = $id;
            
            if ($this->user->deletar()) {
                return ['success' => 'Usuário deletado com sucesso'];
            } else {
                throw new Exception("Erro ao excluir usuário");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function desativar($id) {
        try {
            $this->user->id = $id;
            
            if ($this->user->desativar()) {
                return true;
            } else {
                throw new Exception("Erro ao desativar usuário");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function ativar($id) {
        try {
            $this->user->id = $id;
            
            if ($this->user->ativar()) {
                return true;
            } else {
                throw new Exception("Erro ao desativar usuário");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Processar login
    public function login() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'];
                $senha = $_POST['senha'];
                
                if ($this->user->login($email, $senha)) {
                    session_start();
                    $_SESSION['id'] = $this->user->id;
                    $_SESSION['email'] = $this->user->email;
                    $_SESSION['tipo'] = $this->user->tipo;
                    $_SESSION['nome'] = $this->user->nome;

                    // Retornar redirecionamento com base no tipo de usuário
                    if ($_SESSION['tipo'] == 'admin') {  
                        header('Location: ../VIEW/adm/dashboard-adm.php');
                        exit;
                    } elseif ($_SESSION['tipo'] == 'vendedor') {
                        header('Location: ../VIEW/vend/dashboard_vendedor.php');
                        exit;
                    }
                } else {
                    throw new Exception("Email ou senha inválidos.");
                }
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Processar logout
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        return ['redirect' => '../VIEW/paginas-iniciais/landing_page.php'];
    }

    public function alterar_senha(){
        try {
            $this->user->id = $_SESSION['id'];
            
            $this->user->senha = password_hash($_POST['nova_senha'],PASSWORD_DEFAULT);

            if ($this->user->atualizar_senha()) {
                return true;
            } else {
                throw new Exception("Erro ao atualizar usuário");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function verificar_senha($usuario,$senha){
        $email = $usuario['email'];

        if($this->user->login($email, $senha)){
            return true;
        }
        else{
            return false;
        }
    }
}
