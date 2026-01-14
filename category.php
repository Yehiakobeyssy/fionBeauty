<?php
session_start();
include 'settings/connect.php';
include 'common/function.php';
include 'common/head.php';

// User session
$user_id = $_SESSION['user_id'] ?? $_COOKIE['user_id'] ?? 0;

// Check total items
$stat = $con->prepare('SELECT COUNT(*) AS totalitem FROM tblitems WHERE itmActive = 1');
$stat->execute();
$totalItems = $stat->fetchColumn();
if ($totalItems < 6) {
    header('Location: commingsoon.php');
    exit();
}

// GET parameters
$categoryName = isset($_GET['cat']) ? $_GET['cat'] : 0;
$keyword      = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$subCatId     = isset($_GET['subcat']) ?  $_GET['subcat'] : 0;

if($categoryName > 0 ){
    $checkcat= checkItem('categoryId',"tblcategory", $categoryName);
    if($checkcat > 0 ){
        $stat1 = $con->prepare('SELECT catName FROM tblcategory WHERE categoryId  = ?');
        $stat1->execute([$categoryName]);
        $resulttitle = $stat1->fetch(PDO::FETCH_ASSOC);
        $categoryNewName= $resulttitle['catName'];
        $navbar = '<h5>Home/ Catecories / <strong>'. $categoryNewName .'</strong></h5>';
        $pageTitle = '<h5>'.$categoryNewName.'</h5>';
    }else{
        $navbar = '<h5>Home/ Catecories / <strong></strong></h5>';
        $pageTitle = '';
    }
}elseif($subCatId > 0 ){
    $checksubcat =checkItem('subCatID',"tblsubcategory", $subCatId) ;
    if($checksubcat > 0){
        $sql=$con->prepare('SELECT subCatName,catName 
                            FROM tblsubcategory
                            INNER JOIN tblcategory 
                            ON tblsubcategory.catID = tblcategory.categoryId 
                            WHERE subCatID  = ?');
        $sql->execute([$subCatId]);
        $resulttitle = $sql->fetch(PDO::FETCH_ASSOC);
        $categoryNewName = $resulttitle['catName'];
        $subCatName = $resulttitle['subCatName'];
        $navbar = '<h5> Home / Catecories / '.$categoryNewName .' / <span>'. $subCatName .'</span> </h5>';
        $pageTitle = '<h5>'.$subCatName.'</h5>';
    }else{
        $navbar = '<h5> Home / Catecories / <span></span></h5>';
        $pageTitle = '';
    }
}else{
    $navbar = '<h5>Home/ Catecories / <span></span></h5>';
    $pageTitle = '';
}

// =================== HTML START ===================
?>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.7.0/nouislider.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/category.css?v=1.5">
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
            <?= $navbar ?>
        </div>
        <div class="catecoryname">
            <?= $pageTitle ?>
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
    <input type="text" name="searchCatId" id="searchCatID" hidden>
    <input type="text" name="serachsubcat" id="searchsubCatID" hidden>
    <main>
        
        <aside>
            <div class="catecories">
                <h3>Categories</h3>
                <?php
    // Fetch all categories
    $catStmt = $con->prepare("SELECT categoryId, catName FROM tblcategory WHERE catActive = 1 ORDER BY catName");
    $catStmt->execute();
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($categories as $cat):
        // Fetch subcategories for this category along with item counts
        $subStmt = $con->prepare("
            SELECT s.subCatID, s.subCatName, COUNT(i.itmId) AS ItemsCount
            FROM tblsubcategory s
            LEFT JOIN tblitems i ON i.subCatID = s.subCatID AND i.itmActive = 1
            WHERE s.catID = :catId AND s.subCatActive = 1
            GROUP BY s.subCatID, s.subCatName
            ORDER BY s.subCatName
        ");
        $subStmt->execute([':catId' => $cat['categoryId']]);
        $subcategories = $subStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
        <div class="category-block">
            <div class="cat-header">
                <span><?php echo htmlspecialchars($cat['catName']); ?></span>
                <span class="arrow">▼</span>
            </div>
            <div class="subcats" style="display: none;">
                <?php foreach ($subcategories as $sub): ?>
                    <label>
                        <input type="radio" name="subcategory" class="subcat-radio" value="<?php echo $sub['subCatID']; ?>">
                        <?php echo htmlspecialchars($sub['subCatName']); ?> (<?php echo $sub['ItemsCount']; ?>)
                    </label><br>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
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
    <script src="js/category.js?v=1.6"></script>
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