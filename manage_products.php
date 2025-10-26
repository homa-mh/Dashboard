<?php
require_once 'app/auth_check.php';
if ($_SESSION['user_info']['type'] !== 1) {
    header("Location: index.php");
    die();
}

require_once 'app/controllers/ProductController.php';
$ctrl = new ProductController();
$products = $ctrl->get_products();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>مدیریت محصولات</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body class="bg-gray-100 min-h-screen p-4">

<div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-md">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">مدیریت محصولات</h1>
        <a href="index.php" class="text-gray-700 hover:text-gray-900 flex items-center">
            <span class="material-symbols-outlined mr-1">keyboard_backspace</span>بازگشت
        </a>
    </div>

    <!-- جدول محصولات -->
    <div class="overflow-x-auto">
        <table class="w-full border border-gray-300 rounded-md overflow-hidden">
            <thead class="bg-gray-200 text-sm">
                <tr>
                    <th class="p-2 border-b">ID</th>
                    <th class="p-2 border-b">تصویر</th>
                    <th class="p-2 border-b">نام محصول</th>
                    <th class="p-2 border-b">قیمت (ریال)</th>
                    <th class="p-2 border-b">توضیحات</th>
                    <th class="p-2 border-b">ویرایش</th>
                    <th class="p-2 border-b">حذف</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p) { ?>
                <tr data-id="<?= $p['id'] ?>" class="text-center border-b hover:bg-gray-50">
                    <td class="p-2"><?= htmlspecialchars($p['id']) ?></td>
                    <td class="p-2">
                        <?php if (!empty($p['image'])): ?>
                            <img src="<?= htmlspecialchars($p['image']) ?>" alt="img" class="w-16 h-16 object-cover mx-auto rounded">
                        <?php else: ?>
                            <span class="text-gray-400 text-sm">بدون تصویر</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-2">
                        <input type="text" value="<?= htmlspecialchars($p['title']) ?>" disabled class="w-full text-center border rounded px-1 py-0.5 bg-gray-100 disabled:bg-gray-100">
                    </td>
                    <td class="p-2">
                        <input type="text" value="<?= htmlspecialchars($p['price']) ?>" disabled class="w-full text-center border rounded px-1 py-0.5 bg-gray-100 disabled:bg-gray-100">
                    </td>
                    <td class="p-2">
                        <textarea disabled class="w-full text-center border rounded px-1 py-0.5 bg-gray-100 disabled:bg-gray-100 text-sm"><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
                    </td>
                    <td class="p-2">
                        <button class="edit-btn bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">ویرایش</button>
                    </td>
                    <td class="p-2">
                        <button class="delete-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">حذف</button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- فرم افزودن محصول -->
    <form id="addForm" class="add-form mt-6 p-4 border rounded-md bg-gray-50" enctype="multipart/form-data">
        <h3 class="text-lg font-semibold mb-2">افزودن محصول جدید</h3>
        <div class="flex flex-wrap gap-2 items-center">
            <input type="text" name="title" placeholder="نام محصول" class="border rounded px-2 py-1 flex-1 min-w-[150px]" required>
            <input type="number" name="price" placeholder="قیمت" class="border rounded px-2 py-1 w-32" required>
            <select name="category" class="border rounded px-2 py-1 w-40">
                <option value="repair">تعمیر</option>
                <option value="install">نصب</option>
                <option value="update">آپدیت</option>
                <option value="transfer">انتقال</option>
                <option value="sub">اشتراک</option>
                <option value="user">خرید کاربر</option>
                <option value="user_feedback">فروشگاه</option>
            </select>
            <textarea name="description" placeholder="توضیحات" class="border rounded px-2 py-1 flex-1 min-w-[200px]"></textarea>
            <input type="file" name="image" accept="image/*" class="border rounded px-2 py-1">
            <button type="submit" class="bg-green-500 text-white px-4 py-1 rounded hover:bg-green-600">افزودن</button>
        </div>
    </form>
</div>

<script>
function postData(formData) {
    return fetch("ajax_products.php", {
        method: "POST",
        body: formData
    }).then(res => res.json())
      .catch(err => {
        alert("خطای ارتباط با سرور: " + err);
        console.error(err);
    });
}

// افزودن محصول
document.getElementById("addForm").addEventListener("submit", e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append("action", "add");

    postData(formData).then(data => {
        if (data?.success) {
            alert("✅ محصول افزوده شد");
            location.reload();
        } else alert("❌ خطا در افزودن: " + (data?.error || ""));
    });
});

// ویرایش محصول
document.querySelectorAll(".edit-btn").forEach(btn => {
    btn.addEventListener("click", e => {
        const row = e.target.closest("tr");
        const id = row.dataset.id;
        const titleInput = row.querySelector('input[name="title"]') || row.querySelector('input[type="text"]');
        const priceInput = row.querySelectorAll('input[type="text"]')[1];
        const descInput = row.querySelector('textarea');

        // اگر ورودی غیرفعال است، آن را فعال کن برای ویرایش
        if (titleInput.disabled) {
            titleInput.disabled = false;
            priceInput.disabled = false;
            descInput.disabled = false;
            e.target.textContent = "ذخیره";
            e.target.classList.remove("bg-blue-500");
            e.target.classList.add("bg-green-500");
        } else {
            // ارسال به سرور
            const formData = new FormData();
            formData.append("action", "update");
            formData.append("id", id);
            formData.append("title", titleInput.value);
            formData.append("price", priceInput.value);
            formData.append("description", descInput.value);

            postData(formData).then(data => {
                if (data?.success) {
                    alert("✅ محصول بروزرسانی شد");
                    titleInput.disabled = true;
                    priceInput.disabled = true;
                    descInput.disabled = true;
                    e.target.textContent = "ویرایش";
                    e.target.classList.remove("bg-green-500");
                    e.target.classList.add("bg-blue-500");
                } else alert("❌ خطا در بروزرسانی: " + (data?.error || ""));
            });
        }
    });
});

// حذف محصول
document.querySelectorAll(".delete-btn").forEach(btn => {
    btn.addEventListener("click", e => {
        if (!confirm("آیا مطمئن هستید می‌خواهید این محصول را حذف کنید؟")) return;

        const row = e.target.closest("tr");
        const id = row.dataset.id;

        const formData = new FormData();
        formData.append("action", "delete");
        formData.append("id", id);

        postData(formData).then(data => {
            if (data?.success) {
                alert("✅ محصول حذف شد");
                row.remove();
            } else alert("❌ خطا در حذف: " + (data?.error || ""));
        });
    });
});
</script>

</body>
</html>
