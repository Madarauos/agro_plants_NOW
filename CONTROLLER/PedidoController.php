<?php
require_once __DIR__ . '/../MODEL/PedidoModel.php';
require_once __DIR__ . '/../MODEL/PedidoItensModel.php';
require_once __DIR__ . '/../MODEL/ProdutoModel.php';

class PedidoController {
    private $pedido;
    private $pedidoItens;
    private $produto;

    public function __construct() {
        $this->pedido = new PedidoModel();
        $this->pedidoItens = new PedidoItensModel();
        $this->produto = new ProdutoModel();
    }

    public function index() {
        try {
            $stmt = $this->pedido->lerTodos();
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $pedidos;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function indexPorCliente($id_cliente) {
        try {
            $stmt = $this->pedido->lerPorCliente($id_cliente);
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $pedidos;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function indexPorVendedor($id_vendedor) {
        try {
            $stmt = $this->pedido->lerPorVendedor($id_vendedor);
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $pedidos;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function criar($dadosPedido, $itensPedido) {
        try {
            $this->pedido->data_pedido = date('Y-m-d H:i:s');
            $this->pedido->id_cliente = $dadosPedido['id_cliente'];
            $this->pedido->id_vendedor = $dadosPedido['id_vendedor'];
            $this->pedido->id_cupom = $dadosPedido['id_cupom'] ?? null;
            $this->pedido->status = $dadosPedido['status'] ?? 'FINALIZADO';
            $this->pedido->total = $dadosPedido['total'];

            if ($this->pedido->criar()) {
                $id_pedido = $this->pedido->id;

                foreach ($itensPedido as $item) {
                    $this->pedidoItens->id_pedido = $id_pedido;
                    $this->pedidoItens->id_produto = $item['id_produto'];
                    $this->pedidoItens->quantidade = $item['quantidade'];
                    $this->pedidoItens->preco_unitario = $item['preco_unitario'];
                    $this->pedidoItens->total_item = $item['total_item'];

                    if (!$this->pedidoItens->criar()) {
                        throw new Exception("Erro ao adicionar item ao pedido");
                    }

                    $this->atualizarEstoqueProduto($item['id_produto'], $item['quantidade']);
                }

                return ['success' => 'Pedido criado com sucesso', 'id' => $id_pedido];
            } else {
                throw new Exception("Erro ao criar pedido");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function mostrar($id) {
        try {
            $this->pedido->id = $id;
            $pedido = $this->pedido->lerUm();
            
            if ($pedido) {
                return $pedido;
            } else {
                return ['error' => 'Pedido não encontrado'];
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function atualizar($id, $dadosPedido) {
        try {
            $this->pedido->id = $id;
            
            $this->pedido->data_pedido = $dadosPedido['data_pedido'];
            $this->pedido->id_cliente = $dadosPedido['id_cliente'];
            $this->pedido->id_vendedor = $dadosPedido['id_vendedor'];
            $this->pedido->id_cupom = $dadosPedido['id_cupom'] ?? null;
            $this->pedido->status = $dadosPedido['status'];
            $this->pedido->total = $dadosPedido['total'];

            if ($this->pedido->atualizar()) {
                return ['success' => 'Pedido atualizado com sucesso'];
            } else {
                throw new Exception("Erro ao atualizar pedido");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function atualizarStatus($id, $status) {
        try {
            if ($this->pedido->atualizarStatus($id, $status)) {
                return ['success' => 'Status do pedido atualizado com sucesso'];
            } else {
                throw new Exception("Erro ao atualizar status do pedido");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function deletar($id) {
        try {
            $this->pedidoItens->deletarPorPedido($id);
            
            $this->pedido->id = $id;
            
            if ($this->pedido->deletar()) {
                return ['success' => 'Pedido deletado com sucesso'];
            } else {
                throw new Exception("Erro ao deletar pedido");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function adicionarItem($id_pedido, $id_produto, $quantidade, $preco_unitario) {
        try {
            $total_item = $quantidade * $preco_unitario;

            $this->pedidoItens->id_pedido = $id_pedido;
            $this->pedidoItens->id_produto = $id_produto;
            $this->pedidoItens->quantidade = $quantidade;
            $this->pedidoItens->preco_unitario = $preco_unitario;
            $this->pedidoItens->total_item = $total_item;

            if ($this->pedidoItens->criar()) {
                $this->atualizarEstoqueProduto($id_produto, $quantidade);
                
                $this->recalcularTotalPedido($id_pedido);
                
                return ['success' => 'Item adicionado ao pedido'];
            } else {
                throw new Exception("Erro ao adicionar item ao pedido");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function removerItem($id_item) {
        try {
            $this->pedidoItens->id = $id_item;
            $item = $this->pedidoItens->lerUm();
            
            if ($item) {
                if ($this->pedidoItens->deletar()) {
                    $this->restaurarEstoqueProduto($item['id_produto'], $item['quantidade']);
                    
                    $this->recalcularTotalPedido($item['id_pedido']);
                    
                    return ['success' => 'Item removido do pedido'];
                } else {
                    throw new Exception("Erro ao remover item do pedido");
                }
            } else {
                throw new Exception("Item nÃ£o encontrado");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function listarItens($id_pedido) {
        try {
            $stmt = $this->pedidoItens->lerTodosPorPedido($id_pedido);
            $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $itens;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function atualizarEstoqueProduto($id_produto, $quantidade) {
        $this->produto->id = $id_produto;
        $produto = $this->produto->lerUm();
        
        if ($produto) {
            $nova_quantidade = $produto['quantidade'] - $quantidade;
            $this->produto->atualizarEstoque($id_produto, $nova_quantidade);
        }
    }

    private function restaurarEstoqueProduto($id_produto, $quantidade) {
        $this->produto->id = $id_produto;
        $produto = $this->produto->lerUm();
        
        if ($produto) {
            $nova_quantidade = $produto['quantidade'] + $quantidade;
            $this->produto->atualizarEstoque($id_produto, $nova_quantidade);
        }
    }

    private function recalcularTotalPedido($id_pedido) {
        $itens = $this->listarItens($id_pedido);
        $total = 0;
        
        foreach ($itens as $item) {
            $total += $item['total_item'];
        }
        
        $this->pedido->id = $id_pedido;
        $pedido = $this->pedido->lerUm();
        if ($pedido) {
            $this->pedido->total = $total;
            $this->pedido->atualizar();
        }
    }
}
?>