<?php
header('Content-Type: application/json');
include '../../settings/connect.php';
$provinceID = isset($_POST['provinceID']) ? (int)$_POST['provinceID'] : 0;
$name = isset($_POST['cityName']) ? trim($_POST['cityName']) : '';
if ($provinceID <= 0 || $name === '') { echo json_encode(['success'=>false]); exit; }
try {
  $stmt = $con->prepare("INSERT INTO tblcity (provinceID, cityName, is_deliverable, cityactive) VALUES (?, ?, 1, 1)");
  $stmt->execute([$provinceID, $name]);
  echo json_encode(['success'=>true]);
} catch(Exception $e) { echo json_encode(['success'=>false,'message'=>$e->getMessage()]); }
