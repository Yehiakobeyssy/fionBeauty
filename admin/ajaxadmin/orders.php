<?php
include '../../settings/connect.php'; // adjust path if needed
header('Content-Type: application/json');

$days = isset($_POST['days']) ? (int)$_POST['days'] : 9999; // 9999 = All time
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
$selectedDate = isset($_POST['selectedDate']) ? $_POST['selectedDate'] : '';

$where = [];
$params = [];

// 1. Time filter
if ($days != 9999) {
    if ($days == 1) {
        $where[] = "DATE(i.invoiceDate) = CURDATE()";
    } else {
        $where[] = "i.invoiceDate >= DATE_SUB(CURDATE(), INTERVAL :days DAY)";
        $params[':days'] = $days;
    }
}

// 2. Status filter
if ($status > 0) {
    $where[] = "i.invoiceStatus = :status";
    $params[':status'] = $status;
}

// 3. Selected specific date
if (!empty($selectedDate)) {
    $where[] = "DATE(i.invoiceDate) = :selectedDate";
    $params[':selectedDate'] = $selectedDate;
}

// 4. Search filter (client name, email, invoice code)
if (!empty($search)) {
    $where[] = "(c.clientFname LIKE :search OR c.clientLname LIKE :search OR c.clientEmail LIKE :search OR i.invoiceCode LIKE :search)";
    $params[':search'] = "%$search%";
}

$whereSQL = '';
if (count($where) > 0) {
    $whereSQL = "WHERE " . implode(" AND ", $where);
}

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
        SELECT itmName 
        FROM tbldatailinvoice d
        INNER JOIN tblitems t ON d.itmID = t.itmID
        WHERE d.invoiceID = i.invoiceID
        ORDER BY d.daitailInvoiceId ASC
        LIMIT 1
    ) AS firstItemName,
    (
        SELECT mainpic 
        FROM tbldatailinvoice d
        INNER JOIN tblitems t ON d.itmID = t.itmID
        WHERE d.invoiceID = i.invoiceID
        ORDER BY d.daitailInvoiceId ASC
        LIMIT 1
    ) AS firstItemPic,
    (
        SELECT COUNT(*) 
        FROM tbldatailinvoice d
        WHERE d.invoiceID = i.invoiceID
    ) AS totalItems
FROM tblinvoice i
INNER JOIN tblclient c ON i.clientID = c.clientID
INNER JOIN tblstatus s ON i.invoiceStatus = s.statusID
$whereSQL
ORDER BY i.invoiceID DESC
";

$stmt = $con->prepare($sql);

// bind params
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
