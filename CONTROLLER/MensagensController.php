<?php
require_once __DIR__ . '/../MODEL/MensagensModel.php';

class MessageController {
    private $mensagem;

    public function __construct() {
        $this->mensagem = new MessageModel();
    }

    // Listar todas as mensagens enviadas ao admin
    public function index() {
        try {
            $stmt = $this->mensagem->lerTodas();
            $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Passa para a view
            include_once __DIR__ . '#';
        } catch (Exception $e) {
            $error = $e->getMessage();
            include_once __DIR__ . '/../views/error.php';
        }
    }

    // Criar uma nova mensagem
    public function criar() {
        try {
            $this->mensagem->nome = $_POST['nome'];
            $this->mensagem->email = $_POST['email'];
            $this->mensagem->mensagem = $_POST['mensagem'];

            if ($this->mensagem->criar()) {
                header("Location: /contato?success=Mensagem enviada com sucesso!");
                exit();
            } else {
                throw new Exception("Erro ao enviar mensagem.");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            include_once __DIR__ . '/../views/error.php';
        }
    }

    // Mostrar uma mensagem específica
    public function mostrar($id) {
        try {
            $this->message->id = $id;
            $msg = $this->mensagem->lerUm();

            if ($msg) {
                include_once __DIR__ . '/../views/messages/show.php';
            } else {
                throw new Exception("Mensagem não encontrada.");
            }
        } catch (Exception $e) {
            $error = $e->getMensagem();
            include_once __DIR__ . '/../views/error.php';
        }
    }

    // Deletar mensagem
    public function deletar($id) {
        try {
            $this->mensagem->id = $id;
            if ($this->mensagem->deletar()) {
                header("Location: /messages?success=Mensagem excluída");
                exit();
            } else {
                throw new Exception("Erro ao excluir mensagem.");
            }
        } catch (Exception $e) {
            $error = $e->getMensagem();
            include_once __DIR__ . '/../views/error.php';
        }
    }
}
