<?php
include '../../settings/connect.php';

$cityID = $_POST['cityID'];
$field = $_POST['field'];
$value = $_POST['value'];

$allowed = ['is_deliverable', 'cityactive'];

if (!in_array($field, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid field']);
    exit;
}

try {
    $sql = $con->prepare("UPDATE tblcity SET $field = ? WHERE cityID = ?");
    $sql->execute([$value, $cityID]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
