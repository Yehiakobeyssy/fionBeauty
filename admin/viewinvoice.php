<?php
session_start();
include '../settings/connect.php';
include '../common/function.php';
include '../common/head.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();  
}

$invoiceID = isset($_GET['inv']) ? intval($_GET['inv']) : 0;
if ($invoiceID == 0) {
    echo "Invoice ID not provided.";
    exit;
}

// Company info
$stmt = $con->prepare("SELECT * FROM tblsetting WHERE seetingID = 1");
$stmt->execute();
$company = $stmt->fetch(PDO::FETCH_ASSOC);

// Finance info
$stmt = $con->prepare("SELECT * FROM tblfinancesetting WHERE SettingID = 1");
$stmt->execute();
$finance = $stmt->fetch(PDO::FETCH_ASSOC);

// Invoice + client + address
$stmt = $con->prepare("
    SELECT i.*, c.clientFname, c.clientLname, c.clientPhoneNumber, c.clientEmail,
           a.street, a.poatalCode, a.bultingNo, a.doorNo, a.mainAdd,
           p.provinceName, ci.cityName
    FROM tblinvoice i
    JOIN tblclient c ON i.clientID = c.clientID
    JOIN tbladdresse a ON i.addresseId = a.addresseID
    JOIN tblprovince p ON a.provinceID = p.provinceID
    JOIN tblcity ci ON a.cityID = ci.cityID
    WHERE i.invoiceID = :invoiceID
");
$stmt->bindParam(':invoiceID', $invoiceID);
$stmt->execute();
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    echo "Invoice not found.";
    exit;
}

// Invoice items
$stmt = $con->prepare("
    SELECT d.*, it.itmName 
    FROM tbldatailinvoice d 
    JOIN tblitems it ON d.itmID = it.itmId
    WHERE d.invoiceID = :invoiceID
");
$stmt->bindParam(':invoiceID', $invoiceID);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice <?= htmlspecialchars($invoice['invoiceCode']) ?></title>
<style>
    /* Global */
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 40px 0;
    }

    .invoice-container {
        width: 800px;
        margin: auto;
        background: #fff;
        padding: 40px 50px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 6px;
    }

    /* Header */
    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #009245;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }

    .invoice-header h1 {
        color: #009245;
        font-size: 32px;
        margin: 0;
        letter-spacing: 1px;
    }

    .invoice-header img {
        max-width: 100px;
        height: auto;
    }

    /* Company info */
    .company-info {
        font-size: 14px;
        line-height: 1.6;
        color: #333;
        margin-bottom: 25px;
    }
    .company-info strong {
        color: #00B25C;
    }

    .invoiceinfo{
        margin-bottom: 25px;
        text-align: right;
    }

    /* Client & shipping info */
    .client-section {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }

    .info-box {
        width: 48%;
        background-color: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 15px;
    }

    .info-box h3 {
        margin-top: 0;
        color: #009245;
        border-bottom: 1px solid #009245;
        padding-bottom: 5px;
        font-size: 16px;
    }

    /* Invoice details */
    .invoice-meta {
        text-align: right;
        font-size: 14px;
        margin-bottom: 15px;
    }

    .invoiceinfo strong {
        color: #009245;
    }

    /* Table */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th {
        background-color: #009245;
        color: #fff;
        padding: 10px;
        text-align: left;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    tbody tr:nth-child(even) {
        background-color: #f5f5f5;
    }

    /* Totals */
    tfoot td {
        font-weight: bold;
        font-size: 14px;
    }

    tfoot tr td:last-child {
        text-align: right;
    }

    /* Footer */
    .footer {
        text-align: center;
        margin-top: 50px;
        font-style: italic;
        color: #555;
        border-top: 2px solid #00B25C;
        padding-top: 10px;
    }

    /* Print */
    @media print {
        body {
            background: #fff;
            padding: 0;
        }
        .invoice-container {
            box-shadow: none;
            margin: 0;
            width: 100%;
        }
    }
</style>
</head>
<body>
<div class="invoice-container">

    <!-- Header -->
    <div class="invoice-header">
        <h1>INVOICE</h1>
        <img src="../images/logo.png" alt="Company Logo">
    </div>

    <!-- Company Info -->
    <div class="company-info">
        <strong><?= htmlspecialchars($company['companyName']) ?></strong><br>
        Phone: <?= htmlspecialchars($company['companyPhone']) ?><br>
        Email: <?= htmlspecialchars($company['companyEmail']) ?><br>
        Tax Number: <?= htmlspecialchars($finance['taxNumber']) ?><br>
    </div>

    <div class="invoiceinfo">
        Invoice #: <strong><?= htmlspecialchars($invoice['invoiceCode']) ?></strong><br>
        Date: <strong><?= date('d M Y', strtotime($invoice['invoiceDate'])) ?></strong><br>
        Payment Method: <strong><?= htmlspecialchars($invoice['paymentMethod']) ?></strong>
    </div>

    <!-- Client + Shipping -->
    <div class="client-section">
        <div class="info-box">
            <h3>Bill To</h3>
            <?= htmlspecialchars($invoice['clientFname'] . ' ' . $invoice['clientLname']) ?><br>
            Phone: <?= htmlspecialchars($invoice['clientPhoneNumber']) ?><br>
            Email: <?= htmlspecialchars($invoice['clientEmail']) ?><br>
        </div>

        <div class="info-box">
            <h3>Shipping Address</h3>
            <?= htmlspecialchars($invoice['street']) ?>, 
            <?= htmlspecialchars('Bld ' . $invoice['bultingNo'] . ', Door ' . $invoice['doorNo']) ?><br>
            <?= htmlspecialchars($invoice['cityName']) ?>, 
            <?= htmlspecialchars($invoice['provinceName']) ?> 
            <?= htmlspecialchars($invoice['poatalCode']) ?><br>
        </div>
    </div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price (CAD)</th>
                <th>Total (CAD)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $counter = 1;
            foreach ($items as $item): ?>
            <tr>
                <td><?= $counter++ ?></td>
                <td><?= htmlspecialchars($item['itmName']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['up'], 2) ?></td>
                <td><?= number_format($item['quantity'] * $item['up'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right;">Subtotal</td>
                <td><?= number_format($invoice['Amount'], 2) ?> CAD</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:right;">Discount</td>
                <td><?= number_format($invoice['discount'], 2) ?> CAD</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:right;">Tax</td>
                <td><?= number_format($invoice['tax'], 2) ?> CAD</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:right;">Shipping Fee</td>
                <td><?= number_format($invoice['shippfee'], 2) ?> CAD</td>
            </tr>
            <tr style="background-color:#00B25C; color:#fff;">
                <td colspan="4" style="text-align:right;">Total</td>
                <td><?= number_format($invoice['invoiceAmount'], 2) ?> CAD</td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <div class="footer">
        Thank you for choosing <?= htmlspecialchars($company['companyName']) ?>.<br>
        Please contact us if you have any questions about this invoice.
    </div>

</div>
<script>
    window.onload = function() {
        window.print();
    };
</script>

</body>
</html>
