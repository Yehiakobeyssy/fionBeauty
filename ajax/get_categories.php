<?php
include '../settings/connect.php';

$stmt = $con->query("SELECT categoryId, catName FROM tblcategory ORDER BY catName ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($categories);
