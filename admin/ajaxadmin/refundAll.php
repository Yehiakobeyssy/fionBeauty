<?php
require '../../vendor/autoload.php';
require '../../settings/connect.php';

use \Stripe\Stripe;
use \Stripe\Refund;

if (!isset($_POST['workshopId'])) {
    echo 'invalid';
    exit;
}

$workshopId = (int)$_POST['workshopId'];

try {
    // 1️⃣ Get all invoices related to this workshop
    $stmt = $con->prepare("
        SELECT 
            i.invoiceID, 
            i.invoiceCode, 
            i.totalAmount, 
            i.transactionID, 
            f.SK
        FROM tblinvoiceworkshop i
        INNER JOIN tbldetailinvoiceworkshop di ON di.invoiceID = i.invoiceID
        INNER JOIN tblfinancesetting f ON f.SettingID = 1
        WHERE di.workshopID = ?
    ");
    $stmt->execute([$workshopId]);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($invoices)) {
        echo 'no_invoices';
        exit;
    }

    // 2️⃣ Process refund (if applicable) for each invoice
    foreach ($invoices as $inv) {
        $amount = floatval($inv['totalAmount']);

        if ($amount > 0 && !empty($inv['transactionID'])) {
            Stripe::setApiKey($inv['SK']);
            try {
                Refund::create([
                    'payment_intent' => $inv['transactionID'],
                    'amount' => round($amount * 100),
                ]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // Just log or skip this one
                error_log("Refund failed for invoice {$inv['invoiceCode']}: " . $e->getMessage());
            }
        }

        // 3️⃣ Update invoice status to 6 (Refunded/Cancelled)
        $update = $con->prepare("UPDATE tblinvoiceworkshop SET status = 6 WHERE invoiceID = ?");
        $update->execute([$inv['invoiceID']]);
    }

    // 4️⃣ Delete all workshop bookings and mark workshop inactive
    $con->prepare("DELETE FROM workshop_bookings WHERE workshop_id = ?")->execute([$workshopId]);
    $con->prepare("UPDATE workshops SET ActiveWorkshop = 0 WHERE id = ?")->execute([$workshopId]);

    echo 'success';

} catch (Exception $e) {
    echo 'error: ' . $e->getMessage();
}
