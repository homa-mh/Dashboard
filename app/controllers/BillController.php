<?php
require_once 'app/models/BillModel.php';

class BillController {
    private $model;

    public function __construct() {
        $this->model = new BillModel();
    }

    public function store() {
        $title = $_POST['title'] ?? null;
        $bill_id = $_POST['billID'] ?? null;
        $trace_id = $_POST['traceID'] ?? null;
        


        // بررسی فیلدهای الزامی
        if (empty($title) || empty($bill_id)) {
            header('Location: bill.php?success=لطفا مقادیر اجباری را وارد کنید');
            exit;                        
            
        }
        else{
            header('Location: bill.php?success="$bill_id"');
            if($title == "آب"){ $billName = "Water";}
            else if ($title == "گاز"){$billName = "Gas";}
            else if ($title == "برق"){$billName = "Electricity";}
            
            $response = $this->model->inquireBill($billName,$bill_id, $trace_id);
        }


        // بررسی نتیجه
       if ($response && $response['Status']['Code'] === "G00000") {
            
            $saved = $this->model->check([
                'title' => $title,
                'shenase' => $bill_id,
                'bill_id' => $response['Parameters']['BillID'],
                'trace_id' => $trace_id,
                'payment_id' => $response['Parameters']['PaymentID'],
                'full_name' => $response['Parameters']['FullName'],
                'address' => $response['Parameters']['Address'],
                'amount' => $response['Parameters']['Amount'],
                'payment_date' => $response['Parameters']['PaymentDate'],
                'previous_date' => $response['Parameters']['PreviousDate'],
                'bill_current_date' => $response['Parameters']['CurrentDate'],
                'bill_pdf_url' => $response['Parameters']['BillPdfUrl']
            ]);

            if ($saved) {
                header('Location: charge.php?success=استعلام با موفقیت انجام شد.');
                exit;
            } else {
                header('Location: bill.php?success=خطا در ذخیره‌سازی اطلاعات');
                exit;
            }
        } else {
            $desc = $response['Status']['Description'] ?? 'خطا در استعلام قبض';
            header('Location: bill.php?success=' . urlencode($desc));
            exit;
        }
    }
        
}




