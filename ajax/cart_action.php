<?php
session_start();
include "../settings/connect.php"; // $con = new PDO(...)

$action = $_POST['action'] ?? '';
$itemId = intval($_POST['itemId'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);


if ($action === 'clearall') {
    unset($_SESSION['cart']);
    echo "cleared";
    exit;
}


// --- ADD OR UPDATE CART ---
if ($action === 'update') {
    $quantityInput = $quantity > 0 ? $quantity : null;

    // Get minQuantity
    $stmt = $con->prepare("SELECT minQuantity FROM tblitems WHERE itmId = ?");
    $stmt->execute([$itemId]);
    $minQuantity = $stmt->fetchColumn() ?: 1;

    if (isset($_SESSION['cart'][$itemId])) {
        $_SESSION['cart'][$itemId] += ($quantityInput ?? 1);
    } else {
        $_SESSION['cart'][$itemId] = ($quantityInput ?? $minQuantity);
    }
}

// --- CHANGE QUANTITY (PLUS/MINUS) ---
if ($action === 'change') {
    $newQuantity = intval($_POST['quantity']);
    $stmt = $con->prepare("SELECT minQuantity FROM tblitems WHERE itmId = ?");
    $stmt->execute([$itemId]);
    $minQuantity = $stmt->fetchColumn() ?: 1;

    if ($newQuantity < $minQuantity) $newQuantity = $minQuantity;
    $_SESSION['cart'][$itemId] = $newQuantity;
}

// --- DELETE ITEM ---
if ($action === 'delete') {
    unset($_SESSION['cart'][$itemId]);
}

// --- DISPLAY CART ---
if ($action === 'display') {
    if (empty($_SESSION['cart'])) {
        echo "<p class='text-center'>Your cart is empty.</p>";
        exit;
    }

    echo "<table>
            <thead>
                <tr>
                    <th>Product Details</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Discount</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>";

    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt = $con->prepare("SELECT * FROM tblitems WHERE itmId = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) continue;

        $price = $item['sellPrice'];
        $minQty = $item['minQuantity'];

        // Check discount
        $stmt = $con->prepare("SELECT precent FROM tbldiscountitem WHERE itemID = ? AND quatity  <= ? ORDER BY quatity DESC LIMIT 1");
        $stmt->execute([$id, $qty]);
        $discountPercent = $stmt->fetchColumn() ?: 0;

        $discountAmount = ($price * $discountPercent / 100);
        $discountedPrice = $price - $discountAmount;
        $subtotal = $discountedPrice * $qty;

        echo "
        <tr data-id='{$id}'>
            <td><img src='images/items/{$item['mainpic']}'> {$item['itmName']}</td>
            <td>\${$price}</td>
            <td>
                <div class='input_q'>
                    <button class='qty-btn minus'>−</button>
                    <input type='text' value='{$qty}' class='qty-input' readonly>
                    <button class='qty-btn plus'>+</button>
                </div>
            </td>
            <td>{$discountPercent}%</td>
            <td>\$" . number_format($subtotal, 2) . "</td>
            <td>
                <button class='delete-btn'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='17' height='20' viewBox='0 0 17 20' fill='none'>
                        <path d='M7.20714 8C7.20714 7.50294 6.8042 7.1 6.30714 7.1C5.81008 7.1 5.40714 7.50294 5.40714 8H7.20714ZM5.40714 16C5.40714 16.4971 5.81008 16.9 6.30714 16.9C6.8042 16.9 7.20714 16.4971 7.20714 16H5.40714ZM11.4528 8C11.4528 7.50294 11.0498 7.1 10.5528 7.1C10.0557 7.1 9.65275 7.50294 9.65275 8H11.4528ZM9.65275 16C9.65275 16.4971 10.0557 16.9 10.5528 16.9C11.0498 16.9 11.4528 16.4971 11.4528 16H9.65275ZM3.2206 18.782L3.61009 17.9707L3.2206 18.782ZM2.2929 17.908L3.08454 17.4799H3.08454L2.2929 17.908ZM14.567 17.908L13.7753 17.4798V17.4799L14.567 17.908ZM13.6393 18.782L13.2498 17.9707H13.2498L13.6393 18.782ZM1.00012 4.1C0.503066 4.1 0.100122 4.50294 0.100122 5C0.100122 5.49706 0.503066 5.9 1.00012 5.9V4.1ZM15.8598 5.9C16.3568 5.9 16.7598 5.49706 16.7598 5C16.7598 4.50294 16.3568 4.1 15.8598 4.1V5.9ZM3.81503 5C3.81503 5.49706 4.21798 5.9 4.71503 5.9C5.21209 5.9 5.61503 5.49706 5.61503 5H3.81503ZM11.2449 5C11.2449 5.49706 11.6478 5.9 12.1449 5.9C12.6419 5.9 13.0449 5.49706 13.0449 5H11.2449ZM5.40714 8V16H7.20714V8H5.40714ZM9.65275 8V16H11.4528V8H9.65275ZM13.8984 5V15.8H15.6984V5H13.8984ZM11.4019 18.1H5.45802V19.9H11.4019V18.1ZM1.16153 5V15.8H2.96153V5H1.16153ZM5.45802 18.1C4.84958 18.1 4.44425 18.0994 4.13284 18.0754C3.83045 18.0521 3.69397 18.0109 3.61009 17.9707L2.83111 19.5934C3.20132 19.7711 3.58911 19.8389 3.99469 19.8701C4.39123 19.9006 4.87757 19.9 5.45802 19.9V18.1ZM1.16153 15.8C1.16153 16.3443 1.16074 16.81 1.19381 17.1914C1.22794 17.5849 1.30303 17.9696 1.50125 18.3361L3.08454 17.4799C3.05139 17.4186 3.01079 17.3093 2.98708 17.0358C2.96231 16.7502 2.96153 16.3758 2.96153 15.8H1.16153ZM3.61009 17.9707C3.37525 17.8579 3.1944 17.683 3.08454 17.4799L1.50125 18.3361C1.79844 18.8856 2.26708 19.3226 2.83111 19.5934L3.61009 17.9707ZM13.8984 15.8C13.8984 16.3758 13.8976 16.7502 13.8728 17.0358C13.8491 17.3093 13.8085 17.4186 13.7753 17.4798L15.3586 18.3361C15.5569 17.9696 15.6319 17.5849 15.6661 17.1914C15.6992 16.81 15.6984 16.3443 15.6984 15.8H13.8984ZM11.4019 19.9C11.9823 19.9 12.4687 19.9006 12.8652 19.8701C13.2708 19.8389 13.6586 19.7711 14.0288 19.5934L13.2498 17.9707C13.1659 18.0109 13.0294 18.0521 12.7271 18.0754C12.4156 18.0994 12.0103 18.1 11.4019 18.1V19.9ZM13.7753 17.4799C13.6655 17.683 13.4846 17.8579 13.2498 17.9707L14.0288 19.5934C14.5928 19.3226 15.0615 18.8856 15.3586 18.3361L13.7753 17.4799ZM1.00012 5.9H2.06153V4.1H1.00012V5.9ZM2.06153 5.9H14.7984V4.1H2.06153V5.9ZM14.7984 5.9H15.8598V4.1H14.7984V5.9ZM5.61503 4.2C5.61503 3.05108 6.74461 1.9 8.42995 1.9V0.1C6.01191 0.1 3.81503 1.8143 3.81503 4.2H5.61503ZM8.42995 1.9C10.1153 1.9 11.2449 3.05108 11.2449 4.2H13.0449C13.0449 1.8143 10.848 0.1 8.42995 0.1V1.9ZM3.81503 4.2V5H5.61503V4.2H3.81503ZM11.2449 4.2V5H13.0449V4.2H11.2449Z' fill='#E01212'/>
                    </svg>
                </button>
            </td>
        </tr>";
    }

    echo "</tbody></table>";
}
?>
