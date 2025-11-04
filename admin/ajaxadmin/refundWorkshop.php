<?php
require '../../vendor/autoload.php';
require '../../settings/connect.php';

use \Stripe\Stripe;
use \Stripe\Refund;

if (!isset($_POST['invoiceCode'], $_POST['workshopId'], $_POST['detailID'])) {
    echo 'invalid';
    exit;
}

$invoiceCode = $_POST['invoiceCode'];
$workshopId = $_POST['workshopId'];
$detailID = $_POST['detailID'];
$amount = floatval($_POST['amount']);
$transactionId = $_POST['transactionId'] ?? '';

try {
    // Get invoice + Stripe key
    $stmt = $con->prepare("
        SELECT i.invoiceID, i.transactionID, f.SK
        FROM tblinvoiceworkshop i
        JOIN tblfinancesetting f ON f.SettingID = 1
        WHERE i.invoiceCode = ?
    ");
    $stmt->execute([$invoiceCode]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$invoice) {
        echo 'invoice_not_found';
        exit;
    }

    // Refund if amount > 0
    if ($amount > 0 && !empty($invoice['transactionID'])) {
        Stripe::setApiKey($invoice['SK']);
        Refund::create([
            'payment_intent' => $invoice['transactionID'],
            'amount' => round($amount * 100),
        ]);
    }

    // Delete the detail + booking record
    $con->prepare("DELETE FROM workshop_bookings WHERE workshop_id = ?")->execute([$workshopId]);

    // Update invoice status = 6
    $con->prepare("UPDATE tblinvoiceworkshop SET status = 6 WHERE invoiceCode = ?")->execute([$invoiceCode]);

    echo 'success';
} catch (Exception $e) {
    echo 'error: ' . $e->getMessage();
}
