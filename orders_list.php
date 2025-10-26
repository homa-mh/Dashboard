<?php
require_once 'app/auth_check.php';
if ($_SESSION['user_info']['type'] !== 1) {
    header("Location: index.php");
    die();
}

require_once 'app/Dbh.php';

$db = new Dbh();
$conn = $db->connect();

$stmt = $conn->prepare('SELECT user.first_name , user.last_name , user.phone, pay.id as `order_id`, pay.product, pay.amount, pay.ref_id, pay.paid_at 
    FROM pay 
    INNER JOIN user ON user.id = pay.user_id 
    ORDER BY pay.paid_at DESC');
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لیست سفارشات</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="max-w-6xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">لیست سفارشات</h1>
        <div class="flex gap-3">
            <a href="index.php" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">بازگشت</a>
            <a  onclick="exportTableToCSV()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">📊 خروجی اکسل</a>
        </div>
    </div>

    <div class="overflow-x-auto bg-white rounded-xl shadow-md">
        <table class="min-w-full text-sm text-gray-700 border">
            <thead class="bg-teal-500 text-white sticky top-0">
                <tr>
                    <th class="px-4 py-3">ردیف</th>
                    <th class="px-4 py-3">نام کاربر</th>
                    <th class="px-4 py-3">شماره تلفن</th>
                    <th class="px-4 py-3">کد سفارش</th>
                    <th class="px-4 py-3">محصول</th>
                    <th class="px-4 py-3">مبلغ (تومان)</th>
                    <th class="px-4 py-3">کد پیگیری</th>
                    <th class="px-4 py-3">زمان ثبت سفارش</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $index => $row): ?>
                <tr class="border-b hover:bg-gray-100">
                    <td class="px-4 py-2 text-center"><?= $index + 1 ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['phone']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['order_id']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['product']) ?></td>
                    <td class="px-4 py-2"><?= number_format($row['amount']/10) ?> </td>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['ref_id']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['paid_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        
    </div>
</div>


<script>
function exportTableToCSV(filename = 'orders.csv') {
  const table = document.querySelector('table');
  const rows = Array.from(table.querySelectorAll('tr'));
  if (!rows.length) return;

  // تیتر ستون‌ها
  const headers = Array.from(rows[0].querySelectorAll('th')).map(th => th.innerText.trim());

  // ستون‌هایی که باید متن باشند
  const textCols = new Set(['شماره تلفن', 'کد پیگیری', 'زمان ثبت سفارش']);

  const lines = rows.map((row, rIdx) => {
    const cells = Array.from(row.querySelectorAll('th,td'));
    return cells.map((cell, cIdx) => {
      const header = headers[cIdx] || '';
      let text = (cell.innerText || '').replace(/\r?\n|\r/g, ' ').trim();

      // اگر ردیف دیتا است و ستون باید متن باشد، اکسل را مجبور به متن می‌کنیم
      if (rIdx > 0 && textCols.has(header)) {
        if (header === 'شماره تلفن' || header === 'کد پیگیری') {
          // فقط رقم‌ها را نگه می‌داریم تا Injection رخ نده
          text = text.replace(/[^\d]/g, '');
        }
        // ترفند اکسل: ="متن"
        return '="' + text.replace(/"/g, '""') + '"';
      }

      // حالت عادی: CSV استاندارد با کوتیشن و Escape
      return '"' + text.replace(/"/g, '""') + '"';
    }).join(',');
  }).join('\r\n');

  // BOM برای UTF-8 تا فارسی خراب نشه
  const csv = '\uFEFF' + lines;

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
}
</script>


</body>
</html>
