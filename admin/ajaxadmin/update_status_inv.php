<?php
require '../../vendor/autoload.php';
require '../../settings/connect.php';
require '../../mail.php';

use \Stripe\Stripe;
use \Stripe\Refund;

// Get Stripe Keys
$stat = $con->prepare('SELECT PK, SK FROM tblfinancesetting WHERE SettingID = 1');
$stat->execute();
$result_Keys = $stat->fetch(PDO::FETCH_ASSOC);
$PK = $result_Keys['PK'] ?? '';
$SK = $result_Keys['SK'] ?? '';
Stripe::setApiKey($SK); // Stripe Secret Key

// Check POST data
if (isset($_POST['InvoiceId'], $_POST['status'])) {
    $id = $_POST['InvoiceId'];
    $status = $_POST['status'];

    // Fetch client info
    $sql = $con->prepare('SELECT clientFname, clientLname, clientEmail , invoiceCode 
                          FROM tblclient
                          INNER JOIN tblinvoice ON tblinvoice.clientID = tblclient.clientID 
                          WHERE invoiceID = ?');
    $sql->execute([$id]);
    $result_client = $sql->fetch(PDO::FETCH_ASSOC);

    if (!$result_client) {
        echo 'invalid_client';
        exit;
    }

    $clientname = $result_client['clientFname'] . ' ' . $result_client['clientLname'];
    $clientmail = $result_client['clientEmail'];
    $invoicecode = $result_client['invoiceCode'];

    // Process refund if status = 6
    if ($status == 6) {
        $stmtSelect = $con->prepare("SELECT transactionNO FROM tblinvoice WHERE invoiceID = :id");
        $stmtSelect->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtSelect->execute();
        $invoice = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if ($invoice && !empty($invoice['transactionNO'])) {
            $transactionId = $invoice['transactionNO'];

            try {
                $refund = Refund::create([
                    'payment_intent' => $transactionId,
                ]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
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

        // Prepare status texts and messages
        $statusTexts = [
            2 => 'Processing',
            3 => 'On the way',
            4 => 'Delivered',
            5 => 'Cancelled',
            6 => 'Full Refund',
        ];

        $statusMessages = [
            2 => 'Your order is now being processed and will be prepared shortly.',
            3 => 'Good news! Your order is on the way and will reach you soon.',
            4 => 'Your order has been delivered. We hope you enjoy your purchase!',
            5 => 'Your order has been cancelled. If you have any questions, please contact us.',
            6 => 'A full refund has been processed for your order. The amount should reflect in your account shortly.',
        ];
        $mail->setFrom($applicationemail, 'Fion Beauty Supplies'); // Sender
        $mail->addAddress($clientmail);                    // Receiver
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        // Email body (HTML, without amounts)
        $mail->Subject = "Update on Your Order #{$invoicecode}";
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; background: #f7f7f7; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden;'>

            <!-- Header -->
            <div style='background-color: #009245; color: #fff; padding: 20px; text-align: center;'>
                <img src='".$websiteaddresse."images/logo_white.png' alt='Fion Beauty' style='max-height: 50px; margin-bottom: 10px;'>
                <h1 style='margin: 0; font-size: 24px;'>Fion Beauty Supplies</h1>
            </div>

            <!-- Greeting & Message -->
            <div style='padding: 20px; color: #333;'>
                <h2 style='color: #009245; margin-top: 0;'>Hello {$clientname},</h2>
                <p>The status of your order <strong>#{$invoicecode}</strong> has been updated to <strong>{$statusTexts[$status]}</strong>.</p>
                <p>{$statusMessages[$status]}</p>
            </div>

            <!-- Footer -->
            <div style='margin-top: 20px; padding: 20px; font-family: Arial, sans-serif; color: #6B6B6B;'>
                <p style='font-size: 14px; color: #666;'>If you have any questions, feel free to <a href='mailto:info@fionbeautysupplies.ca' style='color: #009245;'>contact us</a>.</p>
                <p style='font-size: 14px; margin: 0;'>If you didn’t expect this message, please <a href='mailto:info@fionbeautysupplies.ca' style='color: #009245; text-decoration: none;'>contact our support team</a> immediately.</p>
                <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>
                <p style='font-size: 13px; color: #999; text-align: center; margin: 0;'>© " . date('Y') . " Fion Beauty Supplies. All rights reserved.<br>This is an automated message — please do not reply directly.</p>
            </div>

        </div>
        ";

        // Send email
        if ($mail->send()) {
            echo 'success';
        } else {
            echo 'mail_error';
        }

    } else {
        echo 'error';
    }
} else {
    echo 'invalid';
}
?>
