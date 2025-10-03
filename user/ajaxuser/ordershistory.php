<?php
include '../../settings/connect.php';; // ملف الاتصال
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];  
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = (int) $_COOKIE['user_id'];  
} else {
    $user_id = 0 ;
}

// إعداد المتغيرات
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$limit = 7;
$offset = ($page - 1) * $limit;

$search = isset($_POST['search']) ? trim($_POST['search']) : "";

// الأساس
$sql = "SELECT invoiceID, invoiceCode, invoiceDate, statusName,invoiceAmount
        FROM tblinvoice 
        INNER JOIN tblstatus ON tblstatus.statusID = tblinvoice.invoiceStatus
        WHERE clientID = :clientID";

// البحث
if ($search != "") {
    $sql .= " AND (invoiceCode LIKE :search OR statusName LIKE :search)";
}

$sql .= " ORDER BY invoiceID DESC LIMIT :limit OFFSET :offset";

$stmt = $con->prepare($sql);
$stmt->bindValue(':clientID', $user_id, PDO::PARAM_INT);
if ($search != "") {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// اجمالي الصفحات
$countSql = "SELECT COUNT(*) FROM tblinvoice WHERE clientID = :clientID";
if ($search != "") {
    $countSql .= " AND (invoiceCode LIKE :search OR invoiceStatus IN (
        SELECT statusID FROM tblstatus WHERE statusName LIKE :search
    ))";
}
$countStmt = $con->prepare($countSql);
$countStmt->bindValue(':clientID', $user_id, PDO::PARAM_INT);
if ($search != "") {
    $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// جلب تفاصيل كل فاتورة
$data = [];
foreach ($orders as $order) {
    $stat = $con->prepare('SELECT COUNT(daitailInvoiceId) AS items, SUM(quantity*up) AS total 
                            FROM tbldatailinvoice 
                            WHERE invoiceID = ?');
    $stat->execute([$order['invoiceID']]);
    $result = $stat->fetch();

    $data[] = [
        "invoiceID" => $order['invoiceID'],
        "invoiceCode" => $order['invoiceCode'],
        "date" => date("j F, Y", strtotime($order['invoiceDate'])),
        "status" => $order['statusName'],
        "items" => $result['items'],
        "total" => number_format($order['invoiceAmount'], 2)
    ];
}

echo json_encode([
    "orders" => $data,
    "totalPages" => $totalPages,
    "currentPage" => $page
]);
