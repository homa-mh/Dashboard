<?php
declare(strict_types=1);

class SendMessage{
    private $bot_token;
    private $sms_key;
    public function __construct(){
        $this->bot_token = "xx...";
        $this->sms_key = "xx...";
    }
    public function send_sms($phone, $code): bool {
        $receptor = $phone;
        $token = $code;
        $template = "Verify";
        $key = $this->sms_key;
        
        $url = "https://api.kavenegar.com/v1/$key/verify/lookup.json";
        $params = [
            "receptor" => $receptor,
            "token" => $token,
            "template" => $template
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
    
        // تبدیل JSON به آرایه PHP
        $result = json_decode($response, true);
    
        // بررسی وضعیت
        if (isset($result['return']) && $result['return']['status'] == 200) {
            return true;
        } else {
            $_SESSION['error'] = isset($result['return']['message']) 
                ? $result['return']['message'] 
                : 'خطای نامشخص در پاسخ دریافتی.';
            return false;
        }


    }
    
    public function sms_order($phone, $product, $code): bool {
    
        $template = "hoomPlusOrder";
        $key = $this->sms_key;
        
        $url = "https://api.kavenegar.com/v1/$key/verify/lookup.json";
        $params = [
            "receptor" => $phone,
            "token"    => urlencode($product),
            "token2"   => (string)$code,
            "template" => $template
        ];

        $full_url = $url . '?' . http_build_query($params);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $full_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        $result = json_decode($response, true);
    
        if (isset($result['return']) && $result['return']['status'] == 200) {
            return true;
        } else {
            $errorMsg = isset($result['return']['message']) 
                ? $result['return']['message'] 
                : 'خطای نامشخص';
                
            error_log("Kavenegar Error: " . $errorMsg);
            
            $_SESSION['error'] = $errorMsg;
            return false;
        }
    }
    
    public function send_code($chat_id, $code): bool{
        $url = "https://tapi.bale.ai/bot$this->bot_token/sendMessage";
        $data = [
            "chat_id" => '4960937963',
            "text" => "کد ورود شما: $code"
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            error_log("cURL error: " . curl_error($ch));
        }
        
        curl_close($ch);
    
        if ($http_code == 200) {
            return true;
        } else {
            return false;
        }
    }



    public function send_to_bale($channel_chat_id, $message) {
        $url = "https://tapi.bale.ai/bot{$this->bot_token}/sendMessage";
        $data = $_POST;
    
        if (!$data || count($data) == 0) {
            echo json_encode(["status" => "error", "message" => "No data received"]);
            exit();
        }
    

        if (empty($this->bot_token)) {
            echo json_encode(["status" => "error", "message" => "Bot token is missing"]);
            exit();
        }
    

        if (empty($channel_chat_id)) {
            echo json_encode(["status" => "error", "message" => "Chat ID is missing"]);
            exit();
        }
    
        $data_to_send = [
            'chat_id' => $channel_chat_id,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_to_send));
    
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
    
        if ($http_code != 200) {
            return false;
        } else {
            return true;
        }
    }
    public function send_messagge($chat_id, $message) {
        $url = "https://tapi.bale.ai/bot{$this->bot_token}/sendMessage";

    
        if (empty($this->bot_token)) {
            echo json_encode(["status" => "error", "message" => "Bot token is missing"]);
            exit();
        }
    

        if (empty($chat_id)) {
            echo json_encode(["status" => "error", "message" => "Chat ID is missing"]);
            exit();
        }
    
        $data_to_send = [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_to_send));
    
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
    
        if ($http_code != 200) {
            return false;
        } else {
            return true;
        }
    }
    
    
    
}