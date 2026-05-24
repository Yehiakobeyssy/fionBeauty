<?php
session_start();
include '../../settings/connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo "unauthorized";
    exit();
}

$ProjectID = isset($_POST['ProjectID']) ? (int)$_POST['ProjectID'] : 0;

$name = trim($_POST['ProjectName'] ?? '');
$desc = trim($_POST['ProjectDescription'] ?? '');
$active = isset($_POST['ProjectActive']) ? 1 : 0;

if ($ProjectID <= 0) {
    echo "invalid_id";
    exit();
}

if ($name === '') {
    echo "name_required";
    exit();
}

try {

    // =========================
    // GET CURRENT DATA
    // =========================
    $stmt = $con->prepare("SELECT ProjectImage FROM tblproject WHERE ProjectID = ?");
    $stmt->execute([$ProjectID]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        echo "not_found";
        exit();
    }

    $imageName = $project['ProjectImage'];

    // =========================
    // IMAGE UPLOAD (optional)
    // =========================
    if (!empty($_FILES['ProjectImage']['name'])) {

        $ext = pathinfo($_FILES['ProjectImage']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array(strtolower($ext), $allowed)) {
            echo "invalid_image";
            exit();
        }

        $imageName = "project_" . time() . "." . $ext;
        $path = "../../images/projects/" . $imageName;

        move_uploaded_file($_FILES['ProjectImage']['tmp_name'], $path);
    }

    // =========================
    // UPDATE PROJECT
    // =========================
    $stmt = $con->prepare("
        UPDATE tblproject 
        SET ProjectName = ?, 
            ProjectDescription = ?, 
            ProjectActive = ?, 
            ProjectImage = ?
        WHERE ProjectID = ?
    ");

    $stmt->execute([
        $name,
        $desc,
        $active,
        $imageName,
        $ProjectID
    ]);

    echo "success";

} catch (Exception $e) {
    echo "error";
}