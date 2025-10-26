<?php
require_once 'app/models/FactorModel.php';

class FactorController {
    private $model;

    public function __construct() {
        $this->model = new FactorModel();
    }
    
    public function get_factor_by_id($id, $user_id) {
        return $this->model->get_factor_by_id($id, $user_id);
    }
    
    public function add($type, $data, $user_id) {
        $this->store($type);
    }


    public function update($type, $data, $user_id) {
        $this->store($type);
    }

    public function delete($id, $user_id) {

        $saved = $this->model->delete_factor($id, $user_id);
        if ($saved) {
            header('Location: charge.php?success=فاکتور حذف شد.');
            exit;
        } else {
            header('Location: factor.php?success=خطا در حذف فاکتور');
            exit;        
            
        }
    }


    public function store($type) {
        $title = $_POST['title'] ?? '';
        $customTitle = $_POST['custom_title'] ?? null;
        $amount = $_POST['amount'] ?? '';
        $description = $_POST['description'] ?? '';
        $due_date = $_POST['datepicker'] ?? '';

        // در صورت انتخاب "سایر"، عنوان دلخواه جایگزین شود
        if ($title === 'سایر' && !empty($customTitle)) {
            $title = $customTitle;
        }

        // بررسی فیلدهای الزامی
        if (empty($title) || empty($amount) || empty($due_date)) {

            header('Location: factor.php?success=لطفا تمامی مقادیر را پر کنید');
            exit;                        }

        // تبدیل اعداد فارسی به انگلیسی
        $due_date = str_replace(
            ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'],
            ['0','1','2','3','4','5','6','7','8','9'],
            $due_date
        );

        // بررسی ساختار تاریخ
        $dateParts = explode('/', $due_date);
        if (count($dateParts) !== 3) {

            header('Location: factor.php?success=تاریخ نا معتبر است');
            exit;                        }

        list($jy, $jm, $jd) = $dateParts;

        if (!is_numeric($jy) || !is_numeric($jm) || !is_numeric($jd)) {

            header('Location: factor.php?success=تاریخ نا معتبر است');
            exit;                }

        list($gy, $gm, $gd) = $this->jalali_to_gregorian((int)$jy, (int)$jm, (int)$jd);
        $miladiDate = sprintf('%04d-%02d-%02d', $gy, $gm, $gd);
        
        if($type === "add"){
            $saved = $this->model->add_factor([
            'title' => $title,
            'amount' => $amount,
            'description' => $description,
            'due_date' => $miladiDate
        ]);

        if ($saved) {
            header('Location: charge.php?success=فاکتور اضافه شد.');
            exit;
        } else {
            header('Location: factor.php?success=خطا در ذخیره فاکتور');
            exit;        }
        }
        else if($type === "update"){
        // فرض می‌کنیم id فاکتور از فرم ارسال شده:
        $id = $_POST['id'] ?? null;
    
        if (!$id) {
            header('Location: factor.php?success=شناسه فاکتور ارسال نشده است');
            exit;
        }
    
        $saved = $this->model->update_factor([
            'id' => $id,
            'title' => $title,
            'amount' => $amount,
            'description' => $description,
            'due_date' => $miladiDate // همان کلید used در مدل
        ]);
    
        if ($saved) {
            header('Location: charge.php?success=فاکتور ویرایش شد.');
            exit;
        } else {
            header('Location: factor.php?success=خطا در ویرایش فاکتور');
            exit;
            }
        }
    }

    public function jalali_to_gregorian($jy, $jm, $jd) {
        $gy = $jy - 979;
        $gm = $jm - 1;
        $gd = $jd - 1;

        $days = 365 * $gy + (int)($gy / 33) * 8 + (int)((($gy % 33) + 3) / 4);
        $days += 78 + $gd;

        $g_days_in_month = [31,28,31,30,31,30,31,31,30,31,30,31];
        $j_days_in_month = [31,31,31,31,31,31,30,30,30,30,30,29];

        for ($i = 0; $i < $gm; ++$i)
            $days += $j_days_in_month[$i];

        $gy = 1600 + 400 * (int)($days / 146097);
        $days %= 146097;

        $leap = true;
        if ($days >= 36525) {
            $days--;
            $gy += 100 * (int)($days / 36524);
            $days %= 36524;

            if ($days >= 365)
                $days++;
            else
                $leap = false;
        }

        $gy += 4 * (int)($days / 1461);
        $days %= 1461;

        if ($days >= 366) {
            $leap = false;
            $days--;
            $gy += (int)($days / 365);
            $days = $days % 365;
        }

        for ($i = 0; $days >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
            $days -= $g_days_in_month[$i] + ($i == 1 && $leap);

        $gm = $i + 1;
        $gd = $days + 1;

        return [$gy, $gm, $gd];
        }
    }
