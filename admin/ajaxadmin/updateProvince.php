<?php
header('Content-Type: application/json');
include '../../settings/connect.php';

$provinceID = isset($_POST['provinceID']) ? (int)$_POST['provinceID'] : 0;
$name = isset($_POST['provinceName']) ? trim($_POST['provinceName']) : '';
$shippingFee = isset($_POST['shippingFee']) ? (float)$_POST['shippingFee'] : 0;

if ($provinceID <= 0 || $name === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

try {
    $stmt = $con->prepare("UPDATE tblprovince SET provinceName = ?, shippingFee = ? WHERE provinceID = ?");
    $stmt->execute([$name, $shippingFee, $provinceID]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
