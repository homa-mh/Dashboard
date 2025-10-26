<?php
declare(strict_types=1);

require_once 'app/session_config.php';
require_once 'app/models/UnitsModel.php';
require_once 'app/controllers/ChargeCtrl.php';

class UnitsView{
    private $model;
    private $ctrl;
    private $user_id;

    public function __construct(){
        $this->model = new UnitsModel();
        $this->user_id = $_SESSION['user_info']['id'];
        $this->ctrl = new ChargeCtrl();

    }

    public function show_units_index(): void{
        $units = $this->model->get_units($this->user_id) ?? [];
        
        if(!empty($units)){

            foreach ($units as $unit) {
                $borderColor = ($unit['is_empty']) ? 'gray' : 'rgb(21, 143, 19)';
                $backgroundColor = ($unit['is_empty']) ? 'rgba(128, 128, 128, 0.2)' : 'rgba(21, 143, 19, 0.1)';
            
                $url = 'unit.php?' . http_build_query([
                    'unit_num' => $unit['unit_num'],
                    'is_owner' => $unit['is_owner'],
                    'residents' => $unit['num_of_residents'],
                    'parking' => $unit['num_of_parking']
                ]);
            
                echo '<a href="' . htmlspecialchars($url) . '" style="text-decoration: none; color: inherit;">';
                echo '<div class="unit" style="border-right: 6px solid ' . $borderColor . '; background-color: ' . $backgroundColor . ';">';
            
                    echo '<span class="material-icons">person</span>';
            
                    echo '<div class="unitText">';  
                        echo '<div>';
                            echo '<p class="ownerName">' . ($unit['is_owner']  ? 'مالک' : 'مستأجر') . '</p>';
                            echo '<p>';
                                echo 'واحد <span class="unitNumber">' . $unit['unit_num'] . '</span>';
                            echo '</p>';
                        echo '</div>';
                        echo '<div>';
                            echo '<p>ساکنین: <span>' . intval($unit['num_of_residents']) . '</span></p>';
                            echo '<p>پارکینگ: <span>' . intval($unit['num_of_parking']) . '</span></p>';
                        echo '</div>';
                    echo '</div>';
            
                echo '</div>';
                echo '</a>';
            }


        }else{
                echo '<div class="emptyFolder">';
                echo '<br><br>';
                echo '<span class="material-icons">folder_off</span>';
                echo '</div>';
            }
    }

    public function show_factors(){
        $factors = $this->model->get_factors($this->user_id) ?? [];
        
        if(!empty($factors)){
            foreach ($factors as $factor) { 
                echo '<a href="factor.php?id=' . $factor['id'] . '" style="text-decoration: none; color: inherit;">';
                echo '<div class="report" style="border-right: 6px solid rgb(21, 143, 19);">';
                    echo '<p><strong>عنوان فاکتور: </strong>' . htmlspecialchars($factor['title']) . '</p>';
                    echo '<p><strong>مبلغ: </strong>' . number_format((float)($factor['amount'])) . ' تومان</p>';
                    echo '<p><strong>تاریخ ثبت: </strong>' . date('Y-m-d', strtotime($factor['date'])) . '</p>';
                    if (!empty($factor['description'])) {
                        echo '<p><strong>توضیحات: </strong>' . htmlspecialchars($factor['description']) . '</p>';
                    }
                echo '</div>';
                echo '</a>';
            }

        }else{

            echo '<div class="emptyFolder">';
            echo '<br><br>';
            echo '<span class="material-icons">folder_off</span>';
            echo '</div>';
        }

    }
    
    
    public function show_bills(){
        $bills = $this->model->get_bills($this->user_id) ?? [];
        
        $waterBill = null;
        $electricBill = null;
        $gasBill = null;
        
        if (!empty($bills)) {
            foreach ($bills as $bill) {
                switch ($bill['title']) {
                    case 'آب':
                        $waterBill = $bill;
                        break;
                    case 'برق':
                        $electricBill = $bill;
                        break;
                    case 'گاز':
                        $gasBill = $bill;
                        break;
                }
            }
        }
        
        function renderBillBox($bill, $label) {
            if ($bill) {
                echo '<a href="bill_detail.php?id=' . $bill['id'] . '" style="text-decoration: none; color: inherit;">';
                echo '<div class="report" style="border-right: 6px solid rgb(21, 143, 255);">';
                    echo '<p>قبض '.$label. ' : ';
                    echo  number_format($bill['amount']/10) . ' تومان</p>';
                    
                echo '</div>';
                echo '</a>';
            } else {
                echo '<div class="report" style="border-right: 6px solid rgb(21, 143, 255);">';
                echo '<div class="bill-info">قبض ' . $label . ' : استعلام گرفته نشده</div>';;
                echo '</div>';
                
                
            }
        }
        

        renderBillBox($waterBill, 'آب');
        renderBillBox($electricBill, 'برق');
        renderBillBox($gasBill, 'گاز');
}
    
    

    public function show_units_charge(){
        $units = $this->model->get_units($this->user_id) ?? [];
        foreach($units as $unit) { 
            $previous_debt = number_format($unit['debt']);
            echo '<div class="unit">';
                echo '<p>واحد <span class="unitNumber">' . $unit['unit_number'] . '</span></p>';
                echo '<input type="text" name="' . $unit['unit_number'] . '" placeholder="بدهی پیشین: '.  $previous_debt . ' تومان">';
            echo '</div>';
        }
    }

    public function show_selected_months($months){
        return implode("، ", $this->ctrl->get_selected_months($months));
    }

    public function show_confirmation($deadline ){
        $units = $this->model->get_units($_SESSION['user_info']['id']);
        echo  $this->show_selected_months($_SESSION['months']);
        echo '<br>';
        echo  'مهلت پرداخت: ' . $deadline;
        echo '<br>';
        echo '<table class="tableConfirm">';
        echo '<tr><th>نام</th><th>بدهی پیشین</th><th>شارژ این دوره</th><th>مجموع بدهی</th></tr>';
        foreach($units as $unit){
            $debt = (int)$_POST[(string)($unit['unit_number'])] ?? 0;
            $total_debt = $debt + $unit['debt'];
            echo '<tr>';
            echo '<th>' . htmlspecialchars($unit['resident_name']) . '</th>';
            echo '<th>' . number_format($unit['debt']) . '</th>';
            echo '<th>' . number_format($debt) . '</th>';
            echo '<th>' . number_format($total_debt) . '</th>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function confirmation_form(){
        $units = $this->model->get_units($_SESSION['user_info']['id']);
        foreach($units as $unit){
            echo '<input type="hidden" name ="' . $unit['unit_number']. '" value="' . $_POST[(string)$unit['unit_number']] . '">';
        }

    }

}