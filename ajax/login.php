<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 0); // منع أي output
error_reporting(0);

require_once __DIR__ . '/../settings/connect.php';

$email = $_POST['loginemail'] ?? '';
$pass  = $_POST['loginpass'] ?? '';

if (!$email || !$pass) {
    echo json_encode(['success' => false, 'message' => 'Please fill both fields']);
    exit;
}

try {
    $hash = sha1($pass);
    $stmt = $con->prepare('SELECT clientID FROM tblclient WHERE clientEmail = ? AND clientPassword = ? LIMIT 1');
    $stmt->execute([$email, $hash]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = (int)$user['clientID'];
        echo json_encode(['success' => true, 'redirect' => 'index.php']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Email or password incorrect']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
