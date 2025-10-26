<?php 
require_once 'app/auth_check.php';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فرم گارانتی</title>
    <link rel="stylesheet" href="css/base.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        canvas {
            border: 2px solid #ccc;
            border-radius: 0.5rem;
            touch-action: none;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body>

<div class="main container mx-auto p-4 w-full max-w-md">

    <?php require_once 'header.php'; ?>

    <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
        <div class="flex items-center justify-between mb-6">
            <img class="material-icons" src="image/icons/menu.svg" onclick="document.querySelector('.menu').classList.toggle('active')" style="cursor:pointer;">
            <h1 class="text-xl font-bold mx-auto text-center">فرم گارانتی</h1>
        </div>

        <!-- مرحله ۱: اطلاعات مشتری -->
        <div id="step1">
            <form id="formStep1" class="space-y-3">
                <label class="block">
                    <span>نام و نام خانوادگی مشتری:</span>
                    <input type="text" name="customer_name" required class="w-full border rounded-lg p-2 mt-1">
                </label>

                <label class="block">
                    <span>QR Code دستگاه:</span>
                    <input type="text" name="device_qr" required class="w-full border rounded-lg p-2 mt-1">
                </label>

                <label class="block">
                    <span>نام نصاب:</span>
                    <input type="text" name="installer_name" required class="w-full border rounded-lg p-2 mt-1">
                </label>

                <button type="button" id="nextBtn1"
                    class="bg-green-700 text-white px-4 py-2 rounded-lg w-full hover:bg-green-800 mt-4">
                    ادامه
                </button>
            </form>
        </div>

        <!-- مرحله ۲: چک‌لیست -->
        <div id="step2" class="hidden">
            <form id="formStep2" class="space-y-3">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="received" class="w-4 h-4 text-blue-600">
                    آیا محصول دریافت شد؟
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="users_added" class="w-4 h-4 text-blue-600">
                    آیا کاربران اضافه شدند؟
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="trained" class="w-4 h-4 text-blue-600">
                    آیا آموزش داده شد؟
                </label>

                <button type="button" id="nextBtn2"
                    class="bg-green-700 text-white px-4 py-2 rounded-lg w-full hover:bg-green-800 mt-4">
                    ادامه
                </button>
            </form>
        </div>

        <!-- مرحله ۳: امضا -->
        <div id="step3" class="hidden">
            <h2 class="text-lg font-semibold mb-4">امضا:</h2>
            <canvas id="signaturePad" width="300" height="200"></canvas>
            <div class="flex justify-between mt-4">
                <button id="clearBtn" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">پاک کردن</button>
                <button id="submitBtn" class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-800">ارسال فرم</button>
            </div>
        </div>
    </div>
</div>

<script>
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const nextBtn1 = document.getElementById('nextBtn1');
    const nextBtn2 = document.getElementById('nextBtn2');
    const canvas = document.getElementById('signaturePad');
    const ctx = canvas.getContext('2d');
    let drawing = false;
    let formDataAll = {};

    nextBtn1.onclick = () => {
        const form = new FormData(document.getElementById('formStep1'));
        Object.assign(formDataAll, Object.fromEntries(form.entries()));
        step1.classList.add('hidden');
        step2.classList.remove('hidden');
    };

    nextBtn2.onclick = () => {
        const form = new FormData(document.getElementById('formStep2'));
        Object.assign(formDataAll, Object.fromEntries(form.entries()));
        step2.classList.add('hidden');
        step3.classList.remove('hidden');
    };

    // رسم امضا
    const pos = e => {
        const rect = canvas.getBoundingClientRect();
        return { x: e.clientX - rect.left, y: e.clientY - rect.top };
    };
    function draw(e) {
        if (!drawing) return;
        const { x, y } = pos(e);
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';
        ctx.lineTo(x, y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x, y);
    }
    canvas.onmousedown = e => { drawing = true; draw(e); };
    canvas.onmouseup = () => { drawing = false; ctx.beginPath(); };
    canvas.onmousemove = draw;
    canvas.ontouchstart = e => { drawing = true; draw(e.touches[0]); };
    canvas.ontouchmove = e => { draw(e.touches[0]); e.preventDefault(); };
    canvas.ontouchend = () => { drawing = false; ctx.beginPath(); };

    document.getElementById('clearBtn').onclick = () => ctx.clearRect(0, 0, canvas.width, canvas.height);

    document.getElementById('submitBtn').onclick = async () => {
        const data = new FormData();
        for (let k in formDataAll) data.append(k, formDataAll[k]);
        data.append('signature', canvas.toDataURL('image/png'));

        try {
            const res = await fetch('garanti_submit.php', { method: 'POST', body: data });
            const result = await res.json();
            alert(result.message);
            if (result.success) location.reload();
        } catch {
            alert('❌ خطا در ارتباط با سرور. لطفاً مجدداً تلاش کنید.');
        }
    };
</script>
</body>
</html>
