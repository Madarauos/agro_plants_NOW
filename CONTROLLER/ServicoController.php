<?php
require_once __DIR__ . '/../MODEL/ServicoModel.php';

class ServicoController {
    private $servico;

    public function __construct() {
        $this->servico = new ServicoModel();
    }

    public function index() {
        try {
            $stmt = $this->servico->lerTodos();
            $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $servicos;
        } catch (Exception $e) {
            $error = $e->getMessage();
            return ['error' => $error];
        }
    }

    public function indexPorCategoria($id_categoria) {
        try {
            $stmt = $this->servico->lerPorCategoria($id_categoria);
            $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $servicos;
        } catch (Exception $e) {
            $error = $e->getMessage();
            return ['error' => $error];
        }
    }

    public function buscarPorNome($nome) {
        try {
            $stmt = $this->servico->buscarPorNome($nome);
            $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $servicos;
        } catch (Exception $e) {
            $error = $e->getMessage();
            return ['error' => $error];
        }
    }

    public function criar() {
        try {
            require_once __DIR__ . '/ImageController.php';
            $imageController = new ImageController();
            
            $this->servico->nome = $_POST['nome'];
            $this->servico->preco = $this->formatarPreco($_POST['preco']);
            $this->servico->descricao = $_POST['descricao'] ?? null;
            $this->servico->id_cat = $_POST['id_cat'];

            if (!empty($_FILES['foto']['name'])) {
                $uploadResult = $imageController->upload($_FILES['foto'], 'serv_');
                if ($uploadResult['success']) {
                    $this->servico->foto = $uploadResult['filename'];
                } else {
                    throw new Exception("Erro no upload da imagem: " . $uploadResult['error']);
                }
            } else {
                $this->servico->foto = 'img_servico.webp';
            }

            if ($this->servico->criar()) {
                return ['success' => 'Serviço criado com sucesso', 'id' => $this->servico->id];
            } else {
                throw new Exception("Erro ao criar serviço");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return ['error' => $error];
        }
    }

    public function mostrar($id) {
        try {
            $this->servico->id = $id;
            $servico = $this->servico->lerUm();
            
            if ($servico) {
                return $servico;
            } else {
                throw new Exception("Serviço não encontrado");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return ['error' => $error];
        }
    }

    public function atualizar($id) {
        try {
            $this->servico->id = $id;
            
            $this->servico->nome = $_POST['nome'];
            $this->servico->preco = $_POST['preco'];
            $this->servico->descricao = $_POST['descricao'] ?? null;
            $this->servico->id_cat = $_POST['id_cat'];
            $this->servico->foto = $_POST['foto'] ?? null;

            if ($this->servico->atualizar()) {
                return ['success' => 'Serviço atualizado com sucesso'];
            } else {
                throw new Exception("Erro ao atualizar serviço");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return ['error' => $error];
        }
    }

    public function deletar($id) {
        try {
            $this->servico->id = $id;
            
            if ($this->servico->deletar()) {
                return ['success' => 'Serviço deletado com sucesso'];
            } else {
                throw new Exception("Erro ao deletar serviço");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return ['error' => $error];
        }
    }

    private function formatarPreco($preco) {
    $preco = str_replace(['R$', ' '], '', $preco);
    $preco = str_replace('.', '', $preco);
    $preco = str_replace(',', '.', $preco);
    $preco = preg_replace('/[^0-9.]/', '', $preco);

    return floatval($preco);
}
}
?>