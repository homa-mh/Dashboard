<?php
declare(strict_types=1);

require_once 'app/Dbh.php';
class UnitsModel{
    private $pdo;

    public function __construct() {
        $db = new Dbh();
        $this->pdo = $db->connect();
    }

    public function get_units($user_id){
        $sql = "SELECT * FROM unit WHERE user_id = ? ORDER BY unit_num ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function get_factors($user_id){
        $sql = "SELECT * FROM factor WHERE user_id = ? ORDER BY date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function get_bills($user_id){
        $sql = "SELECT * FROM bill WHERE user_id = ? ORDER BY inquired_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function get_bill_by_id($bill_id,$user_id) {
    $sql = "SELECT * FROM bill WHERE id = ? AND user_id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$bill_id, $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
    }

    
    
    public function create($data): ?int {
        $sql = "INSERT INTO unit (user_id, unit_num, num_of_residents, is_empty, is_owner, num_of_parking) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            $_SESSION['user_info']['id'],
            $data['unit_num'],
            $data['num_of_residents'],
            $data['is_empty'],
            $data['is_owner'],
            $data['num_of_parking']
        ]);
    
        if ($success) {
            return (int) $this->pdo->lastInsertId();
        }
    
        return null;
    }
    
    public function update($data): bool {
        $sql = "UPDATE unit SET 
                    num_of_residents = :num_of_residents, 
                    is_empty = :is_empty, 
                    is_owner = :is_owner, 
                    num_of_parking = :num_of_parking  
                WHERE user_id = :user_id AND unit_num = :unit_num";
        
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            ':user_id' => $_SESSION['user_info']['id'],
            ':unit_num' => $data['unit_num'],
            ':num_of_residents' => $data['num_of_residents'],
            ':is_empty' => $data['is_empty'],
            ':is_owner' => $data['is_owner'],
            ':num_of_parking' => $data['num_of_parking']
        ]);
    
        return $success && $stmt->rowCount() > 0;
    }
    
    public function delete($data): bool {
        $sql = "DELETE FROM unit WHERE user_id = :user_id AND unit_num = :unit_num";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            ':user_id' => $_SESSION['user_info']['id'],
            ':unit_num' => $data['unit_num']
        ]);
    
        return $success && $stmt->rowCount() > 0;
    }

        
    
}