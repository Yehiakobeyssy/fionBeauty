<?php
include "../../settings/connect.php";
header('Content-Type: application/json');

try {
    // 🟢 Receive filters
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $block  = isset($_GET['block']) ? $_GET['block'] : '';
    $date   = isset($_GET['date']) ? $_GET['date'] : '';

    // 🟢 Build dynamic WHERE conditions
    $where = "1";
    $params = [];

    if ($search !== '') {
        $where .= " AND (c.clientFname LIKE :search OR c.clientLname LIKE :search OR c.clientEmail LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if ($status !== '') {
        $where .= " AND c.clientActive = :status";
        $params[':status'] = $status;
    }

    if ($block !== '') {
        $where .= " AND c.clientBlock = :block";
        $params[':block'] = $block;
    }

    if ($date !== '') {
        $where .= " AND DATE(c.clientFirstLogin) = :date";
        $params[':date'] = $date;
    }

    // 🟢 Main Query
    $query = "
        SELECT 
            c.clientID,
            CONCAT(c.clientFname, ' ', c.clientLname) AS fullName,
            c.clientPhoneNumber,
            c.clientEmail,
            c.certificate,
            c.clientActive,
            c.clientBlock,
            COUNT(i.invoiceID) AS Orders,
            IFNULL(SUM(CASE WHEN i.invoiceStatus < 5 THEN i.invoiceAmount ELSE 0 END), 0) AS Balance
        FROM tblclient c
        LEFT JOIN tblinvoice i ON c.clientID = i.clientID
        WHERE $where
        GROUP BY c.clientID
        ORDER BY c.clientID DESC
    ";

    $stmt = $con->prepare($query);
    $stmt->execute($params);
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($clients as $c) {
        $data[] = [
            'clientID'    => $c['clientID'],
            'fullName'    => $c['fullName'],
            'phone'       => $c['clientPhoneNumber'],
            'email'       => $c['clientEmail'],
            'certificate' => empty($c['certificate']) ? "Don't have" : "Have",
            'orders'      => (int)$c['Orders'],
            'balance'     => number_format($c['Balance'], 2),
            'status'      => $c['clientActive'] == 1 ? "Active" : "Inactive",
            'block'       => $c['clientBlock'] == 1 ? "Blocked" : "",
        ];
    }

    echo json_encode(['success' => true, 'data' => $data]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
