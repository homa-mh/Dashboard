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
        #export {
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
    <h1>گزارش تردد دستگاه ها</h1>
</div>   


<?php

function displayAnalyzedFields($data) {
    $qr = htmlspecialchars($data["unique_qr_code"] ?? '', ENT_QUOTES, 'UTF-8');
    $groupName = htmlspecialchars($data['action_groups'][0]['action_group_name'] ?? 'بدون گروه', ENT_QUOTES, 'UTF-8');
    $modelName = htmlspecialchars($data["model_name"] ?? 'نامشخص', ENT_QUOTES, 'UTF-8');

    echo "
    <div class='device' onclick='fetchLogs(this)' data-qr='$qr'>
        <span class='material-icons'>home_work</span>
        <p style='display:inline-block; margin-right:10px;'>$groupName</p>
        <div class='user-list'>
            <p>مدل دستگاه: $modelName</p>
            <p>کد QR: $qr</p>
            <div class='div_table'>
                <p style='color:gray'>برای نمایش لاگ‌ها روی دستگاه کلیک کن.</p>
            </div>
        </div>
    </div>
    ";
}






    $apiUrl = "http://185.235.197.220/info-api/device-details/by-phone/";

    
    $apiUrl .= $_SESSION['user_info']['phone'];



    $apiUrl .= "?token=rW8QoV5yP2aL3xZ4hT7jK9bN6mD1sF0g";


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
<script>
function toggleUserList(element) {
    const userList = element.querySelector(".user-list");
    userList.style.display = userList.style.display === "none" ? "block" : "none";
}
</script>

<script>
const logApiBase = 'logs_proxy.php'; // <-- این رو به URL واقعی API لاگ‌هات عوض کن

// کش موقتی تا دوباره fetch نزنه اگر باز/بسته بشه
const logCache = new Map();

function fetchLogs(element) {
    const qr = element.dataset.qr;
    const userList = element.querySelector(".user-list");
    const tableContainer = userList.querySelector(".div_table");

    // toggle نمایش/پنهان اگر قبلاً باز بوده
    if (userList.style.display === "block") {
        userList.style.display = "none";
        return;
    }
    userList.style.display = "block";

    // اگر قبلاً گرفته شده بود، دوباره نمایش بده بدون فراخوانی جدید
    if (logCache.has(qr)) {
        renderTable(tableContainer, logCache.get(qr));
        return;
    }

    tableContainer.innerHTML = "<p style='color:gray'>در حال بارگذاری لاگ‌ها...</p>";

    // فرض: API با پارامتر qr کار می‌کنه. اگر پارامتر متفاوتیه، این خط رو بر اساسش تغییر بده.
    fetch(`${logApiBase}?qr=${encodeURIComponent(qr)}`)
        .then(res => {
            if (!res.ok) throw new Error('HTTP error ' + res.status);
            return res.json();
        })
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                // فقط فیلدهای مورد نظر: نام، شماره، تاریخ، ساعت
                const simplified = data.map(log => {
                    const name = log.user_details?.display_name || '-';
                    const phone = log.user_details?.phone_number || '-';
                    let date = '-';
                    let time = '-';
                    if (log.time) {
                        // فرض می‌کنیم فرمت ISO هست مثل 2025-01-25T20:36:00
                        const dt = new Date(log.time);
                        if (!isNaN(dt)) {
                            // تاریخ به صورت YYYY-MM-DD
                            date = dt.toISOString().split('T')[0];
                            // ساعت محلی (دو بخش ساعت:دقیقه:ثانیه)
                            time = dt.toTimeString().split(' ')[0];
                        }
                    }
                    return { name, phone, date, time };
                });

                logCache.set(qr, simplified);
                renderTable(tableContainer, simplified);
            } else {
                tableContainer.innerHTML = "<p style='color:red'>لاگی یافت نشد</p>";
            }
        })
        .catch(err => {
            console.error("خطا در دریافت لاگ:", err);
            tableContainer.innerHTML = "<p style='color:red'>خطا در بارگذاری لاگ‌ها</p>";
        });
}

function renderTable(container, rows) {
    if (!rows || rows.length === 0) {
        container.innerHTML = "<p style='color:gray'>لاگ قابل نمایش نیست</p>";
        return;
    }
    let table = `<table>
        <tr>
            <th>ردیف</th>
            <th>نام</th>
            <th>شماره</th>
            <th>تاریخ</th>
            <th>ساعت</th>
        </tr>`;
    rows.forEach((r, idx) => {
        table += `<tr>
            <td>${idx + 1}</td>
            <td>${escapeHtml(r.name)}</td>
            <td>${escapeHtml(r.phone)}</td>
            <td>${escapeHtml(r.date)}</td>
            <td>${escapeHtml(r.time)}</td>
        </tr>`;
    });
    table += `</table>`;

    // دکمه خروجی
    table += `<button id="export" onclick='exportTableToExcel(this)'>دریافت اکسل</button>`;

    container.innerHTML = table;
}

function exportTableToExcel(button) {
    const table = button.previousElementSibling; 
    let csv = [];
    const rows = table.querySelectorAll("tr");
    rows.forEach(row => {
        let cols = row.querySelectorAll("th, td");
        let rowData = [];
        cols.forEach(col => {
            let text = col.innerText.replace(/"/g, '""');
            // شماره و تاریخ را به صورت متن ذخیره کنیم
            if (col.cellIndex === 2 || col.cellIndex === 3) {
                rowData.push('="' + text + '"');
            } else {
                rowData.push('"' + text + '"');
            }
        });
        csv.push(rowData.join(","));
    });

    const csvString = csv.join("\n");
    const bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
    const blob = new Blob([bom, csvString], { type: "text/csv;charset=utf-8;" });

    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "logs.csv";
    link.click();
}




// ساده برای جلوگیری از XSS در جاوااسکریپت
function escapeHtml(str) {
    if (typeof str !== 'string') return '';
    return str.replaceAll('&', '&amp;')
              .replaceAll('<', '&lt;')
              .replaceAll('>', '&gt;')
              .replaceAll('"', '&quot;')
              .replaceAll("'", '&#039;');
}
</script>


</body>
</html>
