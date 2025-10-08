<?php
header('Content-Type: application/json');
include '../../settings/connect.php';

$days = isset($_POST['days']) ? intval($_POST['days']) : 9999;
$dateLimit = date('Y-m-d', strtotime("-$days days"));

// 🧾 Query: count quantity of items sold per category
$sql = "
    SELECT 
        c.catName AS categoryName,
        SUM(di.quantity) AS totalSold
    FROM tbldatailinvoice di
    INNER JOIN tblinvoice inv ON di.invoiceID = inv.invoiceID
    INNER JOIN tblitems i ON di.itmID = i.itmId
    INNER JOIN tblcategory c ON i.catId = c.categoryId
    WHERE di.status = 1 
      AND inv.invoiceDate >= :dateLimit
      AND inv.invoiceStatus < 5
    GROUP BY c.categoryId, c.catName
    ORDER BY totalSold DESC
    LIMIT 10
";

$stmt = $con->prepare($sql);
$stmt->execute([':dateLimit' => $dateLimit]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 📊 Google Charts format
$data = [['Category', 'Total Sold']];
foreach ($rows as $row) {
    $data[] = [$row['categoryName'], (int)$row['totalSold']];
}

echo json_encode($data);
?>
