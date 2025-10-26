<?php
declare(strict_types=1);
require_once '../app/controllers/ReportController.php';

class ReportView   {
    private $ctrl;

    public function __construct(){
        $this->ctrl = new ReportController();
    }

    public function show_charges(): void{

        $charges = $this->ctrl->get_charge();
        foreach ($charges as $charge) {
            echo '<div class="report" style=" border-right: 6px solid ' . (($charge['status'] === 0) ? 'rgb(178, 31, 11); background-color: rgba(178, 11, 11, 0.2); "' : 'rgb(21, 143, 19) ;" ') .'>';  
                echo '<p><strong>واحد : </strong>'. $charge['unit_number'].'</p>';
                echo '<p><strong>تاریخ صدور شارژ: </strong><br>'. $charge['created_at'].'</p>';
                echo '<p><strong>دوره شارژ : </strong><br>'. htmlspecialchars($charge['period']).'</p>';
                echo '<p><strong>مبلغ: </strong>'. number_format($charge['amount']/10) . " تومان" .'</p>';
                echo '<p><strong>وضعیت پرداخت : </strong>' . ($charge['status'] === 1 ? "پرداخت شده" : "در انتظار پرداخت") .'</p>';
                echo '<p><strong>روش پرداخت :  </strong>'. ($charge['pay_type'] === 1 ? "آنلاین" : "نقدی")  .'</p>';
                if ($charge['status'] === 0){
                    echo '<form action="cash.php" method="post" class="cashForm">';
                    echo '<input type="hidden" name="amount" value="'. $charge['amount']/10 .'">';
                    echo '<input type="hidden" name="charge_id" value="'. $charge['id'] .'">';
                    echo '<button name = "unit_id" value="' . $charge['unit_id'] . '">دریافت شد</button>';
                    echo '</form>';
                }
            echo '</div><br>';
        } 
        if(!$charges){
            echo '<div class="emptyFolder">';
            echo '<br><br>';
            echo '<span class="material-icons">folder_off</span>';
            echo '</div>';
        }
    }
}