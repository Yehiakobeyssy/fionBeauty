<?php
header('Content-Type: application/json');
include '../../settings/connect.php';

$provinceID = isset($_POST['provinceID']) ? (int)$_POST['provinceID'] : 0;
$field = isset($_POST['field']) ? $_POST['field'] : '';
$value = isset($_POST['value']) ? (float)$_POST['value'] : 0;

// تأكد من الحقول المسموح تعديلها
$allowedFields = ['shippingFee', 'amount_over'];
if($provinceID <= 0 || !in_array($field, $allowedFields)){
    echo json_encode(['success'=>false,'message'=>'Invalid input']);
    exit;
}

try {
    $stmt = $con->prepare("UPDATE tblprovince SET {$field} = ? WHERE provinceID = ?");
    $stmt->execute([$value, $provinceID]);
    echo json_encode(['success'=>true]);
} catch(Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>
