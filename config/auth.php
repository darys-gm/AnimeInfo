<?php
require_once 'database.php';

class Auth {
    private $conn;
    private $db;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        
        // Pastikan session dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    // Register user
    public function register($username, $email, $password) {
        try {
            // Check if user exists
            $checkQuery = "SELECT id FROM users WHERE username = :username OR email = :email";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':username', $username);
            $checkStmt->bindParam(':email', $email);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Username atau email sudah terdaftar!'];
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Registrasi berhasil! Silakan login.'];
            }
            
            return ['success' => false, 'message' => 'Registrasi gagal!'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    // Login user
    public function login($username, $password) {
        try {
            $query = "SELECT * FROM users WHERE username = :username OR email = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['avatar'] = $user['avatar'] ?? 'default-avatar.png';
                    $_SESSION['logged_in'] = true;
                    $_SESSION['login_time'] = time();
                    
                    return ['success' => true, 'message' => 'Login berhasil!'];
                }
            }
            
            return ['success' => false, 'message' => 'Username atau password salah!'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    // Logout
    public function logout() {
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        return ['success' => true, 'message' => 'Logout berhasil!'];
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        // Pastikan session aktif dan user_id ada
        if (session_status() === PHP_SESSION_ACTIVE) {
            return isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
        }
        return false;
    }
    
    // Get current user
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'avatar' => $_SESSION['avatar'] ?? 'default-avatar.png'
            ];
        }
        return null;
    }
    
    // Update user profile
    public function updateProfile($user_id, $username, $email) {
        try {
            $query = "UPDATE users SET username = :username, email = :email WHERE id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute()) {
                if (session_status() === PHP_SESSION_ACTIVE) {
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                }
                return ['success' => true, 'message' => 'Profile updated!'];
            }
            
            return ['success' => false, 'message' => 'Update failed!'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>