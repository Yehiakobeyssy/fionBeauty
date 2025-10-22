<?php
require '../../vendor/autoload.php';
require '../../settings/connect.php';

use \Stripe\Stripe;
use \Stripe\Refund;


$stat = $con->prepare('SELECT PK, SK FROM tblfinancesetting WHERE SettingID = 1');
$stat->execute();
$result_Keys = $stat->fetch(PDO::FETCH_ASSOC);
$PK = $result_Keys['PK'] ?? '';
$SK = $result_Keys['SK'] ?? '';
Stripe::setApiKey($SK); // Replace with your Stripe Secret Key

if (isset($_POST['InvoiceId'], $_POST['status'])) {
    $id = $_POST['InvoiceId'];
    $status = $_POST['status'];

    // First, get the transactionNO if status is 6
    if ($status == 6) {
        $stmtSelect = $con->prepare("SELECT transactionNO FROM tblinvoice WHERE invoiceID = :id");
        $stmtSelect->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtSelect->execute();
        $invoice = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if ($invoice && !empty($invoice['transactionNO'])) {
            $transactionId = $invoice['transactionNO'];

            try {
                // Create a refund
                $refund = Refund::create([
                    'payment_intent' => $transactionId,
                ]);
                // You can log $refund for debugging if needed
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // Handle Stripe error
                echo 'stripe_error: ' . $e->getMessage();
                exit;
            }
        } else {
            echo 'invalid_transaction';
            exit;
        }
    }

    // Update invoice status
    $sql = "UPDATE tblinvoice SET invoiceStatus = :status WHERE invoiceID = :id";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'invalid';
}
?>
