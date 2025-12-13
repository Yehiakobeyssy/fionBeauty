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
    }

    $stat = $con->prepare('SELECT COUNT(*) AS totalitem FROM  tblitems WHERE itmActive = 1');
    $stat->execute();
    $result = $stat->fetch(PDO::FETCH_ASSOC);
    if ($result['totalitem'] < 6) {
        header('Location: commingsoon.php');
        exit(); // Always call exit after redirect
    }
    $filter = $_GET['cat'] ?? $_GET['keyword'] ?? '';

    $result = ['category' => '', 'items' => []];

    if (!$filter) {
        $filter ='';
    }

    // First, check if a category exists with this name
    $sql = "SELECT categoryId, catName FROM tblcategory WHERE catName = :filter LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->execute(['filter' => $filter]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) { 
        // Filter is a category name
        $result['category'] = $category['catName'];

        $sqlItems = "SELECT itmId, itmName FROM tblitems WHERE catId = :catId";
        $stmtItems = $con->prepare($sqlItems);
        $stmtItems->execute(['catId' => $category['categoryId']]);

        while ($row = $stmtItems->fetch(PDO::FETCH_ASSOC)) {
            $result['items'][] = ['id' => $row['itmId'], 'name' => $row['itmName']];
        }

    } else {
        // Filter is a keyword (search in items)
        $sqlItems = "SELECT i.itmId, i.itmName, c.catName 
                    FROM tblitems i 
                    JOIN tblcategory c ON i.catId = c.categoryId 
                    WHERE i.itmName LIKE :filter";
        $stmtItems = $con->prepare($sqlItems);
        $stmtItems->execute(['filter' => "%$filter%"]);

        while ($row = $stmtItems->fetch(PDO::FETCH_ASSOC)) {
            $result['category'] = $row['catName']; // category of the item
            $result['items'][] = ['id' => $row['itmId'], 'name' => $row['itmName']];
        }
    }

?>    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.7.0/nouislider.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/category.css">
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php'; 
        include 'include/catecorysname.php';
    
        $sql = "SELECT slideScr, slideHref 
                FROM tblslideshow 
                WHERE pageSide='category.php' AND slideActive=1 
                ORDER BY slideID ASC";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="titleCatecory">
        <div class="navbarsection">
            <h5>Home/ Catecories / <strong><?php echo $result['category'] ?></strong></h5>
        </div>
        <div class="catecoryname">
            <h2><?php echo $result['category'] ?></h2>
        </div>      
        <div class="desgin">

        </div>
    </div>
    <div class="slideshow-container">
        <?php foreach ($slides as $slide): ?>
            <div class="mySlide">
                <a href="<?php echo htmlspecialchars($slide['slideHref']); ?>">
                    <img src="images/slide/<?php echo htmlspecialchars($slide['slideScr']); ?>" alt="Slide">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <main>
        
        <aside>
            <div class="catecories">
                <h3>Categories</h3>
                <?php
                    // One query to get all categories + item counts
                    $sql = $con->prepare("
                        SELECT c.categoryId, c.catName, COUNT(i.itmId) AS Items
                        FROM tblcategory c
                        LEFT JOIN tblitems i ON i.catId = c.categoryId AND i.itmActive = 1
                        WHERE c.catActive = 1
                        GROUP BY c.categoryId, c.catName
                        ORDER BY c.catName
                    ");
                    $sql->execute();
                    $categories = $sql->fetchAll(PDO::FETCH_ASSOC);

                    // Output radio buttons
                    foreach ($categories as $cat) {
                        $checked = ($result['category'] === $cat['catName']) ? 'checked' : '';
                        echo '
                            <input type="radio" name="category" class="type_cat" value="'.$cat['categoryId'].'" '.$checked.'>
                            <label>'.$cat['catName'].' ('.$cat['Items'].')</label><br>
                        ';
                    }

                ?>
            </div>
            <div class="brands">
                <h3>Brand</h3>
                <?php
                    $sql=$con->prepare('SELECT brandId ,brandName FROM  tblbrand WHERE brandActive = 1');
                    $sql->execute();
                    $brands = $sql->fetchAll();
                    foreach($brands as $brand){
                        echo '
                            <input type="radio" name="brand" class="brand_type" value="'.$brand['brandId'].'" >
                            <label>'.$brand['brandName'].' </label><br>
                        ';
                    }
                ?>
            </div>
            <div class="price">
                <h3>Price</h3>
                <div class="price_input">
                    <div id="rangeSlider"></div>
                    <label>Price : <span id="minprice"></span> - <span id="maxprice"></span></label>
                    <input type="text" id="minValue" readonly hidden>
                    <input type="text" id="maxValue" readonly hidden>
                </div>
            </div>
            
            <div class="rating">
                <h3>Rating</h3>
                <input type="radio" name="rating" class="rating" value="5">
                <label><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>  <strong>5</strong></label><br>
                <input type="radio" name="rating" class="rating" value="4">
                <label><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-muted"></i>  <strong>4</strong></label><br>
                <input type="radio" name="rating" class="rating" value="3">
                <label><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-muted"></i><i class="fas fa-star text-muted"></i>  <strong>3</strong></label><br>
                <input type="radio" name="rating" class="rating" value="2">
                <label><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-muted"></i><i class="fas fa-star text-muted"></i><i class="fas fa-star text-muted"></i>  <strong>2</strong></label><br>
                <input type="radio" name="rating" class="rating" value="1">
                <label><i class="fas fa-star text-warning"></i><i class="fas fa-star text-muted"></i><i class="fas fa-star text-muted"></i><i class="fas fa-star text-muted"></i><i class="fas fa-star text-muted"></i>  <strong>1</strong></label><br>
                <input type="radio" name="rating" class="rating" value="0">
                <label><i class="fas fa-star text-muted"></i><i class="fas fa-star text-muted"></i><i class="fas fa-star text-muted"></i><i class="fas fa-star text-muted"></i><i class="fas fa-star text-muted"></i>  <strong>0</strong></label><br>
            </div>
        </aside>
        <div class="container_items">

        </div>
    </main>
    <?php include  'include/footer.php' ?>
    
    <?php 
        include 'common/jslinks.php';

        $sql=$con->prepare('SELECT MIN(sellPrice) AS minPrice , MAX(sellPrice) AS maxPrice FROM  tblitems WHERE itmActive = 1');
        $sql->execute();
        $result_price = $sql->fetch();
        $minPrice = $result_price['minPrice'];
        $maxPrice = $result_price['maxPrice'];

    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.7.0/nouislider.min.js"></script>
    <script src="js/category.js?v=1.2"></script>
    <script>
        var priceSlider = document.getElementById('rangeSlider');
        var minValueInput = document.getElementById('minValue');
        var maxValueInput = document.getElementById('maxValue');
        var minPriceSpan = document.getElementById('minprice');
        var maxPriceSpan = document.getElementById('maxprice');

        noUiSlider.create(priceSlider, {
            start: [<?= $minPrice?>, <?= $maxPrice?>], // Initial values
            connect: true,
            range: {
            'min': <?= $minPrice?>,
            'max': <?= $maxPrice?> // use PHP value instead of fixed 100
            }
        });
    </script>
</body>