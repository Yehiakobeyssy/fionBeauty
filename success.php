<?php
session_start();
$order = $_SESSION['order_info'] ?? null;
include 'settings/connect.php';
include 'common/function.php';
include 'common/head.php';
require 'vendor/autoload.php';
include 'mail.php'; // PHPMailer config

// Get Stripe keys
$stat = $con->prepare('SELECT PK, SK FROM tblfinancesetting WHERE SettingID = 1');
$stat->execute();
$result_Keys = $stat->fetch(PDO::FETCH_ASSOC);
$PK = $result_Keys['PK'] ?? '';
$SK = $result_Keys['SK'] ?? '';

\Stripe\Stripe::setApiKey($SK);

// Check payment_intent
if (!isset($_GET['payment_intent'])) {
    header("Location: index.php");
    exit();
}

$intent_id = $_GET['payment_intent'];
$intent = \Stripe\PaymentIntent::retrieve($intent_id);
$status = $intent->status; // succeeded, requires_payment_method, etc.
$paymentMethod = $intent->payment_method_types[0] ?? 'unknown';
$amount = $intent->amount_received / 100;

// Get user ID
if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];  
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = (int) $_COOKIE['user_id'];  
} else {
    $user_id = 0;
}

$sql=$con->prepare('SELECT  clientFname,clientLname,clientEmail FROM  tblclient  WHERE clientID = ? ');
$sql->execute([$user_id]);
$result_user = $sql->fetch();

