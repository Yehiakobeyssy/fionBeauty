<?php
header('Content-Type: application/json');
include '../../settings/connect.php';

$name = isset($_POST['provinceName']) ? trim($_POST['provinceName']) : '';
$shippingFee = isset($_POST['shippingFee']) ? (float)$_POST['shippingFee'] : 0;
$amountOver = isset($_POST['amountOver']) ? (float)$_POST['amountOver'] : 0; // ← جديد

if ($name === '') { 
    echo json_encode(['success'=>false]); 
    exit; 
}

try {
    $stmt = $con->prepare("
        INSERT INTO tblprovince 
        (provinceName, provinceCode, is_deliverable, provinceActive, shippingFee, amount_over) 
        VALUES (?, '', 1, 1, ?, ?)
    ");
    $stmt->execute([$name, $shippingFee, $amountOver]); // ← أضفنا $amountOver
    echo json_encode(['success'=>true]);
} catch(Exception $e) { 
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]); 
}
?>
