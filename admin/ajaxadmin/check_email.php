<?php
// ajaxadmin/check_email.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid method']);
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
if (!$email) {
    echo json_encode(['error' => 'No email']);
    exit;
}

require_once __DIR__ . '/../../settings/connect.php'; 

try {
    $sql = $con->prepare('SELECT adminID  FROM tbladmin WHERE adminEmail = ? LIMIT 1');
    $sql->execute([$email]);
    $exists = (bool)$sql->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['exists' => $exists]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Query failed']);
}
