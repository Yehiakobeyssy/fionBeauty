<?php
session_start();
include "../settings/connect.php";

if (!isset($_SESSION['user_id'])) {
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$itemId = (int)$_POST['itemId'];

$stmt = $con->prepare("
    DELETE FROM tblfavoriteitm
    WHERE itemID = ? AND clientID = ?
");

$stmt->execute([$itemId, $user_id]);

echo json_encode([
    'status' => 'success'
]);