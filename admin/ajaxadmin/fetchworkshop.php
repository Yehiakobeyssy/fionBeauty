<?php
require_once("../../settings/connect.php"); // your PDO connection ($con)

// Get filters
$dateFilter = $_POST['date'] ?? '';
$search = $_POST['search'] ?? '';
$paid = $_POST['paid'] ?? '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$perPage = 5;
$start = ($page - 1) * $perPage;

// Base query
$where = [];
$params = [];

// Filters
if ($dateFilter != '') {
    $where[] = "w.workshop_date = ?";
    $params[] = $dateFilter;
}

if ($search != '') {
    $where[] = "(w.title LIKE ? OR w.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($paid !== '') {
    if ($paid == "0") {
        $where[] = "w.cost = 0";
    } elseif ($paid == "1") {
        $where[] = "w.cost > 0";
    }
}

// Build WHERE
$whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

// Count total
$stmt = $con->prepare("SELECT COUNT(*) FROM workshops w $whereSQL");
$stmt->execute($params);
$total = $stmt->fetchColumn();

// Fetch data with pagination
$sql = "SELECT w.*,
        (SELECT COUNT(*) FROM workshop_bookings b WHERE b.workshop_id = w.id) AS totalClients
        FROM workshops w
        $whereSQL
        ORDER BY w.workshop_date DESC
        LIMIT $start, $perPage";

$stmt = $con->prepare($sql);
$stmt->execute($params);
$workshops = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare JSON
$response = [
    "total" => $total,
    "data" => $workshops,
];

echo json_encode($response);
exit;
?>
