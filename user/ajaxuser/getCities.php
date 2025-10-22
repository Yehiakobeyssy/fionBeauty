<?php
include '../../settings/connect.php';

if (isset($_POST['provinceID'])) {
    $provinceID = (int)$_POST['provinceID'];

    $sql = $con->prepare("SELECT cityID, cityName FROM tblcity WHERE provinceID = ? AND cityactive = 1 AND is_deliverable = 1");
    $sql->execute([$provinceID]);
    $cities = $sql->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($cities);
    exit;
}
?>
