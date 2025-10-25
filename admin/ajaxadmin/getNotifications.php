<?php
include "../../settings/connect.php"; // PDO connection
session_start();
$adminId = $_SESSION['admin_id'];

$after_id = isset($_GET['after_id']) ? intval($_GET['after_id']) : 0;

if ($after_id > 0) {
    // ✅ Only load newer notifications than the last one
    $stmt = $con->prepare("
        SELECT n.notificationId, n.text, s.seen
        FROM tblNotification n
        JOIN tblseenNotification s ON n.notificationId = s.notificationId
        WHERE s.adminID = ?
        AND n.notificationId > ?
        ORDER BY n.datenotification DESC
    ");
    $stmt->execute([$adminId, $after_id]);
} else {
    // First load (initial or manual refresh)
    $stmt = $con->prepare("
        SELECT n.notificationId, n.text, s.seen
        FROM tblNotification n
        JOIN tblseenNotification s ON n.notificationId = s.notificationId
        WHERE s.adminID = ?
        ORDER BY n.datenotification DESC
        LIMIT 10
    ");
    $stmt->execute([$adminId]);
}

$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($notifications);
