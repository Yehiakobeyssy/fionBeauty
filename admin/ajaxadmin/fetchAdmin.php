<?php
require_once '../../settings/connect.php';
header('Content-Type: application/json');

$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 9999;

$offset = ($page - 1) * $limit;

// Build WHERE conditions
$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(a.fName LIKE :search OR a.lName LIKE :search OR a.adminEmail LIKE :search OR a.phoneNumber LIKE :search)";
    $params[':search'] = "%$search%";
}

// Duration filter (based on hittingDate)
if ($duration != 9999) {
    $where[] = "a.hittingDate >= DATE_SUB(CURDATE(), INTERVAL :duration DAY)";
    $params[':duration'] = $duration;
}

$whereSql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

// Get total count
$sqlCount = $con->prepare("SELECT COUNT(*) FROM tbladmin a $whereSql");
foreach ($params as $key => $val) {
    $sqlCount->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$sqlCount->execute();
$totalRecords = $sqlCount->fetchColumn();

// Get paginated data
$sql = $con->prepare("
    SELECT a.adminID, a.fName, a.lName, a.phoneNumber, a.adminEmail, a.hittingDate, 
           a.adminActive, a.admin_block, r.adminRoll
    FROM tbladmin a
    LEFT JOIN tbladminroll r ON a.adminRole = r.adminRollId
    $whereSql
    ORDER BY a.adminID DESC
    LIMIT :offset, :limit
");

foreach ($params as $key => $val) {
    $sql->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$sql->bindValue(':offset', $offset, PDO::PARAM_INT);
$sql->bindValue(':limit', $limit, PDO::PARAM_INT);
$sql->execute();

$admins = $sql->fetchAll(PDO::FETCH_ASSOC);

// Format and add status
foreach ($admins as &$row) {
    $row['formattedDate'] = $row['hittingDate']
        ? date('d M Y', strtotime($row['hittingDate']))
        : '-';

    if ($row['adminActive'] == 1) {
        $row['statusText'] = 'Active';
        $row['statusClass'] = 'alert-success';
    } else {
        $row['statusText'] = 'Inactive';
        $row['statusClass'] = 'alert-danger';
    }

    if ($row['admin_block'] == 1) {
        $row['blockText'] = 'Blocked';
        $row['blockClass'] = 'alert-danger';
    } else {
        $row['blockText'] = '';
        $row['blockClass'] = '';
    }
}

echo json_encode([
    'data' => $admins,
    'total' => $totalRecords
]);
