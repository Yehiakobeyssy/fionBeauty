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
                Founded in 2018, <strong>Fion Beauty Supplies</strong> has proudly served professionals across Canada as a trusted source for premium spa supplies, advanced equipment, and high-quality salon furniture.
            </p>

            <p class="p">
                We are dedicated to supporting beauty and wellness professionals with exceptional products that combine innovation, performance, and reliability.
            </p>

            <p class="p">
                As the sole distributor of <strong>ThermoCEUTICAL</strong> in Canada, Fion Beauty Supplies brings cutting-edge Korean skincare technology to Canadian spas and clinics—offering a full line of professional-grade skincare solutions designed to deliver visible, lasting results.
            </p>

            <p class="p">
                Our mission is to empower aestheticians, spas, and wellness centers with the tools and expertise they need to create transformative experiences for their clients.
            </p>

            <p class="p">
                With years of industry knowledge and a deep passion for beauty and care, Fion Beauty Supplies continues to set the standard for quality, trust, and professional excellence across Canada.
            </p>
        </section>
    </main>

    <?php include 'include/footer.php' ?>
    <?php include 'common/jslinks.php' ?>
</body>
</html>
