<?php
require_once 'app/Dbh.php';

class ProfileModel
{
    private $pdo;

    public function __construct(){
        $db = new Dbh();
        $this->pdo = $db->connect();
    }

    public function get_profile_by_user_id($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_profile($data) {
        $sql = "UPDATE user SET first_name = ?,last_name = ?, address = ?, birth_date = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['address'],
            $data['birthday'],
            $_SESSION['user_info']['id']
        ]);
        return $success;
    }
    
}
