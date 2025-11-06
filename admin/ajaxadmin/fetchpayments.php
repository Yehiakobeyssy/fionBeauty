<?php
include '../../settings/connect.php';

$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$perPage = 10;
$start = ($page - 1) * $perPage;

$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$statusFilter = isset($_POST['status']) ? $_POST['status'] : '';
$dateFilter = isset($_POST['date']) ? $_POST['date'] : '';
$timeInterval = isset($_POST['timeinterval']) ? (int)$_POST['timeinterval'] : 999999;

// ✅ Combined base query for invoices + workshops
$baseQuery = "
    SELECT invoiceID, invoiceCode, invoiceDate, clientID, invoiceAmount AS amount,
           paymentMethod, transactionNO AS transactionID, invoiceStatus AS statusID,
           'Order' AS type
    FROM tblinvoice

    UNION ALL

    SELECT invoiceID, invoiceCode, invoiceDate, clientID, totalAmount AS amount,
           method AS paymentMethod, transactionID, status AS statusID,
           'Workshop' AS type
    FROM tblinvoiceworkshop
";

// ✅ Start WHERE block
$where = "WHERE 1=1";

// ✅ Apply filters
if (!empty($statusFilter)) {
    $where .= " AND s.statusID = :statusID";
}

if (!empty($dateFilter)) {
    $where .= " AND DATE(inv.invoiceDate) = :invoiceDate";
} elseif ($timeInterval != 999999) {
    $where .= " AND inv.invoiceDate >= DATE_SUB(CURDATE(), INTERVAL :timeInterval DAY)";
}

if (!empty($search)) {
    $where .= " AND (
        CONCAT(c.clientFname, ' ', c.clientLname) LIKE :search OR
        c.clientPhoneNumber LIKE :search OR
        c.clientEmail LIKE :search OR
        inv.invoiceCode LIKE :search OR
        inv.paymentMethod LIKE :search OR
        inv.transactionID LIKE :search
    )";
}

// ✅ Count total records (no filters)
$totalStmt = $con->prepare("
    SELECT COUNT(*) AS total FROM ($baseQuery) AS allInvoices
");
$totalStmt->execute();
$totalRecords = $totalStmt->fetchColumn();

// ✅ Count filtered records
$countStmt = $con->prepare("
    SELECT COUNT(*) AS total
    FROM ($baseQuery) AS inv
    LEFT JOIN tblclient c ON inv.clientID = c.clientID
    LEFT JOIN tblstatus s ON inv.statusID = s.statusID
    $where
");
if (!empty($search)) $countStmt->bindValue(':search', "%$search%");
if (!empty($statusFilter)) $countStmt->bindValue(':statusID', $statusFilter);
if (!empty($dateFilter)) $countStmt->bindValue(':invoiceDate', $dateFilter);
if ($timeInterval != 999999 && empty($dateFilter))
    $countStmt->bindValue(':timeInterval', $timeInterval, PDO::PARAM_INT);
$countStmt->execute();
$filteredRecords = $countStmt->fetchColumn();

// ✅ Fetch actual data (with LIMIT)
$stmt = $con->prepare("
    SELECT inv.*, 
           CONCAT(c.clientFname, ' ', c.clientLname) AS fullname,
           c.clientPhoneNumber, c.clientEmail,
           s.statusName
    FROM ($baseQuery) AS inv
    LEFT JOIN tblclient c ON inv.clientID = c.clientID
    LEFT JOIN tblstatus s ON inv.statusID = s.statusID
    $where
    ORDER BY inv.invoiceDate DESC
    LIMIT :start, :perPage
");
if (!empty($search)) $stmt->bindValue(':search', "%$search%");
if (!empty($statusFilter)) $stmt->bindValue(':statusID', $statusFilter);
if (!empty($dateFilter)) $stmt->bindValue(':invoiceDate', $dateFilter);
if ($timeInterval != 999999 && empty($dateFilter))
    $stmt->bindValue(':timeInterval', $timeInterval, PDO::PARAM_INT);
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Return JSON
echo json_encode([
    'data' => $data,
    'total' => (int)$filteredRecords,
    'totalAll' => (int)$totalRecords,
    'perPage' => $perPage,
    'start' => $start
]);
?>
