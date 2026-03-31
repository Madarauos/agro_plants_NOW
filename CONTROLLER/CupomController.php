<?php
require_once __DIR__ . '/../MODEL/CupomModel.php';

class CupomController {
    private $cupom;

    public function __construct() {
        $this->cupom = new CupomModel();
    }

    // listar todos os usuarios
    public function index() {
        try {
            $stmt = $this->cupom->lerTodos();
            $cupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $cupons;

        } catch (Exception $e) {
            $error = $e->getMessage();
            echo $error;
        }
    }

    public function criarCupom() {
        try {

            $this->cupom->codigo = $_POST['codigo'];
            $this->cupom->descricao = $_POST['descricao'];
            $this->cupom->tipo = "FIXO";
            $this->cupom->valor = $_POST['valor'];
            $this->cupom->data_validade = $_POST['data_validade'];
            $this->cupom->data_emissao = date('Y-m-d');

            $stmt = $this->cupom->criar();
            return true;

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $error;
        }
    }

    public function mostrar($id) {
        try {
            $this->cupom->id = $id;
            $cupom = $this->cupom->lerUm();
            if ($this->cupom->lerUm()) {
                return $cupom;
            } else {
                throw new Exception("Usuário não encontrado");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            echo $error;
        }
    }

    public function validarCupom($codigo) {
    try {
        $stmt = $this->cupom->lerPorCodigo($codigo); // Método que busca o cupom pelo código
        $cupom = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cupom) {
            // Verificar se o cupom ainda é válido (data de validade)
            $dataHoje = date('Y-m-d');
            if ($cupom['data_validade'] >= $dataHoje) {
                return $cupom; // Retorna os dados do cupom
            } else {
                throw new Exception("O cupom está expirado.");
            }
        } else {
            throw new Exception("Cupom não encontrado.");
        }
    } catch (Exception $e) {
        return false; // Caso ocorra algum erro
    }
    }


    // public function editar($id) {
    //     try {
    //         $this->cupom->id = $id;
            
    //         if ($this->cupom->lerUm()) {
    //             include_once __DIR__ . '/../views/cupons/edit.php';
    //         } else {
    //             throw new Exception("Usuário não encontrado");
    //         }
    //     } catch (Exception $e) {
    //         $error = $e->getMessage();
    //         include_once __DIR__ . '/../views/error.php';
    //     }
    // }

    // processar atualização de usuario
    public function atualizar($id) {
        try {
            $this->cupom->id = $id;
            
            // receber dados do formulario
            $this->cupom->nome = $_POST['nome'];
            $this->cupom->email = $_POST['email'];
            $this->cupom->telefone = $_POST['telefone'] ?? null;
            $this->cupom->CPF = $_POST['CPF'];
            $this->cupom->CNPJ = $_POST['CNPJ'];
            $this->cupom->data_nasc = $_POST['data_nasc'] ?? null;
            
            // atualizar usuario
            if ($this->cupom->atualizar()) {
                header("Location: /cupons/$id?success=Usuário atualizado com sucesso");
                exit();
            } else {
                throw new Exception("Erro ao atualizar usuário");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->editar($id); // voltar para o formulario de edição com erro
        }
    }

    public function deletar($id) {
        try {
            $this->cupom->id = $id;
            
            if ($this->cupom->deletar()) {
                return true;
                exit();
            } else {
                throw new Exception("Erro ao excluir cupom");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            include_once __DIR__ . '/../views/error.php';
        }
    }

    // // mostrar formulario de login
    // public function formularioLogin() {
    //     include_once __DIR__ . '/../views/cupons/login.php';
    // }

    public function login() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'];
                $senha = $_POST['senha'];
                
                if ($this->cupom->login($email, $senha)) {
                    session_start();
                    $_SESSION['id'] = $this->cupom->id;
                    $_SESSION['email'] = $this->cupom->email;
                    $_SESSION['tipo'] = $this->cupom->tipo;
                    $_SESSION['nome'] = $this->cupom->nome;

                    // Redirecionar com base no tipo de usuário
                    if ($_SESSION['tipo'] == 'admin') {  
                        header("Location: ../VIEW/adm/dashboard-adm.php");
                        exit();
                    } elseif ($_SESSION['tipo'] == 'vendedor') {
                        header("Location: ../VIEW/vend/dashboard_vendedor.php");
                        exit();
                    }
                } else {
                    throw new Exception("Email ou senha inválidos.");
                }
            }
        } catch (Exception $e) {
            // retornar para a página de login com mensagem de erro
            header("Location: ../VIEW/paginas-iniciais/pagina-de-login.php?error=" . urlencode($e->getMessage()));
            exit();
        }
    }

    // processar logout
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: ../VIEW/paginas-iniciais/landing_page.php");
        exit();
    }

    // verificar se email ja existe (para AJAX)
    public function checarEmail() {
        $email = $_GET['email'];
        $exists = $this->cupom->emailExiste($email);
        
        header('Content-Type: application/json');
        echo json_encode(['exists' => $exists]);
    }

    // verificar se CPF ja existe (para AJAX)
    public function checarCPF() {
        $cpf = $_GET['cpf'];
        $exists = $this->cupom->cpfExiste($cpf);
        
        header('Content-Type: application/json');
        echo json_encode(['exists' => $exists]);
    }
}