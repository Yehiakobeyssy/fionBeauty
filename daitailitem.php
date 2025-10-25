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

    $item_ID = (isset($_GET['itemid']))?$_GET['itemid']:0 ;

    $checkItem = checkItem('itmId','tblitems',$item_ID);

    if($checkItem == 0 ){
        echo '<script>location.href="category.php"</script>';
    }else{
        $sql=$con->prepare('SELECT * FROM  tblitems WHERE itmId  = ?');
        $sql->execute(array($item_ID));
        $itemInfo= $sql->fetch();
    }


    if ($user_id == 0) {
        $isActive = 0;
    } else {
        $sql = $con->prepare('SELECT clientActive FROM tblclient WHERE clientID = ?');
        $sql->execute([$user_id]);
        $result_user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($result_user) {
            $isActive = $result_user['clientActive'];
        } else {
            $isActive = 0; // في حالة مفيش نتيجة
        }
    }


?>    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/daitailitem.css">
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php'; 
        include 'include/catecorysname.php';
    ?>
    <div class="titleCatecory">
        <div class="navbarsection">
            <h5>Home/ Catecories / Items / <span> <?= $itemInfo['itmName']?></span></h5> 
        </div>
        <div class="catecoryname">
            <h2><?= $itemInfo['itmName']?></h2>
        </div>      
        <div class="desgin">

        </div>
    </div>
    <div class="itemDiscription">
        <div class="image">
            <img src="images/items/<?= $itemInfo['mainpic']?>" alt="" srcset="">
        </div>
        
        <div class="info">
            <h2><?= $itemInfo['itmName']?></h2>
            <div class="rating_item">
                <?php 
                    $sql=$con->prepare('SELECT AVG(rateScore)  AS score,COUNT(rateScore) AS review FROM tblrating WHERE itemID=?');
                    $sql->execute(array($item_ID));
                    $rating_result=$sql->fetch();
                    $rate = $rating_result['score'];

                    for($i=1 ;$i <= 5 ; $i++){
                        if($rate >= $i){
                            echo '<i class="fas fa-star text-warning"></i>';
                        }else{
                            echo '<i class="fas fa-star text-muted"></i>';
                        }
                    };

                    echo ' <label> '.$rating_result['review'].' review </label>';
                ?>
                <?php
                    $sql=$con->prepare('SELECT COUNT(daitailInvoiceId) AS sold FROM tbldatailinvoice WHERE itmID = ?');
                    $sql->execute(array($item_ID))
                ?>
            </div>
            <div class="price">
            <?php
            if($isActive == 1){
                // Check if item has a promotional percent
                $promoPercent = $itemInfo['promotional'] ?? 0; // e.g., 20 for 20%
                
                if($promoPercent > 0){
                    // Calculate new price
                    $newPrice = $itemInfo['sellPrice'] - ($itemInfo['sellPrice'] * $promoPercent / 100);
                    
                    // Show crossed original price and new discounted price
                    echo '<h1 style="display: inline-block; margin-right: 10px;">
                            <span style="text-decoration: line-through; color: #888;">' . number_format($itemInfo['sellPrice'],2) . ' $</span> 
                            <span style="color: #e74c3c; margin-left:5px;">' . number_format($newPrice,2) . ' $</span>
                        </h1>';
                } else {
                    echo '<h1 style="display: inline-block; margin-right: 10px;">'. number_format($itemInfo['sellPrice'],2) .' $</h1>';
                }

                // Fetch up to 3 quantity discounts from tbldiscountitem
                $stmtDisc = $con->prepare("
                    SELECT quatity, precent 
                    FROM tbldiscountitem 
                    WHERE itemID = ? 
                    ORDER BY quatity ASC 
                    LIMIT 3
                ");
                $stmtDisc->execute([$item_ID]);
                $discounts = $stmtDisc->fetchAll(PDO::FETCH_ASSOC);

                if(!empty($discounts)){
                    // Array of different alert classes
                    $alertClasses = ['alert-success', 'alert-info', 'alert-warning', 'alert-danger'];
                    $i = 0; // Counter to rotate classes

                    foreach($discounts as $disc){
                        $class = $alertClasses[$i % count($alertClasses)]; // Rotate classes
                        echo '<div class="alert ' . $class . ' p-1 mb-0" role="alert" style="font-size:14px; display: inline-block; margin-left: 5px;">';
                        echo 'Buy ' . $disc['quatity'] . ', save ' . $disc['precent'] . '%';
                        echo '</div>';
                        $i++;
                    }
                }
            }else{
                echo '<h1>???</h1>';
            }
            ?>
            </div>


            <div class="shortdiscription">
                <p><?= nl2br(substr($itemInfo['itmDesc'], 0, 150)) . (strlen($itemInfo['itmDesc']) > 150 ? '...' : '') ?></p>
            </div>
            <div class="mini">
                <label for="">Minimum Quantity : <span id="minquantity"><?= $itemInfo['minQuantity'] ?></span></label>
            </div>
            <div class="addtocart">
                <div class="inputquantity">
                    <button class="btnquantity" id="qdec">-</button>
                    <input type="number" name="" id="quantity" value="<?= $itemInfo['minQuantity'] ?>">
                    <button class="btnquantity" id="qinc">+</button>
                </div>
                <button class="btnadd btnbuy"><i class="fa-solid fa-credit-card"></i> Buy Now</button>
                <button class="btnadd btncart"  value="<?=$item_ID?>"><i class="fas fa-cart-plus"></i> Add to Cart</button>
            </div>
            <div class="shareitem">
                <label for="">Share Item:</label>
                <button id="btnshare">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                        <path d="M20.2626 4.47332C22.4933 2.23332 25.9293 2.05198 27.9386 4.06665C29.9467 6.08265 29.7653 9.53332 27.5346 11.7733L24.3026 15.0173M13.396 18.6666C11.3866 16.6506 11.5693 13.2 13.7986 10.9613L16.6666 8.08265" stroke="black" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M18.6053 13.3333C20.6133 15.3493 20.432 18.8 18.2013 21.0386L14.9693 24.2826L11.7373 27.5266C9.50667 29.7666 6.07067 29.948 4.06133 27.9333C2.05333 25.9173 2.23467 22.4666 4.46533 20.2266L7.69733 16.9826" stroke="black" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>
                <button id="btn_fb_share">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" role="img" aria-labelledby="fbTitle">
                    <title id="fbTitle">Facebook</title>

                    <!-- Blue circle background -->
                    <circle cx="16" cy="16" r="16" fill="#0068B3"/>

                    <!-- The 'f' icon (16x16) placed at x=8 y=8 to center it inside 32x32 -->
                    <svg x="8" y="8" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <g clip-path="url(#clip0_4102_2433)">
                        <path d="M10.3982 3.19099H11.7128V0.901388C11.486 0.870188 10.706 0.799988 9.79761 0.799988C7.90221 0.799988 6.60381 1.99219 6.60381 4.18339V6.19999H4.51221V8.75959H6.60381V15.2H9.16821V8.76019H11.1752L11.4938 6.20059H9.16761V4.43719C9.16821 3.69739 9.36741 3.19099 10.3982 3.19099Z" fill="white"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_4102_2433">
                            <rect width="14.4" height="14.4" fill="white" transform="translate(0.800049 0.799988)"/>
                        </clipPath>
                        </defs>
                    </svg>
                    </svg>

                </button>
                <button id="btn_whats_share">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                        <g clip-path="url(#clip0_1323_28018)">
                            <path d="M0.682949 15.8085C0.682199 18.4971 1.3902 21.1224 2.73645 23.4363L0.554199 31.3423L8.7082 29.2208C10.9635 30.4391 13.4904 31.0774 16.0582 31.0776H16.065C24.5418 31.0776 31.4422 24.2332 31.4458 15.8205C31.4475 11.744 29.849 7.9107 26.9447 5.02673C24.041 2.14301 20.1791 0.554046 16.0643 0.552185C7.58645 0.552185 0.686574 7.39622 0.683074 15.8085" fill="url(#paint0_linear_1323_28018)"/>
                            <path d="M0.13375 15.8035C0.132875 18.5889 0.86625 21.308 2.2605 23.7048L0 31.8942L8.44637 29.6967C10.7736 30.9558 13.3939 31.6196 16.0601 31.6206H16.067C24.848 31.6206 31.9963 24.53 32 15.8162C32.0015 11.5932 30.3455 7.62208 27.3375 4.63479C24.3291 1.64788 20.3291 0.00173643 16.067 0C7.2845 0 0.13725 7.08961 0.13375 15.8035ZM5.16375 23.292L4.84837 22.7953C3.52262 20.7036 2.82288 18.2865 2.82387 15.8045C2.82675 8.56174 8.76725 2.66915 16.072 2.66915C19.6095 2.67064 22.934 4.03895 25.4345 6.52155C27.9349 9.0044 29.3108 12.3049 29.3099 15.8152C29.3066 23.058 23.366 28.9513 16.067 28.9513H16.0618C13.6851 28.9501 11.3542 28.3168 9.3215 27.12L8.83775 26.8353L3.8255 28.1393L5.16375 23.2919V23.292Z" fill="url(#paint1_linear_1323_28018)"/>
                            <path d="M12.0847 9.19668C11.7864 8.53894 11.4726 8.52567 11.1889 8.51413C10.9567 8.50421 10.6912 8.50496 10.4259 8.50496C10.1604 8.50496 9.72907 8.60406 9.36445 8.9991C8.99945 9.39451 7.97095 10.35 7.97095 12.2935C7.97095 14.237 9.39757 16.1153 9.59645 16.3791C9.79557 16.6424 12.3506 20.7582 16.3971 22.3416C19.7601 23.6575 20.4444 23.3957 21.1743 23.3298C21.9043 23.264 23.5298 22.3745 23.8614 21.4521C24.1933 20.5298 24.1933 19.7392 24.0938 19.574C23.9943 19.4094 23.7288 19.3105 23.3307 19.1131C22.9324 18.9155 20.9752 17.9598 20.6103 17.828C20.2453 17.6963 19.9799 17.6305 19.7144 18.0261C19.4489 18.421 18.6866 19.3105 18.4542 19.574C18.2221 19.838 17.9897 19.8709 17.5917 19.6733C17.1933 19.4751 15.9112 19.0585 14.3901 17.7129C13.2066 16.6658 12.4076 15.3728 12.1753 14.9773C11.9431 14.5824 12.1504 14.3683 12.3501 14.1714C12.5289 13.9944 12.7483 13.7102 12.9476 13.4796C13.1461 13.2489 13.2123 13.0843 13.3451 12.8209C13.4779 12.5572 13.4114 12.3265 13.3121 12.1289C13.2123 11.9313 12.4387 9.9777 12.0847 9.19668Z" fill="white"/>
                        </g>
                        <defs>
                            <linearGradient id="paint0_linear_1323_28018" x1="1545.14" y1="3079.56" x2="1545.14" y2="0.552185" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#1FAF38"/>
                            <stop offset="1" stop-color="#60D669"/>
                            </linearGradient>
                            <linearGradient id="paint1_linear_1323_28018" x1="1600" y1="3189.42" x2="1600" y2="0" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#F9F9F9"/>
                            <stop offset="1" stop-color="white"/>
                            </linearGradient>
                            <clipPath id="clip0_1323_28018">
                            <rect width="32" height="32" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>
                </button>
            </div>
            <?php
                if($itemInfo['extra_shipfee'] > 0 ){
                    echo '
                        <div class="extra-shipping">
                            <label> Extra Shipping Fee:</label>
                            <span class="shipping-amount">$ '.number_format($itemInfo['extra_shipfee'],2).'</span>
                        </div>
                    ';
                }
            ?>
            

            <div class="secure">
                <div class="card_secure">
                    <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <circle cx="22" cy="22" r="22" fill=" #00B25C" fill-opacity="0.06"/>
                        <path d="M15 18.75H30M18 26.25H21M24 26.25H27M17.4 30H27.6C28.4401 30 28.8601 30 29.181 29.7956C29.4632 29.6159 29.6927 29.329 29.8365 28.9762C30 28.5751 30 28.0501 30 27V18C30 16.9499 30 16.4249 29.8365 16.0238C29.6927 15.671 29.4632 15.3841 29.181 15.2044C28.8601 15 28.4401 15 27.6 15H17.4C16.5599 15 16.1399 15 15.819 15.2044C15.5368 15.3841 15.3073 15.671 15.1635 16.0238C15 16.4249 15 16.9499 15 18V27C15 28.0501 15 28.5751 15.1635 28.9762C15.3073 29.329 15.5368 29.6159 15.819 29.7956C16.1399 30 16.5599 30 17.4 30Z" stroke="#3C4242" stroke-width="1.1" stroke-linecap="round"/>
                    </svg>
                    <label for="">Secure Payment</label>
                </div>
                <div class="card_secure">
                    <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <circle cx="22" cy="22" r="22" fill="#00B25C" fill-opacity="0.06"/>
                        <path d="M23.8 26.6667V15.4667C23.8 15.2089 23.5985 15 23.35 15H13.45C13.2015 15 13 15.2089 13 15.4667V26.6667C13 26.9244 13.2015 27.1333 13.45 27.1333H14.8M23.8 26.6667C23.8 26.9244 23.5985 27.1333 23.35 27.1333H18.4M23.8 26.6667V18.2667C23.8 18.0089 24.0015 17.8 24.25 17.8H27.2136C27.333 17.8 27.4474 17.8492 27.5318 17.9367L30.8682 21.3967C30.9526 21.4842 31 21.6029 31 21.7266V26.6667C31 26.9244 30.7985 27.1333 30.55 27.1333H29.2M23.8 26.6667C23.8 26.9244 24.0015 27.1333 24.25 27.1333H25.6M14.8 27.1333C14.8 28.1643 15.6059 29 16.6 29C17.5941 29 18.4 28.1643 18.4 27.1333M14.8 27.1333C14.8 26.1024 15.6059 25.2667 16.6 25.2667C17.5941 25.2667 18.4 26.1024 18.4 27.1333M25.6 27.1333C25.6 28.1643 26.4059 29 27.4 29C28.3941 29 29.2 28.1643 29.2 27.1333M25.6 27.1333C25.6 26.1024 26.4059 25.2667 27.4 25.2667C28.3941 25.2667 29.2 26.1024 29.2 27.1333" stroke="#3C4242" stroke-width="1.1"/>
                    </svg>
                    <label for="">Fast Shipping</label>
                </div>
                <div class="card_secure">
                    <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <circle cx="22" cy="22" r="22" fill="#00B25C" fill-opacity="0.06"/>
                        <path d="M18.4444 28.2222C18.4444 29.2041 17.6485 30 16.6667 30C15.6848 30 14.8889 29.2041 14.8889 28.2222C14.8889 27.2404 15.6848 26.4444 16.6667 26.4444C17.6485 26.4444 18.4444 27.2404 18.4444 28.2222ZM18.4444 28.2222H25.5556C26.5374 28.2222 27.3333 27.4263 27.3333 26.4444V22.8889M25.5556 15.7778C25.5556 16.7596 26.3515 17.5556 27.3333 17.5556C28.3152 17.5556 29.1111 16.7596 29.1111 15.7778C29.1111 14.7959 28.3152 14 27.3333 14C26.3515 14 25.5556 14.7959 25.5556 15.7778ZM25.5556 15.7778H18.4444C17.4626 15.7778 16.6667 16.5737 16.6667 17.5556V21.1111M30 24.6667L27.6476 22.1398C27.474 21.9534 27.1926 21.9534 27.0191 22.1398L24.6667 24.6667M19.3333 19.3333L16.9809 21.8602C16.8074 22.0466 16.526 22.0466 16.3524 21.8602L14 19.3333" stroke="#3C4242" stroke-width="1.1" stroke-linecap="round"/>
                    </svg>
                    <label for="">Fast Shipping & Returns</label>
                </div>

            </div>
        </div>
    </div>
    <div class="takeSection">
        <label id="showDescription" class="active-tab">Description</label>
        <label id="showreviws">Customer's reviews</label>
    </div>
    <section id="Description_section" class="active-section">
        <div class="sec_Des_text">
            <p><?= nl2br($itemInfo['itmDesc']) ?></p>
        </div>
        <div class="sec_Des_img">
            <img src="images/items/<?= $itemInfo['mainpic'] ?>" alt="" srcset="">
        </div>
    </section>
    <section id="reviws_section">
            <div id="reviews_container"></div>
            <button id="load_more" data-offset="0" data-itemid="<?= $item_ID ?>">Load More</button>
    </section>
    <div class="title_item">
        <h3>Similar Products</h3>
    </div>
    <div class="container_items">
        <?php
        // جلب معرف الفئة للمنتج الحالي
        $sql = $con->prepare("SELECT catId FROM tblitems WHERE itmId = ?");
        $sql->execute([$item_ID]);
        $category = $sql->fetch(PDO::FETCH_ASSOC);

        if($category) {
            $categoryID = $category['catId'];

            // جلب 10 منتجات من نفس الفئة مع استبعاد المنتج الحالي
            $sql2 = $con->prepare("
                SELECT *, 
                    (SELECT AVG(rateScore) FROM tblrating r WHERE r.itemID = t.itmId) AS avgRating 
                FROM tblitems t
                WHERE catId = ? AND itmId != ? 
                LIMIT 10
            ");
            $sql2->execute([$categoryID, $item_ID]);
            $relatedItems = $sql2->fetchAll(PDO::FETCH_ASSOC);

            foreach($relatedItems as $item){
                $desc = nl2br(substr($item['itmDesc'], 0, 50)) . (strlen($item['itmDesc']) > 50 ? '...' : '');

                 if($isActive == 1){
                        $priceHtml = '<p class="item-price">'.number_format($item['sellPrice'], 2).' $</p>';
                    }else{
                        $priceHtml = '<p class="item-price"> ??? $</p>';
                    }
                
                ?>
                <div class="items_cards">
                    <div class="card itm_daitail" data-index="<?= $item['itmId'] ?>">
                        <div class="card-image">
                            <img src="images/items/<?= $item['mainpic'] ?>" alt="<?= htmlspecialchars($item['itmName']) ?>">
                        </div>
                        <div class="card-body" >
                            <div class="item-title" data-index="<?= $item['itmId'] ?>">
                                <span class="name"><?= htmlspecialchars($item['itmName']) ?></span>
                                <span class="item-rating"><?= number_format($item['avgRating'], 1) ?> ⭐</span>
                            </div>
                            <p class="item-desc"><?= $desc ?></p>
                            <?= $priceHtml ?>
                            <button class="btn-cart">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>

    </div>

    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    <script src="js/daitailitem.js"></script>

</body>