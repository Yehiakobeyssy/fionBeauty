<?php
session_start();
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $_SESSION['order_info'] = $data;
    echo json_encode(['status'=>'ok']);
}
?>
