<?php
include '../../settings/connect.php';

$provinceID = $_POST['provinceID'];

$sql = $con->prepare("SELECT * FROM tblcity WHERE provinceID = ? ORDER BY cityName");
$sql->execute([$provinceID]);
$cities = $sql->fetchAll(PDO::FETCH_ASSOC);

if ($cities) {
    foreach ($cities as $row) {
        $id = $row['cityID'];
        $name = htmlspecialchars($row['cityName']);
        $is_deliverable = $row['is_deliverable'] ? 'checked' : '';
        $is_active = $row['cityactive'] ? 'checked' : '';

        echo "
        <div class='city-item' data-id='$id'>
            <div class='city-info'>
                <span class='city-name'>$name</span>
            </div>
            <div class='city-toggles'>
                <div>Delivery:
                    <label class='switch'>
                        <input type='checkbox' class='toggle-city-delivery' data-id='$id' $is_deliverable>
                        <span class='slider'></span>
                    </label>
                </div>
                <div>Active:
                    <label class='switch'>
                        <input type='checkbox' class='toggle-city-active' data-id='$id' $is_active>
                        <span class='slider'></span>
                    </label>
                </div>
            </div>
        </div>
        ";
    }
} else {
    echo "<div class='no-cities'>No cities found for this province.</div>";
}
?>
