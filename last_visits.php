<?php
require_once 'app/Dbh.php';

$db = new Dbh();
$conn = $db->connect();

$phoneFilter = isset($_GET['phone']) ? trim($_GET['phone']) : '';

if ($phoneFilter !== '') {
    $sql = "
        SELECT l.phone, COUNT(*) AS total_visits, MAX(l.time) AS last_visit,
               u.first_name, u.last_name, u.address
        FROM login_logs l
        LEFT JOIN user u ON l.phone = u.phone
        WHERE l.phone LIKE ?
        GROUP BY l.phone, u.first_name, u.last_name, u.address
        ORDER BY last_visit DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute(["%{$phoneFilter}%"]);
} else {
    $sql = "
        SELECT l.phone, COUNT(*) AS total_visits, MAX(l.time) AS last_visit,
               u.first_name, u.last_name, u.address
        FROM login_logs l
        LEFT JOIN user u ON l.phone = u.phone
        GROUP BY l.phone, u.first_name, u.last_name, u.address
        ORDER BY last_visit DESC
    ";
    $stmt = $conn->query($sql);
}

$visits = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalNumbers = count($visits);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>بازدیدهای سایت</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body class="bg-gray-100 font-sans">

    <div class="max-w-5xl mx-auto p-4">
        <!-- سربرگ -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl md:text-2xl font-bold text-gray-700">لیست بازدید کاربران</h1>
            <a href="index.php" class="text-gray-600 hover:text-gray-900">
                <span class="material-symbols-outlined">keyboard_backspace</span>
            </a>
        </div>

        <!-- فرم فیلتر -->
        <form method="get" class="flex flex-col sm:flex-row gap-2 sm:gap-4 mb-4">
            <input 
                type="text" 
                name="phone" 
                placeholder="جستجو بر اساس تلفن..." 
                value="<?= htmlspecialchars($phoneFilter) ?>"
                class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400"
            >
            <div class="flex gap-2">
                <button type="submit" 
                    class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600">
                    فیلتر
                </button>
                <a href="visits.php" 
                    class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 text-center">
                    نمایش همه
                </a>
            </div>
        </form>

        <!-- تعداد کاربران + دکمه خروجی -->
        <div class="flex items-center justify-between mb-3">
            <div class="font-medium text-gray-700">
                تعداد شماره‌ها: <?= $totalNumbers ?>
            </div>
            <div class="flex gap-2">
                <button id="exportCsvBtn" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    📊 خروجی اکسل (CSV)
                </button>
                <button id="exportXlsxBtn" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    📘 خروجی اکسل (XLSX)
                </button>
            </div>
        </div>

        <!-- جدول -->
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table id="visitsTable" class="min-w-full text-sm text-center">
                <thead class="bg-teal-500 text-white sticky top-0">
                    <tr>
                        <th class="py-3 px-4">ردیف</th>
                        <th class="py-3 px-4">نام</th>
                        <th class="py-3 px-4">تلفن همراه</th>
                        <th class="py-3 px-4">آدرس</th>
                        <th class="py-3 px-4">تعداد بازدید</th>
                        <th class="py-3 px-4">آخرین بازدید</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalNumbers > 0): ?>
                        <?php foreach ($visits as $index => $row): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="py-2 px-4"><?= $index + 1 ?></td>
                                <td class="py-2 px-4">
                                    <?= htmlspecialchars(trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))) ?: '---' ?>
                                </td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['phone']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['address']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['total_visits']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['last_visit']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-3 text-gray-500">هیچ رکوردی یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- اسکریپت‌های خروجی -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
    (function () {
      // کمک: ستون‌هایی که نباید اکسل آنها را به عدد/تاریخ تبدیل کند
      const textCols = new Set(['تلفن همراه', 'آخرین بازدید']);

      function downloadBlob(filename, blob) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
      }

      // CSV export
      function exportTableToCSV(filename = 'visits.csv') {
        const table = document.getElementById('visitsTable');
        const headerCells = table.querySelectorAll('thead th');
        const headers = Array.from(headerCells).map(th => th.innerText.trim());
        const dataRows = table.querySelectorAll('tbody tr');

        const csvRows = [];
        // header row
        csvRows.push(headers.map(h => '"' + h.replace(/"/g, '""') + '"').join(','));

        dataRows.forEach(row => {
          const cells = row.querySelectorAll('td');
          if (!cells.length) return;
          const rowData = Array.from(cells).map((cell, idx) => {
            const header = headers[idx] || '';
            let text = (cell.innerText || '').replace(/\r?\n|\r/g, ' ').trim();
            // اگر ستون باید متن باشد، از فرمت ="... " استفاده می‌کنیم تا اکسل آن را متن بداند
            if (textCols.has(header)) {
              text = text.replace(/"/g, '""');
              return '="' + text + '"';
            }
            return '"' + text.replace(/"/g, '""') + '"';
          });
          csvRows.push(rowData.join(','));
        });

        const csvString = '\uFEFF' + csvRows.join('\r\n'); // BOM برای UTF-8
        const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        downloadBlob(filename, blob);
      }

      // XLSX export using SheetJS (CDN)
      function exportTableToXLSX(filename = 'visits.xlsx') {
        const table = document.getElementById('visitsTable');
        const headerCells = table.querySelectorAll('thead th');
        const headers = Array.from(headerCells).map(th => th.innerText.trim());
        const dataRows = table.querySelectorAll('tbody tr');

        // ساخت آرایه‌ی دو بعدی (AOA)
        const aoa = [];
        aoa.push(headers);
        dataRows.forEach(row => {
          const cells = row.querySelectorAll('td');
          if (!cells.length) return;
          aoa.push(Array.from(cells).map(td => (td.innerText || '').replace(/\r?\n|\r/g, ' ').trim()));
        });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(aoa);

        // اجباری کردن نوع متن برای ستون‌های حساس
        const phoneIdx = headers.indexOf('تلفن همراه');
        const dateIdx = headers.indexOf('آخرین بازدید');
        const forceTextCols = [phoneIdx, dateIdx].filter(i => i >= 0);

        for (let r = 1; r < aoa.length; r++) {
          for (const c of forceTextCols) {
            const cellAddress = XLSX.utils.encode_cell({ r: r, c: c });
            const val = aoa[r][c] || '';
            ws[cellAddress] = { t: 's', v: val };
          }
        }

        // تنظیم عرض ستون‌ها (اختیاری)
        ws['!cols'] = headers.map(() => ({ wch: 20 }));

        XLSX.utils.book_append_sheet(wb, ws, 'بازدیدها');
        XLSX.writeFile(wb, filename);
      }

      document.getElementById('exportCsvBtn').addEventListener('click', function (e) {
        e.preventDefault();
        exportTableToCSV();
      });

      document.getElementById('exportXlsxBtn').addEventListener('click', function (e) {
        e.preventDefault();
        exportTableToXLSX();
      });
    })();
    </script>
</body>
</html>
