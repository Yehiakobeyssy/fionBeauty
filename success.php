<?php
session_start();
$order = $_SESSION['order_info'] ?? null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Result</title>
    <style>
        body { font-family: Arial; text-align: center; padding: 50px; }
        .success, .cancel { display: none; }
        .success img, .cancel img { width: 150px; margin-bottom: 20px; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px; }
        .btn-primary { background: #28a745; color: #fff; }
        .btn-cancel { background: #dc3545; color: #fff; }
        table { margin: 0 auto; text-align: left; }
        td, th { padding: 5px 10px; }
    </style>
</head>
<body>

<?php
// You can check Stripe's session to know if payment succeeded or cancelled
// For demo, we can simulate via $_GET['status'] = success/cancel
$status = $_GET['status'] ?? 'success';
?>

<div class="success" <?php if($status==='success') echo 'style="display:block;"'; ?>>
    <img src="img/success.png" alt="Success">
    <h2>Payment Successful!</h2>
    <?php if($order): ?>
    <h3>Order Summary</h3>
    <table>
        <tr><th>Address:</th><td><?= htmlspecialchars($order['address']) ?></td></tr>
        <tr><th>Note:</th><td><?= htmlspecialchars($order['note']) ?></td></tr>
        <tr><th>Subtotal:</th><td>$<?= number_format($order['subtotal'],2) ?></td></tr>
        <tr><th>Discount:</th><td>$<?= number_format($order['discount'],2) ?></td></tr>
        <tr><th>Grand Total:</th><td>$<?= number_format($order['grandtotal'],2) ?></td></tr>
    </table>
    <?php endif; ?>
    <button class="btn btn-primary" onclick="window.location.href='index.php'">Continue Shopping</button>
</div>

<div class="cancel" <?php if($status==='cancel') echo 'style="display:block;"'; ?>>
    <img src="img/cancel.png" alt="Cancelled">
    <h2>Payment Cancelled</h2>
    <p>Your payment was not completed. You can try again.</p>
    <button class="btn btn-cancel" onclick="window.location.href='checkout.php'">Return to Checkout</button>
</div>

</body>
</html>
