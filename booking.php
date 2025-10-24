<?php
session_start();
include 'settings/connect.php';
include 'common/function.php';
include 'common/head.php';
require 'vendor/autoload.php';
include 'mail.php'; // PHPMailer config

// Get user info
$user_id = $_SESSION['user_id'] ?? (int)($_COOKIE['user_id'] ?? 0);
if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Check workshop ID
$workshop_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($workshop_id <= 0) {
    header("Location: workshops.php");
    exit();
}

// Get user details
$sql = $con->prepare("SELECT clientFname, clientLname, clientEmail FROM tblclient WHERE clientID = ?");
$sql->execute([$user_id]);
$user = $sql->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.php");
    exit();
}

$user_name  = $user['clientFname'] . ' ' . $user['clientLname'];
$user_email = $user['clientEmail'];

// Get workshop details
$stmt = $con->prepare("SELECT * FROM workshops WHERE id = ?");
$stmt->execute([$workshop_id]);
$workshop = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$workshop) {
    echo "Workshop not found.";
    exit();
}

// Check if user already booked
$stmt = $con->prepare("SELECT COUNT(*) FROM workshop_bookings WHERE user_id = ? AND workshop_id = ?");
$stmt->execute([$user_id, $workshop_id]);
$alreadyBooked = $stmt->fetchColumn();

if ($alreadyBooked > 0) {
    $successMsg = "You have already booked this workshop.";
    $showInvoiceBtn = false;
} else {

    // Generate invoice number
    $sql = $con->prepare("SELECT COUNT(*) AS total FROM tblinvoiceworkshop WHERE clientID = ? AND YEAR(invoiceDate) = YEAR(CURDATE())");
    $sql->execute([$user_id]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $count = (int)$row['total'] + 1;
    $year = date('y');
    $invoiceCode = "WSINV" . str_pad($count, 4, '0', STR_PAD_LEFT) . '-' . $user_id . $year;

    // Create invoice (0 cost)
    $stmt = $con->prepare("INSERT INTO tblinvoiceworkshop (invoiceCode, clientID, invoiceDate, totalAmount, transactionID, method, status)
                           VALUES (?, ?, NOW(), 0, ?, ?, ?)");
    $stmt->execute([$invoiceCode, $user_id, 'FREEBOOKING', 'Free', 1]);
    $invoiceID = $con->lastInsertId();

    // Insert invoice detail (free)
    $stmt = $con->prepare("INSERT INTO tbldetailinvoiceworkshop (invoiceID, workshopID, workshopCost, quantity)
                           VALUES (?, ?, 0, 1)");
    $stmt->execute([$invoiceID, $workshop_id]);

    // Insert booking record
    $stmt = $con->prepare("INSERT INTO workshop_bookings (user_id, workshop_id, booking_date)
                           VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $workshop_id]);

    // Build email
    $mail->setFrom($applicationemail, 'Your Workshop Platform');
    $mail->addAddress($user_email);
    $mail->Subject = "Free Workshop Booking Confirmation - {$invoiceCode}";
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Body = "
    <div style='font-family: Arial, sans-serif; max-width:600px; margin:auto; background:#f7f7f7; border:1px solid #e0e0e0; border-radius:10px; overflow:hidden;'>
        <div style='background:#009245; color:#fff; padding:20px; text-align:center;'>
            <h1>Workshop Booking Confirmation</h1>
        </div>
        <div style='padding:20px; color:#333;'>
            <h2 style='color:#009245;'>Thank you, {$user_name}!</h2>
            <p>You have successfully booked the following free workshop:</p>
            <table style='width:100%; border-collapse:collapse; margin-top:20px;'>
                <thead>
                    <tr style='background:#009245; color:#fff;'>
                        <th style='padding:12px; border:1px solid #ddd;'>Workshop</th>
                        <th style='padding:12px; border:1px solid #ddd;'>Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style='padding:8px; border:1px solid #ddd;'>{$workshop['title']}</td>
                        <td style='padding:8px; border:1px solid #ddd;'>Free</td>
                    </tr>
                </tbody>
            </table>
            <p style='font-weight:bold; font-size:18px; margin-top:20px;'>Total: Free</p>
            <p>Invoice Number: {$invoiceCode}</p>
            <p>If you have questions, contact <a href='mailto:info@fionbeautysupplies.ca'>info@fionbeautysupplies.ca</a>.</p>
        </div>
    </div>
    ";
    $mail->send();

    $successMsg = "Free Workshop Booked Successfully!";
    $showInvoiceBtn = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Free Workshop Booking</title>
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
        <h1 class="text-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($successMsg) ?></h1>
        <p>Thank you! You’ve successfully booked the free workshop.</p>
    <?php else: ?>
        <h1 class="text-warning"><i class="fas fa-info-circle"></i> <?= htmlspecialchars($successMsg) ?></h1>
    <?php endif; ?>
</div>
</body>
</html>
