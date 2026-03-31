<?php
require_once __DIR__ . '/../CONFIG/Config.php';

class DatabaseConnectionException extends RuntimeException {}

class Database {
    private ?PDO $conn = null;

    public function getConexao(): PDO {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DatabaseConfig::HOST,
                DatabaseConfig::DB_NAME,
                DatabaseConfig::CHARSET
            );

            $this->conn = new PDO($dsn, DatabaseConfig::USER, DatabaseConfig::PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DatabaseConfig::CHARSET
            ]);

        } catch (PDOException $e) {
            error_log(sprintf(
                "[%s] Erro de conexão PDO: %s",
                date('d-m-Y H:i:s'),
                $e->getMessage()
            ));
 
            throw new DatabaseConnectionException(
                "Erro ao conectar ao banco de dados.",
                0,
                $e
            );
        }

        return $this->conn;
    }
}