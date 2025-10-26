<?php
session_start();
include '../settings/connect.php';
include '../common/function.php';
require '../vendor/autoload.php'; // Make sure Stripe PHP SDK is installed via Composer

\Stripe\Stripe::setApiKey($con->query("SELECT SK FROM tblfinancesetting WHERE SettingID = 1")->fetchColumn());

// Get POSTed method
$input = json_decode(file_get_contents('php://input'), true);
$method = $input['method'] ?? 'card';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>0,'message'=>'Please login first.']);
    exit;
}

// Calculate total amount from workshop cart
if (!isset($_SESSION['workoutCart']) || empty($_SESSION['workoutCart'])) {
    echo json_encode(['status'=>0,'message'=>'Your workshop cart is empty.']);
    exit;
}

$workshopIds = $_SESSION['workoutCart'];
$placeholders = implode(',', array_fill(0, count($workshopIds), '?'));
$stmt = $con->prepare("SELECT SUM(cost) as total FROM workshops WHERE id IN ($placeholders)");
$stmt->execute($workshopIds);
$total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$amount = intval($total * 100); // amount in cents


$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // e.g., /work/fionBeauty
$success_url = $protocol . $host .  "/successworkshop.php?session_id={CHECKOUT_SESSION_ID}";
$cancel_url  = $protocol . $host .  "/checkoutworkshop.php";


// Setup Stripe Checkout Session for non-card payments
if ($method !== 'card') {
    $payment_method_types = [];
    switch($method){
        case 'paypal':
            $payment_method_types = ['paypal'];
            break;
        case 'klarna':
            $payment_method_types = ['klarna'];
            break;
        case 'afterpay_clearpay':
            $payment_method_types = ['afterpay_clearpay'];
            break;
        default:
            $payment_method_types = ['card'];
    }

    try {
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => $payment_method_types,
            'mode' => 'payment',
            'customer_email' => $_SESSION['user_email'] ?? '',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'cad', 
                    'product_data' => [
                        'name' => 'Workshop Purchase',
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'success_url' => $success_url,
            'cancel_url' => $cancel_url,
        ]);

        echo json_encode(['status'=>1,'clientSecret'=>$session->id]);
    } catch (\Exception $e) {
        echo json_encode(['status'=>0,'message'=>$e->getMessage()]);
    }
    exit;
}

// For card payments, create a PaymentIntent
try {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'usd',
        'payment_method_types' => ['card'],
        'metadata' => [
            'user_id' => $_SESSION['user_id'],
            'type' => 'workshop_purchase'
        ]
    ]);

    echo json_encode(['status'=>1,'clientSecret'=>$paymentIntent->client_secret]);

} catch (\Exception $e) {
    echo json_encode(['status'=>0,'message'=>$e->getMessage()]);
}
