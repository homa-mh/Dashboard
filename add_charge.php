<?php
require_once 'app/auth_check.php';
require_once 'app/controllers/ChargeCtrl.php';
$ctrl = new ChargeCtrl();
$units = $ctrl->get_units($_SESSION['user_info']['id']);
$factors = $ctrl->get_factors($_SESSION['user_info']['id']);
$bills = $ctrl->get_bills($_SESSION['user_info']['id']);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/add_charge.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=keyboard_backspace" />
    <title>فرم چند مرحله‌ای شارژ</title>
</head>


<body dir="rtl">
    
    <div class="main">
        <div class="head">
            <h1>ثبت شارژ</h1>
            <a href="charge.php"><span class="material-symbols-outlined">keyboard_backspace</span></a>
        </div> 
  
        <form id="multiStepForm" action="send_charge.php" method="post">
        
            <!-- Step 1: انتخاب ماه‌ها -->
            <div class="form-step active" id="step-1">
                <h4>انتخاب ماه‌ها</h4>
                
                <div class="checkbox-group">
                    <div class="checkbox-item">
                      <input type="checkbox" id="month-farvardin" name="months[]" value="فروردین">
                      <label for="month-farvardin">فروردین</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="month-ordibehesht" name="months[]" value="اردیبهشت">
                      <label for="month-ordibehesht">اردیبهشت</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="month-khordad" name="months[]" value="خرداد">
                      <label for="month-khordad">خرداد</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="month-tir" name="months[]" value="تیر">
                      <label for="month-tir">تیر</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="month-mordad" name="months[]" value="مرداد">
                      <label for="month-mordad">مرداد</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="month-shahrivar" name="months[]" value="شهریور">
                      <label for="month-shahrivar">شهریور</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="month-mehr" name="months[]" value="مهر">
                      <label for="month-mehr">مهر</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="month-aban" name="months[]" value="آبان">
                      <label for="month-aban">آبان</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="month-azar" name="months[]" value="آذر">
                      <label for="month-azar">آذر</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="month-dey" name="months[]" value="دی">
                      <label for="month-dey">دی</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="month-bahman" name="months[]" value="بهمن">
                      <label for="month-bahman">بهمن</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="month-esfand" name="months[]" value="اسفند">
                      <label for="month-esfand">اسفند</label>
                    </div>
                </div>
            </div>
          

            <!-- Step 2: انتخاب روش محاسبه -->

            <div class="form-step" id="step-2">
                <h3>روش محاسبه</h3>
            
                <div class="radio-group">
                    <div class="radio-item">
                      <input type="radio" id="1" name="method" value="equal" checked>
                      <label for="1">مساوی بین همه</label>
                    </div>
            
                    <div class="radio-item">
                      <input type="radio" id="2" name="method" value="custom">
                      <label for="2">وارد کردن دستی</label>
                    </div>
            
                    <div class="radio-item">
                      <input type="radio" id="3" name="method" value="resident">
                      <label for="3">محاسبه بر اساس تعداد ساکنین</label>
                    </div>
            
                    <div class="radio-item">
                      <input type="radio" id="4" name="method" value="resident_parking">
                      <label for="4">محاسبه بر اساس تعداد ساکنین و تعداد پارکینگ</label>
                    </div>
                </div>
            </div>

            <!-- Step 3: شارژ واحدها -->
            <div class="form-step" id="step-3">
                <h3>مقدار شارژ هر واحد</h3>

                <!-- شارژ ثابت -->
                <label for="fixedCharge"><strong><h5>شارژ ثابت (برای همه واحدها):</h5></strong></label>
                <input type="text" id="fixedCharge" name="fixed_charge" value="0" placeholder="* تومان" style="margin-bottom: 15px; width:60%;">
                <label for="fixedCharge">تومان</label>
  

                <!-- فاکتورها و قبوض با چک‌باکس -->
                <div style="margin-top: 15px;">
                    <h5>انتخاب فاکتورها و قبوض:</h5>
                    <?php
                    $charge = 0;
                    $index = 0;
                
                    if (!empty($bills)) {
                      foreach ($bills as $i => $bill) {
                          $amount = $bill["amount"] / 10;
                          echo '<label><input type="checkbox" class="charge-item" data-amount="' . $amount . '" checked> قبض: ' . htmlspecialchars($bill["title"]) . ' - ' . number_format($amount) . ' تومان</label><br>';
                        
                          echo '<input type="hidden" name="billtitle' . $i . '" value="' . htmlspecialchars($bill["title"]) . '">';
                          echo '<input type="hidden" name="billamount' . $i . '" value="' . $amount . '">';
                        }
                
                    }
                
                    if (!empty($factors)) {
                      foreach ($factors as $i => $factor) {
                          echo '<label><input type="checkbox" class="charge-item" data-amount="' . $factor["amount"] . '" checked> فاکتور: ' . htmlspecialchars($factor["title"]) . ' - ' . number_format($factor["amount"]) . ' تومان</label><br>';
                        
                          echo '<input type="hidden" name="factortitle' . $i . '" value="' . htmlspecialchars($factor["title"]) . '">';
                          echo '<input type="hidden" name="factordescription' . $i . '" value="' . htmlspecialchars($factor["description"] ?? '-') . '">';
                          echo '<input type="hidden" name="factoramount' . $i . '" value="' . $factor["amount"] . '">';
                        }
                
                    }
                
                    if (empty($bills) && empty($factors)) {
                      echo '<p>هیچ فاکتور یا قبضی ثبت نشده است.</p>';
                    }
                    ?>
                </div>

                <!-- هزینه کل و input hidden -->
                <h5 id="total-display">هزینه ساختمان : <?= number_format($charge) ?> تومان</h5>
                <input type="hidden" id="totalCharge" name="total" value="<?= $charge ?>">
            
                <!-- جدول شارژ واحدها -->
                <table style="margin-top: 20px;">
                    <tr><th>واحد</th><th>مقدار شارژ</th></tr>
                    <?php
                    if (!empty($units)) {
                      foreach ($units as $unit) {
                        echo '<tr><td>واحد ' . htmlspecialchars($unit["unit_num"]) . '</td>';
                        echo '<td><input type="text" name="unit' . htmlspecialchars($unit["unit_num"]) . '_charge"
                                      class="charge-input"
                                      data-residents="' . $unit["num_of_residents"] . '"
                                      data-parking="' . $unit["num_of_parking"] . '"></td></tr>';
                      }
                    }
                    ?>
                </table>
            </div>

            <!-- Step 4: تایید نهایی -->
            <div class="form-step" id="step-4">
                <h3>تایید نهایی</h3>
                <div id="confirmation-summary">
                    <h3>پیش‌نمایش شارژ ثبت‌شده</h3>
                    <table id="previewTable">
                        <thead>
                            <tr>
                              <th>شماره واحد</th>
                              <th>مقدار شارژ (تومان)</th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- پر می‌شود با جاوااسکریپت -->
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="submit" value="ثبت نهایی">
            </div>

            <!-- Navigation Buttons -->
            <div class="navigation">
                <button type="button" id="prevBtn">قبلی</button>
                <button type="button" id="nextBtn">بعدی</button>
            </div>

        </form>
    </div>


