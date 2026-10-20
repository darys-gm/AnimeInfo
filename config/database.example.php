<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'anime_db';
    private $username = 'root';
    
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            // Membuat koneksi PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            
            // Set error mode ke exception untuk memudahkan debugging
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Set charset UTF-8
            $this->conn->exec("set names utf8");
            
        } catch(PDOException $e) {
            // Log error (jangan tampilkan detail ke user di production)
            error_log("Connection error: " . $e->getMessage());
            
            // Untuk debugging saat development, uncomment baris berikut:
            // echo "Connection error: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>