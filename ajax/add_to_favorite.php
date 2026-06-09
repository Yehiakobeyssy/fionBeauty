<?php

session_start();
require_once("../settings/connect.php"); // $con PDO

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = (int)$_COOKIE['user_id'];
} else {
    echo json_encode([
        'status' => 'login'
    ]);
    exit;
}

$itemID = isset($_POST['itemID']) ? (int)$_POST['itemID'] : 0;

if ($itemID <= 0) {
    echo json_encode([
        'status' => 'error'
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Check if already exists
|--------------------------------------------------------------------------
*/

$check = $con->prepare("
    SELECT favItmID
    FROM tblfavoriteitm
    WHERE itemID = ?
    AND clientID = ?
    LIMIT 1
");

$check->execute([$itemID, $user_id]);

if ($check->fetch()) {

    echo json_encode([
        'status' => 'exists'
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Insert Favorite
|--------------------------------------------------------------------------
*/

$insert = $con->prepare("
    INSERT INTO tblfavoriteitm
    (
        itemID,
        clientID
    )
    VALUES
    (
        ?,
        ?
    )
");

$insert->execute([$itemID, $user_id]);

echo json_encode([
    'status' => 'success'
]);

exit;