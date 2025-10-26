<?php
require_once 'app/models/ProfileModel.php';

class ProfileController {
    private $model;

    public function __construct() {
        $this->model = new ProfileModel();
    }

    public function get_profile_by_id($user_id) {
        return $this->model->get_profile_by_user_id($user_id);
    }


    public function update($data, $user_id) {
        $this->store($user_id);
    }

    public function store($user_id) {
        $firstname = $_POST['first_name'] ?? '';
        $lastname = $_POST['last_name'] ?? '';
        $address = $_POST['address'] ?? '';
        $birthday = $_POST['datepicker'] ?? '';


        if (empty($firstname) || empty($lastname) || empty($address) || empty($birthday)) {
            header('Location: profile.php?success=لطفا تمامی فیلدها را پر کنید.');
            exit;
        }

        // تبدیل اعداد فارسی به انگلیسی
        $birthday = str_replace(
            ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'],
            ['0','1','2','3','4','5','6','7','8','9'],
            $birthday
        );

        $dateParts = explode('/', $birthday);
        if (count($dateParts) !== 3) {
            header('Location: profile.php?success=تاریخ تولد نامعتبر است.');
            exit;
        }

        list($jy, $jm, $jd) = $dateParts;

        if (!is_numeric($jy) || !is_numeric($jm) || !is_numeric($jd)) {
            header('Location: profile.php?success=تاریخ تولد باید عددی باشد.');
            exit;
        }

        list($gy, $gm, $gd) = $this->jalali_to_gregorian((int)$jy, (int)$jm, (int)$jd);
        $miladiBirthday = sprintf('%04d-%02d-%02d', $gy, $gm, $gd);

        $data = [
            'first_name' => $firstname,
            'last_name' => $lastname,
            'address' => $address,
            'birthday' => $miladiBirthday
        ];

        
        $saved = $this->model->update_profile($data);
        if ($saved) {
            $_SESSION['user_info']['first_name'] = $firstname;
            $_SESSION['user_info']['last_name'] = $lastname;
            header('Location: index.php?success=اطلاعات شما با موفقیت ذخیره شد.');
        } else {
            header('Location: profile.php?success=خطا در ذخیره اطلاعات.');
        }
        
        exit;
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
