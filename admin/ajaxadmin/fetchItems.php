<?php
include '../../settings/connect.php';

$id = (int)$_POST['programId'];

$stmt = $con->prepare("
SELECT i.*
FROM tblitems i
JOIN tblitemprogram ip ON i.itmId = ip.ItemID
WHERE ip.ProgramID = ?
");

$stmt->execute([$id]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));