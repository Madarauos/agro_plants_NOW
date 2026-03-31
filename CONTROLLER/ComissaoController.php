<?php
require_once __DIR__ . '/../MODEL/ComissaoModel.php';

class ComissaoController {
    private $comissao;

    public function __construct() {
        $this->comissao = new ComissaoModel();
    }

    
    public function index() {
        try {
            return $this->comissao->lerTodos();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

   
    public function show($id) {
        try {
            $this->comissao->id = $id;
            return $this->comissao->lerUm();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}