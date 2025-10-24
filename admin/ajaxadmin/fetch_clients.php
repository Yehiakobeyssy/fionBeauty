<?php
require_once("../../settings/connect.php");

$workshopID = $_POST['workshopID'] ?? 0;

$stmt = $con->prepare("
    SELECT 
        c.clientFname, c.clientLname, c.clientPhoneNumber, c.clientEmail,
        i.invoiceCode, i.invoiceDate, i.totalAmount, i.transactionID, i.method
    FROM workshop_bookings b
    JOIN tblclient c ON b.user_id = c.clientID
    JOIN tbldetailinvoiceworkshop d ON d.workshopID = b.workshop_id
    JOIN tblinvoiceworkshop i ON i.invoiceID = d.invoiceID
    WHERE b.workshop_id = ?
    ORDER BY i.invoiceDate DESC
");
$stmt->execute([$workshopID]);
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($clients);
exit;
?>
