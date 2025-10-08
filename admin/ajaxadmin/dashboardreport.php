<?php
header('Content-Type: application/json');
include '../../settings/connect.php';

$type = $_POST['type'] ?? '';
$days = isset($_POST['days']) ? intval($_POST['days']) : 365;
$dateLimit = date('Y-m-d', strtotime("-$days days"));

// --- Determine aggregation ---
if ($days == 1) {
    $groupBy = "HOUR(invoiceDate)";
    $selectPeriod = "DATE_FORMAT(invoiceDate, '%H:00') AS period";
    $xIsTime = true;
} elseif ($days <= 7) {
    $groupBy = "DATE(invoiceDate)";
    $selectPeriod = "DATE(invoiceDate) AS period";
    $xIsTime = true;
} elseif ($days <= 30) {
    $groupBy = "YEAR(invoiceDate), WEEK(invoiceDate)";
    $selectPeriod = "CONCAT('Week ', WEEK(invoiceDate), ' ', YEAR(invoiceDate)) AS period";
    $xIsTime = false;
} else {
    $groupBy = "YEAR(invoiceDate), MONTH(invoiceDate)";
    $selectPeriod = "DATE_FORMAT(invoiceDate, '%b %Y') AS period";
    $xIsTime = false;
}

// --- Summary ---
$summary = [];

$stmt = $con->prepare("SELECT COUNT(DISTINCT clientID) FROM tblinvoice WHERE invoiceStatus < 5 AND invoiceDate >= :dateLimit");
$stmt->execute([':dateLimit'=>$dateLimit]);
$summary['customers'] = (int)$stmt->fetchColumn();

$stmt = $con->prepare("SELECT SUM(di.quantity) FROM tbldatailinvoice di INNER JOIN tblinvoice inv ON di.invoiceID=inv.invoiceID WHERE inv.invoiceDate >= :dateLimit AND inv.invoiceStatus < 5");
$stmt->execute([':dateLimit'=>$dateLimit]);
$summary['items'] = (int)$stmt->fetchColumn();

$stmt = $con->prepare("SELECT SUM(di.quantity) FROM tbldatailinvoice di INNER JOIN tblinvoice inv ON di.invoiceID=inv.invoiceID WHERE di.status=1 AND inv.invoiceStatus<5 AND inv.invoiceDate>=:dateLimit");
$stmt->execute([':dateLimit'=>$dateLimit]);
$summary['inStock'] = (int)$stmt->fetchColumn();

$stmt = $con->prepare("SELECT SUM(di.quantity) FROM tbldatailinvoice di INNER JOIN tblinvoice inv ON di.invoiceID=inv.invoiceID WHERE di.status=2 AND inv.invoiceStatus<5 AND inv.invoiceDate>=:dateLimit");
$stmt->execute([':dateLimit'=>$dateLimit]);
$summary['outStock'] = (int)$stmt->fetchColumn();

$stmt = $con->prepare("SELECT SUM(invoiceAmount) FROM tblinvoice WHERE invoiceStatus < 5 AND invoiceDate>=:dateLimit");
$stmt->execute([':dateLimit'=>$dateLimit]);
$summary['revenue'] = (float)$stmt->fetchColumn();

// --- Chart ---
$chartData = [];
$title = '';

if ($type !== '') {

    switch ($type) {
        case 'customers':
            $sql = "SELECT $selectPeriod, COUNT(DISTINCT clientID) AS value FROM tblinvoice WHERE invoiceStatus<5 AND invoiceDate>=:dateLimit GROUP BY $groupBy ORDER BY invoiceDate ASC";
            $title = "Customers who Purchased";
            break;

        case 'totalProducts':
            $sql = "SELECT $selectPeriod, SUM(di.quantity) AS value FROM tbldatailinvoice di INNER JOIN tblinvoice inv ON di.invoiceID=inv.invoiceID WHERE inv.invoiceDate>=:dateLimit AND inv.invoiceStatus<5 GROUP BY $groupBy ORDER BY invoiceDate ASC";
            $title = "Total Products Sold";
            break;

        case 'inStock':
            $sql = "SELECT $selectPeriod, SUM(di.quantity) AS value FROM tbldatailinvoice di INNER JOIN tblinvoice inv ON di.invoiceID=inv.invoiceID WHERE di.status=1 AND inv.invoiceStatus<5 AND inv.invoiceDate>=:dateLimit GROUP BY $groupBy ORDER BY invoiceDate ASC";
            $title = "Stock Products";
            break;

        case 'outStock':
            $sql = "SELECT $selectPeriod, SUM(di.quantity) AS value FROM tbldatailinvoice di INNER JOIN tblinvoice inv ON di.invoiceID=inv.invoiceID WHERE di.status=2 AND inv.invoiceStatus<5 AND inv.invoiceDate>=:dateLimit GROUP BY $groupBy ORDER BY invoiceDate ASC";
            $title = "Out of Stock Products";
            break;

        case 'revenue':
            $sql = "SELECT $selectPeriod, SUM(invoiceAmount) AS value FROM tblinvoice WHERE invoiceStatus<5 AND invoiceDate>=:dateLimit GROUP BY $groupBy ORDER BY invoiceDate ASC";
            $title = "Revenue";
            break;

        default:
            echo json_encode(['error'=>'Invalid type']);
            exit;
    }

    $stmt = $con->prepare($sql);
    $stmt->execute([':dateLimit'=>$dateLimit]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- Prepare chart data ---
    $chartData[] = ['Date', ucfirst($type)];
    if (count($rows) > 0) {
        foreach ($rows as $r) {
            if ($xIsTime) {
                // convert string date/hour to JS Date object in array
                $chartData[] = [strval($r['period']), (float)$r['value']];
            } else {
                $chartData[] = [$r['period'], (float)$r['value']];
            }
        }
    } else {
        // No data, add a dummy row
        $chartData[] = [$xIsTime ? $dateLimit : 'No data', 0];
    }
}

$response = ['summary'=>$summary];
if ($type!=='') {
    $response['title']=$title;
    $response['chart']=$chartData;
}
echo json_encode($response);
