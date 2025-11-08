<?php
session_start();
include '../../settings/connect.php';

$brandID = $_POST['brandID'] ?? 0;

// Collect uploaded files (images)
$fields = ['mainpic','subpic','reviewpic','statisticpic','smallpic','pic1','pic2','pic3','pic4','pic5'];
$data = [];

foreach($fields as $field){
    if(isset($_FILES[$field]) && $_FILES[$field]['error'] === 0){
        $fileName = time().$_FILES[$field]['name'];
        move_uploaded_file($_FILES[$field]['tmp_name'], '../../images/brands/tamplatepage/'.$fileName);
        $data[$field] = $fileName;
    }
}

// Collect text fields from POST
$textFields = ['textslogan','textslogan1','textslogan2','info'];
foreach($textFields as $txt){
    if(isset($_POST[$txt])){
        $data[$txt] = $_POST[$txt];
    }
}

// Check if record exists
$stmt = $con->prepare("SELECT Id FROM brandPage WHERE BrandID = ?");
$stmt->execute([$brandID]);
$exists = $stmt->fetch(PDO::FETCH_ASSOC);

if($exists){
    // Update existing record
    $updateFields = [];
    foreach($data as $key => $val){
        $updateFields[] = "$key = :$key";
    }
    $sql = "UPDATE brandPage SET ".implode(', ', $updateFields)." WHERE BrandID = :brandID";
    $stmt = $con->prepare($sql);
    $stmt->execute(array_merge($data, ['brandID' => $brandID]));
} else {
    // Insert new record
    $columns = implode(', ', array_merge(['BrandID'], array_keys($data)));
    $placeholders = implode(', ', array_merge([':brandID'], array_map(fn($k)=>":$k", array_keys($data))));
    $sql = "INSERT INTO brandPage ($columns) VALUES ($placeholders)";
    $stmt = $con->prepare($sql);
    $stmt->execute(array_merge(['brandID'=>$brandID], $data));
}

echo json_encode(['success' => true]);
?>
