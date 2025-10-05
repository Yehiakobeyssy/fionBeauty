<?php
session_start();
require_once "../settings/connect.php"; // your PDO $con connection

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Validate itemId
$itemId = isset($_POST['itemId']) ? intval($_POST['itemId']) : 0;
if ($itemId <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid item ID.'
    ]);
    exit;
}

// Get quantity from POST (optional)
$quantityInput = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;

// Fetch item info (to get minQuantity)
$stmt = $con->prepare("SELECT minQuantity FROM tblitems WHERE itmId = :itmId LIMIT 1");
$stmt->execute([':itmId' => $itemId]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Item not found.'
    ]);
    exit;
}

$minQuantity = intval($item['minQuantity']);

// Determine quantity to add
if (isset($_SESSION['cart'][$itemId])) {
    // Already in cart → increment by 1 OR by specified quantity
    $_SESSION['cart'][$itemId] += ($quantityInput !== null ? $quantityInput : 1);
} else {
    // First time → use quantity input OR minQuantity
    $_SESSION['cart'][$itemId] = ($quantityInput !== null ? $quantityInput : $minQuantity);
}

// Return JSON response
echo json_encode([
    'status' => 'success',
    'message' => 'Item added to cart.',
    'cart_count' => array_sum($_SESSION['cart']),
    'cart' => $_SESSION['cart']
]);
exit;
?>
