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
    <h1 class="h1">About Us</h1>
    <p class="p">
        FION BEAUTY SUPPLIES is a professional-only wholesale supplier dedicated to serving spas, salons, and med spas across the nation. 
        We provide licensed beauty professionals with premium-quality products, advanced equipment, and educational resources to help them deliver exceptional services to their clients.
    </p>
    <h2 class="h2">Our Mission</h2>
    <p class="p">
        To empower beauty professionals with access to the highest-quality products, reliable shipping, and industry-leading training, so they can thrive in their business.
    </p>
    <h2 class="h2">Why Choose FION BEAUTY SUPPLIES</h2>
    <ul class="p">
        <li>Exclusive access to top brands like ThermaCEUTICAL.</li>
        <li>Professional-only wholesale pricing and promotions.</li>
        <li>Nationwide shipping and fast delivery.</li>
        <li>Educational resources including protocols, MSDS sheets, and training courses.</li>
    </ul>
</section>
    </main>

    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    <!-- <script src="js/contactus.js"></script> -->

</body>