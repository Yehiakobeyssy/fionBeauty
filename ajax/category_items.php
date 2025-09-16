<?php
// category_items.php
include '../settings/connect.php';

$categoryId = isset($_GET['categoryId']) ? (int)$_GET['categoryId'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 4;
$offset = ($page - 1) * $limit;

// check client active (example: user_id=0)
$user_id = 0;
$stmtClient = $con->prepare("SELECT clientActive FROM tblclient WHERE clientID=?");
$stmtClient->execute([$user_id]);
$clientActive = $stmtClient->fetchColumn() ?: 0;

// total pages
$totalStmt = $con->prepare("SELECT COUNT(*) FROM tblitems WHERE itmActive=1 AND catId=?");
$totalStmt->execute([$categoryId]);
$totalItems = $totalStmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

// fetch items
$stmt = $con->prepare("
    SELECT itmId,itmName,sellPrice,dateAdd,mainpic,
           SUBSTRING(itmDesc,1,40) as itmDesc
    FROM tblitems
    WHERE itmActive=1 AND catId=?
    ORDER BY dateAdd DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
$stmt->bindValue(2, $limit, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// add rating
foreach ($items as &$item) {
    $stmtRate = $con->prepare("SELECT AVG(rateScore) FROM tblrating WHERE itemID=?");
    $stmtRate->execute([$item['itmId']]);
    $item['rating'] = round($stmtRate->fetchColumn() ?: 0, 1);
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "items" => $items,
    "page" => $page,
    "totalPages" => $totalPages,
    "clientActive" => $clientActive
]);