<!-- جاوااسکریپت برای نمایش/مخفی‌سازی -->
<script>
    function toggleFactors() {
        var container = document.getElementById("factors-container");
        if (container.style.display === "none") {
            container.style.display = "block";
        } else {
            container.style.display = "none";
        }
    }
</script>

<script>
let currentStep = 0;
const steps = document.querySelectorAll(".form-step");

function showStep(index) {
    steps.forEach((step, i) => {
    step.classList.toggle("active", i === index);
  });
    document.getElementById("prevBtn").style.display = index === 0 ? "none" : "inline-block";
    document.getElementById("nextBtn").style.display = index === steps.length - 1 ? "none" : "inline-block";

    document.getElementById("nextBtn").textContent = index === steps.length - 1 ? "مشاهده تایید" : "بعدی";
}

document.getElementById("prevBtn").addEventListener("click", () => {
    if (currentStep > 0) {
        currentStep--;
        showStep(currentStep);
    }
});


// محاسبه جمع فاکتورها و قبوض انتخاب‌شده
function updateTotalCharge() {
    let total = 0;
    document.querySelectorAll('.charge-item:checked').forEach(item => {
        total += parseInt(item.dataset.amount || "0");
    });
    
    document.getElementById("total-display").textContent = "هزینه ساختمان : " + total.toLocaleString() + " تومان";
    document.getElementById("totalCharge").value = total;
}


