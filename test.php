    <?php
    
    include "app/bale/SendMessage.php";
    
    $bale = new SendMessage();
    $phone = "09022340943";
    $user = "home mh";
    $pay_id = "1243";
    $product = "شال";
    $bale->sms($phone, $user, $product, $pay_id);
        
