<?php
header('Content-Type: application/json');
include "../../settings/connect.php";

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'filterCategories') {

    $duration = isset($_POST['duration']) ? intval($_POST['duration']) : 9999;
    $search   = isset($_POST['search']) ? trim($_POST['search']) : '';
    $date     = isset($_POST['date']) ? trim($_POST['date']) : '';
    $page     = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $limit    = 10; // 10 categories per page
    $offset   = ($page - 1) * $limit;

    // Count total categories
    $countSql = "SELECT COUNT(*) FROM tblcategory WHERE catActive = 1";
    $countParams = [];
    if ($duration != 9999) $countSql .= " AND catInputDate >= DATE_SUB(CURDATE(), INTERVAL :duration DAY)";
    if (!empty($search)) $countSql .= " AND (catName LIKE :search OR catDescription LIKE :search)";
    if (!empty($date)) $countSql .= " AND DATE(catInputDate) = :date";
    $stmtCount = $con->prepare($countSql);
    if ($duration != 9999) $stmtCount->bindValue(':duration', $duration, PDO::PARAM_INT);
    if (!empty($search)) $stmtCount->bindValue(':search', "%$search%", PDO::PARAM_STR);
    if (!empty($date)) $stmtCount->bindValue(':date', $date, PDO::PARAM_STR);
    $stmtCount->execute();
    $totalRows = $stmtCount->fetchColumn();

    // Main SQL with pagination
    $sql = "
        SELECT 
            c.categoryId,
            c.catName,
            c.catDescription,
            c.carImg,
            c.catInputDate,
            COUNT(DISTINCT i.itmId) AS totalItems,
            COUNT(DISTINCT di.daitailInvoiceId) AS totalOrders
        FROM tblcategory c
        LEFT JOIN tblitems i ON i.catId = c.categoryId AND i.itmActive = 1
        LEFT JOIN tbldatailinvoice di ON di.itmID = i.itmId
        WHERE c.catActive = 1
    ";

    $params = [];
    if ($duration != 9999) { $sql .= " AND c.catInputDate >= DATE_SUB(CURDATE(), INTERVAL :duration DAY)"; $params[':duration']=$duration; }
    if (!empty($search)) { $sql .= " AND (c.catName LIKE :search OR c.catDescription LIKE :search)"; $params[':search']="%$search%"; }
    if (!empty($date)) { $sql .= " AND DATE(c.catInputDate) = :date"; $params[':date']=$date; }

    $sql .= " GROUP BY c.categoryId ORDER BY c.catInputDate DESC LIMIT :offset, :limit";
    $stmt = $con->prepare($sql);
    foreach($params as $k=>$v) $stmt->bindValue($k, $v, PDO::PARAM_STR);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare JSON
    $response['total'] = intval($totalRows);
    $response['page'] = $page;
    $response['limit'] = $limit;
    $response['categories'] = $rows;

    echo json_encode($response);
    exit;
}
echo json_encode(['error'=>'Invalid request']);