function recalculateUnitCharges() {
    const method = document.querySelector('input[name="method"]:checked').value;
    const totalCharge = parseInt(document.getElementById("totalCharge").value || "0");
    const fixedCharge = parseInt(document.getElementById("fixedCharge").value || "0");

    const unitInputs = document.querySelectorAll(".charge-input");
    
    if (method === "equal") {
        const perUnit = Math.floor((totalCharge  / unitInputs.length) + fixedCharge);
        unitInputs.forEach(input => {
            input.value = perUnit;
        });
    } else if (method === "resident") {
        let totalResidents = 0;
        unitInputs.forEach(input => {
          totalResidents += parseInt(input.dataset.residents || "0");
        });
    unitInputs.forEach(input => {
      const res = parseInt(input.dataset.residents || "0");
      const share = res > 0 ? Math.floor(((res / totalResidents) * totalCharge)) : 0;
      input.value = share + fixedCharge;
    });
    } else if (method === "resident_parking") {
    let totalWeight = 0;
    unitInputs.forEach(input => {
      totalWeight += parseInt(input.dataset.residents || "0") + parseInt(input.dataset.parking || "0");
    });
    unitInputs.forEach(input => {
      const weight = parseInt(input.dataset.residents || "0") + parseInt(input.dataset.parking || "0");
      const share = weight > 0 ? Math.floor(((weight / totalWeight) * totalCharge)) : 0;
      input.value = share + fixedCharge;
    });
    } else if (method === "custom") {
    // دستی وارد میشه => تغییر نده
    }
}


// به‌روزرسانی هزینه کل هنگام تغییر چک‌باکس
document.querySelectorAll('.charge-item').forEach(cb => {
  cb.addEventListener('change', updateTotalCharge);
});

document.getElementById("nextBtn").addEventListener("click", () => {
  if (currentStep < steps.length - 1) {
    const method = document.querySelector('input[name="method"]:checked').value;

    if (currentStep === 1) {
      const fixedCharge = parseInt(document.getElementById("fixedCharge").value || "0");
      const totalCharge = parseInt(document.getElementById("totalCharge").value || "0");
      const dynamicCharge = totalCharge;
      const chargeInputs = document.querySelectorAll('.charge-input');

      if (method === "equal") {
        const equalShare = Math.round(dynamicCharge / chargeInputs.length);
        chargeInputs.forEach(input => input.value = equalShare + fixedCharge);

      } else if (method === "resident") {
        let totalResidents = 0;
        chargeInputs.forEach(input => {
          totalResidents += parseInt(input.dataset.residents || "0");
        });

        chargeInputs.forEach(input => {
          const residents = parseInt(input.dataset.residents || "0");
          const share = Math.round((residents / totalResidents) * dynamicCharge);
          input.value = share + fixedCharge;
        });

      } else if (method === "resident_parking") {
        let totalWeight = 0;
        chargeInputs.forEach(input => {
          const residents = parseInt(input.dataset.residents || "0");
          const parking = parseInt(input.dataset.parking || "0");
          totalWeight += residents + parking;
        });

        chargeInputs.forEach(input => {
          const residents = parseInt(input.dataset.residents || "0");
          const parking = parseInt(input.dataset.parking || "0");
          const weight = residents + parking;
          const share = Math.round((weight / totalWeight) * dynamicCharge);
          input.value = share + fixedCharge;
        });

      } else if (method === "custom") {
        chargeInputs.forEach(input => input.value = fixedCharge);
      }
    }

    currentStep++;
    if (currentStep === steps.length - 1) {
      generateConfirmation();
    }

    showStep(currentStep);
  }
});

// پیش‌نمایش نهایی
function generateConfirmation() {
  const months = Array.from(document.querySelectorAll('input[name="months[]"]:checked')).map(cb => cb.value);
  const method = document.querySelector('input[name="method"]:checked').value;
  const charges = Array.from(document.querySelectorAll('.charge-input')).map(input => input.value);
  const fixedCharge = document.getElementById("fixedCharge").value;

  const summaryDiv = document.getElementById("confirmation-summary");
  summaryDiv.innerHTML = `
    <p><strong>ماه‌ها:</strong> ${months.join(', ')}</p>
    <p><strong>روش محاسبه:</strong> ${method}</p>
    <p><strong>شارژ ثابت:</strong> ${fixedCharge} تومان</p>
    <p><strong>مقادیر شارژ:</strong></p>
    <ul>${charges.map((c, i) => `<li>واحد ${i + 1}: ${c} تومان</li>`).join('')}</ul>
  `;
}

showStep(currentStep);


// وقتی فاکتور یا قبض تغییر کرد
document.querySelectorAll('.charge-item').forEach(item => {
  item.addEventListener('change', () => {
    updateTotalCharge();
    recalculateUnitCharges();
  });
});

// وقتی روش محاسبه تغییر کرد
document.querySelectorAll('input[name="method"]').forEach(item => {
  item.addEventListener('change', () => {
    recalculateUnitCharges();
  });
});

// وقتی شارژ ثابت تغییر کرد
document.getElementById("fixedCharge").addEventListener("input", () => {
  recalculateUnitCharges();
});

// به‌روزرسانی اولیه
updateTotalCharge();
recalculateUnitCharges();

</script>

</body>
</html>
