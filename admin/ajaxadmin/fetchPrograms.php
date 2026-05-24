<?php
session_start();
include '../../settings/connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode([]);
    exit();
}

try {

    $stmt = $con->prepare("
        SELECT 
            p.ProgramID,
            p.ProgramName,
            p.ProgramActive,

            (SELECT COUNT(*) 
             FROM tblprogramsection s 
             WHERE s.ProgramID = p.ProgramID) AS sectionCount,

            (SELECT COUNT(*) 
             FROM tblitemprogram ip 
             WHERE ip.ProgramID = p.ProgramID) AS itemCount

        FROM tblprogramm p
        ORDER BY p.ProgramID DESC
    ");

    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    echo json_encode([]);
}