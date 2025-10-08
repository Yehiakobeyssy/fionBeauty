<?php
header('Content-Type: application/json');
include '../../settings/connect.php';

$days = isset($_POST['days']) ? intval($_POST['days']) : 9999;
$dateLimit = date('Y-m-d', strtotime("-$days days"));

// 🧠 Determine grouping granularity
if ($days > 365) {
    $groupBy = "DATE_FORMAT(invoiceDate, '%Y-%m')";
    $label = 'Month';
} elseif ($days > 30) {
    $groupBy = "YEARWEEK(invoiceDate, 1)";
    $label = 'Week';
} elseif ($days > 1) {
    $groupBy = "DATE(invoiceDate)";
    $label = 'Day';
} else {
    $groupBy = "HOUR(invoiceDate)";
    $label = 'Hour';
}

// 💰 Query total revenue per period
$sql = "
    SELECT 
        $groupBy AS period,
        SUM(invoiceAmount) AS totalRevenue
    FROM tblinvoice
    WHERE invoiceDate >= :dateLimit
      AND invoiceStatus < 5
    GROUP BY period
    ORDER BY period ASC
";

$stmt = $con->prepare($sql);
$stmt->execute([':dateLimit' => $dateLimit]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 📊 Format for Google Charts
$data = [[$label, 'Revenue']];
foreach ($rows as $row) {
    switch ($label) {
        case 'Month':
            $periodLabel = date('M Y', strtotime($row['period'] . '-01'));
            break;
        case 'Week':
            $periodLabel = 'Week ' . substr($row['period'], 4);
            break;
        case 'Day':
            $periodLabel = date('d M', strtotime($row['period']));
            break;
        case 'Hour':
            $periodLabel = $row['period'] . ':00';
            break;
        default:
            $periodLabel = $row['period'];
    }

    $data[] = [$periodLabel, round((float)$row['totalRevenue'], 2)];
}

echo json_encode($data);
?>
