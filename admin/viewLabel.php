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

// Get company info
$stmt = $con->prepare("SELECT * FROM tblsetting WHERE seetingID = 1");
$stmt->execute();
$company = $stmt->fetch(PDO::FETCH_ASSOC);

// Get invoice, client, address
$stmt = $con->prepare("
    SELECT i.invoiceCode, i.invoiceDate,
       a.NameAdd, a.phoneNumber,
       a.street, a.poatalCode, a.bultingNo, a.doorNo, p.provinceName, ci.cityName

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Shipping Label <?= htmlspecialchars($invoice['invoiceCode']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #fff;
        margin: 0;
        padding: 0;
    }

    .label-container {
        width: 10cm;
        height: 15cm;
        margin: 0 auto;
        padding: 15px;
        border: 2px solid #000;
        box-sizing: border-box;
        position: relative;
        background-color: #fff;
    }

    /* --- Header --- */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #000;
        padding-bottom: 8px;
        margin-bottom: 10px;
    }

    .header img {
        max-height: 45px;
        max-width: 120px;
    }

    .header .company {
        text-align: right;
        font-size: 11px;
        font-weight: bold;
        line-height: 1.4;
    }

    /* --- Sections --- */
    h3 {
        font-size: 11px;
        margin: 10px 0 5px;
        text-transform: uppercase;
        border-bottom: 1px solid #000;
        padding-bottom: 2px;
    }

    .box {
        border: 1px solid #000;
        padding: 6px 8px;
        font-size: 12px;
        line-height: 1.5;
        margin-bottom: 8px;
    }

    /* --- Barcode --- */
    .barcode {
        text-align: center;
        font-family: 'Libre Barcode 128', monospace;
        font-size: 52px;
        margin-top: 15px;
        letter-spacing: 2px;
    }

    .barcode-text {
        text-align: center;
        font-size: 12px;
        font-weight: bold;
        margin-top: 2px;
    }

    /* --- Footer --- */
    .footer {
        position: absolute;
        bottom: 8px;
        left: 15px;
        right: 15px;
        text-align: center;
        font-size: 10px;
        border-top: 1px solid #000;
        padding-top: 4px;
    }

    /* --- Print Styles --- */
    @media print {
        body {
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .label-container {
            page-break-after: always;
            border: 2px solid #000;
            box-shadow: none;
        }
    }
</style>
</head>
<body>
<div class="label-container">

    <!-- Header -->
    <div class="header">
        <img src="../images/logo.png" alt="Logo">
        <div class="company">
            <?= htmlspecialchars($company['companyName']) ?><br>
            Phone: <?= htmlspecialchars($company['companyPhone']) ?><br>
            Email: <?= htmlspecialchars($company['companyEmail']) ?>
        </div>
    </div>

    <!-- Sender -->
    <h3>From:</h3>
    <div class="box">
        <?= htmlspecialchars($company['companyName']) ?><br>
        <?= htmlspecialchars($company['companyAdd']) ?><br>
        <?= htmlspecialchars($company['companyPhone']) ?><br>
        <?= htmlspecialchars($company['companyEmail']) ?><br>
    </div>

    <!-- Recipient -->
    <h3>To:</h3>
    <div class="box">
        <strong><?= htmlspecialchars($invoice['NameAdd']) ?></strong><br>
        <?= htmlspecialchars($invoice['street']) ?>, 
        <?= htmlspecialchars('Bld ' . $invoice['bultingNo'] . ', Door ' . $invoice['doorNo']) ?><br>
        <?= htmlspecialchars($invoice['cityName']) ?>, <?= htmlspecialchars($invoice['provinceName']) ?><br>
        <?= htmlspecialchars($invoice['poatalCode']) ?><br>
        Phone: <?= htmlspecialchars($invoice['phoneNumber']) ?>
    </div>

    <!-- Barcode -->
    <div class="barcode">
        *<?= htmlspecialchars($invoice['invoiceCode']) ?>*
    </div>
    <div class="barcode-text">
        Invoice #<?= htmlspecialchars($invoice['invoiceCode']) ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        Handle with care · Generated on <?= date('d M Y', strtotime($invoice['invoiceDate'])) ?>
    </div>
</div>

<script>
window.onload = function() {
    window.print();
};
</script>
</body>
</html>