$user_email    = $result_user['clientEmail'];
$user_name     = $result_user['clientFname'].' ' .$result_user['clientLname'] ;
// Order details
if($order) {
    $shipping_add  = htmlspecialchars($order['address']);
    $order_note    = htmlspecialchars($order['note']);
    $subtotal      = $order['subtotal'];
    $totalDiscount = $order['discount'];
    $totaltax      = $order['tax'];
    $grandOrder    = $order['grandtotal'];

    $stmt = $con->prepare("
        SELECT tblprovince.shippingFee
        FROM tbladdresse
        INNER JOIN tblprovince ON tblprovince.provinceID = tbladdresse.provinceID
        WHERE addresseID = ?
    ");
    $stmt->execute([$shipping_add]);
    $shippfee = $stmt->fetchColumn();
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Status</title>
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
<?php if($status === 'succeeded' && isset($_SESSION['cart'])): ?>

<?php
$check = $con->prepare("SELECT invoiceID  FROM tblinvoice WHERE transactionNO = ?");
$check->execute([$intent_id]);
$exists = $check->fetchColumn();

if ($exists) {
    echo '<h1 class="text-success"><i class="fas fa-check-circle"></i> Payment Already Processed</h1>';
    echo '<p>Your payment was already recorded. Thank you!</p>';
    echo '<a href="index.php" class="btn">Show Invoice</a>';
    // Clear cart (if still set)
    unset($_SESSION['cart']);
    unset($_SESSION['order_info']);
    exit();
}
// Generate invoice
$sql = $con->prepare("SELECT COUNT(*) AS total FROM tblinvoice WHERE clientID = ? AND YEAR(invoiceDate) = YEAR(CURDATE())");
$sql->execute([$user_id]);
$row = $sql->fetch(PDO::FETCH_ASSOC);
$count = (int)$row['total'] + 1;
$year = date('y');
$invoiceNumber = str_pad($count, 4, '0', STR_PAD_LEFT);
$invoiceCode = "INV" . $invoiceNumber .'-'.$user_id . $year;

// Insert invoice
$sql= $con->prepare('INSERT INTO tblinvoice (invoiceCode,clientID,addresseId,Amount,discount,tax,shippfee,invoiceAmount,invoicePaid,paymentMethod,transactionNO,invoiceStatus,invoiceNote)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
$sql->execute([$invoiceCode,$user_id,$shipping_add,$subtotal,$totalDiscount,$totaltax,$shippfee,$grandOrder,1,$paymentMethod,$intent_id,1,$order_note]);
$invoiceID = $con->lastInsertId();

// Insert invoice items
$itemsHtml = '';
foreach ($_SESSION['cart'] as $id => $qty) {
    $stmt = $con->prepare("SELECT * FROM tblitems WHERE itmId = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) continue;

    $price = $item['sellPrice'];
    $stmt = $con->prepare("SELECT precent FROM tbldiscountitem WHERE itemID = ? AND quatity <= ? ORDER BY quatity DESC LIMIT 1");
    $stmt->execute([$id, $qty]);
    $discountPercent = $stmt->fetchColumn() ?: 0;

    $stat = $con->prepare('INSERT INTO tbldatailinvoice (invoiceID,itmID,quantity,up,discount,status)
                           VALUES (?,?,?,?,?,?)');
    $stat->execute([$invoiceID,$id,$qty,$price,$discountPercent,1]);

    $totalPrice = $price * $qty;
    $itemsHtml .= "
        <tr>
            <td style='padding:8px; border:1px solid #ddd;'>{$item['itmName']}</td>
            <td style='padding:8px; border:1px solid #ddd;'>{$qty}</td>
            <td style='padding:8px; border:1px solid #ddd;'>{$price}</td>
            <td style='padding:8px; border:1px solid #ddd;'>{$totalPrice}</td>
        </tr>
    ";
}

// Send email
$mail->setFrom($applicationemail, 'Fion Beauty Supplies');
$mail->addAddress($user_email);
$mail->Subject = "Order Confirmation - {$invoiceCode}";
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->Body = "
<div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; background: #f7f7f7; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden;'>
    
    <!-- Header -->
    <div style='background-color: #009245; color: #fff; padding: 20px; text-align: center;'>
        <img src='".$websiteaddresse."images/logo_white.png' alt='Fion Beauty' style='max-height: 50px; margin-bottom: 10px;'>
        <h1 style='margin: 0; font-size: 24px;'>Fion Beauty Supplies</h1>
    </div>

    <!-- Greeting -->
    <div style='padding: 20px; color: #333;'>
        <h2 style='color: #009245; margin-top: 0;'>Thank you for your order, {$user_name}!</h2>
        <p>Your payment has been received successfully. Here are your order details:</p>

        <!-- Order Table -->
        <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
            <thead>
                <tr style='background-color: #009245; color: #fff; text-align: left;'>
                    <th style='padding: 12px; border: 1px solid #ddd;'>Item</th>
                    <th style='padding: 12px; border: 1px solid #ddd;'>Quantity</th>
                    <th style='padding: 12px; border: 1px solid #ddd;'>Price</th>
                    <th style='padding: 12px; border: 1px solid #ddd;'>Total</th>
                </tr>
            </thead>
            <tbody>
                {$itemsHtml}
            </tbody>
        </table>

        <!-- Summary -->
        <div style='margin-top: 20px; font-size: 16px;'>
            <p><strong>Subtotal:</strong> {$subtotal} $</p>
            <p><strong>Discount:</strong> {$totalDiscount} $</p>
            <p><strong>Tax:</strong> {$totaltax} $</p>
            <p><strong>Shipping Fee:</strong> {$shippfee} $</p>
            <p style='font-weight: bold; font-size: 18px;'>Grand Total: {$grandOrder} $</p>
        </div>

        <!-- Footer Note -->
        <!-- Footer -->
        
        <div style='margin-top: 40px; padding: 20px; font-family: Arial, sans-serif; color: #6B6B6B;'>
            <p style=' font-size: 14px; color: #666;'>If you have any questions, feel free to <a href='mailto:info@fionbeautysupplies.ca' style='color: #009245;'>contact us</a>.</p>
            <p style='font-size: 14px; margin: 0;'>
                If you didn’t expect this message, please <a href='mailto:info@fionbeautysupplies.ca' style='color: #009245; text-decoration: none;'>contact our support team</a> immediately.
            </p>

            <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>

            <p style='font-size: 13px; color: #999; text-align: center; margin: 0;'>
                © " . date('Y') . " Fion Beauty Supplies. All rights reserved.<br>
                This is an automated message — please do not reply directly.
            </p>
        </div>

    </div>
</div>
";

$mail->send();

$notificationText = "New order created: Order No #$invoiceCode by $user_name";
$stmt = $con->prepare("INSERT INTO tblNotification (text) VALUES (?)");
$stmt->execute([$notificationText]);
$notificationId = $con->lastInsertId();
$admins = $con->query("SELECT adminID  FROM  tbladmin WHERE admin_block = 0")->fetchAll(PDO::FETCH_COLUMN);
$stmtSeen = $con->prepare("INSERT INTO tblseennotification (notificationId, adminID, seen) VALUES (?, ?, 0)");
foreach ($admins as $adminId) {
    $stmtSeen->execute([$notificationId, $adminId]);
}
// Clear cart
unset($_SESSION['cart']);
unset($_SESSION['order_info']);
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
