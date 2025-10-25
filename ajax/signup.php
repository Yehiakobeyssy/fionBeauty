<?php
session_start();
include '../settings/connect.php';   // adjust path if needed
include '../common/function.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientFname        = trim($_POST['clientFname'] ?? '');
    $clientLname        = trim($_POST['clientLname'] ?? '');
    $clientPhoneNumber  = trim($_POST['clientPhoneNumber'] ?? '');
    $clientEmail        = trim($_POST['clientEmail'] ?? '');
    $profession         = (int)($_POST['profession'] ?? 0);
    $clientPassword     = $_POST['clientPassword'] ?? '';
    $conformPassword    = $_POST['conformPassword'] ?? '';

    // Validate empty fields
    if (empty($clientFname) || empty($clientLname) || empty($clientPhoneNumber) ||
        empty($clientEmail) || empty($profession) || empty($clientPassword) || empty($conformPassword)) {
        $response['message'] = "All fields are required.";
        echo json_encode($response); exit;
    }

    // Validate password match
    if ($clientPassword !== $conformPassword) {
        $response['message'] = "Password and confirmation do not match.";
        echo json_encode($response); exit;
    }

    // Check if email exists
    // Check if email exists directly
    $stmt = $con->prepare("SELECT COUNT(*) FROM tblclient WHERE clientEmail = ?");
    $stmt->execute([$clientEmail]);
    $exists = $stmt->fetchColumn();

    if ($exists > 0) {
        $response['message'] = "This email is already registered.";
        echo json_encode($response); exit;
    }


    // Handle certificate upload
    $newfilename = '';
    if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] === UPLOAD_ERR_OK) {
        $temp = explode(".", $_FILES['certificate']['name']);
        $ext = strtolower(end($temp));
        $allowed = ['jpg','jpeg','png','pdf'];

        if (!in_array($ext, $allowed)) {
            $response['message'] = "Invalid file type. Allowed: jpg, jpeg, png, pdf.";
            echo json_encode($response); exit;
        }

        $newfilename = round(microtime(true)) . '.' . $ext;
        $uploadPath = __DIR__ . '/../documents/' . $newfilename;

        if (!move_uploaded_file($_FILES['certificate']['tmp_name'], $uploadPath)) {
            $response['message'] = "File upload failed.";
            echo json_encode($response); exit;
        }
    }

    // Hash password
    $hashedPassword = sha1($clientPassword);

    // Defaults
    $clientActive      = 0;
    $clientActivation  = sha1(date('Y.m.d'));
    $clientBlock       = 0;
    $pushId            = '';
    $clientAbout       = '';

    try {
        $sql = $con->prepare("INSERT INTO tblclient 
            (clientFname, clientLname, clientPhoneNumber, clientEmail, certificate, profession, clientPassword, clientActive, clientActivation, clientBlock, pushId, clientAbout) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        
        $sql->execute([
            $clientFname,
            $clientLname,
            $clientPhoneNumber,
            $clientEmail,
            $newfilename,
            $profession,
            $hashedPassword,
            $clientActive,
            $clientActivation,
            $clientBlock,
            $pushId,
            $clientAbout
        ]);

        $response['success'] = true;
        $response['message'] = "Account created successfully.";


        $notificationText = "New client signed up: $clientFname $clientLname";
        $stmt = $con->prepare("INSERT INTO tblNotification (text) VALUES (?)");
        $stmt->execute([$notificationText]);
        $notificationId = $con->lastInsertId();
        $admins = $con->query("SELECT adminID  FROM  tbladmin WHERE admin_block = 0")->fetchAll(PDO::FETCH_COLUMN);
        $stmtSeen = $con->prepare("INSERT INTO tblseennotification (notificationId, adminID, seen) VALUES (?, ?, 0)");
        foreach ($admins as $adminId) {
            $stmtSeen->execute([$notificationId, $adminId]);
        }


    } catch (Exception $e) {
        $response['message'] = "Error creating account: " . $e->getMessage();
    }
} else {
    $response['message'] = "Invalid request method.";
}

echo json_encode($response);
