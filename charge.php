<?php
require_once 'app/auth_check.php';
require_once 'app/views/UnitsView.php';
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>


<!DOCTYPE html>
<html lang="fa-ir">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/units.css">
    <link rel="stylesheet" href="css/index.css">
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <title>صفحه اصلی</title>


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
    
    <!-- tab -->
    <script>
        function showTab(index) {
            let tabs = document.querySelectorAll(".tab");
            let contents = document.querySelectorAll(".content");

            tabs.forEach(tab => tab.classList.remove("active"));
            contents.forEach(content => content.classList.remove("active"));

            tabs[index].classList.add("active");
            contents[index].classList.add("active");
        }
    </script>
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
            <h1>ساختمان</h1>
        </div>
        <a href="add_charge.php">
            <div class="add">
                <p>ثبت شارژ ساختمان</p>
                <p>+</p>
            </div>
        </a>
        
        

        <div class="tabs">
            <div class="tab active" onclick="showTab(0)">فهرست واحد ها</div>
            <div class="tab" onclick="showTab(1)">فهرست هزینه ها</div>
        </div>
    
        <div class="content active">
            <div class="units">
                <a href="unit.php">
                    <div class="addUnitLink">
                        <span class="material-icons">edit</span>
                        <p>افزودن واحد</p>
                    </div>
                </a>
        
                <!-- list of units  -->
                <?php
                    $view = new UnitsView();
                    $view->show_units_index();
                    
                ?>
            </div>
        </div>     
        <div class="content">
            <div class="reports">
                <?php

                    $view->show_bills();
                    $view->show_factors();
                ?>
            </div>
        </div>
    </div>

    <!-- going to report page -->
    <script defer>
    window.onload = function () {
        document.querySelectorAll(".unit").forEach(unit => {
            unit.addEventListener("click", function () {
                let buildingId = this.getAttribute("data-id");
                window.location.href = `report.php?id=${buildingId}`;});})

        }

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
    <!-- account settlement request -->
    <script>
        document.querySelectorAll('.confirm-btn').forEach(button => {
            button.addEventListener('click', function () {
                if (confirm("آیا درخواست تسویه حساب ارسال شود؟\nدرخواست شما تا 24 ساعت آینده بررسی خواهد شد.")) {
                    window.location.href = this.getAttribute("data-url");
                }
            });
        });
    </script>
    
    
     
    
</body>
</html>