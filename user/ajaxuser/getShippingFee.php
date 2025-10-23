<?php
session_start();
include '../../settings/connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$addressID = (int)($data['addressID'] ?? 0);

if($addressID > 0){
    $stmt = $con->prepare("
        SELECT tblprovince.shippingFee
        FROM tbladdresse
        INNER JOIN tblprovince ON tblprovince.provinceID = tbladdresse.provinceID
        WHERE addresseID = ?
    ");
    $stmt->execute([$addressID]);
    $fee = $stmt->fetchColumn();
    
    echo json_encode(['shippingFee' => $fee ?? 0]);
} else {
    echo json_encode(['shippingFee' => 0]);
}
?>
