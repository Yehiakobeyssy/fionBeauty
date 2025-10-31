<?php
session_start();
include '../../settings/connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$addressID = (int)($data['addressID'] ?? 0);

// --- Initialize ---
$totalAmount = 0;       // total before discounts
$totalDiscount = 0;     // all discounts combined
$totalCategoryDiscount = 0;
$shippingFee = 0;
$extraFee = 0;
$hasPromotional = false;
$hasDiscount = false;
$allFreeCategory = true;

// --- Get finance settings ---
$stmt = $con->prepare("SELECT taxPercent, includeTax FROM tblfinancesetting WHERE SettingID = 1");
$stmt->execute();
$finance = $stmt->fetch(PDO::FETCH_ASSOC);
$taxPercent = $finance['taxPercent'] ?? 0;
$includeTax = $finance['includeTax'] ?? 0;

// --- Get province shipping info ---
$provinceFee = 0;
$amountOver = 0;
if ($addressID > 0) {
    $stmt = $con->prepare("
        SELECT p.shippingFee, p.amount_over
        FROM tbladdresse a
        JOIN tblprovince p ON p.provinceID = a.provinceID
        WHERE a.addresseID = ?
    ");
    $stmt->execute([$addressID]);
    $province = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($province) {
        $provinceFee = (float)$province['shippingFee'];
        $amountOver = (float)$province['amount_over'];
    }
}

// --- Loop through cart items ---
$categoryTotals = []; // subtotal per category
if (!empty($_SESSION['cart'])) {
    $itemIds = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($itemIds), '?'));

    $stmtItems = $con->prepare("
        SELECT i.itmId, i.sellPrice, i.extra_shipfee, i.promotional, i.catId, c.shippingfree_accepted
        FROM tblitems i
        JOIN tblcategory c ON c.categoryId = i.catId
        WHERE i.itmId IN ($placeholders)
    ");
    $stmtItems->execute($itemIds);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        $itemId = $item['itmId'];
        $qty = $_SESSION['cart'][$itemId] ?? 0;
        $subtotal = $item['sellPrice'] * $qty;

        // --- Extra shipping fee per item ---
        $extraFee += (float)$item['extra_shipfee'];

        // --- Item promotional discount ---
        $promoDiscount = ($subtotal * $item['promotional']) / 100;
        if ($promoDiscount > 0) $hasPromotional = true;

        // --- Quantity-based discount ---
        $stmtDiscount = $con->prepare("
            SELECT precent 
            FROM tbldiscountitem 
            WHERE itemID = ? AND quatity <= ? 
            ORDER BY quatity DESC LIMIT 1
        ");
        $stmtDiscount->execute([$itemId, $qty]);
        $qtyDiscountPercent = (float)($stmtDiscount->fetchColumn() ?? 0);
        $qtyDiscount = ($subtotal * $qtyDiscountPercent) / 100;
        if ($qtyDiscount > 0) $hasDiscount = true;

        // --- Total discounts per item ---
        $itemDiscount = $promoDiscount + $qtyDiscount;
        $totalDiscount += $itemDiscount;

        // --- Track subtotal per category ---
        $catId = $item['catId'];
        if (!isset($categoryTotals[$catId])) $categoryTotals[$catId] = 0;
        $categoryTotals[$catId] += $subtotal;

        // --- Check free shipping eligibility per category ---
        if ((int)$item['shippingfree_accepted'] !== 1) $allFreeCategory = false;

        // --- Accumulate total amount ---
        $totalAmount += $subtotal;
    }
}

// --- Apply category-level discounts ---
foreach ($categoryTotals as $catId => $catSubtotal) {
    $stmt = $con->prepare("SELECT amountOver, discount FROM tblcategory WHERE categoryId = ?");
    $stmt->execute([$catId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($category && $catSubtotal >= $category['amountOver']) {
        $catDiscount = ($catSubtotal * $category['discount']) / 100;
        $totalCategoryDiscount += $catDiscount;
    }
}

// --- Total discount ---
$totalDiscount += $totalCategoryDiscount;

// --- Determine shipping fee ---
if (!$hasPromotional && !$hasDiscount && $allFreeCategory && $totalAmount >= $amountOver) {
    $shippingFee = $extraFee; // Free shipping + extra per-item fees
} else {
    $shippingFee = $provinceFee + $extraFee; // Normal shipping
}

// --- Apply tax ---
if ($includeTax == 1) {
    $subtotalExclTax = $totalAmount / (1 + $taxPercent / 100);
    $taxAmount = $totalAmount - $subtotalExclTax;
    $grandTotal = $totalAmount - $totalDiscount + $shippingFee;
} else {
    $taxBase = $totalAmount - $totalDiscount;
    $taxAmount = $taxBase * ($taxPercent / 100);
    $grandTotal = $taxBase + $taxAmount + $shippingFee;
}

// --- Return JSON ---
echo json_encode([
    'subtotal' => round($totalAmount, 2),
    'discount' => round($totalDiscount, 2),
    'categoryDiscount' => round($totalCategoryDiscount, 2),
    'tax' => round($taxAmount, 2),
    'shipping' => round($shippingFee, 2),
    'total' => round($grandTotal, 2)
]);
