<?php
include '../../settings/connect.php';

header('Content-Type: application/json; charset=utf-8');

$catId = isset($_POST['catId']) ? (int) $_POST['catId'] : 0;

if ($catId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid category ID'
    ]);
    exit;
}

try {
    $sql = "
        SELECT 
            subCatID,
            subCatName,
            subCatPic,
            subCatDiscription
        FROM tblsubcategory
        WHERE subCatActive = 1
          AND catID = :catId
        ORDER BY subCatName ASC
    ";

    $stmt = $con->prepare($sql);
    $stmt->bindValue(':catId', $catId, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
        // 'error' => $e->getMessage() // enable only in debug
    ]);
}
