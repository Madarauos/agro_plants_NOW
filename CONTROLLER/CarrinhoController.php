<?php
require_once __DIR__ . '/../MODEL/CarrinhoModel.php';
require_once __DIR__ . '/../MODEL/CarrinhoItensModel.php';
require_once __DIR__ . '/../MODEL/ProdutoModel.php';
require_once __DIR__ . '/../MODEL/PedidoModel.php';
require_once __DIR__ . '/../MODEL/PedidoItensModel.php';

class CarrinhoController {
    private $carrinho;
    private $carrinhoItens;
    private $pedido;        
    private $pedidoItens;   
    private $produto;      

    public function __construct() {
        $this->carrinho = new CarrinhoModel();
        $this->carrinhoItens = new CarrinhoItensModel();
        $this->pedido = new PedidoModel();
        $this->pedidoItens = new PedidoItensModel();
        $this->produto = new ProdutoModel(); 
    }

    public function criarCarrinho($id_cliente) {
        try {
            $this->carrinho->id_cliente = $id_cliente;
            $this->carrinho->valor_total = 0;

            if ($this->carrinho->criar()) {
                return ['success' => 'Carrinho criado com sucesso', 'id' => $this->carrinho->id];
            } else {
                throw new Exception("Erro ao criar carrinho");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function obterCarrinho($id_cliente) {
        try {
            $stmt = $this->carrinho->lerPorCliente($id_cliente);
            $carrinho = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($carrinho) {
                return $carrinho;
            } else {
                $novoCarrinho = $this->criarCarrinho($id_cliente);
                if (isset($novoCarrinho['id'])) {
                    $stmt = $this->carrinho->lerUmPorId($novoCarrinho['id']);
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    throw new Exception("Erro ao criar carrinho");
                }
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function adicionarItem($id_carrinho, $id_produto, $quantidade) {
        try {
            if ($this->carrinhoItens->itemExiste($id_carrinho, $id_produto)) {
                $itemAtual = $this->carrinhoItens->lerPorCarrinhoEProduto($id_carrinho, $id_produto);
                $novaQuantidade = $itemAtual['quantidade'] + $quantidade;

                $this->carrinhoItens->id_carrinho = $id_carrinho;
                $this->carrinhoItens->id_produto = $id_produto;
                $this->carrinhoItens->quantidade = $novaQuantidade;

                if ($this->carrinhoItens->atualizarQuantidadeExata($id_carrinho, $id_produto, $novaQuantidade)) {
                    $this->carrinho->atualizarValorTotal($id_carrinho);
                    return ['success' => 'Quantidade atualizada no carrinho'];
                } else {
                    throw new Exception("Erro ao atualizar quantidade no carrinho");
                }
            } else {
                $this->carrinhoItens->id_carrinho = $id_carrinho;
                $this->carrinhoItens->id_produto = $id_produto;
                $this->carrinhoItens->quantidade = $quantidade;

                if ($this->carrinhoItens->criar()) {
                    $this->carrinho->atualizarValorTotal($id_carrinho);
                    return ['success' => 'Item adicionado ao carrinho'];
                } else {
                    throw new Exception("Erro ao adicionar item ao carrinho");
                }
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function removerItem($id_item) {
        try {
            $this->carrinhoItens->id = $id_item;
            $item = $this->carrinhoItens->lerUm();
            if ($item) {
                if ($this->carrinhoItens->deletar()) {
                    $this->carrinho->atualizarValorTotal($item['id_carrinho']);
                    return ['success' => 'Item removido do carrinho'];
                } else {
                    throw new Exception("Erro ao remover item do carrinho");
                }
            } else {
                throw new Exception("Item não encontrado");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function atualizarQuantidade($id_item, $quantidade) {
        try {
            $pdo = new PDO("mysql:host=192.168.22.9;dbname=143p2;charset=utf8", "turma143p2", "sucesso@143");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = "UPDATE carrinho_itens SET quantidade = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$quantidade, $id_item]);
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Erro ao atualizar quantidade: " . $e->getMessage());
            return false;
        }
    }
    
    public function atualizarQuantidadeItem($id_item, $quantidade) {
        try {
            $this->carrinhoItens->id = $id_item;
            $this->carrinhoItens->quantidade = $quantidade;

            if ($this->carrinhoItens->atualizar()) {
                $item = $this->carrinhoItens->lerUm();
                $this->carrinho->atualizarValorTotal($item['id_carrinho']);
                return ['success' => 'Quantidade atualizada'];
            } else {
                throw new Exception("Erro ao atualizar quantidade");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function listarItens($id_carrinho) {
        try {
            $stmt = $this->carrinhoItens->lerTodosPorCarrinho($id_carrinho);
            $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $itens;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function calcularValorTotal($id_carrinho) {
        try {
            $total = $this->carrinho->calcularValorTotal($id_carrinho);
            return ['valor_total' => $total];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function limparCarrinho($id_carrinho) {
        try {
            if ($this->carrinhoItens->deletarPorCarrinho($id_carrinho)) {
                $this->carrinho->atualizarValorTotal($id_carrinho);
                return ['success' => 'Carrinho limpo'];
            } else {
                throw new Exception("Erro ao limpar carrinho");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function deletarCarrinho($id_carrinho) {
        try {
            $this->carrinho->id = $id_carrinho;
            
            if ($this->carrinho->deletar()) {
                return ['success' => 'Carrinho deletado com sucesso'];
            } else {
                throw new Exception("Erro ao deletar carrinho");
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function criarPedidoDoCarrinho($id_cliente, $id_vendedor, $status = 'PAGO') {
        try {
            $carrinho = $this->obterCarrinho($id_cliente);
            
            if (!$carrinho || isset($carrinho['error'])) {
                throw new Exception("Carrinho não encontrado");
            }

            $id_carrinho = $carrinho['id'];
            
            $itens = $this->listarItens($id_carrinho);
            
            if (empty($itens) || isset($itens['error'])) {
                throw new Exception("Carrinho vazio. Adicione produtos antes de criar o pedido.");
            }

            $total = $carrinho['valor_total'];

            $this->pedido->data_pedido = date('Y-m-d H:i:s');
            $this->pedido->id_cliente = $id_cliente;
            $this->pedido->id_vendedor = $id_vendedor;
            $this->pedido->id_cupom = null;
            $this->pedido->status = $status; 
            $this->pedido->total = $total;

            if (method_exists($this->pedido, 'criarSemCupom')) {
                $criado = $this->pedido->criarSemCupom();
            } else {
                $this->pedido->id_cupom = null;
                $criado = $this->pedido->criar();
            }

            if (!$criado) {
                throw new Exception("Erro ao criar pedido");
            }

            $id_pedido = $this->pedido->id;

             foreach ($itens as $item) {
                $this->pedidoItens->id_pedido = $id_pedido;
                $this->pedidoItens->id_produto = $item['id_produto'];
                $this->pedidoItens->quantidade = $item['quantidade'];
                $this->pedidoItens->preco_unitario = $item['preco_unitario'];
                $this->pedidoItens->total_item = $item['preco_unitario'] * $item['quantidade'];

                if (!$this->pedidoItens->criar()) {
                    throw new Exception("Erro ao adicionar item ao pedido");
                }

                $this->atualizarEstoqueProduto($item['id_produto'], $item['quantidade']);
            }

            $this->limparCarrinho($id_carrinho);

            return [
                'success' => 'Pedido criado com sucesso!',
                'id_pedido' => $id_pedido,
                'total' => $total,
                'status' => $status
            ];

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function carrinhoTemItens($id_cliente) {
        try {
            $carrinho = $this->obterCarrinho($id_cliente);
            if (!$carrinho || isset($carrinho['error'])) {
                return false;
            }

            $itens = $this->listarItens($carrinho['id']);
            return !empty($itens) && !isset($itens['error']);
        } catch (Exception $e) {
            return false;
        }
    }

    private function atualizarEstoqueProduto($id_produto, $quantidade_vendida) {
        try {
            $this->produto->id = $id_produto;
            $produto = $this->produto->lerUm();
            
            if ($produto) {
                $nova_quantidade = $produto['quantidade'] - $quantidade_vendida;
                
                if ($nova_quantidade < 0) {
                    throw new Exception("Estoque insuficiente para o produto ID: " . $id_produto);
                }
                
                if (!$this->produto->atualizarEstoque($id_produto, $nova_quantidade)) {
                    throw new Exception("Erro ao atualizar estoque do produto ID: " . $id_produto);
                }
                
                if ($nova_quantidade <= 5) {
                    $this->criarNotificacaoEstoqueBaixo($produto['nome'], $nova_quantidade);
                }
                
                return true;
            } else {
                throw new Exception("Produto não encontrado ID: " . $id_produto);
            }
        } catch (Exception $e) {
            error_log("Erro ao atualizar estoque: " . $e->getMessage());
            throw $e;
        }
    }

    private function criarNotificacaoEstoqueBaixo($produtoNome, $quantidadeAtual) {
        try {
            require_once __DIR__ . '/NotificacaoController.php';
            $notificacaoCtrl = new NotificacaoController();
            $notificacaoCtrl->criarNotificacaoEstoque($produtoNome, $quantidadeAtual);
        } catch (Exception $e) {
            error_log("Erro ao criar notificação de estoque: " . $e->getMessage());
        }
    }
}
?>