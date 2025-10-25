<?php
    session_start();
    include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';

    if (isset($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];  
    } elseif (isset($_COOKIE['user_id'])) {
        $user_id = (int) $_COOKIE['user_id'];  
    } else {
        $user_id = 0; // if neither session nor cookie exist
    };
?>    
<link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
<link href="common/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="common/fcss/all.min.css">
<link rel="stylesheet" href="common/fcss/fontawesome.min.css">
<link rel="stylesheet" href="common/root.css">
<link rel="stylesheet" href="css/pages.css">
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php'; 
        include 'include/catecorysname.php';
    ?>
    <main>
        <section class="card mt-3 mb-3">
            <h1 class="h1">Fion Beauty Supplies</h1>
            <h2 class="h2">Terms and Conditions</h2>

            <p class="p">
                Welcome to Fion Beauty Supplies. By accessing or purchasing from our website, you agree to comply with and be bound by the following Terms and Conditions. 
                Please read them carefully before using our site or placing an order.
            </p>

            <hr>

            <h3>1. General</h3>
            <p class="p">
                Fion Beauty Supplies reserves the right to update or modify these Terms and Conditions at any time without prior notice. 
                Continued use of our website after such changes constitutes your acceptance of the updated terms.
            </p>

            <hr>

            <h3>2. Products and Services</h3>
            <p class="p">
                All products listed on our website are intended for professional use in spas, salons, and clinics. 
                Product descriptions, prices, and availability are subject to change without notice.
            </p>

            <hr>

            <h3>3. Orders and Payments</h3>
            <ul class="p">
                <li>All orders are processed in Canadian dollars (CAD).</li>
                <li>We accept major credit cards and other secure payment methods.</li>
                <li>Orders are subject to verification and approval before processing.</li>
                <li>Once an order is confirmed, it cannot be modified or canceled after shipment.</li>
            </ul>

            <hr>

            <h3>4. Shipping and Delivery</h3>
            <ul class="p">
                <li>We ship across Canada using trusted carriers.</li>
                <li>Delivery times may vary depending on your location and product availability.</li>
                <li>Shipping fees are calculated at checkout.</li>
                <li>Fion Beauty Supplies is not responsible for delays caused by shipping carriers or external factors.</li>
            </ul>

            <hr>

            <h3>5. Returns and Exchanges</h3>
            <ul class="p">
                <li>Due to the nature of beauty and spa products, all sales are final unless items are defective or damaged upon arrival.</li>
                <li>Any claims for defective or incorrect items must be reported within 7 business days of delivery.</li>
                <li>Returned products must be unused, in their original packaging, and accompanied by proof of purchase.</li>
                <li>Authorized returns may be subject to a restocking fee.</li>
            </ul>

            <hr>

            <h3>6. Warranty</h3>
            <p class="p">
                Equipment and machines purchased from Fion Beauty Supplies come with a limited manufacturer’s warranty. 
                Warranty details vary by product and brand. 
                Damage due to misuse, improper installation, or unauthorized repair is not covered.
            </p>

            <hr>

            <h3>7. Distributor Policy</h3>
            <p class="p">
                Fion Beauty Supplies is the sole authorized distributor of <strong>ThermoCEUTICAL</strong> in Canada. 
                Any unauthorized resale or distribution of ThermoCEUTICAL products is strictly prohibited and may result in legal action.
            </p>

            <hr>

            <h3>8. Limitation of Liability</h3>
            <p class="p">
                Fion Beauty Supplies shall not be liable for any indirect, incidental, or consequential damages arising from the use or inability to use our products or services.
            </p>

            <hr>

            <h3>9. Privacy Policy</h3>
            <p class="p">
                We respect your privacy. Personal information collected through our website is used solely to process your orders and improve customer service. 
                We never share or sell your information to third parties without consent.
            </p>

            <hr>

            <h3>10. Contact Information</h3>
            <p class="p">
                <?php
                    $sql=$con->prepare('SELECT companyPhone,companyAdd,companyEmail FROM  tblsetting WHERE seetingID = 1');
                    $sql->execute();
                    $info = $sql->fetch();
                ?>
                For any inquiries regarding our Terms and Conditions or policies, please contact:<br>
                📧 <strong><?= $info['companyEmail'] ?></strong><br>
                📞 <em><?= $info['companyPhone'] ?></em><br>
                📍 <em><?= $info['companyAdd'] ?></em>
            </p>
        </section>
    </main>

    <?php include 'include/footer.php' ?>
    <?php include 'common/jslinks.php' ?>
</body>
</html>

