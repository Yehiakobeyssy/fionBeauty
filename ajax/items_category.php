<?php
session_start();
include '../settings/connect.php';

// ====================
// Read filters from AJAX or GET
// ====================
$categoryId = isset($_POST['categoryId']) ? (int)$_POST['categoryId'] : 0;
$subCatId   = isset($_POST['subCatId']) ? (int)$_POST['subCatId'] : 0;
$brandId    = isset($_POST['brandId']) ? (int)$_POST['brandId'] : 0;
$minPrice   = isset($_POST['minPrice']) ? (float)$_POST['minPrice'] : 0;
$maxPrice   = isset($_POST['maxPrice']) ? (float)$_POST['maxPrice'] : 0;
$rating     = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$keyword    = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';

// ====================
// Determine user ID
// ====================
$user_id = $_SESSION['user_id'] ?? ($_COOKIE['user_id'] ?? 0);

// ====================
// Check if client is active
// ====================
$stmtClient = $con->prepare("SELECT clientActive FROM tblclient WHERE clientID = ?");
$stmtClient->execute([$user_id]);
$clientActive = $stmtClient->fetchColumn() ?: 0;

// ====================
// Base SQL query
// ====================
$sql = "
    SELECT 
        i.itmId,
        i.itmName,
        i.itmDesc,
        i.ingredients,
        i.mainpic,
        i.sellPrice,
        i.promotional,
        i.subCatID,
        c.catName,
        b.brandName,
        COALESCE(AVG(r.rateScore), 0) AS avgRating
    FROM tblitems i
    JOIN tblcategory c ON i.catId = c.categoryId
    LEFT JOIN tblbrand b ON i.brandId = b.brandId
    LEFT JOIN tblrating r ON i.itmId = r.itemID
    WHERE i.itmActive = 1
";

$filters = [];
$params  = [];

// ====================
// Apply filters
// ====================
if ($categoryId > 0) {
    $filters[] = "i.catId = :catId";
    $params[':catId'] = $categoryId;
}

if ($subCatId > 0) {
    $filters[] = "i.subCatID = :subCatId";
    $params[':subCatId'] = $subCatId;
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

// Keyword search across multiple fields
if (!empty($keyword)) {
    $filters[] = "(i.itmName LIKE :keyword OR i.itmDesc LIKE :keyword OR i.ingredients LIKE :keyword)";
    $params[':keyword'] = "%$keyword%";
}

// Append filters to SQL
if (!empty($filters)) {
    $sql .= " AND " . implode(" AND ", $filters);
}

// Group and filter by rating if provided
$sql .= " GROUP BY i.itmId";
if ($rating > 0) {
    $sql .= " HAVING AVG(r.rateScore) >= :rating";
    $params[':rating'] = $rating;
}

$sql .= " ORDER BY i.itmName ASC";

// ====================
// Execute query
// ====================
$stmt = $con->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ====================
// Hide prices if client not active
// ====================
if ($clientActive == 0) {
    foreach ($items as &$item) {
        unset($item['sellPrice']);
    }
}

// ====================
// Return JSON
// ====================
header('Content-Type: application/json');
echo json_encode($items);
