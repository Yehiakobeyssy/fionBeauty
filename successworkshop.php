<?php
session_start();
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

// Check payment_intent or session_id
$intent_id = $_GET['payment_intent'] ?? $_GET['session_id'] ?? null;
if (!$intent_id) {
    header("Location: index.php");
    exit();
}

// Retrieve PaymentIntent or Checkout Session
try {
    if (isset($_GET['payment_intent'])) {
        $intent = \Stripe\PaymentIntent::retrieve($intent_id);
        $status = $intent->status;
        $paymentMethod = $intent->payment_method_types[0] ?? 'unknown';
        $amount = $intent->amount_received / 100;
    } else { // Checkout session
        $session = \Stripe\Checkout\Session::retrieve($intent_id);
        $status = $session->payment_status === 'paid' ? 'succeeded' : 'failed';
        $paymentMethod = $session->payment_method_types[0] ?? 'unknown';
        $amount = $session->amount_total / 100;
    }
} catch (\Exception $e) {
    echo "Error retrieving payment: ".$e->getMessage();
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'] ?? (int)($_COOKIE['user_id'] ?? 0);
$sql = $con->prepare('SELECT clientFname, clientLname, clientEmail FROM tblclient WHERE clientID = ?');
$sql->execute([$user_id]);
$user = $sql->fetch();
$user_name = $user['clientFname'].' '.$user['clientLname'];
$user_email = $user['clientEmail'];

// Check if workshop cart exists
if (!isset($_SESSION['workoutCart']) || empty($_SESSION['workoutCart'])) {
    echo "No workshops booked.";
    exit();
}

// Only process successful payments
if ($status === 'succeeded') {

    // Generate invoice number
    $sql = $con->prepare("SELECT COUNT(*) AS total FROM tblinvoiceworkshop WHERE clientID = ? AND YEAR(invoiceDate) = YEAR(CURDATE())");
    $sql->execute([$user_id]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $count = (int)$row['total'] + 1;
    $year = date('y');
    $invoiceCode = "WSINV" . str_pad($count, 4, '0', STR_PAD_LEFT) . '-' . $user_id . $year;

    // Insert invoice record
    $stmt = $con->prepare('INSERT INTO tblinvoiceworkshop (invoiceCode,clientID, invoiceDate, totalAmount, transactionID, method, status) VALUES (?,?, NOW(), ?, ?, ?, ?)');
    $stmt->execute([$invoiceCode,$user_id, $amount, $intent_id, $paymentMethod, 1]);
    $invoiceID = $con->lastInsertId();

    $itemsHtml = '';
    foreach ($_SESSION['workoutCart'] as $workshop_id) {
        // Get workshop details
        $stmt = $con->prepare("SELECT * FROM workshops WHERE id = ?");
        $stmt->execute([$workshop_id]);
        $workshop = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$workshop) continue;

        $workshopCost = $workshop['cost'];

        // Insert detail invoice record
        $stmt = $con->prepare("INSERT INTO tbldetailinvoiceworkshop (invoiceID, workshopID, workshopCost, quantity) VALUES (?, ?, ?, ?)");
        $stmt->execute([$invoiceID, $workshop_id, $workshopCost, 1]);

        // Insert booking
        $stmt = $con->prepare("INSERT INTO workshop_bookings (user_id, workshop_id, booking_date) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $workshop_id]);

        $itemsHtml .= "
            <tr>
                <td style='padding:8px; border:1px solid #ddd;'>{$workshop['title']}</td>
                <td style='padding:8px; border:1px solid #ddd;'>1</td>
                <td style='padding:8px; border:1px solid #ddd;'>{$workshopCost}</td>
                <td style='padding:8px; border:1px solid #ddd;'>{$workshopCost}</td>
            </tr>
        ";
    }

    // Send confirmation email
    $mail->setFrom($applicationemail, 'Your Workshop Platform');
    $mail->addAddress($user_email);
    $mail->Subject = "Workshop Booking Confirmation - {$invoiceCode}";
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin:auto; background: #f7f7f7; border:1px solid #e0e0e0; border-radius:10px; overflow:hidden;'>
        <div style='background:#009245; color:#fff; padding:20px; text-align:center;'>
            <h1>Workshop Booking Confirmation</h1>
        </div>
        <div style='padding:20px; color:#333;'>
            <h2 style='color:#009245;'>Thank you, {$user_name}!</h2>
            <p>Your payment of <strong>\${$amount}</strong> has been received. Here are your booked workshops:</p>
            <table style='width:100%; border-collapse: collapse; margin-top:20px;'>
                <thead>
                    <tr style='background:#009245; color:#fff; text-align:left;'>
                        <th style='padding:12px; border:1px solid #ddd;'>Workshop</th>
                        <th style='padding:12px; border:1px solid #ddd;'>Qty</th>
                        <th style='padding:12px; border:1px solid #ddd;'>Price</th>
                        <th style='padding:12px; border:1px solid #ddd;'>Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                </tbody>
            </table>
            <p style='font-weight:bold; font-size:18px; margin-top:20px;'>Grand Total: \${$amount}</p>
            <p>Invoice Number: {$invoiceCode}</p>
            <p>If you have questions, contact <a href='mailto:info@fionbeautysupplies.ca'>info@fionbeautysupplies.ca</a>.</p>
        </div>
    </div>
    ";
    $mail->send();


    $notificationText = "New paid workshop booking: {$workshop['title']} by $user_name";
    $stmt = $con->prepare("INSERT INTO tblNotification (text) VALUES (?)");
    $stmt->execute([$notificationText]);
    $notificationId = $con->lastInsertId();
    $admins = $con->query("SELECT adminID  FROM  tbladmin WHERE admin_block = 0")->fetchAll(PDO::FETCH_COLUMN);
    $stmtSeen = $con->prepare("INSERT INTO tblseennotification (notificationId, adminID, seen) VALUES (?, ?, 0)");
    foreach ($admins as $adminId) {
        $stmtSeen->execute([$notificationId, $adminId]);
    }
    // Clear workshop cart
    unset($_SESSION['workoutCart']);

    $successMsg = "Payment Successful! Your workshops have been booked.";
    $showInvoiceBtn = true;

} else {
    $successMsg = "Payment Failed or Pending. Please try again.";
    $showInvoiceBtn = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Workshop Payment Status</title>
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="css/success.css">
</head>
<body>
<?php 
include 'include/header.php';
include 'include/clientheader.php';
include 'include/catecorysname.php';
?>
<div class="status-box">
    <?php if($showInvoiceBtn): ?>
        <h1 class="text-success"><i class="fas fa-check-circle"></i> Payment Successful!</h1>
        <p>Thank you for your order. Your payment has been completed successfully.</p>
    
    <?php else: ?>
        <h1 class="text-danger"><i class="fas fa-times-circle"></i> Payment Declined</h1>
        <p>Your payment was not completed. Please try again or choose a different payment method.</p>
    <?php endif; ?>
</div>
</body>
</html>
