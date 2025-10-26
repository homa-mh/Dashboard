<?php
declare(strict_types=1);
require_once '../app/models/ReportModel.php';

class ReportController{
    private $model;

    public function __construct(){
        $this->model = new ReportModel();
    }

    public function get_charge(): array{
        if(isset($_GET['id']) && !empty($_GET['id'])){
            return $this->model->get_unit_charges($_GET['id']) ?? [];
        }
        else{
            return $this->model->get_all_charges() ?? [];
        }
    }
    public function payed_by_cash($unit_id, $amount, $charge_id) {
        
        if(!empty($this->model->payed_charge($charge_id, 0))){
            if(!empty($this->model->payed_units($unit_id, ((int)$amount)*10))){
                
                return 'با موفقیت ثبت شد.';
            }
        }else{
            return 'خطا در ثبت پرداخت نقدی';
        }
        

    }
}
