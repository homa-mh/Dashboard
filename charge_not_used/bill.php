<?php
require_once 'app/auth_check.php';
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
    <title>استعلام قبوض</title>

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
            
            <h1>استعلام قبوض</h1>
            
            <a href="charge.php"><span class="material-symbols-outlined">
                keyboard_backspace
                </span></a>
            
            
        </div>

        <div class="container">
            <form action="bill_inquiry.php" method="post" id="addForm">
                <select id="titleSelect" name="title" required >
                    <option value="" disabled selected>نوع استعلام قبض</option>
                    <option value="آب">آب</option>
                    <option value="گاز">گاز</option>
                    <option value="برق">برق</option>
                </select>

                <input type="text" id="billID" name="billID" placeholder="شماره قبض *">

                
                <input type="text" name="traceID" id="traceID" placeholder="شماره پیگیری (اختیاری)">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="divBillInquiryButtons">
                
                <button id="billInquiry">استعلام</button>
            </div>
            
        </form>

        </div>
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
