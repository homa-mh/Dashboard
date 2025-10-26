<?php
declare(strict_types=1);
require_once 'app/models/ChargeModel.php';
require_once 'app/bale/SendMessage.php';

class ChargeCtrl{
    private $chargeModel;
    private $baleModel;

    public function __construct(){

        $this->chargeModel = new ChargeModel();
        $this->baleModel = new SendMessage();
    }


    

    public function get_units($user_id){
        return $this->chargeModel->get_units($user_id);
    }
    
    public function get_factors($user_id){
        return $this->chargeModel->get_factors($user_id);
    }
    public function get_bills($user_id){
        return $this->chargeModel->get_bills($user_id);
    }
    public function update_factors($user_id){
        $this->chargeModel->update_factors($user_id);
    }
    
    public function send_charge_full($months, $unit_charges, $total, $chat_id, $fixed_charge, $factors = [])
    {
        $month_text = implode("، ", $months);
    
        $message = "📢 *هزینه شارژ ساختمان*\n";
        $message .= "ماه‌های محاسبه‌شده: $month_text\n";
        $message .= "هزینه کل: " . number_format($total) . " تومان\n\n";
    
        // افزودن شارژ ثابت
        $message .= "💠 *شارژ ثابت هر واحد: " . number_format($fixed_charge) . " تومان*\n\n";
    
        $message .= "🔸 *مقدار شارژ واحدها:*\n";
        foreach ($unit_charges as $unit => $amount) {
            $message .= "واحد {$unit}: " . number_format($amount) . " تومان\n";
        }
    
        if (!empty($factors)) {
            $message .= "\n📋 *فاکتورها:*\n";
            foreach ($factors as $f) {
                $message .= "• " . $f['title'] . (!empty($f['description']) ? " (" . $f['description'] . "): " : ": ")  . number_format((float)($f['amount'])) . " تومان\n";
            }
        }
    
        $result = $this->baleModel->send_to_bale($chat_id, $message);
    
        return $result;
    }



}