<?php
require_once __DIR__ . '/../MODEL/ServicoModel.php';
require_once __DIR__ . '/../MODEL/CategoriaModel.php';

class SobreServicoController {
    private $servicoModel;
    private $categoriaModel;

    public function __construct() {
        $this->servicoModel = new ServicoModel();
        $this->categoriaModel = new CategoriaModel();
    }

    public function carregarServico($id) {
        try {
            if (!$id) {
                throw new Exception("Nenhum serviço selecionado.");
            }

            $this->servicoModel->id = $id;
            $servico = $this->servicoModel->lerUm();
            
            if (!$servico) {
                throw new Exception("Serviço não encontrado");
            }

            $this->categoriaModel->id = $servico['id_cat'];
            $categoria = $this->categoriaModel->lerUm();
            $categoria_nome = $categoria ? $categoria['nome'] : 'Categoria não encontrada';

            return [
                'success' => true,
                'servico' => $servico,
                'categoria_nome' => $categoria_nome
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>