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
    <h1 class="h1">Terms of Use</h1>
    <p class="p">
        Welcome to FION BEAUTY SUPPLIES. By using our website, you agree to the following terms:
    </p>

    <ul class="p">
        <li><strong>Professional-Only Access:</strong> Our products and pricing are available exclusively to licensed beauty professionals. Account approval is required before viewing wholesale pricing.</li>
        <li><strong>Account Responsibility:</strong> Users are responsible for maintaining the confidentiality of their login credentials.</li>
        <li><strong>Product Information:</strong> While we strive for accuracy, pricing, availability, and descriptions may change without notice.</li>
        <li><strong>Intellectual Property:</strong> All website content, images, and branding are property of FION BEAUTY SUPPLIES and may not be reproduced without permission.</li>
        <li><strong>Limitation of Liability:</strong> FION BEAUTY SUPPLIES is not liable for damages arising from product use, website errors, or third-party services.</li>
    </ul>
</section>



    </main>

    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    
</body>