<?php
include '../../settings/connect.php';

header('Content-Type: application/json; charset=utf-8');

$catId = isset($_POST['catId']) ? (int)$_POST['catId'] : 0;

if ($catId <= 0) {
    echo json_encode([
        'success' => false,
        'data' => []
    ]);
    exit;
}

$sql = "
    SELECT subCatID, subCatName
    FROM tblsubcategory
    WHERE subCatActive = 1
      AND catID = :catId
    ORDER BY subCatName
";

$stmt = $con->prepare($sql);
$stmt->bindValue(':catId', $catId, PDO::PARAM_INT);
$stmt->execute();

echo json_encode([
    'success' => true,
    'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
