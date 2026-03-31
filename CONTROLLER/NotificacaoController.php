<?php
require_once __DIR__ . '/../MODEL/NotificacaoModel.php';

class NotificacaoController {
    private $notificacao;

    public function __construct() {
        $this->notificacao = new NotificacaoModel();
    }

    public function criarNotificacaoEstoque($produtoNome, $quantidadeAtual) {
        try {
            $this->notificacao->titulo = "Estoque Baixo - " . $produtoNome;
            $this->notificacao->assunto = "O produto '{$produtoNome}' está com estoque baixo. Quantidade atual: {$quantidadeAtual} unidades.";

            if ($this->notificacao->criar()) {
                return ['success' => 'Notificação de estoque criada com sucesso'];
            } else {
                throw new Exception("Erro ao criar notificação de estoque");
            }
        } catch (Exception $e) {
            error_log("Erro NotificacaoController: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function criarNotificacaoContato($nome, $email, $mensagem) {
        try {
            $this->notificacao->titulo = "Novo Contato - " . $nome;
            $this->notificacao->assunto = "Novo email de contato:\n\nDe: {$nome} ({$email})\n\nMensagem:\n{$mensagem}";

            if ($this->notificacao->criar()) {
                return ['success' => 'Notificação de contato criada com sucesso'];
            } else {
                throw new Exception("Erro ao criar notificação de contato");
            }
        } catch (Exception $e) {
            error_log("Erro NotificacaoController: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function listarNotificacoes($limit = 0) {
        try {
            $stmt = $this->notificacao->lerTodas($limit);
            $notificacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $notificacoes;
        } catch (Exception $e) {
            error_log("Erro ao listar notificações: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function deletarNotificacao($id) {
        try {
            if ($this->notificacao->deletar($id)) {
                return ['success' => 'Notificação deletada com sucesso'];
            } else {
                throw new Exception("Erro ao deletar notificação");
            }
        } catch (Exception $e) {
            error_log("Erro ao deletar notificação: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function contarNotificacoes() {
        try {
            
            $notificacoes = $this->listarNotificacoes(); 
            return is_array($notificacoes) ? count($notificacoes) : 0;
        } catch (Exception $e) {
            error_log("Erro ao contar notificações: " . $e->getMessage());
            return 0;
        }
    }

    public function pesquisar() {
        try {
            $pesquisa = "%" . $_POST["pesquisa"] . "%";
            $this->notificacao->titulo = $pesquisa;
            $this->notificacao->assunto = $pesquisa;
            $resultado = $this->notificacao->Pesquisar();
            if ($this->notificacao->Pesquisar()) {
                return $resultado;
            } else {
                throw new Exception("Nenhuma notificação encontrada");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return $error;
        }
    }



}
?>