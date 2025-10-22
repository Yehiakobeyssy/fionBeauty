<?php
require '../../vendor/autoload.php';
require '../../settings/connect.php';

use \Stripe\Stripe;
use \Stripe\Refund;

if (isset($_POST['daitailInvoiceId'], $_POST['status'])) {
    $id = $_POST['daitailInvoiceId'];
    $status = $_POST['status'];

    // Fetch detail for refund calculation
    $stmtDetail = $con->prepare("
        SELECT d.quantity, d.up, d.discount, i.transactionNO, f.includeTax, f.taxPercent, f.SK
        FROM tbldatailinvoice d
        INNER JOIN tblinvoice i ON i.invoiceID = d.invoiceID
        INNER JOIN tblfinancesetting f ON f.SettingID = 1
        WHERE d.daitailInvoiceId = :id
    ");
    $stmtDetail->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtDetail->execute();
    $detail = $stmtDetail->fetch(PDO::FETCH_ASSOC);

    if (!$detail) {
        echo json_encode(['success' => false]);
        exit;
    }

    // Calculate refund amount
    $amount = $detail['quantity'] * $detail['up'];
    $amount -= ($detail['discount'] / 100) * $amount;
    if ($detail['includeTax'] == 0) {
        $amount += ($detail['taxPercent'] / 100) * $amount;
    }

    if (isset($_POST['checkOnly']) && $_POST['checkOnly'] == 1) {
        // Just return amount for confirmation
        echo json_encode(['success' => true, 'amount' => $amount]);
        exit;
    }

    // Otherwise, proceed with updating status
    $sql = "UPDATE tbldatailinvoice SET status = :status WHERE daitailInvoiceId = :id";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Process Stripe refund if status = 3
        if ($status == 3 && !empty($detail['transactionNO']) && !empty($detail['SK'])) {
            Stripe::setApiKey($detail['SK']);
            try {
                Refund::create([
                    'payment_intent' => $detail['transactionNO'],
                    'amount' => round($amount * 100), // cents
                ]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                echo 'stripe_error: ' . $e->getMessage();
                exit;
            }
        }
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'invalid';
}
?>
