<?php
    include '../settings/connect.php';

    $catid= (isset($_GET['catID']))?$_GET['catID']:0;

    $sql= $con->prepare('SELECT carImg FROM tblcategory WHERE categoryId  = ?');
    $sql->execute(array($catid));
    $result= $sql->fetch();
    $imgsource = $result['carImg'];

    header('Content-Type: application/json');
    echo json_encode(['imgsource' => $imgsource]);

?>