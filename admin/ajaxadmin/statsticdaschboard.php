<?php
header('Content-Type: application/json');
error_reporting(0);       // hide warnings
ini_set('display_errors', 0);

header('Content-Type: application/json');
include '../../settings/connect.php'; // your PDO connection file

// Get time interval (default 9999 days)
$days = isset($_POST['days']) ? intval($_POST['days']) : 9999;

// Calculate date limit
$dateLimit = date('Y-m-d', strtotime("-$days days"));

// -----------------------------
// 1. Total Revenue
// -----------------------------
// --- Calculate total from tblinvoice ---
$sqlInvoice = "SELECT SUM(invoiceAmount) AS totalRevenue
               FROM tblinvoice
               WHERE invoiceStatus < 5
               AND invoiceDate >= :dateLimit";
$stmtInvoice = $con->prepare($sqlInvoice);
$stmtInvoice->execute([':dateLimit' => $dateLimit]);
$totalInvoice = $stmtInvoice->fetchColumn() ?? 0;


// --- Calculate total from tblinvoiceworkshop ---
$sqlWorkshop = "SELECT SUM(totalAmount) AS totalRevenue
                FROM tblinvoiceworkshop
                WHERE invoiceDate >= :dateLimit";
$stmtWorkshop = $con->prepare($sqlWorkshop);
$stmtWorkshop->execute([':dateLimit' => $dateLimit]);
$totalWorkshop = $stmtWorkshop->fetchColumn() ?? 0;


// --- Sum both totals ---
$totalRevenue = $totalInvoice + $totalWorkshop;


// -----------------------------
// 2. Total Commission
// -----------------------------
// Commission = ((quantity * up) - discount%) * (commtion / 100)
// where tbldatailinvoice.status = 1
$sqlCommission = "
    SELECT SUM(
        ((di.quantity * di.up) - ((di.discount / 100) * (di.quantity * di.up))) 
        * (it.commtion / 100)
    ) AS totalCommission
    FROM tbldatailinvoice di
    INNER JOIN tblinvoice inv ON di.invoiceID = inv.invoiceID
    INNER JOIN tblitems it ON di.itmID = it.itmId
    WHERE di.status = 1
    AND inv.invoiceDate >= :dateLimit
";
$stmtCommission = $con->prepare($sqlCommission);
$stmtCommission->execute([':dateLimit' => $dateLimit]);
$totalCommission = $stmtCommission->fetchColumn() ?? 0;

// -----------------------------
// 3. Total Clients
// -----------------------------
$sqlClients = "SELECT COUNT(*) FROM tblclient";
$stmtClients = $con->query($sqlClients);
$totalClients = $stmtClients->fetchColumn() ?? 0;

// -----------------------------
// 4. Orders (Invoices) Status Counts
// -----------------------------
$sqlOrders = "
    SELECT 
        SUM(CASE WHEN invoiceStatus = 4 THEN 1 ELSE 0 END) AS completeOrders,
        SUM(CASE WHEN invoiceStatus < 4 THEN 1 ELSE 0 END) AS processingOrders,
        COUNT(*) AS totalOrders
    FROM tblinvoice
    WHERE invoiceDate >= :dateLimit
";
$stmtOrders = $con->prepare($sqlOrders);
$stmtOrders->execute([':dateLimit' => $dateLimit]);
$orders = $stmtOrders->fetch(PDO::FETCH_ASSOC);

// -----------------------------
// Return JSON
// -----------------------------
echo json_encode([
    'totalRevenue'      => round($totalRevenue, 2),
    'totalCommission'   => round($totalCommission, 2),
    'totalClients'      => intval($totalClients),
    'completeOrders'    => intval($orders['completeOrders']),
    'processingOrders'  => intval($orders['processingOrders']),
    'totalOrders'       => intval($orders['totalOrders'])
]);

?>
