<?php
session_start();
include '../../settings/connect.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) && !isset($_COOKIE['user_id'])) {
    echo json_encode(['success'=>false,'message'=>'You must be logged in']);
    exit;
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : (int)$_COOKIE['user_id'];
$itemID = isset($_POST['itemID']) ? (int)$_POST['itemID'] : 0;
$rateScore = isset($_POST['rateScore']) ? (int)$_POST['rateScore'] : 0;
$commentClient = isset($_POST['commentClient']) ? trim($_POST['commentClient']) : '';

if($itemID <= 0 || $rateScore <= 0 || empty($commentClient)) {
    echo json_encode(['success'=>false,'message'=>'All fields are required']);
    exit;
}

try {
    $stmt = $con->prepare("INSERT INTO tblrating (dateRate, clientID, itemID, rateScore, commentClient) VALUES (NOW(), ?, ?, ?, ?)");
    $stmt->execute([$user_id, $itemID, $rateScore, $commentClient]);
    
    echo json_encode(['success'=>true,'message'=>'Review submitted successfully!']);
} catch (PDOException $e) {
    echo json_encode(['success'=>false,'message'=>'Database error: '.$e->getMessage()]);
}
