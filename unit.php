<?php
require_once 'app/auth_check.php';

$unit_num = $_GET['unit_num'] ?? null;
$is_owner = isset($_GET['is_owner']) && $_GET['is_owner'] == '1';
$residents = $_GET['residents'] ?? null;
$parking = $_GET['parking'] ?? null;

$is_edit_mode = $unit_num !== null;
?>
<!DOCTYPE html>
<html lang="fa-ir">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit_mode ? 'ویرایش واحد' : 'افزودن واحد' ?></title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/add.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&icon_names=keyboard_backspace" />
</head>
<body dir="rtl">
    <div class="main">
        <div class="head">
            <h1><?= $is_edit_mode ? 'ویرایش واحد' : 'افزودن واحد' ?></h1>
            <a href="charge.php">
                <span class="material-symbols-outlined">keyboard_backspace</span>
            </a>
        </div>

        <form action="add_unit.php" method="POST" id="editForm">
            
            <input type="number" name="unit_num" id="unit_num" placeholder="شماره واحد  *" required 
                value="<?= htmlspecialchars($unit_num) ?>" <?= $is_edit_mode ? 'readonly' : '' ?> >

            <input type="checkbox" name="is_empty" id="is_empty" class="isEmpty" 
                <?= ($residents == 0 || $residents === null) ? 'checked' : '' ?>>
            <label for="is_empty" class="isEmpty" form="editForm">واحد خالی است</label>

            <input type="checkbox" name="is_owner" id="is_owner" class="isOwner" <?= !$is_owner ? 'checked' : '' ?>>
            <label for="is_owner" class="isOwner" form="editForm">مستأجر است</label>

            <input type="number" name="num_of_residents" id="resident_count" placeholder="تعداد ساکنین *" 
                required <?= ($residents == 0 || $residents === null) ? 'disabled' : '' ?>
                value="<?= htmlspecialchars($residents ?? '') ?>">

            <input type="number" name="num_of_parking" id="parking_count" placeholder="تعداد پارکینگ *" required 
                value="<?= htmlspecialchars($parking ?? '') ?>">

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <?php if ($is_edit_mode): ?>
                <input type="submit" name="action" value="ویرایش واحد">
                <input type="submit" name="action" value="حذف واحد" style="background-color: crimson; color: white;">
            <?php else: ?>
                <input type="submit" name="action" value="افزودن واحد">
            <?php endif; ?>

        </form>
    </div>

    <script>
    document.getElementById("is_empty").addEventListener("change", function () {
        let disable = this.checked;
        document.getElementById("resident_count").disabled = disable;
    });
    </script>
</body>
</html>
