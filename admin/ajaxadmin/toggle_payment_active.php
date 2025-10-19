<?php
require '../../settings/connect.php';
if(isset($_POST['id'], $_POST['active'])){
    $stmt = $con->prepare("UPDATE tblpaymentmethods SET methodActive=? WHERE methodID=?");
    $stmt->execute([$_POST['active'], $_POST['id']]);
}
