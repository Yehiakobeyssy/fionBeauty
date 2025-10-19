<?php
header('Content-Type: application/json');
include '../../settings/connect.php';
$name = isset($_POST['provinceName']) ? trim($_POST['provinceName']) : '';
if ($name === '') { echo json_encode(['success'=>false]); exit; }
try {
  $stmt = $con->prepare("INSERT INTO tblprovince (provinceName, provinceCode, is_deliverable, provinceActive) VALUES (?, '', 1, 1)");
  $stmt->execute([$name]);
  echo json_encode(['success'=>true]);
} catch(Exception $e) { echo json_encode(['success'=>false,'message'=>$e->getMessage()]); }
