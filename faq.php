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
    <h1 class="h1">FAQ</h1>

    <h2 class="h2">Who can purchase from FION BEAUTY SUPPLIES?</h2>
    <p class="p">Only licensed beauty professionals, spas, salons, and med spas. All new accounts require verification of credentials.</p>

    <h2 class="h2">How do I register for a professional account?</h2>
    <p class="p">Click <strong>Register</strong> → Fill out business details → Upload license or certificate → Submit → Wait for admin approval.</p>

    <h2 class="h2">What payment methods are accepted?</h2>
    <p class="p">We accept credit cards (Visa, MasterCard), PayPal, Interac e-Transfer, and approved invoicing for registered professionals.</p>

    <h2 class="h2">Can I return products?</h2>
    <p class="p">Yes, returns are accepted within 14 days for unopened items. All returns require prior authorization from our support team.</p>

    <h2 class="h2">How can I access product protocols or MSDS sheets?</h2>
    <p class="p">Once approved and logged in, visit the <strong>Resources</strong> section to download MSDS, treatment protocols, and product catalogs.</p>

    <h2 class="h2">Do you offer training?</h2>
    <p class="p">Yes, book online training sessions for facial treatments, lasers, dermaplaning, and advanced spa services through the <strong>Training & Education</strong> section.</p>
</section>


    </main>

    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    
</body>