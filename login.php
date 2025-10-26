<?php
require_once 'app/session_config.php';

// مرحله فعلی را از سشن بخوانیم یا مقدار پیش‌فرض را مرحله ۱ قرار دهیم
$current_step = $_SESSION['step'] ?? 'step1';
?>
<!DOCTYPE html>
<html lang="fa-ir">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/form.css">
    <style>
        body {
            color: white;
            background-color: #119F89;
        }
        .hidden {
            display: none;
        }
        .main {
            text-align: center;
            padding: 20px;
        }
        .main input, .main button {
            padding: 10px;
            border: none;
            font-size: 16px;
        }
        #sendCodeBtn, #submit {
            border: 1.5px solid gray;
        }
        .otp-container {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 10px;
            direction: ltr;
        }
        .otp-container input {
            width: 40px;
            height: 48px;
            text-align: center;
            font-size: 20px;
            border: none;
            border-radius: 5px;
            direction: ltr;
            unicode-bidi: plaintext;
        }
        #loginForm button {
            width: 60%;
            background-image: none;
            background-color: white;
            color: #119F89;
            margin: 10px auto;
        }
        .change-number {
            background: none;
            border: none;
            color: yellow;
            text-decoration: underline;
            cursor: pointer;
            margin-top: 10px;
        }
        #footer {
            position: fixed;
            bottom: 0;
            margin: auto;
            width: 100%;
        }
        #footer p {
            font-size: 18px;
            text-align: center;
        }
    </style>
