<?php
require_once 'app/models/UnitsModel.php';

class UnitController {
    private $model;

    public function __construct() {
        $this->model = new UnitsModel();
    }

    public function store() {
        $unit_num = $_POST['unit_num'] ?? '';
        $num_of_residents = $_POST['num_of_residents'] ?? 0;
        $is_empty = $_POST['is_empty'] ?? '';
        $is_owner = isset($_POST['is_owner']) ? false : true;
        $num_of_parking = $_POST['num_of_parking'] ?? 0;

        $saved = $this->model->create([
            'unit_num' => $unit_num,
            'num_of_residents' => $num_of_residents,
            'is_empty' => $is_empty,
            'is_owner' => $is_owner,
            'num_of_parking' => $num_of_parking
        ]);

    
        if ($saved) {
            header("Location: charge.php?success=با موفقیت اضافه شد.");
            exit;
        } else {
            header('Location: charge.php?success=خطا در ذخیره واحد');
            exit;        
            
        }
    }
    
    public function update() {
        $unit_num = $_POST['unit_num'] ?? '';
        $num_of_residents = $_POST['num_of_residents'] ?? 0;
        $is_empty = $_POST['is_empty'] ?? '';
        $is_owner = isset($_POST['is_owner']) ? false : true;
        $num_of_parking = $_POST['num_of_parking'] ?? 0;

        $saved = $this->model->update([
            'unit_num' => $unit_num,
            'num_of_residents' => $num_of_residents,
            'is_empty' => $is_empty,
            'is_owner' => $is_owner,
            'num_of_parking' => $num_of_parking
        ]);

        if ($saved) {
            header("Location: charge.php?success=با موفقیت ویرایش شد.");
            exit;
        } else {
            header('Location: charge.php?success=خطا در ویرایش واحد');
            exit;        
            
        }
    }
    
    public function delete() {
        $unit_num = $_POST['unit_num'] ?? '';

        $saved = $this->model->delete([
            'unit_num' => $unit_num
        ]);

        if ($saved) {
            header("Location: charge.php?success=با موفقیت حذف شد.");
            exit;
        } else {
            header('Location: charge.php?success=خطا در حذف واحد');
            exit;        
            
        }
    }
}