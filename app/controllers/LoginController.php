<?php
declare(strict_types=1);
require_once 'app/session_config.php';
require_once 'app/models/LoginModel.php';
require_once 'app/bale/SendMessage.php';

class LoginController{
    private $model;
    private $msg;
    public function __construct(){
        $this->model = new LoginModel();
        $this->msg = new SendMessage();
    }
    public function send_code($user_name): bool{
        if(empty($user_name)){
            $_SESSION['error'] = "لطفا تلفن همراه خود را وارد کنید.";
            return false;
        }


        $user = $this->model->check_user_api($user_name);

        // شماره‌های استثنا (ادمین)
        $adminPhones = ["09022340943", "09389887880" , "09332898741"];
        
        // اگر شماره در API پیدا نشده و جزو استثنا هم نیست
        if (!$user && !in_array($user_name, $adminPhones)) {
            $_SESSION['error'] = "این شماره در سیستم دستگاهی ندارد یا مجاز به ورود نیست.";
            return false;
        }
        
        $user = $this->model->get_user($user_name);
            if(preg_match('/^09\d{9}$/', $user_name)){
                $this->model->add_user($user_name);
            }else{
                $_SESSION['error'] = "تلفن همراه وارد شده اشتباه است";
                return false;
            }
            
        $code = rand(100000, 999999);

        $send_status = $this->msg->send_sms($user_name, $code);
        // $send_status = $this->msg->send_code($user['chat_id'], $code);
        
        if(!$send_status){
            $_SESSION['error'] = "خطا در ارسال کد";
            return false;
        }

        $_SESSION['pre_value'] = $user_name;
        $_SESSION['success'] = "کد ارسال شد";
        $_SESSION['send_time'] = time();
        $_SESSION['code'] = $code;
        return true;
    }

    public function code_check($user_name, $pwd): bool{
        // admin login
        if($user_name === '09332898741' && $pwd === '1234'){
            
            $this->model->log_login($user_name);
    
            $now = date("H:i:s Y-m-d");
            $msg = "📥 حسین زمانیان وارد شد\n👤 شماره: $user_name\n⏰ زمان: $now";
            
            $this->msg->send_to_bale('6441576316', $msg);  
        return true;
        }
        if(!isset($_SESSION['pre_value'])){
            $_SESSION['error'] = "لطفا با یک مرورگر وارد شوید.";
            return false;
        }
        if($user_name != $_SESSION['pre_value']){
            $_SESSION['error'] = "شماره تلفن تغییر کرده است. لطفا مجددا کد را دریافت کنید.";
            return false;
        }
        if(empty($user_name) || empty($pwd)){
            $_SESSION['error'] = "لطفا فیلد ها را تکمیل کنید.";
            return false;
        }
        if(!isset($_SESSION['code'])){
            $_SESSION['error'] = "لطفا با یک مرورگر وارد شوید.";
            return false;
        }
        if($pwd != $_SESSION['code']){
            $_SESSION['error'] = "کدی که وارد کرده اید اشتباه است.";
            return false;
        }
        // without login log
        // return true;
        
        
        // with login log
        if ($pwd == $_SESSION['code']) {

            $this->model->log_login($user_name);
    
            $now = date("H:i:s Y-m-d");
            $msg = "📥 کاربر وارد شد\n👤 شماره: $user_name\n⏰ زمان: $now";
            
            $this->msg->send_to_bale('6441576316', $msg);  
            
            return true;
        }

    }
    public function set_sessions($user_name){
        unset($_SESSION['code']);
        unset($_SESSION['pre_value']);
        unset($_SESSION['send_time']);
        // new
        if(empty($user_name)){
            $_SESSION['error'] = "لطفا تلفن همراه خود را وارد کنید.";
            return false;
        }
        $user = $this->model->get_user($user_name);
        if(!$user){
            if(preg_match('/^09\d{9}$/', $user_name)){
                $this->model->add_user($user_name);
                $user = $this->model->get_user($user_name); // 👈 دوباره بگیر
            }else{
                $_SESSION['error'] = "تلفن همراه وارد شده اشتباه است";
                return false;
    }
}

        
        // new end
        
        $_SESSION['user_info'] = $user;
        return true;
    }
}