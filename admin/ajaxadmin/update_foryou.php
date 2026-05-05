<?php
include '../../settings/connect.php';

$itmId = isset($_POST['itmId']) ? intval($_POST['itmId']) : 0;
$value = isset($_POST['value']) ? intval($_POST['value']) : 0;

if ($itmId > 0) {
    $stmt = $con->prepare("
        UPDATE tblitems 
        SET foryouSection = :val 
        WHERE itmId = :id
    ");

    $stmt->bindValue(':val', $value, PDO::PARAM_INT);
    $stmt->bindValue(':id', $itmId, PDO::PARAM_INT);
    $stmt->execute();

    echo 'success';
} else {
    echo 'invalid';
}