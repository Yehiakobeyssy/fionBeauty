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

// --- 1️⃣ Main Invoices ---
$sql1 = "
    SELECT 
        $groupBy AS period,
        SUM(invoiceAmount) AS totalRevenue
    FROM tblinvoice
    WHERE invoiceDate >= :dateLimit
      AND invoiceStatus < 5
    GROUP BY period
    ORDER BY period ASC
";
$stmt1 = $con->prepare($sql1);
$stmt1->execute([':dateLimit' => $dateLimit]);
$data1 = $stmt1->fetchAll(PDO::FETCH_KEY_PAIR); // [period => totalRevenue]

// --- 2️⃣ Workshop Invoices ---
$sql2 = "
    SELECT 
        $groupBy AS period,
        SUM(totalAmount) AS totalRevenue
    FROM tblinvoiceworkshop
    WHERE invoiceDate >= :dateLimit
    GROUP BY period
    ORDER BY period ASC
";
$stmt2 = $con->prepare($sql2);
$stmt2->execute([':dateLimit' => $dateLimit]);
$data2 = $stmt2->fetchAll(PDO::FETCH_KEY_PAIR); // [period => totalRevenue]

// --- 3️⃣ Merge both results ---
$allPeriods = array_unique(array_merge(array_keys($data1), array_keys($data2)));
sort($allPeriods);

$result = [[$label, 'Invoices Revenue', 'Workshop Revenue']];

foreach ($allPeriods as $period) {
    $invoiceVal = isset($data1[$period]) ? (float)$data1[$period] : 0;
    $workshopVal = isset($data2[$period]) ? (float)$data2[$period] : 0;

    // Format label
    switch ($label) {
        case 'Month':
            $periodLabel = date('M Y', strtotime($period . '-01'));
            break;
        case 'Week':
            $periodLabel = 'Week ' . substr($period, 4);
            break;
        case 'Day':
            $periodLabel = date('d M', strtotime($period));
            break;
        case 'Hour':
            $periodLabel = $period . ':00';
            break;
        default:
            $periodLabel = $period;
    }

    $result[] = [$periodLabel, round($invoiceVal, 2), round($workshopVal, 2)];
}

echo json_encode($result);
?>
