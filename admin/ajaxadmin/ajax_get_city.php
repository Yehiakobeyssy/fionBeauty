<?php
// ✅ include your database connection
include '../../settings/connect.php'; // or your main config file where $con is defined

if (isset($_POST['provinceID'])) {
    $provinceID = intval($_POST['provinceID']); // secure cast to integer

    $stmt = $con->prepare("SELECT cityID, cityName FROM tblcity WHERE provinceID = :pid ORDER BY cityName ASC");
    $stmt->execute([':pid' => $provinceID]);

    // Default option
    echo '<option value="">-- Select City --</option>';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . htmlspecialchars($row['cityID']) . '">' . htmlspecialchars($row['cityName']) . '</option>';
    }
}
?>
