<?php
session_start();
include '../settings/connect.php';
include '../common/function.php';

$method = $_POST['method'] ?? '';

if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = (int) $_COOKIE['user_id'];
} else {
    echo json_encode(['status'=>0,'message'=>'Please login first.']);
    exit;
}

// Get Stripe Public Key
$stat = $con->prepare('SELECT PK FROM tblfinancesetting WHERE SettingID = 1');
$stat->execute();
$result_Keys = $stat->fetch(PDO::FETCH_ASSOC);
$PK = $result_Keys['PK'] ?? '';

// Get user info
$sql = $con->prepare('SELECT clientFname, clientLname, clientEmail FROM tblclient WHERE clientID = ?');
$sql->execute([$user_id]);
$clientInfo = $sql->fetch();
$username = $clientInfo['clientFname'].' '.$clientInfo['clientLname'];
$usermail = $clientInfo['clientEmail'];
$country = "CA"; // default

$success_url = "http://localhost/work/fionBeauty/successworkshop.php";

$html = '';

switch($method){
    case 'card':
        $html .= <<<HTML
<div style="text-align:center;">
    <form id="card-form">
        <div id="card-element"></div>
        <div id="card-message" style="color:red;margin-top:5px;"></div>
        <button type="button" class="paybtn" id="card-pay-btn">Pay by Card</button>
    </form>
</div>
<script>
(async () => {
    const stripe = Stripe("{$PK}");
    const resp = await fetch("create_payment_intent_workshop.php", {
        method:"POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({method:"card"})
    });
    const data = await resp.json();

    const elements = stripe.elements({clientSecret: data.clientSecret});
    const cardEl = elements.create("payment", {layout:"tabs"});
    cardEl.mount("#card-element");

    const btn = document.getElementById("card-pay-btn");
    const msg = document.getElementById("card-message");

    btn.onclick = async () => {
        btn.disabled = true;
        const {error} = await stripe.confirmPayment({
            elements,
            confirmParams: { return_url: "{$success_url}" }
        });
        if(error){ msg.textContent = error.message; btn.disabled=false; }
    };
})();
</script>
HTML;
    break;

    case 'paypal':
        $html .= <<<HTML
<div style="text-align:center;">
    <p>You will be redirected to PayPal to complete your payment.</p>
    <button class="paybtn" id="paypal-btn">Pay with PayPal</button>
    <div id="paypal-message" style="color:red;margin-top:5px;"></div>
</div>
<script>
document.getElementById("paypal-btn").addEventListener("click", async ()=>{
    const stripe = Stripe("{$PK}");
    const resp = await fetch("create_payment_intent_workshop.php", {
        method:"POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({method:"paypal"})
    });
    const data = await resp.json();
    await stripe.redirectToCheckout({ sessionId: data.clientSecret });
});
</script>
HTML;
    break;

    case 'klarna':
        $html .= <<<HTML
<div style="text-align:center;">
    <p>You will be redirected to Klarna to complete your payment.</p>
    <button class="paybtn" id="klarna-pay">Pay with Klarna</button>
    <div id="klarna-message" style="color:red;margin-top:5px;"></div>
</div>
<script>
document.getElementById("klarna-pay").addEventListener("click", async ()=>{
    const stripe = Stripe("{$PK}");
    const resp = await fetch("create_payment_intent_workshop.php", {
        method:"POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({method:"klarna"})
    });
    const data = await resp.json();
    await stripe.redirectToCheckout({ sessionId: data.clientSecret });
});
</script>
HTML;
    break;

    case 'afterpay_clearpay':
        $html .= <<<HTML
<div style="text-align:center;">
    <p>You will be redirected to Afterpay / Clearpay to complete your payment in installments.</p>
    <button class="paybtn" id="afterpay-btn">Pay with Afterpay</button>
    <div id="afterpay-message" style="color:red;margin-top:5px;"></div>
</div>
<script>
document.getElementById("afterpay-btn").addEventListener("click", async ()=>{
    const stripe = Stripe("{$PK}");
    const resp = await fetch("create_payment_intent_workshop.php", {
        method:"POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({method:"afterpay_clearpay"})
    });
    const data = await resp.json();
    await stripe.redirectToCheckout({ sessionId: data.clientSecret });
});
</script>
HTML;
    break;

    default:
        $html .= "<p>Unknown payment method.</p>";
}

echo json_encode(['status'=>1,'html'=>$html]);
