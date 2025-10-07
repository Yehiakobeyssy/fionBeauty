<?php
header('Content-Type: application/json');
session_start();
include '../../settings/connect.php';

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// Email required
if ($email === "") {
    echo json_encode(['status' => 'invalid_email']);
    exit;
}

// Check email exists
$sql = $con->prepare("SELECT adminID, adminPassword FROM tbladmin WHERE adminEmail = ?");
$sql->execute([$email]);
$admin = $sql->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo json_encode(['status' => 'invalid_email']);
    exit;
}

// If password is empty → only checking email
if ($password === "") {
    echo json_encode(['status' => 'ok']); // email exists
    exit;
}

// Check password
if ($admin['adminPassword'] !== sha1($password)) {
    echo json_encode(['status' => 'wrong_password']);
    exit;
}

// Successful login
$_SESSION['adminID'] = $admin['adminID'];
echo json_encode(['status' => 'success']);
