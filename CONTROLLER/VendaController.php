<?php
require_once __DIR__ . '/../MODEL/VendaModel.php';

class VendaController {
    private $venda;

    public function __construct() {
        $this->venda = new VendaModel();
    }

    public function index($filtro = null) {
        if ($filtro === null) {
            $stmt = $this->venda->lerTodos();
        } else {
            $stmt = $this->venda->lerEspecifico($filtro);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function mostrar($id) {
        try {
            $this->venda->id = $id;
            $venda = $this->venda->lerUm();

            if ($venda) {
                return $venda;
            } else {
                throw new Exception("Venda não encontrada.");
            }
        } catch (Exception $e) {
            return ["erro" => $e->getMessage()];
        }
    }

    public function criarVenda($dados) {
        try {
            error_log("Tentando criar venda com dados: " . print_r($dados, true));
            
            $this->venda->data_venda = $dados['data_venda'];
            $this->venda->id_pedido = $dados['id_pedido'];
            $this->venda->id_vendedor = $dados['id_vendedor'];
            $this->venda->id_cliente = $dados['id_cliente'];
            $this->venda->total = $dados['total'];

            $resultado = $this->venda->criar();
            error_log("Resultado da criação da venda: " . ($resultado ? 'true' : 'false'));
            
            return $resultado;
        } catch (Exception $e) {
            error_log("ERRO em criarVenda: " . $e->getMessage());
            return ["erro" => $e->getMessage()];
        }
    }

    public function deletar($id) {
        try {
            $this->venda->id = $id;
            return $this->venda->deletar();
        } catch (Exception $e) {
            return ["erro" => $e->getMessage()];
        }
    }

    /**
     * 🔁 Itens da venda
     */
    public function listarItensDaVenda($id_venda) {
        return $this->venda->getItensVenda($id_venda);
    }
}
