<?php
declare(strict_types=1);
require_once 'app/Dbh.php';
class LoginModel{
    private $pdo;
    public function __construct(){
        $db = new Dbh();
        $this->pdo = $db->connect();
    }
    public function get_user($phone){
        $sql = "SELECT * FROM user WHERE phone = ? ;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$phone]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function check_user_api($phone) {
        $url = "http://185.235.197.220/info-api/device?customer_phone=" . urlencode($phone) . "&token=rW8QoV5yP2aL3xZ4hT7jK9bN6mD1sF0g";
    
        // تماس با API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // حداکثر 10 ثانیه
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        // اگر درخواست با خطا مواجه شد
        if ($response === false || $httpCode !== 200) {
            return null;
        }
    
        // تجزیه JSON
        $data = json_decode($response, true);
    
        // اگر کاربر پیدا نشد
        if (isset($data['detail']) && $data['detail'] === 'Device not found') {
            return null;
        }
    
        // در غیر این صورت، داده دستگاه رو برگردون
        return $data;
    }

    
    public function add_user($phone){
        $sql = "INSERT INTO user (phone) VALUES(?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$phone]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    public function log_login($phone) {
    $sql = "INSERT INTO login_logs (phone) VALUES(?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$phone]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

}

