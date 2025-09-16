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
    <div class="newArrvals section_index">
        <div class="sectiontitle">
            <h4>Featured</h4>
        </div>
        <div class="title">
            <h2>New Arrivals</h2>
        </div>
        <?php
            $sql = "SELECT categoryId, catName, catDescription, carImg 
                    FROM tblcategory 
                    WHERE catActive = 1 
                    ORDER BY catInputDate DESC 
                    LIMIT 4";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="container_new">
            <?php if(count($categories) >= 4): ?>
                <!-- Left Image -->
                <div class="left-image">
                    <img src="images/items/<?= $categories[0]['carImg'] ?>" alt="<?= $categories[0]['catName'] ?>" class="category-img">
                    <div class="overlay">
                        <h3><?= $categories[0]['catName'] ?></h3>
                        <p><?= $categories[0]['catDescription'] ?></p>
                        <a href="category.php?cat=<?= $categories[0]['catName'] ?>" class="btn btn-primary">Shop Now</a>
                    </div>
                </div>

                <!-- Right Images -->
                <div class="right-images">
                    <div class="top-right">
                        <img src="images/items/<?= $categories[1]['carImg'] ?>" alt="<?= $categories[1]['catName'] ?>" class="category-img">
                        <div class="overlay">
                            <h3><?= $categories[1]['catName'] ?></h3>
                            <p><?= $categories[1]['catDescription'] ?></p>
                            <a href="category.php?cat=<?= $categories[1]['catName'] ?>"  class="btn btn-primary">Shop Now</a>
                        </div>
                    </div>

                    <div class="bottom-right">
                        <div>
                            <img src="images/items/<?= $categories[2]['carImg'] ?>" alt="<?= $categories[2]['catName'] ?>" class="category-img">
                            <div class="overlay">
                                <h3><?= $categories[2]['catName'] ?></h3>
                                <p><?= $categories[2]['catDescription'] ?></p>
                                <a href="category.php?cat=<?= $categories[2]['catName'] ?>" class="btn btn-primary">Shop Now</a>
                            </div>
                        </div>
                        <div>
                            <img src="images/items/<?= $categories[3]['carImg'] ?>" alt="<?= $categories[3]['catName'] ?>" class="category-img">
                            <div class="overlay">
                                <h3><?= $categories[3]['catName'] ?></h3>
                                <p><?= $categories[3]['catDescription'] ?></p>
                                <a href="category.php?cat=<?= $categories[3]['catName'] ?>" class="btn btn-primary">Shop Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="foryou section_index">
        <div class="sectiontitle">
            <h4>For you </h4>
        </div>
        <div class="title">
            <h2>Best Selling Products</h2>
        </div>
        <div class="items_cards"></div>

        <div class="pagination">
            <button id="btnBack">Back</button>
            <button id="btnNext">Next</button>
        </div>
    </div>
    <div class="special_item section_index">
        <?php
            // Fetch one random active item with its brand
            $stmt = $con->prepare("
                SELECT i.itmId, i.itmName, i.itmDesc, i.mainpic, b.brandName
                FROM tblitems i
                JOIN tblbrand b ON i.brandId = b.brandId
                WHERE i.itmActive = 1
                ORDER BY RAND()
                LIMIT 1
            ");
            $stmt->execute();
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>

            <?php if ($item): ?>
            <section class="featured-item">
            <div class="container_spe">
                <div class="item-content">
                    <div class="item-text">
                        <h2>Don’t Miss Out! Grab the <span class="item-name"><?= htmlspecialchars($item['itmName']) ?></span> by <span class="brand-name"><?= htmlspecialchars($item['brandName']) ?></span> Now!</h2>
                        <p><?= htmlspecialchars($item['itmDesc']) ?></p>
                        <a href="buy.php?itmId=<?= $item['itmId'] ?>" class="btn-buy">Buy Now</a>
                    </div>
                    <div class="item-image">
                        <img src="images/items/<?= htmlspecialchars($item['mainpic']) ?>" alt="<?= htmlspecialchars($item['itmName']) ?>" class="remove-white">
                    </div>
                </div>
            </div>
            </section>
        <?php endif; ?>        
    </div>
    <div id="categories_container">
    </div>       
    <?php include 'common/jslinks.php'?>
    <script src="js/index.js"></script>
</body>