<?php
session_start();
include 'settings/connect.php';
include 'common/function.php';
include 'common/head.php';

$user_id = $_SESSION['user_id'] ?? (int)($_COOKIE['user_id'] ?? 0);

$brandID = isset($_GET['bid']) ? (int)$_GET['bid'] : 0;

// Check if brand exists
$checkbarnd = checkItem('brandId','tblbrand',$brandID);
$checksite = checkItem('BrandID','brandpage',$brandID);
if($checkbarnd == 0){
    header("Location: index.php");
    exit();
}
if($checksite == 0){
    header("Location: index.php");
    exit();
}

$stmt = $con->prepare("SELECT * FROM brandPage WHERE BrandID = ?");
$stmt->execute([$brandID]);
$brandPage = $stmt->fetch(PDO::FETCH_ASSOC);

$sql=$con->prepare('SELECT * FROM tblbrand WHERE brandId = ?');
$sql->execute([$brandID]);
$brandinfo= $sql->fetch(PDO::FETCH_ASSOC);
?>

<link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
<link href="common/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="common/fcss/all.min.css">
<link rel="stylesheet" href="common/fcss/fontawesome.min.css">
<link rel="stylesheet" href="common/root.css">
<link rel="stylesheet" href="css/brandinfo..css">



</head>
<body>
    <?php 
    include 'include/header.php';
    include 'include/clientheader.php'; 
    ?>

    <main class="brand-page">

        <!-- SECTION 1: Main Brand Image -->
        <div class="section section1 ">
            <div class="main-image-wrapper">
                <img src="images/brands/tamplatepage/<?php echo $brandPage['mainpic'] ?? 'default.jpg'; ?>" alt="Brand Image" class="main-image">

                <!-- Circle text using SVG -->
                <svg class="circle-text-svg" viewBox="0 0 200 200">
                    <defs>
                        <path id="circlePath" d="M100,100 m-80,0 a80,80 0 1,1 160,0 a80,80 0 1,1 -160,0"/>
                    </defs>
                    <text>
                        <textPath href="#circlePath" startOffset="0" text-anchor="start"
                                textLength="502" lengthAdjust="spacingAndGlyphs">
                            <?php
                                $brandName = strtoupper($brandinfo['brandName'] ?? "BrandName");
                                // repeat brand name to fill circle
                                echo htmlentities($brandName . " * " . $brandName . " * " . $brandName);
                            ?>
                        </textPath>
                    </text>
                </svg>
            </div>
        </div>

        <div class="section section2">
            <div class="section2-image-wrapper">
                <img src="images/brands/tamplatepage/<?php echo $brandPage['subpic'] ?? 'default-sub.jpg'; ?>" 
                    alt="Sub Brand Image" class="section2-image">

                <!-- Text content overlay -->
                <div class="section2-text-wrapper">
                    <div class="slogan">
                        <?php 
                            $slogan = $brandPage['textslogan'] ?? "Your catchy slogan here";
                            echo htmlentities($slogan); 
                        ?>
                    </div>
                    <div class="subtitle">
                        <?php 
                            echo htmlentities($brandinfo['subtitle'] ?? "Subtitle text here"); 
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="section section3">
            <div class="section3-image-wrapper">
                <img src="images/brands/tamplatepage/<?php echo $brandPage['reviewpic'] ?? 'default-review.jpg'; ?>" 
                    alt="Brand Reviews" class="section3-image">

                <div class="section3-content">
                    <!-- Left slogan -->
                    <div class="slogan-left">
                        <?php
                            $slogan1 = $brandPage['textslogan1'] ?? "Your catchy slogan here";
                            echo htmlentities($slogan1);
                        ?>
                    </div>

                    <!-- Right reviews -->
                    <div class="reviews-wrapper">
                        <?php
                        // Fetch 3 best ratings for this brand
                        $brandId = $brandinfo['brandId'];
                        $stmt = $con->prepare("
                            SELECT r.rateScore, r.commentClient, r.dateRate, c.clientFname, c.clientLname, c.clientEmail
                            FROM tblrating r
                            JOIN tblitems i ON r.itemID = i.itmId
                            JOIN tblclient c ON r.clientID = c.clientID
                            WHERE i.brandId = ?
                            ORDER BY r.rateScore DESC, r.dateRate DESC
                            LIMIT 3
                        ");
                        $stmt->execute([$brandId]);
                        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($reviews as $review) {
                            // Obfuscate email
                            $email = $review['clientEmail'];
                            $emailMasked = substr($email, 0, 3) . str_repeat('*', max(0, strlen($email) - 3));

                            echo '<div class="review-card">';
                            echo '<div class="review-header">';
                            echo '<span class="client-email">' . htmlentities($emailMasked) . '</span>';
                            echo '<span class="review-score">⭐' . htmlentities($review['rateScore']) . '</span>';
                            echo '</div>';
                            echo '<p class="review-comment">' . htmlentities($review['commentClient']) . '</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section section4">
            <div class="section4-image-wrapper">
                <img src="images/brands/tamplatepage/<?php echo $brandPage['statisticpic'] ?? 'default-statistic.jpg'; ?>" 
                    alt="Statistics" class="section4-image">
            </div>

            <div class="section4-stats">

                <?php
                $brandId = $brandinfo['brandId'];

                // Number of items
                $stmt = $con->prepare("SELECT COUNT(*) FROM tblitems WHERE brandId = ?");
                $stmt->execute([$brandId]);
                $itemsCount = $stmt->fetchColumn();

                // Number sold
                $stmt = $con->prepare("
                    SELECT COUNT(*) 
                    FROM tbldatailinvoice d
                    JOIN tblitems i ON d.itmID = i.itmId
                    WHERE i.brandId = ?
                ");
                $stmt->execute([$brandId]);
                $soldCount = $stmt->fetchColumn();

                // Number of unique clients
                $stmt = $con->prepare("
                    SELECT COUNT(DISTINCT inv.clientID)
                    FROM tbldatailinvoice d
                    JOIN tblitems i ON d.itmID = i.itmId
                    JOIN tblinvoice inv ON d.invoiceID = inv.invoiceID
                    WHERE i.brandId = ?
                ");
                $stmt->execute([$brandId]);
                $clientCount = $stmt->fetchColumn();
                ?>

                <div class="stat-card">
                    <div class="stat-number"><?php echo $itemsCount; ?></div>
                    <div class="stat-label">Number of Items</div>
                </div>

                <div class="stat-card">
                    <div class="stat-number"><?php echo $soldCount; ?></div>
                    <div class="stat-label">Items Sold</div>
                </div>

                <div class="stat-card">
                    <div class="stat-number"><?php echo $clientCount; ?></div>
                    <div class="stat-label">Number of Clients</div>
                </div>

            </div>
        </div>

        <div class="section section5">

            <div class="section5-wrapper">

                <!-- Left part of brand name -->
                <div class="brand-left">
                    <?php 
                        $brand = $brandinfo['brandName'];
                        // Split word in half automatically
                        $mid = ceil(strlen($brand) / 2);
                        echo htmlentities(substr($brand, 0, $mid));
                    ?>
                </div>

                <!-- Center image -->
                <div class="center-img">
                    <img src="images/brands/tamplatepage/<?php echo $brandPage['smallpic'] ?? 'default-small.jpg'; ?>" 
                        alt="Brand" />
                </div>

                <!-- Right part of brand name -->
                <div class="brand-right">
                    <?php 
                        echo htmlentities(substr($brand, $mid));
                    ?>
                </div>

            </div>

            <!-- Slogan under everything -->
            <div class="section5-slogan">
                <?php echo htmlentities($brandPage['textslogan2']); ?>
            </div>

        </div>

        <div class="section section6">
            <div class="x-gallery">

                <div class="col-left">
                    <img src="images/brands/tamplatepage/<?php echo $brandPage['pic1']; ?>" class="x-img left top">
                    <img src="images/brands/tamplatepage/<?php echo $brandPage['pic2']; ?>" class="x-img left bottom">
                </div>

                <div class="col-center">
                    <img src="images/brands/tamplatepage/<?php echo $brandPage['pic3']; ?>" class="x-img center-img">
                </div>

                <div class="col-right">
                    <img src="images/brands/tamplatepage/<?php echo $brandPage['pic4']; ?>" class="x-img right top">
                    <img src="images/brands/tamplatepage/<?php echo $brandPage['pic5']; ?>" class="x-img right bottom">
                </div>

            </div>
        </div>
        <?php
            $brandId = $brandinfo['brandId'];

            $stmt = $con->prepare("
                    SELECT itmId, itmName, itmDesc, mainpic
                    FROM tblitems
                    WHERE brandId = ?
                    LIMIT 10
                ");
                $stmt->execute([$brandId]);
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="section section7">
            <div class="items-grid">

                <?php foreach ($items as $item): ?>
                    <div class="item-card">
                        <div class="item-img-wrapper">
                            <img src="images/items/<?php echo $item['mainpic']; ?>" 
                                alt="<?php echo htmlentities($item['itmName']); ?>">
                        </div>
                        <div class="item-info">
                            <h3 class="item-title"><?php echo htmlentities($item['itmName']); ?></h3>
                            <p class="item-desc"><?php echo htmlentities($item['itmDesc']); ?></p>
                            <button class="btn-cart" data-id="<?php echo $item['itmId']; ?>">
                                Add to cart
                            </button>
                        </div>

                        
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </main>

    <?php 
    include 'include/footer.php';
    include 'common/jslinks.php';
    ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
<script src="js/brandinfo.js"></script>
<script>
$(document).ready(function(){

    // Initialize WOW.js for animations
    new WOW().init();

    // Simple fade-in on scroll for images
    $('.section img').hide().each(function(i){
        $(this).delay(i*200).fadeIn(800);
    });

    // Hover animation for review cards
    $('.review-card').hover(function(){
        $(this).animate({marginTop: '-10px'}, 200);
    }, function(){
        $(this).animate({marginTop: '0px'}, 200);
    });

    // Scroll parallax effect for brand main image
    $(window).scroll(function(){
        var scrollTop = $(window).scrollTop();
        $('.section1 .main-image').css({
            transform: 'translateY(' + scrollTop * 0.2 + 'px)'
        });
    });

});
</script>

</body>
</html>
