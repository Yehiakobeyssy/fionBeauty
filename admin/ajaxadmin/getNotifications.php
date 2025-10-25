<?php
include "../../settings/connect.php"; // PDO connection
session_start();
$adminId = $_SESSION['admin_id'];

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit  = isset($_GET['limit'])  ? intval($_GET['limit'])  : 5;

// Get notifications for this admin, newest first
$stmt = $con->prepare("
    SELECT n.text, s.seen
    FROM tblNotification n
    JOIN tblseenNotification s ON n.notificationId = s.notificationId
    WHERE s.adminID = ?
    ORDER BY n.datenotification DESC
    LIMIT ?, ?
");
$stmt->bindValue(1, $adminId, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->bindValue(3, $limit, PDO::PARAM_INT);
$stmt->execute();

$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($notifications);
