<?php
session_start();
include '../../settings/connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo "unauthorized";
    exit();
}

$ProgramID = $_POST['ProgramID'] ?? 0;

if (!$ProgramID) {
    echo "invalid";
    exit();
}

try {

    // =========================
    // GET CURRENT STATUS
    // =========================
    $stmt = $con->prepare("SELECT ProgramActive FROM tblprogramm WHERE ProgramID = ?");
    $stmt->execute([$ProgramID]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "not_found";
        exit();
    }

    // =========================
    // TOGGLE VALUE
    // =========================
    $newStatus = $row['ProgramActive'] == 1 ? 0 : 1;

    // =========================
    // UPDATE
    // =========================
    $stmt = $con->prepare("
        UPDATE tblprogramm 
        SET ProgramActive = ? 
        WHERE ProgramID = ?
    ");

    $stmt->execute([$newStatus, $ProgramID]);

    echo "success";

} catch (Exception $e) {
    echo "error";
}