<?php
include '../../settings/connect.php'; // your PDO connection ($con)

$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
$date   = isset($_POST['date']) ? trim($_POST['date']) : '';
$page   = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$limit  = 7;
$offset = ($page - 1) * $limit;

// Build dynamic WHERE conditions
$conditions = [];
$params = [];

if ($search != '') {
    $conditions[] = "brandName LIKE :search";
    $params[':search'] = "%$search%";
}
if ($status !== '') {
    $conditions[] = "brandActive = :status";
    $params[':status'] = $status;
}
if ($date != '') {
    $conditions[] = "DATE(brandInputDate) = :date";
    $params[':date'] = $date;
}

$where = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

// Count total records
$countStmt = $con->prepare("SELECT COUNT(*) as total FROM tblbrand $where");
$countStmt->execute($params);
$total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Fetch current page data
$query = "SELECT * FROM tblbrand $where ORDER BY brandId DESC LIMIT :offset, :limit";
$stmt = $con->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return JSON
echo json_encode([
    'total' => $total,
    'brands' => $brands,
    'limit' => $limit
]);
?>
