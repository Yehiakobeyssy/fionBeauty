<?php
// Include the database connection file
include '../setting/connect.php';

try {
    // Get the search term from the query string
    $searchTerm = isset($_GET['keyword']) ? $_GET['keyword'] : '';

    // Fetch item names and descriptions with optional search filter
    $query = "SELECT 
        i.itmId,
        i.itmName,
    FROM tblitem i
    WHERE LOWER(i.itmName) LIKE :searchTerm
    ORDER BY i.itmId ASC";

    $stmt = $con->prepare($query);
    $stmt->bindValue(':searchTerm', '%' . strtolower($searchTerm) . '%', PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($result);
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>
