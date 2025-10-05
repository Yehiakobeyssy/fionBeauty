<?php
session_start();
include "../settings/connect.php"; // $con = new PDO(...)

if (empty($_SESSION['cart'])) {
    echo "<p class='text-center'>Your cart is empty.</p>";
    exit;
}

// --- Get finance setting ---
$stmt = $con->prepare("SELECT taxPercent, includeTax FROM tblfinancesetting WHERE SettingID = 1");
$stmt->execute();
$finance = $stmt->fetch(PDO::FETCH_ASSOC);
$taxPercent = $finance['taxPercent'] ?? 0;
$includeTax = $finance['includeTax'] ?? 0;

// Initialize totals
$totalSubtotal = 0;  // before discount
$totalDiscount = 0;
$totalTax = 0;

// Loop over cart
foreach ($_SESSION['cart'] as $itemId => $qty) {
    // Get item details
    $stmt = $con->prepare("SELECT itmId, itmName, sellPrice, minQuantity FROM tblitems WHERE itmId = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) continue;

    $price = $item['sellPrice'];
    $subtotal = $price * $qty;

    // Get discount percent (if any)
    $stmt = $con->prepare("SELECT precent FROM tbldiscountitem WHERE itemID = ? AND quatity <= ? ORDER BY quatity DESC LIMIT 1");
    $stmt->execute([$itemId, $qty]);
    $discountPercent = $stmt->fetchColumn() ?: 0;
    $discountAmount = ($price * $qty * $discountPercent) / 100;

    // Accumulate totals
    $totalSubtotal += $subtotal;
    $totalDiscount += $discountAmount;
}

// --- Apply tax logic ---
if ($includeTax == 1) {
    // Prices already include tax
    $subtotalExcludingTax = $totalSubtotal / (1 + ($taxPercent / 100)); // real subtotal (without tax)
    $totalTax = $totalSubtotal - $subtotalExcludingTax; // extracted tax
    $grandTotal = $totalSubtotal - $totalDiscount; // tax already included in price
} else {
    // Prices do not include tax → add tax after discount
    $taxBase = $totalSubtotal - $totalDiscount;
    $totalTax = $taxBase * ($taxPercent / 100);
    $grandTotal = $taxBase + $totalTax;
}
?>

<table class="grand-total-table">
    <tr>
        <td>Subtotal (Before Discount)</td>
        <td>$<?= number_format(($includeTax ? $subtotalExcludingTax : $totalSubtotal), 2) ?></td>
    </tr>
    <tr>
        <td>Total Discount</td>
        <td>− $<?= number_format($totalDiscount, 2) ?></td>
    </tr>
    <tr>
        <td>Tax (<?= $taxPercent ?>%)</td>
        <td>$<?= number_format($totalTax, 2) ?></td>
    </tr>
    <tr class="grand-row">
        <td><strong>Grand Total</strong></td>
        <td><strong>$<?= number_format($grandTotal, 2) ?></strong></td>
    </tr>
</table>

<div class="checkout-container">
    <button class="checkout-btn btn btn-primary">🛒 Process to Checkout</button>
</div>
