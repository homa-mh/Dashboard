<?php
require_once 'app/auth_check.php';
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

    <title>هوم پلاس</title>


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
        body{
            background-color:#119F89;
            color:white;
        }
        .banner-container{
            margin:20vh auto 0;
        }
        .banner-container p{
            text-align: center;

        }
        .footer{
            position:fixed;
            bottom:0;
            display:block;
            margin:auto;
            width:100%;
        }
        .footer p{
            text-align :left;
            display:inline-block;
            transform:translateY(-10px);
            margin-right:60px;
        }
        .footer img{
            display:inline-block;
            width:40px;
        }
    </style>
</head>
<body dir="rtl">
    <div class="main">


                <?php
            require_once 'header.php';

            ?>

        <div class="head">
            <img class="material-icons" src="image/icons/menu_white.svg" onclick="document.querySelector('.menu').classList.toggle('active')" style="cursor:pointer;">
        </div>
        
        <div class="banner-container">
            <p>هوم پلاس</p>
            <p>دستیار هوشمند مدیر ساختمان</p>
            <p>هر آنچه مدیر ساختمان نیاز دارد!</p>
        </div>
        
        <div class="footer">
            <p>smarthoom.com</p>
            <img src="image/hoom_logo.png">
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