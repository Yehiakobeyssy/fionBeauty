<?php
// items.php
include '../settings/connect.php'; // contains $con connection
$user_id = 0 ; // example logged client id (replace with real session)

// pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 4;
$offset = ($page - 1) * $limit;

// check client active
$stmtClient = $con->prepare("SELECT clientActive FROM tblclient WHERE clientID=?");
$stmtClient->execute([$user_id]);
$clientActive = $stmtClient->fetchColumn() ?: 0;

// total pages
$totalStmt = $con->query("SELECT COUNT(*) FROM tblitems WHERE itmActive=1");
$totalItems = $totalStmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

// fetch items
$stmt = $con->prepare("
    SELECT itmId,itmName,sellPrice,dateAdd,
           SUBSTRING(itmDesc,1,40) as itmDesc
    FROM tblitems
    WHERE itmActive=1
    ORDER BY dateAdd DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// add rating
foreach ($items as &$item) {
  $stmtRate = $con->prepare("SELECT AVG(rateScore) FROM tblrating WHERE itemID=?");
  $stmtRate->execute([$item['itmId']]);
  $item['rating'] = round($stmtRate->fetchColumn() ?: 0, 1);
}

echo json_encode([
  "items" => $items,
  "page" => $page,
  "totalPages" => $totalPages,
  "clientActive" => $clientActive
]);
