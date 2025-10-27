<?php
require_once 'app/auth_check.php';
require_once 'app/controllers/FactorController.php';

$factorData = null;


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $factorController = new FactorController();
    $factorData = $factorController->get_factor_by_id($_GET['id'], $_SESSION['user_info']['id']);
}
?>


<!DOCTYPE html>
<html lang="fa-ir">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/add.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=keyboard_backspace" />
    
    <!-- for date picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker/dist/css/persian-datepicker.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-date/dist/persian-date.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-datepicker/dist/js/persian-datepicker.min.js"></script>
    <title>ثبت فاکتور جدید</title>

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
        
        <?php require_once 'header.php'; ?>
              
        
        <div class="head">
            <span class="material-icons"  onclick="let sidebar =
            document.querySelector('.menu'); sidebar.classList.toggle('active');
            this.classList.toggle('fa-spin');" style="cursor: pointer;">menu</span>
            
            <h1>ثبت فاکتور</h1>
            
            <a href="charge.php"><span class="material-symbols-outlined">
                keyboard_backspace
                </span></a>
        </div>

        <div class="container">
        
            <form action="add_factor.php" method="post" id="addForm">
                
                <div class="divDate">
                <label for="datepicker">تاریخ را مشخص کنید:</label>
                <input type="text" id="datepicker" name="datepicker"
                value="<?= isset($factorData['date']) ? htmlspecialchars($factorData['date']) : '' ?>">
                </div>
            
                <select id="titleSelect" name="title" required onchange="handleSelectChange(this)">
                    <option value="" disabled <?= !isset($factorData) ? 'selected' : '' ?>>عنوان فاکتور</option>
                    <option value="نظافت" <?= isset($factorData['title']) && $factorData['title'] == 'نظافت' ? 'selected' : '' ?>>نظافت</option>
                    <option value="آسانسور" <?= isset($factorData['title']) && $factorData['title'] == 'آسانسور' ? 'selected' : '' ?>>آسانسور</option>
                    <option value="تعمیرات" <?= isset($factorData['title']) && $factorData['title'] == 'تعمیرات' ? 'selected' : '' ?>>تعمیرات</option>
                    <option value="سایر" <?= isset($factorData['title']) && $factorData['title'] == 'سایر' ? 'selected' : '' ?>>سایر</option>
                </select>
            
                <div id="customTitleContainer" style="display:<?= (isset($factorData['title']) && $factorData['title'] == 'سایر') ? 'block' : 'none' ?>;">
                    <input type="text" id="customTitle" name="custom_title" placeholder="عنوان را وارد کنید *"
                           value="<?= isset($factorData['custom_title']) ? htmlspecialchars($factorData['custom_title']) : '' ?>" >
                </div>
            
                <input type="text" name="amount" id="amount" placeholder="مبلغ فاکتور *" value="<?= isset($factorData['amount']) ? number_format($factorData['amount']) : '' ?>" required>
                
                <input type="text" name="description" id="description" placeholder="توضیحات (اختیاری)" value="<?= isset($factorData['description']) ? htmlspecialchars($factorData['description']) : '' ?>">
    
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="divAddFactorButtons">
                    <?php if ($factorData): ?>
                    <input type="hidden" name="id" value="<?= $factorData['id'] ?>">
                    
                    <div class="divAddFactorButtons">
                        <button type="submit" name="addFactor" id="addFactor" value="update">ویرایش فاکتور</button>
                        <button type="submit" name="addFactor" id="addFactor" value="delete" style="background-color: #e74c3c;">حذف فاکتور</button>
                    </div>
                    
                    <?php else: ?>
                    <button type="submit" name="addFactor" id="addFactor" value="add">ثبت فاکتور</button>
                </div>
                <?php endif; ?>
    
            </form>
    </div>
    
    <script>
        function handleSelectChange(selectElement) {
            const customInput = document.getElementById("customTitleContainer");
            if (selectElement.value === "سایر") {
                customInput.style.display = "block";
                document.getElementById("customTitle").required = true;
            } else {
                customInput.style.display = "none";
                document.getElementById("customTitle").required = false;
            }
        }
    </script>
    
    <script>
        $(document).ready(function() {
            $("#datepicker").persianDatepicker({
                format: "YYYY/MM/DD", // فرمت تاریخ شمسی
                autoClose: true
            });
        });
    </script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('input[name^="amount"]').forEach(input => {
            input.addEventListener("input", function() {
                let value = this.value.replace(/,/g, ''); // حذف کاماهای قبلی
                if (!isNaN(value) && value.length > 0) {
                    this.value = Number(value).toLocaleString('en-US'); // اضافه کردن کاما سه رقم سه رقم
                }
            });
        });
    
        // حذف کاماها هنگام ارسال فرم
        document.getElementById("addForm").addEventListener("submit", function() {
            document.querySelectorAll('input[name^="amount"]').forEach(input => {
                input.value = input.value.replace(/,/g, ''); // حذف کاما قبل از ارسال
                });
            });
        });
    </script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const titleSelect = document.getElementById("titleSelect");
            const customTitleContainer = document.getElementById("customTitleContainer");
            const customTitleInput = document.getElementById("customTitle");
            
            const currentTitle = "<?= isset($factorData['title']) ? $factorData['title'] : '' ?>";
            const customTitle = "<?= isset($factorData['custom_title']) ? $factorData['custom_title'] : '' ?>";
            
    
            let found = false;
            for (let i = 0; i < titleSelect.options.length; i++) {
                if (titleSelect.options[i].value === currentTitle) {
                    found = true;
                    break;
                }
            }
    
            if (!found && currentTitle !== "") {
                titleSelect.value = "سایر";
                customTitleContainer.style.display = "block";
                customTitleInput.value = currentTitle;
                customTitleInput.required = true;
            } else if (titleSelect.value === "سایر") {
                customTitleContainer.style.display = "block";
                customTitleInput.required = true;
            }
        });
    </script>
    
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
