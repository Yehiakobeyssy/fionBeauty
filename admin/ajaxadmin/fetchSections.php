<?php
include '../../settings/connect.php';

$id = (int)$_POST['programId'];

$stmt = $con->prepare("SELECT * FROM tblprogramsection WHERE ProgramID=?");
$stmt->execute([$id]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));