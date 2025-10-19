<?php
require '../../settings/connect.php';
if(isset($_POST['id'], $_POST['note'])){
    $stmt = $con->prepare("UPDATE tblpaymentmethods SET methodNote=? WHERE methodID=?");
    $stmt->execute([$_POST['note'], $_POST['id']]);
}
