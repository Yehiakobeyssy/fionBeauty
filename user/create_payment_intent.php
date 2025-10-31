<?php
session_start();
header('Content-Type: application/json');
require '../settings/connect.php';
require '../vendor/autoload.php';

// --- Get Stripe Secret Key ---
\Stripe\Stripe::setApiKey(getStripeSecretKey($con));

// --- Read input ---
$input = json_decode(file_get_contents("php://input"), true);
$method = $input['method'] ?? 'card';

// --- Shipping fee (from session) ---
$shipfee = isset($_SESSION['order_info']['shipfee']) ? floatval($_SESSION['order_info']['shipfee']) : 0;

// --- Get finance settings ---
$stmt = $con->prepare("SELECT taxPercent, includeTax FROM tblfinancesetting WHERE SettingID = 1");
$stmt->execute();
$finance = $stmt->fetch(PDO::FETCH_ASSOC);
$taxPercent = $finance['taxPercent'] ?? 0;
$includeTax = $finance['includeTax'] ?? 0;

// --- Initialize totals ---
$totalSubtotal = 0;
$totalDiscount = 0;
$categoryTotals = [];

// --- Loop through cart ---
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $itemId => $qty) {
        // Get item details
        $stmt = $con->prepare("SELECT itmId, sellPrice, promotional, catId FROM tblitems WHERE itmId = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) continue;

        $price = $item['sellPrice'];
        $subtotal = $price * $qty;

        // Promotional discount
        $promoDiscount = ($subtotal * $item['promotional']) / 100;

        // Quantity-based discount
        $stmt = $con->prepare("
            SELECT precent 
            FROM tbldiscountitem 
            WHERE itemID = ? AND quatity <= ? 
            ORDER BY quatity DESC LIMIT 1
        ");
        $stmt->execute([$itemId, $qty]);
        $discountPercent = $stmt->fetchColumn() ?: 0;
        $qtyDiscount = ($subtotal * $discountPercent) / 100;

        // Subtotal after both discounts
        $finalSubtotal = $subtotal - $promoDiscount - $qtyDiscount;

        // Add totals
        $totalSubtotal += $subtotal;
        $totalDiscount += $promoDiscount + $qtyDiscount;

        // Track total per category
        $catId = $item['catId'];
        if (!isset($categoryTotals[$catId])) $categoryTotals[$catId] = 0;
        $categoryTotals[$catId] += $subtotal;
    }
}

// --- Apply category-based discounts ---
foreach ($categoryTotals as $catId => $catSubtotal) {
    $stmt = $con->prepare("SELECT amountOver, discount FROM tblcategory WHERE categoryId = ?");
    $stmt->execute([$catId]);
    $cat = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cat && $catSubtotal >= $cat['amountOver']) {
        $catDiscount = ($catSubtotal * $cat['discount']) / 100;
        $totalDiscount += $catDiscount;
    }
}

// --- Tax Calculation ---
if ($includeTax == 1) {
    // Prices already include tax → extract
    $subtotalExclTax = $totalSubtotal / (1 + ($taxPercent / 100));
    $taxAmount = $totalSubtotal - $subtotalExclTax;
    $total = $totalSubtotal - $totalDiscount; // tax included
} else {
    // Add tax after discounts
    $taxBase = $totalSubtotal - $totalDiscount;
    $taxAmount = $taxBase * ($taxPercent / 100);
    $total = $taxBase + $taxAmount;
}

// --- Add shipping fee ---
$total += $shipfee;

// --- Convert to smallest currency unit (cents) ---
$amount = (int) round($total * 100);

// --- Supported payment methods ---
$supportedMethods = ['card', 'klarna', 'afterpay_clearpay', 'paypal'];
if (!in_array($method, $supportedMethods)) $method = 'card';

// --- Create Stripe PaymentIntent ---
try {
    $intent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'cad',
        'payment_method_types' => [$method],
        'metadata' => [
            'method' => $method,
            'user_id' => $_SESSION['user_id'] ?? 'guest'
        ]
    ]);

    echo json_encode([
        'clientSecret' => $intent->client_secret,
        'summary' => [
            'subtotal' => round($totalSubtotal, 2),
            'discount' => round($totalDiscount, 2),
            'tax' => round($taxAmount, 2),
            'shipping' => round($shipfee, 2),
            'total' => round($total, 2)
        ]
    ]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// --- Helper function ---
function getStripeSecretKey($con)
{
    $stmt = $con->prepare("SELECT SK FROM tblfinancesetting WHERE SettingID = 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['SK'] ?? '';
}
?>
