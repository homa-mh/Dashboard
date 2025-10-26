<?php
declare(strict_types=1);
require_once "app/Dbh.php";
class ChargeModel{
    private $pdo;
    public function __construct(){
        $db = new Dbh();
        $this->pdo = $db->connect();
    }


    public function get_units($user_id){
        $sql = "SELECT * FROM unit WHERE user_id = ? ORDER BY unit_num ASC;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }
    
    public function get_factors($user_id){
        $sql = "SELECT * FROM factor WHERE user_id = ? and is_sent = 0 ORDER BY date ASC;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }
    public function get_bills($user_id){
        $sql = "SELECT * FROM bill WHERE user_id = ? ORDER BY inquired_date ASC;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }
    public function update_factors($user_id){
        $sql = "UPDATE factor SET is_sent = 1 WHERE user_id = ? ;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        
    }
}