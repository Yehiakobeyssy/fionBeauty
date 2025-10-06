<?php
    session_start();
    $order = $_SESSION['order_info'] ?? null;
    include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';
    require 'vendor/autoload.php';

    $stat = $con->prepare('SELECT PK, SK FROM tblfinancesetting WHERE SettingID = 1');
    $stat->execute();
    $result_Keys = $stat->fetch(PDO::FETCH_ASSOC);
    $PK = $result_Keys['PK'] ?? '';
    $SK = $result_Keys['SK'] ?? '';


    \Stripe\Stripe::setApiKey($SK);
    if (isset($_GET['payment_intent'])) {
        $intent_id = $_GET['payment_intent'];
        $intent = \Stripe\PaymentIntent::retrieve($intent_id);

        $status = $intent->status; // succeeded, requires_payment_method, etc.
        $paymentMethod = $intent->payment_method_types[0] ?? 'unknown';
        $amount = $intent->amount_received / 100; // in your currency unit
    }else{
        header("Location: index.php");
        exit();
    }


    if (isset($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];  
    } elseif (isset($_COOKIE['user_id'])) {
        $user_id = (int) $_COOKIE['user_id'];  
    } else {
        $user_id = 0; // if neither session nor cookie exist
    };
    // You can check Stripe's session to know if payment succeeded or cancelled
    // For demo, we can simulate via $_GET['status'] = success/cancel
    //$status = $_GET['status'] ?? 'success';

    if(isset($_SESSION['order_info'])){
        $shipping_add       = htmlspecialchars($order['address']);
        $order_note         = htmlspecialchars($order['note']);
        $subtotal           = $order['subtotal'];
        $totalDiscount      = $order['discount'];
        $totaltax           = $order['tax'];
        $grandOrder         = $order['grandtotal'];
    }
    


?>

    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/success.css">
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php'; 
        include 'include/catecorysname.php';
    ?>

    <div class="status-box">
        <?php if($status === 'succeeded'): ?>
            <?php
                if(isset($_SESSION['cart'])){
                    $sql = $con->prepare("SELECT COUNT(*) AS total FROM tblinvoice WHERE clientID = ?");
                    $sql->execute([$user_id]);
                    $row = $sql->fetch(PDO::FETCH_ASSOC);
                    $count = (int)$row['total'] + 1;
                    $year = date('y'); // '25' for 2025
                    $invoiceNumber = str_pad($count, 3, '0', STR_PAD_LEFT);

                    $invoiceCode = "INV" . $invoiceNumber . $year;
                    $clientID =  $user_id ;
                    $addresseId = $shipping_add;
                    $Amount  = $subtotal;
                    $discount = $totalDiscount;
                    $tax = $totaltax;
                    $invoiceAmount = $grandOrder;
                    $invoicePaid = 1;
                    $paymentMethod =  $paymentMethod ;
                    $transactionNO = $intent_id;
                    $invoiceStatus = 1;
                    $invoiceNote = $order_note;

                    $sql= $con->prepare('INSERT INTO tblinvoice (invoiceCode,clientID,addresseId,Amount,discount,tax,invoiceAmount,invoicePaid,paymentMethod,transactionNO,invoiceStatus,invoiceNote)
                                                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
                    $sql->execute([$invoiceCode,$clientID,$addresseId,$Amount,$discount,$tax,$invoiceAmount,$invoicePaid,$paymentMethod,$transactionNO,$invoiceStatus,$invoiceNote]);

                    $invoiceID = $con->lastInsertId();

                    foreach ($_SESSION['cart'] as $id => $qty) {
                            $stmt = $con->prepare("SELECT * FROM tblitems WHERE itmId = ?");
                            $stmt->execute([$id]);
                            $item = $stmt->fetch(PDO::FETCH_ASSOC);

                            if (!$item) continue;

                            $price = $item['sellPrice'];
                            $minQty = $item['minQuantity'];

                            // Check discount
                            $stmt = $con->prepare("SELECT precent FROM tbldiscountitem WHERE itemID = ? AND quatity  <= ? ORDER BY quatity DESC LIMIT 1");
                            $stmt->execute([$id, $qty]);
                            $discountPercent = $stmt->fetchColumn() ?: 0;

                            $invoiceID  = $invoiceID;
                            $itmID      = $id;
                            $quantity   = $qty;
                            $up         = $price;
                            $discount   = $discountPercent;
                            $status     = 1;
                                            
                            $stat = $con->prepare('INSERT INTO tbldatailinvoice (invoiceID,itmID,quantity,up,discount,status)
                                                    VALUES (?,?,?,?,?,?)');
                            $stat ->execute([$invoiceID,$itmID,$quantity,$up,$discount,$status]);
                    }
                    unset($_SESSION['cart']);
                    unset($_SESSION['order_info']);
                }
                
            ?>
            <h1 class="text-success"><i class="fas fa-check-circle"></i> Payment Successful!</h1>
            <p>Thank you for your order. Your payment has been completed successfully.</p>
            <a href="index.php" class="btn">Show Invoice</a>
            

        <?php else: ?>
            <h1 class="text-danger"><i class="fas fa-times-circle"></i> Payment Declined</h1>
            <p>Your payment was not completed. Please try again or choose a different payment method.</p>
            <a href="user/checkout.php" class="btn cancel">Try Again</a>
        <?php endif; ?>
    </div>
</body>
</html>
