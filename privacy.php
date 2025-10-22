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
    <h1 class="h1">Privacy Policy</h1>
    <p class="p">
        At FION BEAUTY SUPPLIES, protecting your personal and professional information is our priority. This Privacy Policy outlines how we collect, use, and safeguard your data.
    </p>

    <h2 class="h2">Information Collected</h2>
    <ul class="p">
        <li>Personal and business details (name, business name, license number, address, email, phone).</li>
        <li>Account activity, order history, and training bookings.</li>
    </ul>

    <h2 class="h2">Use of Data</h2>
    <ul class="p">
        <li>To verify professional credentials and approve accounts.</li>
        <li>To process orders and manage shipping.</li>
        <li>To communicate updates, promotions, and training opportunities.</li>
    </ul>

    <h2 class="h2">Data Protection</h2>
    <p class="p">All personal information is stored securely and encrypted. Only authorized personnel have access to your information.</p>

    <h2 class="h2">Third Parties</h2>
    <p class="p">We do not sell your data. Third-party services are used solely for order processing, payments, and shipping.</p>
</section>



    </main>

    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    
</body>