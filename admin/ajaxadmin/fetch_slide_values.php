<?php
include '../../settings/connect.php'; // your PDO $con connection

$type = $_GET['type'] ?? '';
$response = [];

if($type === 'brand'){
    $stmt = $con->query("SELECT brandId, brandName FROM tblbrand");
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($brands as $b){
        $response[] = [
            'name' => $b['brandName'],
            'href' => "brandinfo.php?bid=" . $b['brandId']
        ];
    }
} elseif($type === 'category'){
    $stmt = $con->query("SELECT catName FROM tblcategory");
    $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($cats as $c){
        $response[] = [
            'name' => $c['catName'],
            'href' => "category.php?cat=" . urlencode($c['catName'])
        ];
    }
} elseif($type === 'item'){
    $stmt = $con->query("SELECT itmId, itmName FROM tblitems");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($items as $i){
        $response[] = [
            'name' => $i['itmName'],
            'href' => "daitailitem.php?itemid=" . $i['itmId']
        ];
    }
}

echo json_encode($response);
