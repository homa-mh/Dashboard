<?php
require_once 'app/Dbh.php';

class FactorModel
{
    private $pdo;
    public function __construct(){
        $db = new Dbh();
        $this->pdo = $db->connect();
    }
    
    public function get_factor_by_id(int $id, $user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM factor WHERE id = ? AND user_id = ?");
    
        $stmt->execute([$id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function add_factor($data): ?int {
        $sql = "INSERT INTO factor (user_id, title, amount, description, date) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            $_SESSION['user_info']['id'],
            $data['title'],
            $data['amount'],
            $data['description'],
            $data['due_date']
        ]);
    
        if ($success) {
            return (int) $this->pdo->lastInsertId();
        }
    
        return null;
    }
    

    public function update_factor($data) {
        $sql = "UPDATE factor SET title = ?, amount = ?, description = ?, date = ? WHERE id = ? AND user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            $data['title'],
            $data['amount'],
            $data['description'],
            $data['due_date'], 
            $data['id'],       
            $_SESSION['user_info']['id']
        ]);
        
        return $success; 
    }


    public function delete_factor($id, $user_id) {
        $sql = "DELETE FROM factor WHERE id = ? AND user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id, $user_id]);
    }
}
