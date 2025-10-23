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
    $stat = $con->prepare('SELECT COUNT(*) AS totalitem FROM  tblitems WHERE itmActive = 1');
    $stat->execute();
    $result = $stat->fetch(PDO::FETCH_ASSOC);
    if ($result['totalitem'] >= 6) {
        header('Location: index.php');
        exit(); // Always call exit after redirect
    }
?>    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/commingsoon.css">
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php'; 
        include 'include/catecorysname.php';
    ?>
    <div class="container_comming">
        <img src="images/logo.png" alt="FION Logo">
        <h1>Coming Soon</h1>
        <p>
            Get ready for <strong>FION BEAUTY SUPPLIES</strong>, the comprehensive online platform designed exclusively for beauty professionals. <br>
            Soon, you'll be able to shop leading brands, book advanced training sessions, and download all the professional resources you need to succeed.  
        </p>
        <p><strong>Be the first to know! Sign up for launch updates.</strong></p>
    </div>
    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    <script src="js/commingsoon.js"></script>
</body>