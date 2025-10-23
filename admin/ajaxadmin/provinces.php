<?php
include '../../settings/connect.php';

// List provinces with their cities
$stmt = $con->prepare("SELECT * FROM tblprovince ORDER BY provinceName ASC");
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = (int)$row['provinceID'];
    $name = htmlspecialchars($row['provinceName']);
    $code = htmlspecialchars($row['provinceCode']);
    $is_deliverable = (int)$row['is_deliverable'];
    $active = (int)$row['provinceActive'];
    $shippingFee = number_format((float)$row['shippingFee'], 2);

    $deliverLabel = $is_deliverable 
        ? "<span style='color:green;font-size:12px;'>Deliverable</span>" 
        : "<span style='color:red;font-size:12px;'>No Delivery</span>";
    $activeLabel = $active 
        ? "<span style='font-size:12px;color:green;'>Active</span>" 
        : "<span style='font-size:12px;color:orange;'>Inactive</span>";

    echo "
    <div class='province-card'>
      <div class='province-header' data-id='{$id}'>
        <div>
          <strong class='province-name'>{$name}</strong> 
          &nbsp; Shipping Fee: <strong>\${$shippingFee}</strong>
          &nbsp; {$deliverLabel} &nbsp; {$activeLabel}
        </div>
        <div>
          <button class='btn btn-small addCityBtn' data-provinceid='{$id}'>+ City</button>
          <button class='btn btn-small editProvinceBtn' data-id='{$id}' data-name='{$name}' data-fee='{$shippingFee}'>Edit</button>
          <div class='province-toggles'>
            <div>Delivery: 
              <label class='switch'>
                <input type='checkbox' class='toggle-delivery' data-id='{$id}' ".($row['is_deliverable'] ? 'checked' : '').">
                <span class='slider'></span>
              </label>
            </div>
            <div>Active:
              <label class='switch'>
                <input type='checkbox' class='toggle-active' data-id='{$id}' ".($row['provinceActive'] ? 'checked' : '').">
                <span class='slider'></span>
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class='city-list'>
    ";

    // Load cities for this province
    $cityStmt = $con->prepare("SELECT * FROM tblcity WHERE provinceID = ? ORDER BY cityName ASC");
    $cityStmt->execute([$id]);
    while ($city = $cityStmt->fetch(PDO::FETCH_ASSOC)) {
        $cityName = htmlspecialchars($city['cityName']);
        echo "<div class='city-name'>{$cityName}</div>";
    }

    echo "</div></div>";
}
?>
