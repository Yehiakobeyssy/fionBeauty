<?php
session_start();
include '../settings/connect.php';

// قراءة الفلاتر من AJAX
$categoryId   = isset($_POST['categoryId']) ? (int)$_POST['categoryId'] : 0;
$minPrice     = isset($_POST['minPrice']) ? (float)$_POST['minPrice'] : 0;
$maxPrice     = isset($_POST['maxPrice']) ? (float)$_POST['maxPrice'] : 0;
$rating       = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$brandId      = isset($_POST['brandId']) ? (int)$_POST['brandId'] : 0;

if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];  
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = (int) $_COOKIE['user_id'];  
} else {
    $user_id = 0;
}

$stmtClient = $con->prepare("SELECT clientActive FROM tblclient WHERE clientID=?");
$stmtClient->execute([$user_id]);
$clientActive = $stmtClient->fetchColumn() ?: 0;

// الأساسيات
$sql = "
    SELECT i.itmId, i.itmName, i.itmDesc, i.mainpic, i.sellPrice,i.promotional,
           c.catName, b.brandName,
           COALESCE(AVG(r.rateScore),0) AS avgRating
    FROM tblitems i
    JOIN tblcategory c ON i.catId = c.categoryId
    LEFT JOIN tblbrand b ON i.brandId = b.brandId
    LEFT JOIN tblrating r ON i.itmId = r.itemID
    WHERE i.itmActive = 1
";

$filters = [];
$params  = [];

// الفلاتر
if ($categoryId > 0) {
    $filters[] = "i.catId = :catId";
    $params[':catId'] = $categoryId;
}
if ($brandId > 0) {
    $filters[] = "i.brandId = :brandId";
    $params[':brandId'] = $brandId;
}
if ($minPrice > 0) {
    $filters[] = "i.sellPrice >= :minPrice";
    $params[':minPrice'] = $minPrice;
}
if ($maxPrice > 0) {
    $filters[] = "i.sellPrice <= :maxPrice";
    $params[':maxPrice'] = $maxPrice;
}

if (!empty($filters)) {
    $sql .= " AND " . implode(" AND ", $filters);
}

$sql .= " GROUP BY i.itmId";

if ($rating > 0) {
    $sql .= " HAVING AVG(r.rateScore) >= :rating";
    $params[':rating'] = $rating;
}

$sql .= " ORDER BY i.itmName ASC";

$stmt = $con->prepare($sql);
$stmt->execute($params);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ إخفاء السعر إذا العميل غير مفعل
if ($clientActive == 0) {
    foreach ($items as &$item) {
        unset($item['sellPrice']);
    }
}

// JSON output
header('Content-Type: application/json');
echo json_encode($items);
