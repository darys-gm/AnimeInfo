<?php
require_once 'database.php';

class AnimeList {
    private $conn;
    private $db;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    // Add anime to user's list
    public function addAnime($user_id, $anime_id, $anime_title, $anime_image, $anime_score, $total_episodes, $status = 'planned') {
        try {
            // Cek apakah anime sudah ada di list user
            $checkQuery = "SELECT id FROM user_anime_list WHERE user_id = :user_id AND anime_id = :anime_id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':user_id', $user_id);
            $checkStmt->bindParam(':anime_id', $anime_id);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                // Jika sudah ada, update status
                $query = "UPDATE user_anime_list SET 
                          anime_status = :anime_status, 
                          updated_at = CURRENT_TIMESTAMP 
                          WHERE user_id = :user_id AND anime_id = :anime_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':anime_status', $status);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':anime_id', $anime_id);
                return $stmt->execute();
            } else {
                // Insert baru
                $query = "INSERT INTO user_anime_list (user_id, anime_id, anime_title, anime_image, anime_score, anime_total_episodes, anime_status) 
                          VALUES (:user_id, :anime_id, :anime_title, :anime_image, :anime_score, :anime_total_episodes, :anime_status)";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':anime_id', $anime_id);
                $stmt->bindParam(':anime_title', $anime_title);
                $stmt->bindParam(':anime_image', $anime_image);
                $stmt->bindParam(':anime_score', $anime_score);
                $stmt->bindParam(':anime_total_episodes', $total_episodes);
                $stmt->bindParam(':anime_status', $status);
                
                return $stmt->execute();
            }
        } catch(PDOException $e) {
            error_log("Add Anime Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Remove anime from user's list
    public function removeAnime($user_id, $anime_id) {
        try {
            $query = "DELETE FROM user_anime_list WHERE user_id = :user_id AND anime_id = :anime_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':anime_id', $anime_id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Remove Anime Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Update anime status
    public function updateStatus($user_id, $anime_id, $status, $progress = null) {
        try {
            $query = "UPDATE user_anime_list SET anime_status = :status";
            if ($progress !== null && $progress > 0) {
                $query .= ", anime_progress = :progress";
            }
            $query .= " WHERE user_id = :user_id AND anime_id = :anime_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':anime_id', $anime_id);
            if ($progress !== null && $progress > 0) {
                $stmt->bindParam(':progress', $progress);
            }
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Update Status Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get user's anime list
    public function getUserAnimeList($user_id, $status = null, $limit = 24, $offset = 0) {
        try {
            $query = "SELECT * FROM user_anime_list WHERE user_id = :user_id";
            if ($status && $status != 'all') {
                $query .= " AND anime_status = :status";
            }
            $query .= " ORDER BY added_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            if ($status && $status != 'all') {
                $stmt->bindParam(':status', $status);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get User Anime List Error: " . $e->getMessage());
            return [];
        }
    }
    
    // Check if anime is in user's list
    public function isInList($user_id, $anime_id) {
        try {
            $query = "SELECT id FROM user_anime_list WHERE user_id = :user_id AND anime_id = :anime_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':anime_id', $anime_id);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Is In List Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get user's list count
    public function getListCount($user_id, $status = null) {
        try {
            $query = "SELECT COUNT(*) as total FROM user_anime_list WHERE user_id = :user_id";
            if ($status && $status != 'all') {
                $query .= " AND anime_status = :status";
            }
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            if ($status && $status != 'all') {
                $stmt->bindParam(':status', $status);
            }
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'];
        } catch(PDOException $e) {
            error_log("Get List Count Error: " . $e->getMessage());
            return 0;
        }
    }
}
?>