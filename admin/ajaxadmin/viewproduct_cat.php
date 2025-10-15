<?php
include '../../settings/connect.php';

$catId = isset($_POST['catId']) ? intval($_POST['catId']) : 0;
$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 9999;
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$date = isset($_POST['date']) ? trim($_POST['date']) : '';
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 12;
$offset = ($page - 1) * $limit;

// --- DATE FILTER ---
$dateCondition = '';
if ($duration != 9999) {
    $dateCondition = " AND i.dateAdd >= DATE_SUB(CURDATE(), INTERVAL :duration DAY)";
}
if (!empty($date)) {
    $dateCondition = " AND DATE(i.dateAdd) = :exactDate";
}

// --- SEARCH FILTER ---
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = " AND (i.itmDesc LIKE :search OR b.brandName LIKE :search)";
}

// --- COUNT TOTAL ---
$sqlCount = "SELECT COUNT(*) FROM tblitems i 
             LEFT JOIN tblbrand b ON i.brandId = b.brandId
             WHERE i.catId = :catId $dateCondition $searchCondition";

$stmt = $con->prepare($sqlCount);
$stmt->bindValue(':catId', $catId, PDO::PARAM_INT);
if ($duration != 9999 && empty($date)) $stmt->bindValue(':duration', $duration, PDO::PARAM_INT);
if (!empty($date)) $stmt->bindValue(':exactDate', $date);
if (!empty($search)) $stmt->bindValue(':search', "%$search%");
$stmt->execute();
$total = $stmt->fetchColumn();

// --- GET PRODUCTS ---
$sql = "SELECT i.*, b.brandName
        FROM tblitems i 
        LEFT JOIN tblbrand b ON i.brandId = b.brandId
        WHERE i.itmActive=1 AND i.catId = :catId $dateCondition $searchCondition
        ORDER BY i.itmId DESC
        LIMIT :limit OFFSET :offset";

$stmt = $con->prepare($sql);
$stmt->bindValue(':catId', $catId, PDO::PARAM_INT);
if ($duration != 9999 && empty($date)) $stmt->bindValue(':duration', $duration, PDO::PARAM_INT);
if (!empty($date)) $stmt->bindValue(':exactDate', $date);
if (!empty($search)) $stmt->bindValue(':search', "%$search%");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$html = '';
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $html .= '
    <div class="card_product">
        <div class="img_product">
            <img src="../images/items/' . htmlspecialchars($row['mainpic']) . '" alt="">
        </div>
        <div class="item_discription">
            <h5>' . htmlspecialchars($row['itmName']) . '</h5>
            <label><small>' . htmlspecialchars($row['brandName']) . '</small></label><br>
            <label>' . htmlspecialchars(substr($row["itmDesc"], 0, 75)) . '...</label>
            <h6>$' . number_format($row['sellPrice'], 2) . '</h6>
        </div>
        <div class="controlitem">
            <button class="btn btn-primary" onclick="window.location.href=\'manageproducts.php?do=edititm&itmId=' . $row['itmId'] . '\'">Edit</button>
            <button class="btn btn-ghost" onclick="if(confirm(\'Are you sure you want to delete this item?\')) window.location.href=\'manageproducts.php?do=deleteitm&itmId=' . $row['itmId'] . '\'">Delete</button>
        </div>
    </div>';
}


echo json_encode([
    'html' => $html ?: '<p>No products found.</p>',
    'total' => $total,
    'page' => $page,
    'limit' => $limit
]);
?>
