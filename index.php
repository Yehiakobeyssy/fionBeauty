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
    if ($result['totalitem'] < 6) {
        header('Location: commingsoon.php');
        exit(); // Always call exit after redirect
    }
?>    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/index.css?v=1.3">
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
    <?php
        $totalStmt = $con->query("SELECT COUNT(*) FROM tblitems WHERE itmActive=1 AND promotional > 0");
        $totalItems = $totalStmt->fetchColumn();
        if($totalItems > 0){
            echo '
                <div class="flashsale section_index">
                    <div class="title_flash">
                        <h5>Flash Sales</h5>
                    </div>
                    <div class="items_cards"></div>
                    <div class="pagination">
                        <button id="btnBack_pro">Back</button>
                        <button id="btnNext_pro">Next</button>
                    </div>
                </div>
            ';
        }
    ?>
    
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
            <?php foreach ($categories as $index => $cat): ?>
                <div class="category-row <?= $index % 2 !== 0 ? 'reverse' : '' ?>">
                    
                    <div class="category-image">
                        <img src="images/items/<?= htmlspecialchars($cat['carImg']) ?>" alt="<?= htmlspecialchars($cat['catName']) ?>">
                    </div>

                    <div class="category-content">
                        <h2><?= htmlspecialchars($cat['catName']) ?></h2>
                        <p><?= htmlspecialchars($cat['catDescription']) ?></p>

                        <a href="category.php?cat=<?= urlencode($cat['catName']) ?>" class="btn-shop">
                            Shop Now
                        </a>
                    </div>

                </div>
            <?php endforeach; ?>
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
                        <a href="daitailitem.php?itemid=<?= $item['itmId'] ?>" class="btn-buy">Buy Now</a>
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
    <div class="topbrands section_index">
        <h2>Top Brands Deal</h2>
        <div class="brandslogos">
            <?php 
                $sql= $con->prepare('SELECT brandId ,brandName , brandIcon  FROM tblbrand  WHERE brandActive = 1 ORDER BY RAND() LIMIT 5');
                $sql->execute();
                $brands = $sql->fetchAll();
                foreach($brands as $brand){
                    echo '
                        <a class="brnadcard" href="brandinfo.php?bid='.$brand['brandId'].'">
                            <img src="images/brands/'.$brand['brandIcon'].'" >
                            <label><strong>'.$brand['brandName'].'</strong></label>
                        </a>
                    ';
                }
            ?>
        </div>        
    </div>
    <div class="whatyouWillGet">
        <h3>What you will get?</h3>
        <div class="cards_gets">
            <div class="card_service">
                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
                    <!-- Circle background with border -->
                    <circle cx="30" cy="30" r="28" fill="#009245" stroke="#00B25C" stroke-width="4"/>
                    
                    <!-- Delivery icon centered -->
                    <g transform="translate(10,10) scale(1.0)">
                        <g clip-path="url(#clip0_4067_3870)">
                        <path d="M11.6666 31.6667C13.5075 31.6667 14.9999 30.1743 14.9999 28.3333C14.9999 26.4924 13.5075 25 11.6666 25C9.82564 25 8.33325 26.4924 8.33325 28.3333C8.33325 30.1743 9.82564 31.6667 11.6666 31.6667Z" stroke="#FAFAFA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M28.3333 31.6667C30.1743 31.6667 31.6667 30.1743 31.6667 28.3333C31.6667 26.4924 30.1743 25 28.3333 25C26.4924 25 25 26.4924 25 28.3333C25 30.1743 26.4924 31.6667 28.3333 31.6667Z" stroke="#FAFAFA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8.33325 28.3335H6.99992C5.89535 28.3335 4.99992 27.4381 4.99992 26.3335V21.6668M3.33325 8.3335H19.6666C20.7712 8.3335 21.6666 9.22893 21.6666 10.3335V28.3335M14.9999 28.3335H24.9999M31.6666 28.3335H32.9999C34.1045 28.3335 34.9999 27.4381 34.9999 26.3335V18.3335M34.9999 18.3335H21.6666M34.9999 18.3335L30.5825 10.9712C30.2211 10.3688 29.5701 10.0002 28.8675 10.0002H21.6666" stroke="#FAFAFA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5 11.8184H11.6667" stroke="#FAFAFA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1.81812 15.4546H8.48478" stroke="#FAFAFA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5 19.0908H11.6667" stroke="#FAFAFA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_4067_3870">
                            <rect width="40" height="40" fill="white"/>
                        </clipPath>
                        </defs>
                    </g>
                </svg>
                <div class="disription_card_willget">
                    <h4> FAST DELIVERY</h4>
                    <label for="">Delivery for all orders</label>
                </div>    
            </div>
            <div class="card_service">
                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
                    <circle cx="30" cy="30" r="28" fill="#009245" stroke="#00B25C" stroke-width="4"/>
                    
                    <!-- Service icon centered -->
                    <g transform="translate(8,8) scale(1.1)">
                        <g clip-path="url(#clip0_4067_3887)">
                        <path d="M13.3334 24.9998C13.3334 23.1589 11.841 21.6665 10.0001 21.6665C8.15913 21.6665 6.66675 23.1589 6.66675 24.9998V28.3332C6.66675 30.1741 8.15913 31.6665 10.0001 31.6665C11.841 31.6665 13.3334 30.1741 13.3334 28.3332V24.9998Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M33.3334 24.9998C33.3334 23.1589 31.841 21.6665 30.0001 21.6665C28.1591 21.6665 26.6667 23.1589 26.6667 24.9998V28.3332C26.6667 30.1741 28.1591 31.6665 30.0001 31.6665C31.841 31.6665 33.3334 30.1741 33.3334 28.3332V24.9998Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6.66675 24.9998V19.9998C6.66675 16.4636 8.07151 13.0722 10.572 10.5717C13.0725 8.07126 16.4639 6.6665 20.0001 6.6665C23.5363 6.6665 26.9277 8.07126 29.4282 10.5717C31.9287 13.0722 33.3334 16.4636 33.3334 19.9998V24.9998" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M30 31.6665C30 32.9926 28.9464 34.2644 27.0711 35.202C25.1957 36.1397 22.6522 36.6665 20 36.6665" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_4067_3887">
                            <rect width="40" height="40" fill="white"/>
                        </clipPath>
                        </defs>
                    </g>
                </svg>
                <div class="disription_card_willget">
                    <h4>24/7 CUSTOMER SERVICE</h4>
                    <label for="">Friendly 24/7 customer support</label>
                </div> 
            </div>
            <div class="card_service">
                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
                    <circle cx="30" cy="30" r="28" fill="#009245" stroke="#00B25C" stroke-width="4"/>
                    
                    <!-- Secure icon centered -->
                    <g transform="translate(8,8) scale(1.1)">
                        <path d="M19.9832 2.5874C21.0047 2.5874 22.0041 2.73663 22.7576 3.01807L31.075 6.13525H31.0759C33.2954 6.96202 35.0505 9.50761 35.0505 11.8667V24.2495C35.0505 25.3367 34.7063 26.5895 34.1238 27.7485C33.5778 28.8348 32.8404 29.8024 32.031 30.4556L31.8679 30.5825L24.7009 35.9321L24.6951 35.937C23.4124 36.9261 21.7238 37.4331 19.9998 37.4331C18.277 37.433 16.5847 36.9263 15.2644 35.9478H15.2634L8.09937 30.5991C7.22666 29.9484 6.42532 28.9208 5.84253 27.7593C5.25969 26.5976 4.91675 25.3447 4.91675 24.2661V11.8667C4.91675 9.50749 6.67169 6.96189 8.89136 6.13525H8.89233L17.2087 3.01807C17.9622 2.73655 18.9615 2.58743 19.9832 2.5874ZM20.0007 4.08545C19.2021 4.08763 18.3752 4.19487 17.7419 4.43115L17.741 4.43213L9.42456 7.54834H9.42358C8.59608 7.85993 7.85485 8.52245 7.32397 9.29053C6.7929 10.0589 6.43335 10.9898 6.43335 11.8833V24.2661C6.43335 25.1606 6.74393 26.1893 7.20093 27.1011C7.65781 28.0126 8.29317 28.8726 9.00073 29.4009L16.1677 34.7505C17.2296 35.5444 18.6282 35.9252 20.0017 35.9253C21.3756 35.9253 22.7779 35.5442 23.8474 34.7515L23.8494 34.7505L31.0154 29.4009L31.0164 29.3999C31.7311 28.8638 32.3667 28.0049 32.822 27.0942C33.2774 26.1836 33.5837 25.1596 33.5837 24.2661V11.8667C33.5837 10.9807 33.2233 10.0539 32.6931 9.28662C32.1626 8.51907 31.4221 7.85386 30.5974 7.53369L30.5925 7.53174L22.2751 4.41455L22.2664 4.41162C21.6282 4.18643 20.8001 4.08327 20.0007 4.08545Z" fill="#FAFAFA" stroke="#FAFAFA"/>
                        <path d="M24.4038 14.77C24.6919 14.4822 25.1754 14.482 25.4634 14.77C25.7513 15.058 25.7511 15.5415 25.4634 15.8296L18.2964 22.9966C18.1451 23.1478 17.9573 23.2163 17.7661 23.2163C17.5751 23.2162 17.388 23.1477 17.2368 22.9966L14.5532 20.313C14.2654 20.0249 14.2652 19.5414 14.5532 19.2534C14.8412 18.9654 15.3247 18.9655 15.6128 19.2534L17.7661 21.4067L18.1206 21.0532L24.4038 14.77Z" fill="#FAFAFA" stroke="#FAFAFA"/>
                    </g>
                </svg>
                <div class="disription_card_willget">
                    <h4>MONEY BACK GUARANTEE</h4>
                    <label for="">We reurn money within 30 days</label>
                </div> 
            </div>
        </div>
    </div>
    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    <script src="js/index.js?v=1.1"></script>
</body>