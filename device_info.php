<?php
require_once 'app/auth_check.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحلیل جست و جو</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/base.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;700&display=swap');
    
        body {
            font-family: 'Vazirmatn', sans-serif;
            direction: rtl;
            background-color: #f9f9f9;
            color: #222;
            margin: 0;
            padding: 0;
        }
    
        .device {
            border: 2px solid #80cbc4;
            /*background: linear-gradient(to left, #80cbc4, #80cbc4);*/
            background-color:white;
            border-radius: 12px;
            padding: 15px;
            margin: 20px auto;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.3s, background-color 0.3s;
        }
        
        .device:hover {
            /*background-color: #b2ebf2;*/
            transform: scaleX(1.03);

        }
        
        .device .material-icons {
            font-size: 30px;
            color: #80cbc4;
            vertical-align: middle;
            transform: translateY(-5px);
        }
        
        .device p {
            margin: 5px 0;
            color: #004d40;
            font-size: 16px;
        }
        
        .user-list {
            display: none;
            margin-top: 15px;
            background-color: #ffffff;
            border: 1px dashed #80cbc4;
            padding: 10px;
            border-radius: 8px;
            animation: fadeIn 0.3s ease-in-out;
        }
        .div_table{
            overflow-x:auto;

        }
        
        .user-list table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .user-list th, .user-list td {
            border: 1px solid #b2dfdb;
            padding: 8px;
            text-align: right;
            font-size: 14px;
        }
        
        .user-list th {
            background-color: #80cbc4;
            color: white;
        }
        .export-button {
            text-align: center;
            margin: 15px auto;
        }
        .export-button button {
            background-color: #0c6c61;
            color: white;
            padding: 8px 14px;
            border:none;
            border-radius: 10px;
        }

        
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }

    </style>



</head>
<body>
<?php
require_once 'header.php';

?>
<div class="main">
<div class="head">
    <span class="material-icons"  onclick="let sidebar =
    document.querySelector('.menu'); sidebar.classList.toggle('active');
    this.classList.toggle('fa-spin');" style="cursor: pointer;">menu</span>
    <h1>دستگاه ها</h1>
</div>   


<?php

function displayAnalyzedFields($data) {
    echo '<div class="device" onclick="toggleUserList(this)">
    <span class="material-icons">home_work</span>';

    if (!empty($data['action_groups'][0]['action_group_name'])) {
        echo '<p style="display:inline-block; margin-right:10px">' . htmlspecialchars($data['action_groups'][0]['action_group_name']) . '</p>';

    } else {
        echo '<p style="display:inline-block; margin-right:10px; color: gray;">بدون نام</p>';
    }


    if (!empty($data['model_name'])) {
        echo '<p>مدل دستگاه: ' . htmlspecialchars($data["model_name"]) . '</p>';
    } else {
        echo '<p style="color: gray;">مدل دستگاه مشخص نیست</p>';
    }

    if (!empty($data['unique_qr_code'])) {
        echo '<p>کد کیو آر: ' . htmlspecialchars($data["unique_qr_code"]) . '</p>';
    } else {
        echo '<p style="color: gray;">کد QR موجود نیست</p>';
    }
    
    if (!empty($data['ble_mac'])) {
        echo '<p>آدرس مک دستگاه : ' . htmlspecialchars($data["ble_mac"]) . '</p>';
    } else {
        echo '<p style="color: gray;">مک دستگاه موجود نیست</p>';
    }

    // نمایش تعداد کاربران به جای لیست کامل
    if (!empty($data['action_groups'][0]['access_list']['users'])) {
        $userCount = count($data['action_groups'][0]['access_list']['users']);
        echo '<p>تعداد کاربران: ' . $userCount . '</p>';
    } else {
        echo '<p style="color: gray;">کاربری یافت نشد</p>';
    }

    echo '</div>';
}





    $apiUrl = "http://185.235.197.220/info-api/device-details/by-phone/";
    // $apiUrl = "http://185.235.197.220:8001/api/device-details/by-phone/";
    
    
    // $apiUrl .= "09366774986";
    $apiUrl .= $_SESSION['user_info']['phone'];
    $apiUrl .= "?token=rW8QoV5yP2aL3xZ4hT7jK9bN6mD1sF0g";


    $apiUrl = rtrim($apiUrl, '&');


    $ch = curl_init();


    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 


    $response = curl_exec($ch);


    if (curl_errno($ch)) {
        echo "Error fetching data from the API: " . curl_error($ch);
        exit;
    }


    curl_close($ch);


    $data = json_decode($response, true);


    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "Error: The response is not a valid JSON. Please try again later.";
    } elseif ($data) {
        if (isset($data["devices"])) {
            foreach ($data["devices"] as $item) {
                displayAnalyzedFields($item);
            }
        } else {
            displayAnalyzedFields($data);
        }

    } else {
        echo "No data found or API returned an error.";
    }
    


?>
</div>

</body>
</html>
