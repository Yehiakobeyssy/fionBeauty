<?php
    session_start();
    include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';

    $user_id = 0;
?>    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php'; 
        include 'include/catecorysname.php';
    
        $sql = "SELECT slideScr, slideHref 
                FROM tblslideshow 
                WHERE pageSide='index.php' AND slideActive=1 
                ORDER BY slideID ASC";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="slideshow-container">
        <?php foreach ($slides as $slide): ?>
            <div class="mySlide">
                <a href="<?php echo htmlspecialchars($slide['slideHref']); ?>">
                    <img src="images/slide/<?php echo htmlspecialchars($slide['slideScr']); ?>" alt="Slide">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <?php include 'common/jslinks.php'?>
    <script src="js/index.js"></script>
</body>