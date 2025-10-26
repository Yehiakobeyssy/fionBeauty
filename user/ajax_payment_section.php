<?php
session_start();
include '../settings/connect.php';
$method = $_GET['method'] ?? '';

// Get Stripe PK from database
$stat = $con->prepare('SELECT PK FROM tblfinancesetting WHERE SettingID = 1');
$stat->execute();
$result_Keys = $stat->fetch(PDO::FETCH_ASSOC);
$PK = $result_Keys['PK'] ?? '';

// Get user ID
if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];  
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = (int) $_COOKIE['user_id'];  
} else {
    header("Location: ../login.php");
    exit(); 
}

// Get user info
$sql = $con->prepare('SELECT clientFname, clientLname, clientEmail FROM tblclient WHERE clientID = ?');
$sql->execute([$user_id]);
$clientInfo = $sql->fetch();
$username = $clientInfo['clientFname'].' '.$clientInfo['clientLname'];
$usermail = $clientInfo['clientEmail'];
$country = "CA"; // Default country Canada

// ======= DYNAMIC URLS =======
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$success_url = $protocol . $host . "/success.php";
$cancel_url  = $protocol . $host .  "/user/checkout.php";
?>

<style>
.paybtn {
    background:var(--color-primary);
    color:var(--color-white);
    border:none;
    border-radius:6px;
    padding:10px 20px;
    margin-top:10px;
    cursor:pointer;
    transition:0.3s;
}
.paybtn:hover { background:var(--color-primary-variant); }
</style>

<?php if ($method === 'card'): ?>
<div style="text-align:center;">
    <form id="card-form">
        <div id="card-element"></div>
        <div id="card-message" style="color:red;margin-top:5px;"></div>
        <button type="button" class="paybtn" id="card-pay-btn">Pay by Card</button>
    </form>
</div>

<script>
(async () => {
    const stripe = Stripe("<?= $PK ?>");
    const resp = await fetch("create_payment_intent.php", {
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
        await saveOrderInfo();
        const {error} = await stripe.confirmPayment({
            elements,
            confirmParams: { return_url: "<?= $success_url ?>" }
        });
        if(error){ msg.textContent = error.message; btn.disabled=false; }
    };
})();
</script>

<?php elseif ($method === 'paypal'): ?>
<div style="text-align:center;">
    <p>You will be redirected to PayPal to complete your payment.</p>
    <button class="paybtn" id="paypal-btn">Pay with PayPal</button>
    <div id="paypal-message" style="color:red;margin-top:5px;"></div>
</div>
<script>
document.getElementById("paypal-btn").addEventListener("click", async ()=>{
    const stripe = Stripe("<?= $PK ?>");
    await saveOrderInfo();
    const resp = await fetch("create_payment_intent.php", {
        method:"POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({method:"paypal"})
    });
    const data = await resp.json();
    const {error} = await stripe.confirmPayPalPayment(data.clientSecret, {
        payment_method: {
            billing_details: { name: "<?= $username ?>", email: "<?= $usermail ?>", address: { country: "<?= $country ?>" } }
        },
        return_url: "<?= $success_url ?>"
    });
    if(error) document.getElementById("paypal-message").textContent = error.message;
});
</script>

<?php elseif ($method === 'klarna'): ?>
<div style="text-align:center;">
    <p>You will be redirected to Klarna to complete your payment.</p>
    <button class="paybtn" id="klarna-pay">Pay with Klarna</button>
    <div id="klarna-message" style="color:red;margin-top:5px;"></div>
</div>
<script>
document.getElementById("klarna-pay").addEventListener("click", async ()=>{
    const stripe = Stripe("<?= $PK ?>");
    await saveOrderInfo();
    const resp = await fetch("create_payment_intent.php", {
        method:"POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({method:"klarna"})
    });
    const data = await resp.json();
    const {error} = await stripe.confirmKlarnaPayment(data.clientSecret, {
        payment_method: {
            billing_details: { name: "<?= $username ?>", email: "<?= $usermail ?>", address: { country: "<?= $country ?>" } }
        },
        return_url: "<?= $success_url ?>"
    });
    if(error) document.getElementById("klarna-message").textContent = error.message;
});
</script>

<?php elseif ($method === 'afterpay_clearpay'): ?>
<div style="text-align:center;">
    <p>You will be redirected to Afterpay / Clearpay to complete your payment in 4 installments.</p>
    <button class="paybtn" id="afterpay-btn">Pay with Afterpay</button>
    <div id="afterpay-message" style="color:red;margin-top:5px;"></div>
</div>
<script>
document.getElementById("afterpay-btn").addEventListener("click", async ()=>{
    const stripe = Stripe("<?= $PK ?>");
    await saveOrderInfo();
    const resp = await fetch("create_payment_intent.php", {
        method:"POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({method:"afterpay_clearpay"})
    });
    const data = await resp.json();
    const {error} = await stripe.confirmAfterpayClearpayPayment(data.clientSecret, {
        payment_method: {
            billing_details: { name: "<?= $username ?>", email: "<?= $usermail ?>", address: { country: "<?= $country ?>" } }
        },
        return_url: "<?= $success_url ?>"
    });
    if(error) document.getElementById("afterpay-message").textContent = error.message;
});
</script>

<?php else: ?>
<p>Unknown payment method.</p>
<?php endif; ?>
