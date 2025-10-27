<?php
require_once 'app/auth_check.php';
require_once 'app/models/UnitsModel.php';
require_once 'app/helpers/DateHelper.php';

$model = new UnitsModel();
$user_id = $_SESSION['user_info']['id'];
$bill_id = isset($_GET['id']) ? $_GET['id'] : 0;

$bill = $model->get_bill_by_id($bill_id, $user_id);

if (!$bill) {
    echo "قبض مورد نظر یافت نشد.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fa-ir">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/bill.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=keyboard_backspace" />
    <!-- for date picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker/dist/css/persian-datepicker.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-date/dist/persian-date.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-datepicker/dist/js/persian-datepicker.min.js"></script>
    <title>جزئیات قبض</title>

    <style>
        
        
        /* Toast notification styling */
        .toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: white;
            color:rgb(58, 151, 61);
            padding: 15px 20px;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            opacity: 1;
            transition: opacity 0.5s ease-in-out;
        }
    
    </style>
</head>
<body dir="rtl">
    <div class="main">
        
        <?php
            require_once 'header.php';

            ?>
              
        
        <div class="head">
            <span class="material-icons"  onclick="let sidebar =
            document.querySelector('.menu'); sidebar.classList.toggle('active');
            this.classList.toggle('fa-spin');" style="cursor: pointer;">menu</span>
            
            <h1>جزئیات قبض</h1>
            
            <a href="charge.php"><span class="material-symbols-outlined">
                keyboard_backspace
                </span></a>
            
            
        </div>
        
        <div class="container">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0 8px; font-size: 14px;">

        <colgroup>
            <col style="width: 40%;">
            <col style="width: 60%;">
        </colgroup>
        <tr>
            <td><strong>نوع قبض:</strong></td>
            <td><?= htmlspecialchars($bill['title']) ?></td>
        </tr>
        <tr>
            <td><strong>شناسه:</strong></td>
            <td><?= htmlspecialchars($bill['shenase']) ?></td>
        </tr>
        <tr>
            <td><strong>شناسه پرداخت:</strong></td>
            <td><?= htmlspecialchars($bill['payment_id']) ?></td>
        </tr>
        <tr>
            <td><strong>مبلغ:</strong></td>
            <td><?= number_format($bill['amount']/10) ?> تومان</td>
        </tr>
        <tr>
            <td><strong>نام صاحب قبض:</strong></td>
            <td><?= htmlspecialchars($bill['full_name']) ?></td>
        </tr>
        <tr>
            <td><strong>آدرس:</strong></td>
            <td><?= nl2br(htmlspecialchars($bill['address'])) ?></td>
        </tr>
        <tr>
            <td><strong>تاریخ قرائت پیشین:</strong></td>
            <td><?= JalaliDate::formatJalali(JalaliDate::convertToISODate($bill['previous_date'])) ?></td>
        </tr>
        <tr>
            <td><strong>تاریخ قرائت کنونی:</strong></td>
            <td><?= JalaliDate::formatJalali(JalaliDate::convertToISODate($bill['bill_current_date'])) ?></td>
        </tr>
        <tr>
            <td><strong>مهلت پرداخت:</strong></td>
            <td><?= JalaliDate::formatJalali(JalaliDate::convertToISODate($bill['payment_date'])) ?></td>
        </tr>
        <tr>
            <td><strong>تاریخ استعلام:</strong></td>
            <td><?= JalaliDate::formatJalali($bill['inquired_date']) ?></td>
        </tr>
    </table>
</div>

        
            

    <!-- show errors -->
    <script>

        function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        function removeQueryParam(param) {
            const url = new URL(window.location);
            url.searchParams.delete(param);
            window.history.replaceState({}, document.title, url);
        }


        if (getQueryParam('success')) {

            const toast = document.createElement("div");
            toast.classList.add("toast");
            toast.innerText = getQueryParam('success');
            document.body.appendChild(toast);

            removeQueryParam('success');

            setTimeout(() => {
                toast.style.opacity = "0";
                setTimeout(() => toast.remove(), 500); 
            }, 3000);
        }
    </script>
</body>
</html>