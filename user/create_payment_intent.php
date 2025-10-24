<?php
session_start();
header('Content-Type: application/json');
require '../settings/connect.php';
require '../vendor/autoload.php';
\Stripe\Stripe::setApiKey(getStripeSecretKey());

$input = json_decode(file_get_contents("php://input"), true);
$method = $input['method'] ?? 'card';
$shipfee = isset($_SESSION['order_info']['shipfee']) ? floatval($_SESSION['order_info']['shipfee']) : 0;

// Calculate total amount (example using session cart)


$sql=$con->prepare('SELECT taxPercent ,includeTax FROM  tblfinancesetting WHERE SettingID  = 1');
$sql->execute();
$fin= $sql->fetch();

$includeTax =$fin['includeTax'];
$taxper = $fin['taxPercent'];
$total = 0;
if(isset($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $id => $qty){
        $stmt = $con->prepare("SELECT sellPrice, promotional FROM tblitems WHERE itmId=?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$item) continue;

        $price = $item['sellPrice'];

        // Promotional discount
        $promoDiscount = ($price * $qty * $item['promotional']) / 100;

        // Quantity-based discount
        $stmt = $con->prepare("SELECT precent FROM tbldiscountitem WHERE itemID = ? AND quatity <= ? ORDER BY quatity DESC LIMIT 1");
        $stmt->execute([$id, $qty]);
        $discountPercent = $stmt->fetchColumn() ?: 0;
        $qtyDiscount = ($price * $qty * $discountPercent) / 100;

        // Subtotal after both discounts
        $subtotal = ($price * $qty) - $promoDiscount - $qtyDiscount;

        $total += $subtotal;
    } 
}


if ($includeTax == 0) {
    $total += $total * ($taxper / 100);
}
$total+=$shipfee;
// Convert to cents for payment gateway
$amount = (int)round($total * 100);


$supportedMethods = ['card','klarna','afterpay_clearpay','paypal'];
if(!in_array($method, $supportedMethods)) $method='card';

try {
    $intent = \Stripe\PaymentIntent::create([
        'amount'=>$amount,
        'currency'=>'eur',
        'payment_method_types'=>[$method],
        'metadata'=>['method'=>$method,'user_id'=>$_SESSION['user_id'] ?? 'guest']
    ]);
    echo json_encode(['clientSecret'=>$intent->client_secret]);
} catch(\Stripe\Exception\ApiErrorException $e){
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}

function getStripeSecretKey(){
    global $con;
    $stmt=$con->prepare("SELECT SK FROM tblfinancesetting WHERE SettingID=1");
    $stmt->execute();
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    return $row['SK'] ?? '';
}
?>
