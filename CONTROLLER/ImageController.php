<?php
class ImageController {
    private $uploadPath;
    
    public function __construct() {
        $this->uploadPath = realpath(__DIR__ . '/../PUBLIC/img/') . '/';
        
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }
    
    public function upload($file, $prefix = '') {
        try {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Erro no upload: ' . $file['error']);
            }
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Tipo de arquivo não permitido: ' . $file['type']);
            }
            
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception('Arquivo muito grande. Tamanho máximo: 5MB');
            }
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = $prefix . uniqid() . '.' . $extension;
            $destination = $this->uploadPath . $fileName;
            
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new Exception('Falha ao mover arquivo enviado');
            }
            
            return ['success' => true, 'filename' => $fileName];
        } catch (Exception $e) {
            error_log("Erro no upload de imagem: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function delete($filename) {
        if (!empty($filename) && $filename !== 'img_produto.webp' && $filename !== 'img_servico.webp') {
            $filePath = $this->uploadPath . $filename;
            if (file_exists($filePath)) {
                return unlink($filePath);
            }
        }
        return false;
    }
}
?>