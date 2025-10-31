<?php
session_start();
include "../settings/connect.php"; // $con = new PDO(...)

if (empty($_SESSION['cart'])) {
    echo "<p class='text-center'>Your cart is empty.</p>";
    exit;
}

// --- Get finance settings ---
$stmt = $con->prepare("SELECT taxPercent, includeTax FROM tblfinancesetting WHERE SettingID = 1");
$stmt->execute();
$finance = $stmt->fetch(PDO::FETCH_ASSOC);

$taxPercent = $finance['taxPercent'] ?? 0;
$includeTax = $finance['includeTax'] ?? 0;

// --- Initialize totals ---
$totalSubtotal = 0;   // Before discount
$totalDiscount = 0;   // All discounts (item, promotional, category)
$totalTax = 0;        // Tax amount

// --- Track subtotal by category for category-based discounts ---
$categoryTotals = [];

// --- Loop through the cart items ---
foreach ($_SESSION['cart'] as $itemId => $qty) {
    // Get item details
    $stmt = $con->prepare("
        SELECT itmId, itmName, sellPrice, minQuantity, promotional, catId 
        FROM tblitems 
        WHERE itmId = ?
    ");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) continue;

    $price = $item['sellPrice'];
    $subtotal = $price * $qty;

    // --- Promotional discount per item ---
    $promoDiscount = ($subtotal * $item['promotional']) / 100;

    // --- Quantity-based discount ---
    $stmt = $con->prepare("
        SELECT precent 
        FROM tbldiscountitem 
        WHERE itemID = ? AND quatity <= ? 
        ORDER BY quatity DESC 
        LIMIT 1
    ");
    $stmt->execute([$itemId, $qty]);
    $discountPercent = $stmt->fetchColumn() ?: 0;
    $qtyDiscount = ($subtotal * $discountPercent) / 100;

    // --- Add to totals ---
    $totalSubtotal += $subtotal;
    $totalDiscount += $promoDiscount + $qtyDiscount;

    // --- Track total per category ---
    $catId = $item['catId'];
    if (!isset($categoryTotals[$catId])) {
        $categoryTotals[$catId] = 0;
    }
    $categoryTotals[$catId] += $subtotal;
}

// --- Apply category-based discounts ---
foreach ($categoryTotals as $catId => $catSubtotal) {
    $stmt = $con->prepare("SELECT amountOver, discount FROM tblcategory WHERE categoryId = ?");
    $stmt->execute([$catId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category && $catSubtotal >= $category['amountOver']) {
        $catDiscountValue = ($catSubtotal * $category['discount']) / 100;
        $totalDiscount += $catDiscountValue;
    }
}

// --- Apply tax logic ---
if ($includeTax == 1) {
    // Prices already include tax
    $subtotalExcludingTax = $totalSubtotal / (1 + ($taxPercent / 100));
    $totalTax = $totalSubtotal - $subtotalExcludingTax; // Extracted tax
    $grandTotal = $totalSubtotal - $totalDiscount;      // Tax already included
} else {
    // Add tax after discounts
    $taxBase = $totalSubtotal - $totalDiscount;
    $totalTax = $taxBase * ($taxPercent / 100);
    $grandTotal = $taxBase + $totalTax;
}
?>

<!-- 🧾 Totals Table -->
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

<!-- 🛒 Checkout Button -->
<div class="checkout-container text-center mt-3">
    <button class="checkout-btn btn btn-primary">🛒 Proceed to Checkout</button>
</div>
