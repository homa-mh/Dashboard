<?php
require_once 'app/auth_check.php';
require_once 'app/controllers/ProfileController.php';


$userData = null;


$profileController = new ProfileController();
$userData = $profileController->get_profile_by_id($_SESSION['user_info']['id']);

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
    <title>حساب کاربری</title>

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
            
            <h1>اطلاعات کاربری</h1>
            
            <a href="index.php"><span class="material-symbols-outlined">
                keyboard_backspace
                </span></a>
        </div>

        <div class="container">
        
            <form action="update_user_profile.php" method="post" id="userForm">
    
                <div class="divDate">
                <label for="datepicker">تاریخ تولد:</label>
                <input type="text" id="datepicker" name="datepicker" value="<?= isset($userData['birth_date']) ? htmlspecialchars($userData['birth_date']) : '' ?>" >
                </div>
            
                <input type="text" name="first_name" id="first_name" placeholder="نام *"
                       value="<?= isset($userData['first_name']) ? htmlspecialchars($userData['first_name']) : '' ?>" required>
            
                <input type="text" name="last_name" id="last_name" placeholder="نام خانوادگی *"
                       value="<?= isset($userData['last_name']) ? htmlspecialchars($userData['last_name']) : '' ?>" required>
            
                <input type="text" name="address" id="address" placeholder="آدرس *"
                       value="<?= isset($userData['address']) ? htmlspecialchars($userData['address']) : '' ?>" required>
            
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
                <div class="divAddUserButtons">
                        <input type="hidden" name="id" value="<?= $userData['id'] ?>">
                        <button type="submit" name="submitUser" id="updateUser" value="update">ویرایش اطلاعات</button>
                </div>
    
            </form>
        </div>
    
    
    
    <script>
        $(document).ready(function() {
            $("#datepicker").persianDatepicker({
                format: "YYYY/MM/DD", // فرمت تاریخ شمسی
                autoClose: true
            });
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
