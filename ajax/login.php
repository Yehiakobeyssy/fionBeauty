<?php
// ajax/login.php
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$email = isset($_POST['loginemail']) ? trim($_POST['loginemail']) : '';
$pass  = isset($_POST['loginpass']) ? $_POST['loginpass'] : '';

if (!$email || !$pass) {
    echo json_encode(['success' => false, 'message' => 'Please fill both fields']);
    exit;
}

require_once __DIR__ . '/../settings/connect.php'; // $con

try {
    $hash = sha1($pass); // keep existing hashing (not recommended long-term)
    $stmt = $con->prepare('SELECT clientID FROM tblclient WHERE clientEmail = ? AND clientPassword = ? LIMIT 1');
    $stmt->execute([$email, $hash]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user_id'] = (int)$user['clientID'];
        // optionally set cookie (if wanted)
        // setcookie('user_id', $_SESSION['user_id'], time()+60*60*24*30, '/');
        echo json_encode(['success' => true, 'redirect' => 'index.php']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Email or password incorrect']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
