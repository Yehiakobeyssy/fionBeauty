<?php
// ajax/displayitems.php
header('Content-Type: application/json; charset=utf-8');

include '../settings/connect.php'; // make sure this path is correct and $con is a PDO instance

try {
    $searchTerm = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

    // Provide a fallback so empty search returns some rows (optional)
    $like = '%' . strtolower($searchTerm) . '%';

    $query = "SELECT i.itmId, i.itmName
              FROM tblitems i
              WHERE LOWER(i.itmName) LIKE :searchTerm
              ORDER BY i.itmName ASC
              LIMIT 50"; // limit for performance

    $stmt = $con->prepare($query);
    $stmt->bindValue(':searchTerm', $like, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $result]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
