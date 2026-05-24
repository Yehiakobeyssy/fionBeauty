<?php
include '../../settings/connect.php';

$con->prepare("
DELETE FROM tblitemprogram 
WHERE ProgramID=? AND ItemID=?
")->execute([
    $_POST['programId'],
    $_POST['itemId']
]);

echo "success";