<?php
include "../../settings/connect.php";
session_start();
$adminId = $_SESSION['admin_id'];

$stmt = $con->prepare("SELECT COUNT(*) AS count FROM tblseennotification WHERE adminID = ? AND seen = 0");
$stmt->execute([$adminId]);
$count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

echo json_encode(['count' => intval($count)]);
