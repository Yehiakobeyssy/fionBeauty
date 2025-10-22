<?php
include '../../settings/connect.php';

if (isset($_POST['cityID'])) {
    $cityID = (int)$_POST['cityID'];

    $sql = $con->prepare("SELECT p.provinceID, p.provinceName 
                          FROM tblprovince p
                          JOIN tblcity c ON c.provinceID = p.provinceID
                          WHERE c.cityID = ? LIMIT 1");
    $sql->execute([$cityID]);
    $province = $sql->fetch(PDO::FETCH_ASSOC);

    echo json_encode($province);
    exit;
}
?>
