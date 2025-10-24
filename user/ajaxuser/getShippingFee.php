<?php
session_start();
include '../../settings/connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$addressID = (int)($data['addressID'] ?? 0);

$shippingFee = 0;

if ($addressID > 0 && !empty($_SESSION['cart'])) {

    // جلب بيانات المحافظة
    $stmt = $con->prepare("
        SELECT p.shippingFee, p.amount_over
        FROM tbladdresse a
        JOIN tblprovince p ON p.provinceID = a.provinceID
        WHERE a.addresseID = ?
    ");
    $stmt->execute([$addressID]);
    $province = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$province) {
        echo json_encode(['shippingFee' => 0]);
        exit;
    }

    $provinceFee = (float)$province['shippingFee'];
    $amountOver = (float)$province['amount_over'];

    // جلب بيانات العناصر
    $itemIds = array_keys($_SESSION['cart']);
    $totalAmount = 0;
    $allFreeCategory = true;
    $hasDiscount = false;

    if (!empty($itemIds)) {
        $inQuery = implode(',', array_fill(0, count($itemIds), '?'));
        $stmtItems = $con->prepare("
            SELECT i.itmId, i.sellPrice, c.shippingfree_accepted
            FROM tblitems i
            JOIN tblcategory c ON c.categoryId = i.catId
            WHERE i.itmId IN ($inQuery)
        ");
        $stmtItems->execute($itemIds);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $itemId = $item['itmId'];
            $qty = $_SESSION['cart'][$itemId] ?? 0;
            $totalAmount += $item['sellPrice'] * $qty;

            // تحقق إذا كل العناصر من فئة تقبل الشحن المجاني
            if ((int)$item['shippingfree_accepted'] !== 1) {
                $allFreeCategory = false;
            }

            // تحقق من وجود خصم
            $stmtDiscount = $con->prepare("
                SELECT precent 
                FROM tbldiscountitem 
                WHERE itemID = ? AND quatity <= ? 
                ORDER BY quatity DESC 
                LIMIT 1
            ");
            $stmtDiscount->execute([$itemId, $qty]);
            $discountPercent = (float)($stmtDiscount->fetchColumn() ?? 0);

            if ($discountPercent > 0) {
                $hasDiscount = true;
            }
        }
    }

    // تطبيق قواعد الشحن
    if (!$hasDiscount && $allFreeCategory && $totalAmount >= $amountOver) {
        $shippingFee = 0; // تحقق الشرط → شحن مجاني
    } else {
        $shippingFee = $provinceFee; // غير ذلك → شحن عادي
    }
}

echo json_encode(['shippingFee' => $shippingFee]);
