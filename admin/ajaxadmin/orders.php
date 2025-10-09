<?php
include '../../settings/connect.php';
header('Content-Type: application/json');

$days = isset($_POST['days']) ? (int)$_POST['days'] : 9999;
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
$selectedDate = isset($_POST['selectedDate']) ? $_POST['selectedDate'] : '';

$where = [];
$params = [];

// Time filter
if ($days != 9999) {
    if ($days == 1) {
        $where[] = "DATE(i.invoiceDate) = CURDATE()";
    } else {
        $where[] = "i.invoiceDate >= DATE_SUB(CURDATE(), INTERVAL :days DAY)";
        $params[':days'] = $days;
    }
}

// Status filter
if ($status > 0) {
    $where[] = "i.invoiceStatus = :status";
    $params[':status'] = $status;
}

// Selected date
if (!empty($selectedDate)) {
    $where[] = "DATE(i.invoiceDate) = :selectedDate";
    $params[':selectedDate'] = $selectedDate;
}

// Search
if (!empty($search)) {
    $where[] = "(c.clientFname LIKE :search OR c.clientLname LIKE :search OR c.clientEmail LIKE :search OR i.invoiceCode LIKE :search)";
    $params[':search'] = "%$search%";
}

$whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

$sql = "
SELECT 
    i.invoiceID,
    i.invoiceCode,
    i.invoiceDate,
    i.invoiceAmount,
    i.transactionNO,
    s.statusName,
    CONCAT(c.clientFname, ' ', c.clientLname) AS clientName,
    c.clientEmail,
    (
        SELECT COUNT(*) 
        FROM tbldatailinvoice d
        WHERE d.invoiceID = i.invoiceID
    ) AS totalItems,
    (
        SELECT SUM(
            (d.quantity * d.up - (d.quantity * d.up * d.discount / 100)) * (t.commtion / 100)
        )
        FROM tbldatailinvoice d
        INNER JOIN tblitems t ON d.itmID = t.itmID
        WHERE d.invoiceID = i.invoiceID
    ) AS invoiceCommition
FROM tblinvoice i
INNER JOIN tblclient c ON i.clientID = c.clientID
INNER JOIN tblstatus s ON i.invoiceStatus = s.statusID
$whereSQL
ORDER BY i.invoiceID DESC
";

$stmt = $con->prepare($sql);
foreach ($params as $key => $value) {
    if ($key === ':days') {
        $stmt->bindValue($key, (int)$value, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($key, $value);
    }
}
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
