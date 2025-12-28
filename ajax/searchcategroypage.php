<?php
session_start();
include '../settings/connect.php';

/* =========================
   Read & normalize inputs
========================= */
$categoryname = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$subCat       = isset($_GET['subcat']) ? trim($_GET['subcat']) : '';

$result = [
    'status'   => 'error',
    'catID'    => null,
    'subCatID' => null
];

/* =========================
   1. Resolve category (if provided)
========================= */
if ($categoryname !== '') {

    $sql = "SELECT categoryId
            FROM tblcategory
            WHERE catName LIKE ?
            LIMIT 1";

    $stmt = $con->prepare($sql);
    $stmt->execute(['%' . $categoryname . '%']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $result['catID'] = (int)$row['categoryId'];
    }
}

/* =========================
   2. Resolve subcategory
========================= */
if ($subCat !== '') {

    // If category is already known, enforce relation
    if ($result['catID'] !== null) {

        $sql = "SELECT subCatID
                FROM tblsubcategory
                WHERE subCatName LIKE ?
                  AND categoryId = ?
                LIMIT 1";

        $stmt = $con->prepare($sql);
        $stmt->execute([
            '%' . $subCat . '%',
            $result['catID']
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $result['subCatID'] = (int)$row['subCatID'];
        }

    // Otherwise resolve subcategory alone
    } else {

        $sql = "SELECT subCatID, catID
                FROM tblsubcategory
                WHERE subCatName LIKE ?
                LIMIT 1";

        $stmt = $con->prepare($sql);
        $stmt->execute(['%' . $subCat . '%']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $result['subCatID'] = (int)$row['subCatID'];
            $result['catID']    = (int)$row['catID'];
        }
    }
}

/* =========================
   3. Final status
========================= */
if ($result['catID'] !== null) {
    $result['status'] = 'success';
}

header('Content-Type: application/json');
echo json_encode($result);
