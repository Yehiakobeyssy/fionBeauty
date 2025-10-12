<?php
include '../../settings/connect.php';
header('Content-Type: application/json');

$clientID = isset($_GET['clientID']) ? (int)$_GET['clientID'] : 0;

if ($clientID <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid client ID.']);
    exit;
}

$sql = "
SELECT 
    i.invoiceID,
    i.invoiceCode,
    i.invoiceDate,
    i.invoiceAmount,
    i.transactionNO,
    s.statusName,

    -- Total items in the invoice
    (
        SELECT COUNT(*) 
        FROM tbldatailinvoice d
        WHERE d.invoiceID = i.invoiceID
    ) AS totalItems,

    -- Commission
    (
        SELECT SUM(
            (d.quantity * d.up - (d.quantity * d.up * d.discount / 100)) * (t.commtion / 100)
        )
        FROM tbldatailinvoice d
        INNER JOIN tblitems t ON d.itmID = t.itmID
        WHERE d.invoiceID = i.invoiceID
    ) AS invoiceCommition,

    -- First item name
    (
        SELECT t.itmName
        FROM tbldatailinvoice d
        INNER JOIN tblitems t ON d.itmID = t.itmID
        WHERE d.invoiceID = i.invoiceID
        ORDER BY d.daitailInvoiceId ASC
        LIMIT 1
    ) AS firstItemName,

    -- First item main picture
    (
        SELECT t.mainpic
        FROM tbldatailinvoice d
        INNER JOIN tblitems t ON d.itmID = t.itmID
        WHERE d.invoiceID = i.invoiceID
        ORDER BY d.daitailInvoiceId ASC
        LIMIT 1
    ) AS firstItemPic

FROM tblinvoice i
LEFT JOIN tblstatus s ON i.invoiceStatus = s.statusID
WHERE i.clientID = :clientID
ORDER BY i.invoiceID DESC
";

$stmt = $con->prepare($sql);
$stmt->bindValue(':clientID', $clientID, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
