<div class="menu">
    <div class="profile">
        <div class="profileDetail">
            <a href="index.php"><img src="image/hoom_logo.png"></a>
            <img class="material-icons" src="image/icons/back_white.svg" onclick="document.querySelector('.menu').classList.toggle('active')" style="cursor:pointer;">
        </div>
    </div>

    <div class="menu-content">
        <!-- بخش ۱: حساب کاربری -->
        <div class="menu-section">
            <h3>حساب کاربری</h3>
            <ul>
                <a href="profile.php" class="text-black"><li><img class="material-icons" src="image/icons/person.svg">حساب کاربری (<?= $_SESSION['user_info']['first_name']." ".$_SESSION['user_info']['last_name'] ?>)</li></a>
                <a href="info.php" class="text-black"><li><img class="material-icons" src="image/icons/info.svg">کاربران هوم</li></a>
                <a href="device_info.php" class="text-black"><li><img class="material-icons" src="image/icons/info.svg">اطلاعات دستگاه ها</li></a>

                <a href="logout.php" class="text-black"><li><img class="material-icons" src="image/icons/logout.svg"> خروج</li></a>
            </ul>
        </div>
        
        <!-- بخش ۲: خدمات -->
        <div class="menu-section">
            <h3>خدمات مدیر ساختمان</h3>
            <ul>
                <!--<a href="charge.php"><li><img class="material-icons" src="image/icons/person.svg">payments شارژ ساختمان</li></a>-->
                <!--<a href="factor.php"><li><img class="material-icons" src="image/icons/person.svg">receipt_long ثبت فاکتور</li></a>-->
                <!--<a href="bill.php"><li><img class="material-icons" src="image/icons/person.svg">request_quote پیگیری قبض</li></a>-->
                <a href="https://sanjagh.pro/b/107?utm_source=smarthome&utm_medium=basketpage&utm_campaign=buildingManager" class="text-black"><li><img class="material-icons" src="image/icons/service.svg">خدمات تعمیر و نگهداری ساختمان</li></a>
                <a href="https://hamsadeha.com/" class="text-black"><li><img class="material-icons" src="image/icons/money.svg">مدیریت شارژ ساختمان</li></a>
                <a href="usage_logs.php" class="text-black"><li><img class="material-icons" src="image/icons/monitor.svg"> کنترل ورود و خروج</li></a>
            </ul>
        </div>

        <!-- بخش ۳: پشتیبانی -->
        <div class="menu-section">
            <h3>پشتیبانی</h3>
            <ul>
                <a href="fix.php" class="text-black"><li><img class="material-icons" src="image/icons/repair_service.svg"> درخواست تعمیرکار</li></a>
                <a href="constructor.php" class="text-black"><li><img class="material-icons" src="image/icons/settings.svg"> درخواست نصاب</li></a>
                <a href="change_owner.php" class="text-black"><li><img class="material-icons" src="image/icons/rent.svg"> انتقال مالکیت دستگاه</li></a>
                <a href="update_form.php" class="text-black"><li><img class="material-icons" src="image/icons/update.svg"> به روزرسانی دستگاه</li></a>
                <a href="https://docs.google.com/forms/d/e/1FAIpQLScF-9yBaWPPAwRirpfWG5tywNJaQWa87lYUg07at-KL_ZqZSw/viewform?usp=header" class="text-black">
                    <li><img class="material-icons" src="image/icons/u_turn_left.svg"> درخواست مرجوعی</li>
                </a>
                <!--<a href="https://docs.google.com/forms/d/e/1FAIpQLSeCGGl9Mt4e3XLlrr1WmAC_a0qZYi_iwRKP8vCNdaRrmfYV3Q/viewform?usp=header" class="text-black">-->
                <!--    <li><img class="material-icons" src="image/icons/verified.svg"> گارانتی</li>-->
                <!--</a>-->
                <a href="garanti.php" class="text-black">
                    <li><img class="material-icons" src="image/icons/verified.svg"> گارانتی</li>
                </a>
            </ul>
        </div>

        <!-- بخش ۴: فروش -->
        <div class="menu-section">
            <h3>فروش</h3>
            <ul>
                <a href="https://hoshikala.com/product-category/products/door-smart-controler/" class="text-black">
                    <li><img class="material-icons" src="image/icons/shopping.svg">خرید دستگاه هوم</li>
                </a>
                <!--<a href="shop.php" class="text-black">-->
                <!--    <li><img class="material-icons" src="image/icons/shopping.svg">فروشگاه</li>-->
                <!--</a>-->
                <a href="subscription.php" class="text-black">
                    <li><img class="material-icons" src="image/icons/premium.svg"> اشتراک</li>
                </a>
                
                <a href="buy_user.php" class="text-black">
                    <li><img class="material-icons" src="image/icons/add_person.svg"> کاربر برای دستگاه</li>
                </a>
                <!--<a href="subscription.php?type=silver" class="text-black">-->
                <!--    <li><span class="material-icons">workspace_premium</span>اشتراک نقره ای</li>-->
                <!--</a>-->
                <!--<a href="subscription.php?type=gold" class="text-black">-->
                <!--    <li><span class="material-icons">workspace_premium</span>اشتراک طلایی</li>-->
                <!--</a>-->
            </ul>
        </div>

        <!-- فقط برای ادمین -->
        <?php if($_SESSION['user_info']['type'] == 1){ ?>
        <div class="menu-section">
            <h3>مدیریت</h3>
            <ul>
                <a href="shop.php" class="text-black">
                    <li><img class="material-icons" src="image/icons/shopping.svg">فروشگاه</li>
                </a>
                <a href="manage_products.php" class="text-black">
                    <li><img class="material-icons" src="image/icons/info.svg"> محصولات</li>
                </a>
                <a href="visits.php" class="text-black">
                    <!--<li><span class="material-icons">web بازدید های سایت</li>-->
                </a>
                <a href="last_visits.php" class="text-black">
                    <li><img class="material-icons" src="image/icons/web.svg"> بازدید ها</li>
                </a>
                <a href="orders_list.php" class="text-black">
                    <li><img class="material-icons" src="image/icons/web.svg"> سفارشات</li>
                </a>
            </ul>
            <br>
        </div>
        <?php } ?>


    </div>
</div>
