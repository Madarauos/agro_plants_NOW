<?php
require_once __DIR__ . '/../MODEL/ClienteModel.php';
require_once __DIR__ . '/../MODEL/PedidoModel.php';

class ClienteController {
    private $cliente;
    private $pedido;

    public function __construct() {
        $this->cliente = new ClienteModel();
        $this->pedido = new PedidoModel();
    }

    public function index() {
        try {
            $stmt = $this->cliente->lerTodosAtivos();
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $clientes;
        } catch (Exception $e) {
            $error = $e->getMessage();
            include_once __DIR__ . '/../views/error.php';
        }
    }

    public function criarCliente() {
        try {
            if(strlen($_POST['CPF/CNPJ']) == 14){
                $_POST['CNPJ'] = $_POST['CPF/CNPJ'];
                $_POST['CPF'] = null;
            } elseif (strlen($_POST['CPF/CNPJ']) == 11){
                $_POST['CPF'] = $_POST['CPF/CNPJ'];
                $_POST['CNPJ'] = null;
            }
            else{   
                $_POST['CNPJ'] = null;
                $_POST['CPF'] = null;
                throw new Exception("Preencha o campo de CPF ou CNPJ");
            }
            
            $this->cliente->nome = $_POST['nome'];
            $this->cliente->email = $_POST['email'];
            $this->cliente->telefone = $_POST['telefone'];
            $this->cliente->CPF = $_POST['CPF'];
            $this->cliente->CNPJ = $_POST['CNPJ'];
            $this->cliente->data_nasc = $_POST['data_nasc'] ?? 'null';

            $stmt = $this->cliente->criar();
            return true;

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $error;
        }
    }

    public function mostrar($id) {
        try {
            $this->cliente->id = $id;
            $cliente = $this->cliente->lerUm();
            if ($this->cliente->lerUm()) {
                return $cliente;
            } else {
                throw new Exception("Usuário não encontrado");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return $error;
        }
    }

    public function pesquisar() {
        try {
            $pesquisa = "%" . $_GET["pesquisa"] . "%";
            $this->cliente->nome = $pesquisa;
            $this->cliente->email = $pesquisa;
            
            $stmt = $this->cliente->lerTodosAtivos();
            $todosClientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $resultado = array_filter($todosClientes, function($cliente) use ($pesquisa) {
                return stripos($cliente['nome'], trim($pesquisa, '%')) !== false || 
                    stripos($cliente['email'], trim($pesquisa, '%')) !== false;
            });
            
            if (!empty($resultado)) {
                return array_values($resultado); // Reindexar o array
            } else {
                throw new Exception("Usuário não encontrado");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return $error;
        }
    }
    
    public function indexComPedidos() {
        return $this->cliente->lerTodosComUltimoPedido();
    }

    /**
     * Busca clientes com informações do último pedido
     * Inclui status do pedido e ID
     */
    public function indexComStatusPedidos() {
        try {
            $stmt = $this->cliente->lerTodosAtivos();
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Para cada cliente, buscar o último pedido
            foreach ($clientes as &$cliente) {
                $pedidoStmt = $this->pedido->lerPorCliente($cliente['id']);
                $pedidos = $pedidoStmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($pedidos)) {
                    usort($pedidos, function($a, $b) {
                        return strtotime($b['data_pedido']) - strtotime($a['data_pedido']);
                    });
                    $ultimoPedido = $pedidos[0];
                    $cliente['ultimo_pedido'] = [
                        'id' => $ultimoPedido['id'],
                        'status' => $ultimoPedido['status'],
                        'data' => $ultimoPedido['data_pedido'],
                        'total' => $ultimoPedido['total']
                    ];
                } else {
                    $cliente['ultimo_pedido'] = null;
                }
            }
            
            return $clientes;
        } catch (Exception $e) {
            error_log("Erro ao buscar clientes com pedidos: " . $e->getMessage());
            return [];
        }
    }

    public function indexPorCliente($id_cliente) {
        try {
            $stmt = $this->pedido->lerPorCliente($id_cliente);
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $pedidos;
        } catch (Exception $e) {
            error_log("Erro ao buscar pedidos do cliente: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Filtra clientes por status do último pedido
     */
    public function filtrarPorStatusPedido($status) {
        try {
            $clientes = $this->indexComStatusPedidos();
            
            if (empty($status)) {
                return $clientes;
            }
            
            return array_filter($clientes, function($cliente) use ($status) {
                return $cliente['ultimo_pedido'] !== null && 
                    $cliente['ultimo_pedido']['status'] === $status;
            });
        } catch (Exception $e) {
            error_log("Erro ao filtrar clientes: " . $e->getMessage());
            return [];
        }
    }

    public function atualizar($id) {
        try {
            $this->cliente->id = $id;
            $this->cliente->nome = $_POST['nome'];
            $this->cliente->email = $_POST['email'];
            $this->cliente->telefone = $_POST['telefone'] ?? null;
            
            if(isset($_POST['CPF'])){
                $this->cliente->CPF = $_POST['CPF'];
            }
            if(isset($_POST['CNPJ'])){
                $this->cliente->CNPJ = $_POST['CNPJ'];
            }
            $this->cliente->data_nasc = $_POST['data_nasc'] ?? null;
            
            // atualizar usuario
            if ($this->cliente->atualizar()) {
                return True;
                exit();
            } else {
                throw new Exception("Erro ao atualizar usuário");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return $error;
        }
    }

    public function deletar($id) {
        try {
            $this->cliente->id = $id;
            
            if ($this->cliente->deletar()) {
               return true;
                exit();
            } else {
                throw new Exception("Erro ao excluir cliente");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            include_once __DIR__ . '/../views/error.php';
        }
    }

    public function login() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'];
                $senha = $_POST['senha'];
                
                if ($this->cliente->login($email, $senha)) {
                    session_start();
                    $_SESSION['id'] = $this->cliente->id;
                    $_SESSION['email'] = $this->cliente->email;
                    $_SESSION['tipo'] = $this->cliente->tipo;
                    $_SESSION['nome'] = $this->cliente->nome;

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

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: ../VIEW/paginas-iniciais/landing_page.php");
        exit();
    }

    public function checarEmail() {
        $email = $_GET['email'];
        $exists = $this->cliente->emailExiste($email);
        
        header('Content-Type: application/json');
        echo json_encode(['exists' => $exists]);
    }

    public function checarCPF() {
        $cpf = $_GET['cpf'];
        $exists = $this->cliente->cpfExiste($cpf);
        
        header('Content-Type: application/json');
        echo json_encode(['exists' => $exists]);
    }

        public function desativar($id) {
        try {
            $this->cliente->id = $id;
            
            if ($this->cliente->desativar($id)) {
                return true;
            } else {
                throw new Exception("Erro ao desativar cliente");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return $error;
        }
    }

    public function ativar($id) {
        try {
            $this->cliente->id = $id;
            
            if ($this->cliente->ativar($id)) {
                return true;
            } else {
                throw new Exception("Erro ao ativar cliente");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return $error;
        }
    }
}
?>