</head>
<body dir="rtl">
    <div class="main">
        <img src="image/hoompluslogo.jpg" alt="hoom plus logo" id="logo" style="width:40%">
        <h2 id="heading">پنل اختصاصی مدیر ساختمان</h2>

        <!-- نمایش پیام‌ها -->
        <?php if (!empty($_SESSION['error'])): ?>
            <p style="color:red;"><?php echo $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <p style="color:green;"><?php echo $_SESSION['success']; ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <br>
        <form action="register.php" method="POST" id="loginForm"> 

            <!-- مرحله اول -->
            <div id="step1" class="<?= ($current_step === 'step1') ? '' : 'hidden' ?>">
                <label>ورود با شماره تلفن مالک (admin) دستگاه</label>
                <input type="text" name="username" id="username" placeholder="* شماره تلفن مثال: 09123456789" value="<?= htmlspecialchars($_SESSION['pre_value'] ?? '', ENT_QUOTES) ?>">
                <br><button type="button" id="sendCodeBtn">ارسال کد</button>
            </div>

            <!-- مرحله دوم -->
            <div id="step2" class="<?= ($current_step === 'step2') ? '' : 'hidden' ?>">
                <br><br>
                <label id="otpLabel">
                    کد شش رقمی ارسال شده به شماره 
                    <?= htmlspecialchars($_SESSION['pre_value'] ?? '', ENT_QUOTES) ?>
                </label>
                <div class="otp-container">
                    <input type="text" inputmode="numeric" pattern="\d*" maxlength="1" class="otp" name="otp[]">
                    <input type="text" inputmode="numeric" pattern="\d*" maxlength="1" class="otp" name="otp[]">
                    <input type="text" inputmode="numeric" pattern="\d*" maxlength="1" class="otp" name="otp[]">
                    <input type="text" inputmode="numeric" pattern="\d*" maxlength="1" class="otp" name="otp[]">
                    <input type="text" inputmode="numeric" pattern="\d*" maxlength="1" class="otp" name="otp[]">
                    <input type="text" inputmode="numeric" pattern="\d*" maxlength="1" class="otp" name="otp[]">
                </div>
                <input type="hidden" name="password" id="fullOtp">
                <button type="submit" id="submit" name="action" value="login">ورود</button>
                <button type="button" class="change-number" id="changeNumberBtn" style="background:none; color:white;">تغییر شماره تلفن</button>
            </div>

        </form>
    </div>

    <div id="footer" class="<?= ($current_step === 'step2') ? 'hidden' : '' ?>">
        <p>پشتیبانی واتساپ:</p>
        <p>09026810408</p>
        <p>تلفن پشتیبانی:</p>
        <p>02191303614</p>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const step1 = document.getElementById("step1");
            const step2 = document.getElementById("step2");
            const sendCodeBtn = document.getElementById("sendCodeBtn");
            const changeNumberBtn = document.getElementById("changeNumberBtn");
            const usernameInput = document.getElementById("username");
            const otpInputs = Array.from(document.querySelectorAll(".otp"));
            const fullOtpInput = document.getElementById("fullOtp");
            const heading = document.getElementById("heading");
            const footer = document.getElementById("footer");
            const otpLabel = document.getElementById("otpLabel");
            const loginForm = document.getElementById("loginForm");

            // کمک برای تایپ فقط عدد
            otpInputs.forEach((input) => {
                input.addEventListener("input", (e) => {
                    // فقط رقم اول را نگه دار
                    input.value = input.value.replace(/\D/g, '').slice(0,1);
                });
            });

            otpInputs.forEach((input, index) => {
                input.addEventListener("input", () => {
                    if (input.value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                    updateOtpValue();
                });

                input.addEventListener("keydown", (e) => {
                    if (e.key === "Backspace" && !input.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });

                // پخش خودکار کد هنگام پیست — از ایندکس فعلی شروع می‌شود
                input.addEventListener("paste", (e) => {
                    e.preventDefault();
                    const pasteData = (e.clipboardData || window.clipboardData).getData("text") || "";
                    const digits = pasteData.replace(/\D/g, '').split('');
                    if (digits.length === 0) return;

                    const start = otpInputs.indexOf(input);
                    for (let i = 0; i < digits.length && (start + i) < otpInputs.length; i++) {
                        otpInputs[start + i].value = digits[i];
                    }
                    // پس از قرار دادن، فوکوس را روی اولین خانه خالی بعدی قرار بده
                    const firstEmpty = otpInputs.findIndex((el, i) => i > start && el.value === "");
                    if (firstEmpty !== -1) {
                        otpInputs[firstEmpty].focus();
                    } else {
                        // اگر همه پر شدند، روی آخرین فیلد فوکوس کن
                        otpInputs[Math.min(start + digits.length - 1, otpInputs.length - 1)].focus();
                    }
                    updateOtpValue();
                });
            });

            function updateOtpValue() {
                let otpCode = "";
                otpInputs.forEach(inp => otpCode += inp.value || "");
                fullOtpInput.value = otpCode;
            }

            // مطمئن شو قبل submit مقدار کامل آپدیت شده
            loginForm.addEventListener("submit", function() {
                updateOtpValue();
            });

            sendCodeBtn.addEventListener("click", function() {
                const phone = usernameInput.value.trim();
                if (phone === "") {
                    alert("لطفاً شماره تلفن را وارد کنید");
                    return;
                }

                fetch("register.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=send_code&username=${encodeURIComponent(phone)}`
                })
                .then(res => res.text())
                .then(data => {
                    console.log("Server response:", data);
                    heading.classList.add("hidden");
                    footer.classList.add("hidden");
                    otpLabel.textContent = `کد شش رقمی ارسال شده به شماره ${phone}`;
                    step1.classList.add("hidden");
                    step2.classList.remove("hidden");
                    otpInputs[0].focus();
                })
                .catch(err => {
                    console.error("Error:", err);
                });
            });

            changeNumberBtn.addEventListener("click", function() {
                step2.classList.add("hidden");
                step1.classList.remove("hidden");
                heading.classList.remove("hidden");
                footer.classList.remove("hidden");
                // پاکسازی فیلدهای otp
                otpInputs.forEach(i => i.value = "");
                fullOtpInput.value = "";
            });
        });
    </script>
</body>
</html>
