<?php
require_once 'app/auth_check.php';
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
        button{
            display:block;
            margin:30px auto;
            width:40%;
            background-color:white;
            cursor:pointer;
            border-radius:15px;
            border:1px solid gray;
            padding:5px;
            
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
            this.classList.toggle('fa-spin');" style="cursor: pointer; color:white;text-size:20px;">menu</span>
            <h1>درخواست سرویس کار</h1>
        </div>
        
        <div class="banner-container">
            <p>هزینه ویزیت 350 هزار تومان می باشد.</p>
            <p>پس از پرداخت هزینه درخواست شما ثبت خواهد شد.</p>
            <p>توجه داشته باشید که هزینه ایاب ذهاب با خود مشتری می باشد.</p>
            <form action="https://hoomplus.ir/hoomPlus/payment/Request.php" method="POST">
                <input type="number" name="amount" value="3500000" hidden>
                <input type="text" name="info" value="01<?= $_SESSION['user_info']['id'] ?>" hidden>
                <button>پرداخت</button>
            </form>
        </div>
        

        
    </div>

    

    
</body>
</html>