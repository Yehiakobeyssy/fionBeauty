<?php
session_start();
include '../settings/connect.php';
include '../common/function.php';

// تحقق من تسجيل الدخول
if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];  
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = (int) $_COOKIE['user_id'];  
} else {
    header("Location: ../login.php");
    exit(); 
}

include '../common/head.php';
?>
<link rel="stylesheet" href="../common/root.css">
<link rel="stylesheet" href="css/checkoutworkshop.css">
<script src="https://js.stripe.com/v3/"></script>

</head>
<body>

<?php
include 'include/header.php';
include 'include/clientheader.php';
include 'include/catecorysname.php';
?>

<main>
    <div class="sections_side">
        <div class="welcome_note">
            <h2>Your <span>Workshop Cart</span></h2>
        </div>

        <?php
        if (!isset($_SESSION['workoutCart']) || empty($_SESSION['workoutCart'])) {
            echo "<p class='text-center'>Your cart is empty. <a href='../workshops.php'>Browse Workshops</a></p>";
        } else {
            $workshopIds = $_SESSION['workoutCart'];
            $placeholders = implode(',', array_fill(0, count($workshopIds), '?'));
            $stmt = $con->prepare("SELECT * FROM workshops WHERE id IN ($placeholders)");
            $stmt->execute($workshopIds);
            $workshops = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = 0;

            echo '<div class="info_add">';
            foreach ($workshops as $row) {
                $total += $row['cost'];
                ?>
                <div class="info">
                    <div class="design"><?= strtoupper(substr($row['title'],0,1)) ?></div>
                    <h4><?= htmlspecialchars($row['title']) ?></h4>
                    <label><?= htmlspecialchars($row['description']) ?></label>
                    <p><strong>Date:</strong> <?= $row['workshop_date'] ?> | <strong>Time:</strong> <?= substr($row['start_time'],0,5) ?></p>
                    <p><strong>Duration:</strong> <?= $row['duration_hours'] ?> hours</p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                    <p><strong>Price:</strong> $<?= number_format($row['cost'],2) ?></p>
                </div>
                <?php
            }
            echo '</div>';

            // المجموع الكلي
            ?>
            <div class="main_add mt-3">
                <h4>Total: $<?= number_format($total,2) ?></h4>
            </div>

            <!-- طرق الدفع -->
            <div class="payment_methods">
                <h4>Choose Payment Method</h4>
                <div class="containerpayment">
                    <?php
                    $sql = $con->prepare("SELECT methodName, methodNote FROM tblpaymentmethods WHERE methodActive = 1");
                    $sql->execute();
                    $methods = $sql->fetchAll();
                    foreach ($methods as $method):
                        $slug = strtolower(str_replace([' ', '/'], '_', $method['methodName']));
                    ?>
                    <div class="payment_option" data-method="<?=$slug?>">
                        <label>
                            <input type="radio" name="paymentMethod" value="<?=$slug?>" <?= $slug==='card'?'checked':'' ?>>
                            <strong><?=$method['methodName']?></strong>
                        </label>
                        <p class="note"><?=$method['methodNote']?></p>
                        <div class="method_container" id="method_<?=$slug?>"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php
        }
        ?>
    </div>
</main>

<?php include 'include/footer.php'; ?>
<?php include '../common/jslinks.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {

    async function loadPayment(method){
        const container = document.getElementById("method_" + method);
        container.style.display = "block";
        container.innerHTML = "<p style='color:#009245'>Loading...</p>";

        try {
            const resp = await fetch("ajax_payment_section_workshop.php", {
                method: "POST",
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: "method=" + method
            });
            const data = await resp.json();
            if(data.status === 1){
                container.innerHTML = data.html;
                container.querySelectorAll("script").forEach(s=>eval(s.innerText));
            } else {
                container.innerHTML = "<p style='color:red'>" + data.message + "</p>";
            }
        } catch(err){
            container.innerHTML = "<p style='color:red'>Error loading payment.</p>";
            console.error(err);
        }
    }

    const radios = document.querySelectorAll("input[name='paymentMethod']");
    radios.forEach(radio=>{
        radio.addEventListener("change", function(){
            radios.forEach(r=>document.getElementById("method_" + r.value).style.display='none');
            loadPayment(this.value);
        });
    });

    // تحميل الافتراضي عند الصفحة
    const defaultRadio = document.querySelector("input[name='paymentMethod']:checked");
    if(defaultRadio) loadPayment(defaultRadio.value);

});
</script>
