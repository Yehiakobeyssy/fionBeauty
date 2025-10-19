<?php
header('Content-Type: application/json');
include '../../settings/connect.php';

$cityID = isset($_POST['cityID']) ? (int)$_POST['cityID'] : 0;
$name = isset($_POST['cityName']) ? trim($_POST['cityName']) : '';

if ($cityID <= 0 || $name === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

try {
    $stmt = $con->prepare("UPDATE tblcity SET cityName = ? WHERE cityID = ?");
    $stmt->execute([$name, $cityID]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
