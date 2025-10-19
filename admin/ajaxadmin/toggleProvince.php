<?php
header('Content-Type: application/json');
include '../../settings/connect.php';

$provinceID = isset($_POST['provinceID']) ? (int)$_POST['provinceID'] : 0;
$field = isset($_POST['field']) ? $_POST['field'] : '';
$value = isset($_POST['value']) ? (int)$_POST['value'] : null;

$allowed = ['provinceActive', 'is_deliverable'];

if ($provinceID <= 0 || !in_array($field, $allowed) || !in_array($value, [0,1])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

try {
    // --- Update province field ---
    $sql = "UPDATE tblprovince SET {$field} = ? WHERE provinceID = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$value, $provinceID]);

    // --- If delivery status changed, update all cities as well ---
    if ($field === 'is_deliverable') {
        $sql2 = "UPDATE tblcity SET is_deliverable = ? WHERE provinceID = ?";
        $stmt2 = $con->prepare($sql2);
        $stmt2->execute([$value, $provinceID]);
    }



    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
