<?php
include "../../settings/connect.php";
session_start();
$adminId = $_SESSION['admin_id'];

$stmt = $con->prepare("UPDATE tblseennotification SET seen = 1 WHERE adminID = ?");
$stmt->execute([$adminId]);
echo json_encode(['success' => true]);
