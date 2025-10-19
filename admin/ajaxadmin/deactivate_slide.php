<?php
require_once '../../settings/connect.php'; // adjust your path
header('Content-Type: application/json');

if(isset($_POST['id'])){
    $id = (int)$_POST['id'];
    $stmt = $con->prepare("UPDATE tblslideshow SET slideActive = 0 WHERE slideID = ?");
    $stmt->execute([$id]);
    echo json_encode(['status'=>'success','message'=>'✅ Slide deactivated']);
} else {
    echo json_encode(['status'=>'error','message'=>'❌ Invalid request']);
}
