<?php
include '../settings/connect.php';

$sql = "
    SELECT DISTINCT c.categoryId, c.catName
    FROM tblcategory c
    INNER JOIN tblitems i ON i.catId = c.categoryId
    WHERE i.itmActive = 1
    ORDER BY RAND()
    LIMIT 5
";

$stmt = $con->prepare($sql);
$stmt->execute();

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($categories);
