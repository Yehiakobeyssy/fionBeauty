<?php
    session_start();
    include '../settings/connect.php';
    include '../common/function.php';

    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit();  
    }else{
        $admin_id = $_SESSION['admin_id'];
        $sql=$con->prepare('SELECT fName, lName FROM  tbladmin WHERE adminID  = ?');
        $sql->execute([$admin_id]);
        $result =  $sql->fetch();
        $admin_name = $result['fName'].' ' . $result['lName'];
    }
    include '../common/head.php';

    $do = isset($_GET['do'])?$_GET['do']:'managecat';


    if (isset($_POST['btnnewitem'])) {
        $filename = ''; // default if no image uploaded

        // 1. Handle image upload
        if (!empty($_FILES['carImg']['name'])) {
            $files = $_FILES['carImg'];

            // Get file extension
            $ext = strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));

            // Allowed image types
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($ext, $allowed)) {
                // Generate numeric filename: timestamp + random number
                $filename = time() . rand(1000, 9999) . "." . $ext;
                $destination = "../images/items/" . $filename;

                if (!move_uploaded_file($files['tmp_name'], $destination)) {
                    echo "<script>alert('Error uploading image');</script>";
                    $filename = ''; // reset on failure
                }
            } else {
                echo "<script>alert('Invalid file type');</script>";
            }
        }

        // 2. Collect category data
        $catName = $_POST['catName'] ?? '';
        $catDescription = $_POST['catDescription'] ?? '';
        $carImg = $filename;
        $shippingfree_accepted = $_POST['shippingfree_accepted'];
        $amountOver = $_POST['amountOver'];
        $discount = $_POST['discount'];
        $catActive = 1;

        // 3. Insert into database
        $sql = $con->prepare('INSERT INTO tblcategory (catName, catDescription, carImg,shippingfree_accepted,amountOver,discount, catActive) VALUES (?, ?, ?,?,?,?, ?)');
        $sql->execute([$catName, $catDescription, $carImg,$shippingfree_accepted,$amountOver,$discount, $catActive]);

        // 4. Redirect to view the category
        $catId = $con->lastInsertId();
        echo '<script>location.href="manageproducts.php?do=viewcat&catid=' . $catId . '"</script>';
    }


    
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/manageproducts.css?v=1.1">
</head>
<body> 
    <?php include 'include/adminheader.php' ?>
    <?php
            $sql = $con->prepare('SELECT admin_block FROM  tbladmin WHERE adminID   = ?');
            $sql->execute([$admin_id]);
            $result_block = $sql->fetch();
            $isBlock = $result_block['admin_block'];

            if ($isBlock == 1) {
                echo '
                    <div class="alert alert-danger">
                        <h2>OPPS! You are Blocked from Admin</h2>
                    </div>
                    <script> 
                        setTimeout(function() {
                            window.location.href = "../index.php";
                        }, 2000);
                    </script>
                ';
                exit(); // stop the rest of the page from executing
            }
        ?>
    <main>
        <?php include 'include/adminaside.php'?>
        <div class="container_info">
            <?php
                switch ($do) {
                    case 'managecat':
                        ?>
                            <div class="titlecat">
                                <a href="manageproducts.php?do=addcat"> + Add Category</a>
                            </div>
                            <div class="filter_manage_category">
                                <div class="filter-row">
                                    <div class="filter-item duration-buttons">
                                        <div class="duration-group">
                                            <button type="button" class="duration-btn active" data-value="9999">All Time</button>
                                            <button type="button" class="duration-btn" data-value="365">Last Year</button>
                                            <button type="button" class="duration-btn" data-value="30">Last Month</button>
                                            <button type="button" class="duration-btn" data-value="7">Last Week</button>
                                            <button type="button" class="duration-btn" data-value="1">Today</button>
                                        </div>
                                        <input type="hidden" name="duration" id="duration" value="9999">
                                    </div>
                                    <div class="filter-item">
                                        <input type="text" id="search" name="search" placeholder="Search category...">
                                    </div>
                                    <div class="filter-item">
                                        <input type="date" id="date" name="date" placeholder="Select Date">
                                    </div>
                                </div>
                            </div>
                            <div class="mangetablecatgory">
                                <div class="title_table">
                                    <h4>Categories</h4>
                                </div>
                                <table>
                                    <thead>
                                        <th>Category</th>
                                        <th>Orders</th>
                                        <th>Items</th>
                                        <th>Accepted Free shipping</th>
                                        <th>Create At</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody id="viewManageCategory">

                                    </tbody>
                                </table>
                            </div>
                            <div id="paginationContainer" ></div>
                            <script src="js/managepoduct_managecat.js"></script>
                        <?php
                        
                        break;

                    case 'viewcat':
                            $cat = isset($_GET['catid'])?$_GET['catid']:0;
                            $checkCat = checkItem('categoryId','tblcategory',$cat);
                            if($checkCat == 0){
                                echo '<script> location.href="manageproducts.php"</script>';
                            }
                        ?>
                        <div class="titlecat">
                            <a href="manageproducts.php?do=editcat&catid=<?=$cat?>">Edit</a>
                            <a href="manageproducts.php?do=deletecat&catid=<?=$cat?>">Delete</a>
                        </div>
                        <div class="category_info">
                            
                            <div class="category_name admin-category-info">
                                <?php
                                    $sql = $con->prepare('SELECT carImg, catName, catDescription, catInputDate, shippingfree_accepted, amountOver, discount FROM tblcategory WHERE categoryId = ?');
                                    $sql->execute([$cat]);
                                    $categoryInfo = $sql->fetch(PDO::FETCH_ASSOC);
                                ?>
                                
                                <div class="img">
                                    <img src="../images/items/<?= htmlspecialchars($categoryInfo['carImg']) ?>" alt="<?= htmlspecialchars($categoryInfo['catName']) ?>">
                                </div>
                                
                                <div class="namecat">
                                    <h5><?= htmlspecialchars($categoryInfo['catName']) ?></h5>
                                    <label><?= htmlspecialchars($categoryInfo['catDescription']) ?></label><br>
                                    <label><strong>Accepted Free Shipping:</strong> <?= $categoryInfo['shippingfree_accepted'] == 1 ? 'Yes' : 'No' ?></label><br>
                                    
                                    <!-- New: Amount Over and Discount -->
                                    <label>
                                        <strong>Discount Rule:</strong> 
                                        <?php if ($categoryInfo['amountOver'] > 0 && $categoryInfo['discount'] > 0): ?>
                                            Get <strong><?= number_format($categoryInfo['discount'], 2) ?>%</strong> discount when purchase total in this category exceeds <strong>$<?= number_format($categoryInfo['amountOver'], 2) ?></strong>
                                        <?php else: ?>
                                            No category discount
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="15" viewBox="0 0 19 15" fill="none"> <path fill-rule="evenodd" clip-rule="evenodd" d="M16.7812 7.5C16.7812 4.27834 14.039 1.66667 10.6562 1.66667C7.54781 1.66667 4.9802 3.87195 4.58422 6.72937L5.22503 6.11908C5.56674 5.79364 6.12076 5.79364 6.46247 6.11908C6.80418 6.44452 6.80418 6.97216 6.46247 7.29759L3.96561 9.67555C3.79475 9.83827 3.51775 9.83827 3.34689 9.67555L0.850032 7.29759C0.508323 6.97216 0.508323 6.44452 0.850032 6.11908C1.19174 5.79364 1.74576 5.79364 2.08747 6.11908L2.81403 6.81105C3.17925 2.99184 6.55089 0 10.6562 0C15.0055 0 18.5312 3.35786 18.5312 7.5C18.5312 11.6421 15.0055 15 10.6562 15C8.16895 15 5.95061 13.9008 4.50883 12.1879C4.20657 11.8288 4.26721 11.3043 4.64426 11.0165C5.02132 10.7286 5.57201 10.7863 5.87427 11.1454C6.9982 12.4807 8.72238 13.3333 10.6562 13.3333C14.039 13.3333 16.7812 10.7217 16.7812 7.5ZM11.5312 4.16667C11.5312 3.70643 11.1395 3.33333 10.6562 3.33333C10.173 3.33333 9.78125 3.70643 9.78125 4.16667V7.5C9.78125 7.96024 10.173 8.33333 10.6562 8.33333H12.8438C13.327 8.33333 13.7188 7.96024 13.7188 7.5C13.7188 7.03976 13.327 6.66667 12.8438 6.66667H11.5312V4.16667Z" fill="#667085"/> </svg>
                                        <strong>Created At:</strong> <?= date('d M Y', strtotime($categoryInfo['catInputDate'])) ?>
                                    </label>
                                </div>
                            </div>


                            <div class="statistic_cat">
                                <?php
                                    $sql = "
                                            SELECT 
                                                c.categoryId,
                                                SUM(
                                                    CASE 
                                                        WHEN d.status = 1 THEN (d.quantity * d.up) - ((d.discount / 100) * (d.quantity * d.up))
                                                        ELSE 0
                                                    END
                                                ) AS Balance,
                                                COUNT(CASE WHEN d.status = 1 THEN 1 END) AS Orders,
                                                COUNT(DISTINCT CASE WHEN i.itmActive = 1 THEN i.itmId END) AS ActiveItems,
                                                COUNT(CASE WHEN d.status > 1 THEN 1 END) AS Cancel
                                            FROM tblcategory c
                                            LEFT JOIN tblitems i ON c.categoryId = i.catId
                                            LEFT JOIN tbldatailinvoice d ON i.itmId = d.itmID
                                            WHERE c.categoryId = :cat
                                            GROUP BY c.categoryId
                                            ";

                                    $stmt = $con->prepare($sql);
                                    $stmt->execute([':cat' => $cat]);
                                    $data = $stmt->fetch(PDO::FETCH_ASSOC);

                                ?>
                                <div class="synpole">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <g clip-path="url(#clip0_2002_42513)">
                                            <path d="M17.6752 13.2417C17.145 14.4955 16.3158 15.6002 15.2601 16.4595C14.2043 17.3187 12.9541 17.9063 11.6189 18.1707C10.2836 18.4352 8.90386 18.3685 7.6003 17.9766C6.29673 17.5846 5.10903 16.8793 4.14102 15.9223C3.17302 14.9653 2.45419 13.7857 2.04737 12.4867C1.64055 11.1877 1.55814 9.8088 1.80734 8.47059C2.05653 7.13238 2.62975 5.87559 3.47688 4.81009C4.324 3.74459 5.41924 2.90283 6.66684 2.3584" stroke="#009245" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M18.3333 10.0003C18.3333 8.90598 18.1178 7.82234 17.699 6.8113C17.2802 5.80025 16.6664 4.88159 15.8926 4.10777C15.1187 3.33395 14.2001 2.72012 13.189 2.30133C12.178 1.88254 11.0943 1.66699 10 1.66699V10.0003H18.3333Z" stroke="#009245" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_2002_42513">
                                            <rect width="20" height="20" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="totalbalance">
                                    <label for="">Total Balnace</label>
                                    <h5><?= number_format($data['Balance'],2) ?>$</h5>
                                </div>
                                <div class="allstatistic">
                                    <div class="cardstatistic">
                                        <label for="">All Orders</label>
                                        <h5><?= $data['Orders']?></h5>
                                    </div>
                                    <div class="cardstatistic">
                                        <label for="">Items</label>
                                        <h5><?= $data['ActiveItems']?></h5>
                                    </div>
                                    <div class="cardstatistic">
                                        <label for="">Cancel Orders</label>
                                        <h5><?= $data['Cancel']?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container_products">
                            <div class="title_products">
                                <h5>Products</h5>
                            </div>
                            <div class="filter_manage_category">
                                <div class="filter-row">
                                    <div class="filter-item duration-buttons">
                                        <div class="duration-group">
                                            <button type="button" class="duration-btn active" data-value="9999">All Time</button>
                                            <button type="button" class="duration-btn" data-value="365">Last Year</button>
                                            <button type="button" class="duration-btn" data-value="30">Last Month</button>
                                            <button type="button" class="duration-btn" data-value="7">Last Week</button>
                                            <button type="button" class="duration-btn" data-value="1">Today</button>
                                        </div>
                                        <input type="hidden" name="duration" id="durationitem" value="9999">
                                    </div>
                                    <div class="filter-item">
                                        <input type="text" id="search" name="search" placeholder="Search Item...">
                                    </div>
                                    <div class="filter-item">
                                        <input type="date" id="date" name="date" placeholder="Select Date">
                                    </div>
                                </div>
                            </div>
                            <div id="product_result" class="result_product"></div>
                            <div id="paginationContainer" class="pagination_container"></div>
                        </div>
                        <div class="addproduct">
                            <a href="manageproducts.php?do=additm&cat=<?=$cat?>" class="btn btn-primary">Add Product</a>
                        </div>
                        <script src="js/managepoduct_viewcat.js"></script>
                        <?php
                        break;

                    case 'addcat':
                        ?>
                            <div class="title">
                                <h5>New Category</h5>
                            </div>
                            <form class="category-addform" method="POST" enctype="multipart/form-data">
                                <div class="thumbnail-section">
                                    <img id="thumbnail" src="https://via.placeholder.com/150" alt="">
                                    <p>Add Thumbnail for category</p>
                                    <!-- Make the label clickable directly -->
                                    <label for="file-upload" class="choose-button">
                                        Chose Image 
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_4342_4243)">
                                                <path d="M4.16667 5.83301H5C5.44203 5.83301 5.86595 5.65741 6.17851 5.34485C6.49107 5.03229 6.66667 4.60837 6.66667 4.16634C6.66667 3.94533 6.75446 3.73337 6.91074 3.57709C7.06702 3.42081 7.27899 3.33301 7.5 3.33301H12.5C12.721 3.33301 12.933 3.42081 13.0893 3.57709C13.2455 3.73337 13.3333 3.94533 13.3333 4.16634C13.3333 4.60837 13.5089 5.03229 13.8215 5.34485C14.134 5.65741 14.558 5.83301 15 5.83301H15.8333C16.2754 5.83301 16.6993 6.0086 17.0118 6.32116C17.3244 6.63372 17.5 7.05765 17.5 7.49967V14.9997C17.5 15.4417 17.3244 15.8656 17.0118 16.1782C16.6993 16.4907 16.2754 16.6663 15.8333 16.6663H4.16667C3.72464 16.6663 3.30072 16.4907 2.98816 16.1782C2.67559 15.8656 2.5 15.4417 2.5 14.9997V7.49967C2.5 7.05765 2.67559 6.63372 2.98816 6.32116C3.30072 6.0086 3.72464 5.83301 4.16667 5.83301" stroke="#009245" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M10 13.333C11.3807 13.333 12.5 12.2137 12.5 10.833C12.5 9.4523 11.3807 8.33301 10 8.33301C8.61929 8.33301 7.5 9.4523 7.5 10.833C7.5 12.2137 8.61929 13.333 10 13.333Z" stroke="#009245" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_4342_4243">
                                                <rect width="20" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </label>
                                    <input id="file-upload" name="carImg" type="file" accept="image/*" onchange="previewImage(event)" style="display:none;">
                                </div>
                                <div class="form-section">
                                    <h2>Category Details</h2>
                                    <input type="text" placeholder="Category Name" name="catName" required>
                                    <label for="">Accepted Free Shipping</label>
                                    <select name="shippingfree_accepted" id="">
                                        <option value="1">Yes</option>
                                        <option value="0">NO</option>
                                    </select>
                                    <div class="catprice">
                                        <input type="number" name="amountOver" step="0.01" placeholder="Spend amount over (e.g. 100.00)">
                                        <input type="number" name="discount" step="0.01" placeholder="Discount percentage (e.g. 10%)" min="0" max="100">
                                    </div>
                                    <textarea placeholder="Category Discription" name="catDescription" required></textarea>
                                    <div class="buttons">
                                        <button type="submit" class="btn btn-primary" name="btnnewitem">Save Changes</button>
                                        <button type="button" class="btn btn-ghost">Cancel</button>
                                    </div>
                                </div>
                            </form>
                            <script src="js/managepoduct_addcat.js"></script>
                            <script>
                                function previewImage(event) {
                                    const reader = new FileReader();
                                    reader.onload = function(){
                                        const output = document.getElementById('thumbnail');
                                        output.src = reader.result;
                                    };
                                    reader.readAsDataURL(event.target.files[0]);
                                }
                            </script>
                        <?php
                        break;
                    case 'editcat':
                        $cat = isset($_GET['catid'])?$_GET['catid']:0;
                        $checkCat = checkItem('categoryId','tblcategory',$cat);
                        if($checkCat == 0){
                            echo '<script> location.href="manageproducts.php"</script>';
                        }else{
                            $stmt = $con->prepare("SELECT * FROM tblcategory WHERE categoryId  = ?");
                            $stmt->execute([$cat]);
                            $category = $stmt->fetch(PDO::FETCH_ASSOC);
                        }

                        if (isset($_POST['btnupdateitem'])) {
                                $catName = $_POST['catName'] ?? '';
                                $catDescription = $_POST['catDescription'] ?? '';
                                $filename = $category['carImg']; // default: keep old image
                                $shippingfree_accepted = $_POST['shippingfree_accepted'];
                                $amountOver = $_POST['amountOver'];
                                $discount = $_POST['discount'];

                                // Handle new image upload
                                if (!empty($_FILES['carImg']['name'])) {
                                    $files = $_FILES['carImg'];
                                    $ext = strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));
                                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                                    if (in_array($ext, $allowed)) {
                                        $newFilename = time() . rand(1000, 9999) . "." . $ext;
                                        $destination = "../images/items/" . $newFilename;

                                        if (move_uploaded_file($files['tmp_name'], $destination)) {
                                            // Delete old image if exists
                                            if (!empty($filename) && file_exists("../images/items/" . $filename)) {
                                                unlink("../images/items/" . $filename);
                                            }
                                            $filename = $newFilename;
                                        } else {
                                            echo "<script>alert('Error uploading new image');</script>";
                                        }
                                    } else {
                                        echo "<script>alert('Invalid file type');</script>";
                                    }
                                }

                                // Update database
                                $sql = $con->prepare("UPDATE tblcategory SET catName=?, catDescription=?,shippingfree_accepted=? ,amountOver=?,discount=?, carImg=? WHERE categoryId =?");
                                $sql->execute([$catName, $catDescription,$shippingfree_accepted,$amountOver,$discount, $filename, $cat]);

                                echo '<script>location.href="manageproducts.php?do=viewcat&catid=' . $cat . '"</script>';
                            }

                        ?>
                            <div class="title">
                                <h5>Edit Category</h5>
                            </div>
                            <form class="category-addform" method="POST" enctype="multipart/form-data">
                                <div class="thumbnail-section">
                                    <img id="thumbnail" src="<?php echo !empty($category['carImg']) ? '../images/items/' . $category['carImg'] : 'https://via.placeholder.com/150'; ?>" alt="">
                                    <p>Add Thumbnail for category</p>
                                    <label for="file-upload" class="choose-button">
                                        Choose Image
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_4342_4243)">
                                                <path d="M4.16667 5.83301H5C5.44203 5.83301 5.86595 5.65741 6.17851 5.34485C6.49107 5.03229 6.66667 4.60837 6.66667 4.16634C6.66667 3.94533 6.75446 3.73337 6.91074 3.57709C7.06702 3.42081 7.27899 3.33301 7.5 3.33301H12.5C12.721 3.33301 12.933 3.42081 13.0893 3.57709C13.2455 3.73337 13.3333 3.94533 13.3333 4.16634C13.3333 4.60837 13.5089 5.03229 13.8215 5.34485C14.134 5.65741 14.558 5.83301 15 5.83301H15.8333C16.2754 5.83301 16.6993 6.0086 17.0118 6.32116C17.3244 6.63372 17.5 7.05765 17.5 7.49967V14.9997C17.5 15.4417 17.3244 15.8656 17.0118 16.1782C16.6993 16.4907 16.2754 16.6663 15.8333 16.6663H4.16667C3.72464 16.6663 3.30072 16.4907 2.98816 16.1782C2.67559 15.8656 2.5 15.4417 2.5 14.9997V7.49967C2.5 7.05765 2.67559 6.63372 2.98816 6.32116C3.30072 6.0086 3.72464 5.83301 4.16667 5.83301" stroke="#009245" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M10 13.333C11.3807 13.333 12.5 12.2137 12.5 10.833C12.5 9.4523 11.3807 8.33301 10 8.33301C8.61929 8.33301 7.5 9.4523 7.5 10.833C7.5 12.2137 8.61929 13.333 10 13.333Z" stroke="#009245" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_4342_4243">
                                                    <rect width="20" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </label>
                                    <input id="file-upload" name="carImg" type="file" accept="image/*" onchange="previewImage(event)" style="display:none;">
                                </div>
                                <div class="form-section">
                                    <h2>Category Details</h2>
                                    <input type="text" placeholder="Category Name" name="catName" value="<?php echo htmlspecialchars($category['catName']); ?>" required>
                                    <label for="">Accepted Free Shipping</label>
                                    <select name="shippingfree_accepted" id="">
                                        <option value="1" <?= $category['shippingfree_accepted'] == 1 ? 'selected' : '' ?>>Yes</option>
                                        <option value="0" <?= $category['shippingfree_accepted'] == 0 ? 'selected' : '' ?>>No</option>
                                    </select>
                                    <div class="catprice">
                                        <input type="number" name="amountOver" step="0.01" placeholder="Spend amount over (e.g. 100.00)" value="<?php echo htmlspecialchars($category['amountOver']); ?>">
                                        <input type="number" name="discount" step="0.01" min="0" max="100" placeholder="Discount percentage (e.g. 10%)" value="<?php echo htmlspecialchars($category['discount']); ?>">
                                    </div>
                                    <textarea placeholder="Category Description" name="catDescription" required><?php echo htmlspecialchars($category['catDescription']); ?></textarea>
                                    <div class="buttons">
                                        <button type="submit" class="btn btn-primary" name="btnupdateitem">Update Category</button>
                                        <button type="button" class="btn btn-ghost" onclick="window.history.back();">Cancel</button>
                                    </div>
                                </div>
                            </form>

                            <script>
                                function previewImage(event) {
                                    const reader = new FileReader();
                                    reader.onload = function(){
                                        const output = document.getElementById('thumbnail');
                                        output.src = reader.result;
                                    };
                                    reader.readAsDataURL(event.target.files[0]);
                                }
                            </script>
                        <?php
                        break;

                    case 'deletecat':
                        $cat = isset($_GET['catid'])?$_GET['catid']:0;
                        $checkCat = checkItem('categoryId','tblcategory',$cat);
                        if($checkCat == 0){
                            echo '<script> location.href="manageproducts.php"</script>';
                        }else{
                            $stmt = $con->prepare("SELECT * FROM tblcategory WHERE categoryId  = ?");
                            $stmt->execute([$cat]);
                            $category = $stmt->fetch(PDO::FETCH_ASSOC);
                        }

                        if(isset($_POST['btndelete'])){
                            $sql=$con->prepare('UPDATE tblcategory SET catActive = 0 WHERE categoryId =?');
                            $sql->execute([$cat]);
                            echo '<script>location.href="manageproducts.php"</script>';
                        }
                        ?>
                            <div class="delete_container">
                                <form action="" method="post" class="alert alert-danger">
                                    <p>Do you want to delete <br>
                                    Category <span><?=$category['catName']?></span></p>
                                    <div class="btncontrol">
                                        <button type="submit" class="btn btn-primary" name="btndelete">Yes</button>
                                        <a href="manageproducts.php" class="btn btn-ghost">No</a>
                                    </div>
                                </form>
                            </div>
                        <?php
                        break;

                    case 'additm':
                        $cat = isset($_GET['cat'])?$_GET['cat']:0;

                            if(isset($_POST['btnaddItem'])){
                                $filename = ''; // default if no image uploaded
                                if (!empty($_FILES['mainpic']['name'])) {
                                    $files = $_FILES['mainpic'];
                                    $ext = strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));
                                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                    if (in_array($ext, $allowed)) {
                                        $filename = time() . rand(1000, 9999) . "." . $ext;
                                        $destination = "../images/items/" . $filename;
                                        if (!move_uploaded_file($files['tmp_name'], $destination)) {
                                            echo "<script>alert('Error uploading image');</script>";
                                            $filename = '';
                                        }
                                    } else {
                                        echo "<script>alert('Invalid file type');</script>";
                                    }
                                }

                                $catId          = $_POST['catId'];
                                $brandId        = $_POST['brandId'];
                                $itmName        = $_POST['itmName'];
                                $itmDesc        = $_POST['itmDesc'];
                                $mainpic        = $filename;
                                $sellPrice      = $_POST['sellPrice'];
                                $commtion       = $_POST['commtion'];
                                $promotional    = $_POST['promotional']??0;
                                $extra_shipfee  = isset($_POST['extra_shipfee'])?$_POST['extra_shipfee']:0;
                                $itmActive      = 1;
                                $getDiscount    = isset($_POST['getDiscount']) ? 1 : 0;
                                $minQuantity    = $_POST['minQuantity'];

                                $sql = $con ->prepare('INSERT INTO tblitems (catId,brandId,itmName,itmDesc,mainpic,sellPrice,commtion,promotional,extra_shipfee,itmActive,getDiscount,minQuantity)
                                                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
                                $sql->execute([
                                    $catId,         
                                    $brandId,       
                                    $itmName,        
                                    $itmDesc,        
                                    $mainpic,        
                                    $sellPrice,      
                                    $commtion,
                                    $promotional,
                                    $extra_shipfee,      
                                    $itmActive,      
                                    $getDiscount,   
                                    $minQuantity,   
                                ]);

                                $itmId  = $con->lastInsertId();

                                $discounts = [];
                                if (!empty($_POST['discountData'])) {
                                    $discounts = json_decode($_POST['discountData'], true);
                                }
                                if ($getDiscount && !empty($discounts)) {
                                    $ins = $con->prepare("INSERT INTO  tbldiscountitem (itemID, quatity, precent) VALUES (?, ?, ?)");
                                    foreach ($discounts as $d) {
                                        $ins->execute([$itmId, $d['qty'], $d['discount']]);
                                    }
                                }

                                echo "<script>alert('Item successfully added!'); window.location='manageproducts.php';</script>"; 
                            }
                        ?>
                            <div class="title">
                                <h5>New Product</h5>
                            </div>
                            <form class="product-addform" method="POST" enctype="multipart/form-data">
                                <div class="genegralinfo">
                                    <div class="thumbnail-section">
                                        <img id="thumbnail" src="https://via.placeholder.com/150" alt="">
                                        <p>Add Thumbnail for Product</p>
                                        <!-- Make the label clickable directly -->
                                        <label for="file-upload" class="choose-button">
                                            Chose Image 
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_4342_4243)">
                                                    <path d="M4.16667 5.83301H5C5.44203 5.83301 5.86595 5.65741 6.17851 5.34485C6.49107 5.03229 6.66667 4.60837 6.66667 4.16634C6.66667 3.94533 6.75446 3.73337 6.91074 3.57709C7.06702 3.42081 7.27899 3.33301 7.5 3.33301H12.5C12.721 3.33301 12.933 3.42081 13.0893 3.57709C13.2455 3.73337 13.3333 3.94533 13.3333 4.16634C13.3333 4.60837 13.5089 5.03229 13.8215 5.34485C14.134 5.65741 14.558 5.83301 15 5.83301H15.8333C16.2754 5.83301 16.6993 6.0086 17.0118 6.32116C17.3244 6.63372 17.5 7.05765 17.5 7.49967V14.9997C17.5 15.4417 17.3244 15.8656 17.0118 16.1782C16.6993 16.4907 16.2754 16.6663 15.8333 16.6663H4.16667C3.72464 16.6663 3.30072 16.4907 2.98816 16.1782C2.67559 15.8656 2.5 15.4417 2.5 14.9997V7.49967C2.5 7.05765 2.67559 6.63372 2.98816 6.32116C3.30072 6.0086 3.72464 5.83301 4.16667 5.83301" stroke="#009245" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M10 13.333C11.3807 13.333 12.5 12.2137 12.5 10.833C12.5 9.4523 11.3807 8.33301 10 8.33301C8.61929 8.33301 7.5 9.4523 7.5 10.833C7.5 12.2137 8.61929 13.333 10 13.333Z" stroke="#009245" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_4342_4243">
                                                    <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </label>
                                        <input id="file-upload" name="mainpic" type="file" accept="image/*" onchange="previewImage(event)" style="display:none;">
                                    </div>
                                    <div class="form-section">
                                        <h2>Product Details</h2>
                                        <input type="text" placeholder="Product Name" name="itmName" required>
                                        <div class="barnd">
                                            <div class="selectoption">
                                                <label for="">Category</label>
                                                <select name="catId" id="" required>
                                                    <option value="">[Select Category]</option>
                                                    <?php
                                                        $stat = $con->prepare('SELECT categoryId , catName FROM  tblcategory WHERE catActive = 1 ORDER BY catName ');
                                                        $stat->execute();
                                                        $cats = $stat->fetchAll();
                                                        foreach($cats as $ca){
                                                            if($cat == $ca['categoryId']){
                                                                echo '<option value="'.$ca['categoryId'].'" selected>'.$ca['catName'].'</option>';
                                                            }else{
                                                                echo '<option value="'.$ca['categoryId'].'">'.$ca['catName'].'</option>';
                                                            }
                                                            
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="selectoption">
                                                <label for="">Brand</label>
                                                <select name="brandId" id="" required>
                                                    <option value="">[Select Brand]</option>
                                                    <?php
                                                        $stat = $con->prepare('SELECT brandId , brandName FROM  tblbrand WHERE brandActive = 1 ORDER BY brandName');
                                                        $stat->execute();
                                                        $brands = $stat->fetchAll();
                                                        foreach($brands as $brand){
                                                            echo '<option value="'.$brand['brandId'].'" >'.$brand['brandName'].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            
                                        </div>
                                        <div class="barnd">
                                            <div class="selectoption">
                                                <label for="">Sell Price</label>
                                                <input type="number" name="sellPrice" id="" step="0.01" placeholder="Sell price" required>
                                            </div>
                                            <div class="selectoption">
                                                <label for="">Promotional discount (%)</label>
                                                <input type="number" name="promotional" id="" step="0.01" placeholder="Promotional discount"  min="0" max="100" >
                                            </div>
                                        </div>
                                        <div class="barnd">
                                            <div class="selectoption">
                                                <label for="">Extra Shipping Fee</label>
                                                <input type="number" name="extra_shipfee" id="" step="0.01" placeholder="Extra Shipping Fee">
                                            </div>
                                            <div class="selectoption">
                                                <label for="">Commition (%)</label>
                                                <input type="number" name="commtion" id="" step="0.01" placeholder="Commition (%)"  min="0" max="100">
                                            </div>
                                        </div>
                                        <div class="quantity">
                                            <label for="">Minimum Quantity</label>
                                            <input type="number" name="minQuantity" id="" required value="1">
                                        </div>
                                        <textarea placeholder="Product Discription" name="itmDesc" required></textarea>
                                    </div>
                                </div>
                                <div class="discount_info">
                                    <div class="toggle-container">
                                        <span id="statusText">Get Discount:</span>
                                        <label class="switch">
                                            <input type="checkbox" id="discountToggle" name="getDiscount">
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="discount-section" id="discountSection">
                                        <div class="frmdis">
                                            <input type="number" id="qty" placeholder="Quantity" min="1">
                                            <input type="number" id="discount" placeholder="Discount %" min="0" step="0.1">
                                        </div>
                                        <button id="addDiscount">Add</button>
                                        <table id="discountTable">
                                        <thead>
                                            <tr>
                                            <th>Quantity</th>
                                            <th>Discount (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        </table>

                                        <!-- <button id="finish">Finish</button> -->
                                        <input type="hidden" name="discountData" id="discountData">
                                    </div>
                                </div>

                                <div class="buttons btnsitems">
                                        <button type="submit" class="btn btn-primary" name="btnaddItem">Save Item</button>
                                        <button type="button" class="btn btn-ghost" onclick="window.history.back();">Cancel</button>
                                </div>                        
                            </form>

                            <script src="js/managepoduct_additm.js"></script>
                            <script>
                                function previewImage(event) {
                                    const reader = new FileReader();
                                    reader.onload = function(){
                                        const output = document.getElementById('thumbnail');
                                        output.src = reader.result;
                                    };
                                    reader.readAsDataURL(event.target.files[0]);
                                }
                            </script>
                        <?php
                        break;
                    case 'edititm':

                        $itmId = isset($_GET['itmId']) ? intval($_GET['itmId']) : 0;
                        if ($itmId <= 0) {
                            echo "<script>alert('Invalid item ID'); window.location='manageproducts.php';</script>";
                            exit;
                        }

                        // Fetch item data
                        $stmt = $con->prepare("SELECT * FROM tblitems WHERE itmId = ?");
                        $stmt->execute([$itmId]);
                        $item = $stmt->fetch(PDO::FETCH_ASSOC);

                        if (!$item) {
                            echo "<script>alert('Item not found'); window.location='manageproducts.php';</script>";
                            exit;
                        }

                        // Fetch discount data
                        $discountStmt = $con->prepare("SELECT quatity, precent FROM tbldiscountitem WHERE itemID = ?");
                        $discountStmt->execute([$itmId]);
                        $discounts = $discountStmt->fetchAll(PDO::FETCH_ASSOC);

                        if (isset($_POST['btneditItem'])) {
                            $filename = $item['mainpic']; // default to existing image

                            // Image upload (optional)
                            if (!empty($_FILES['mainpic']['name'])) {
                                $files = $_FILES['mainpic'];
                                $ext = strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));
                                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                                if (in_array($ext, $allowed)) {
                                    $filename = time() . rand(1000, 9999) . "." . $ext;
                                    $destination = "../images/items/" . $filename;

                                    if (!move_uploaded_file($files['tmp_name'], $destination)) {
                                        echo "<script>alert('Error uploading new image');</script>";
                                        $filename = $item['mainpic'];
                                    } else {
                                        // Delete old image
                                        if (!empty($item['mainpic']) && file_exists("../images/items/" . $item['mainpic'])) {
                                            unlink("../images/items/" . $item['mainpic']);
                                        }
                                    }
                                } else {
                                    echo "<script>alert('Invalid file type');</script>";
                                }
                            }

                            // Prepare data
                            $catId       = $_POST['catId'];
                            $brandId     = $_POST['brandId'];
                            $itmName     = trim(strip_tags($_POST['itmName']));
                            $itmDesc     = trim(strip_tags($_POST['itmDesc']));
                            $sellPrice   = $_POST['sellPrice'];
                            $commtion    = $_POST['commtion'];
                            $promotional = $_POST['promotional']??0;
                            $extra_shipfee = isset($_POST['extra_shipfee']) ? $_POST['extra_shipfee'] : 0;
                            $itmActive   = 1;
                            $getDiscount = isset($_POST['getDiscount']) ? 1 : 0;
                            $minQuantity = $_POST['minQuantity'];

                            // Update item
                            $sql = $con->prepare("UPDATE tblitems 
                                SET catId=?, brandId=?, itmName=?, itmDesc=?, mainpic=?, sellPrice=?, commtion=?,promotional=?,extra_shipfee=?, itmActive=?, getDiscount=?, minQuantity=? 
                                WHERE itmId=?");
                            $sql->execute([
                                $catId, $brandId, $itmName, $itmDesc, $filename,
                                $sellPrice, $commtion,$promotional,$extra_shipfee, $itmActive, $getDiscount, $minQuantity,
                                $itmId
                            ]);

                            // Update discounts
                            $discounts = [];
                            if (!empty($_POST['discountData'])) {
                                $discounts = json_decode($_POST['discountData'], true);
                            }

                            // Clear old discounts
                            $con->prepare("DELETE FROM tbldiscountitem WHERE itemID = ?")->execute([$itmId]);

                            if ($getDiscount && !empty($discounts)) {
                                $ins = $con->prepare("INSERT INTO tbldiscountitem (itemID, quatity, precent) VALUES (?, ?, ?)");
                                foreach ($discounts as $d) {
                                    if (is_numeric($d['qty']) && is_numeric($d['discount'])) {
                                        $ins->execute([$itmId, $d['qty'], $d['discount']]);
                                    }
                                }
                            }

                            echo "<script>alert('Item updated successfully!'); window.location='manageproducts.php';</script>";
                        }
                        ?>

                        <div class="title">
                            <h5>Edit Product</h5>
                        </div>

                        <form class="product-addform" method="POST" enctype="multipart/form-data">
                            <div class="genegralinfo">
                                <div class="thumbnail-section">
                                    <img id="thumbnail" src="../images/items/<?php echo !empty($item['mainpic']) ? $item['mainpic'] : 'https://via.placeholder.com/150'; ?>" alt="">
                                    <p>Change Thumbnail</p>
                                    <label for="file-upload" class="choose-button">Choose Image</label>
                                    <input id="file-upload" name="mainpic" type="file" accept="image/*" onchange="previewImage(event)" style="display:none;">
                                </div>

                                <div class="form-section">
                                    <h2>Product Details</h2>
                                    <input type="text" placeholder="Product Name" name="itmName" required value="<?php echo htmlspecialchars($item['itmName']); ?>">

                                    <div class="barnd">
                                        <div class="selectoption">
                                            <label>Category</label>
                                            <select name="catId" required>
                                                <option value="">[Select Category]</option>
                                                <?php
                                                $stat = $con->prepare('SELECT categoryId, catName FROM tblcategory WHERE catActive = 1 ORDER BY catName');
                                                $stat->execute();
                                                $cats = $stat->fetchAll();
                                                foreach ($cats as $ca) {
                                                    $sel = ($item['catId'] == $ca['categoryId']) ? 'selected' : '';
                                                    echo '<option value="' . $ca['categoryId'] . '" ' . $sel . '>' . $ca['catName'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="selectoption">
                                            <label>Brand</label>
                                            <select name="brandId" required>
                                                <option value="">[Select Brand]</option>
                                                <?php
                                                $stat = $con->prepare('SELECT brandId, brandName FROM tblbrand WHERE brandActive = 1 ORDER BY brandName');
                                                $stat->execute();
                                                $brands = $stat->fetchAll();
                                                foreach ($brands as $brand) {
                                                    $sel = ($item['brandId'] == $brand['brandId']) ? 'selected' : '';
                                                    echo '<option value="' . $brand['brandId'] . '" ' . $sel . '>' . $brand['brandName'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="barnd"> 
                                        <div class="selectoption">
                                            <label for="">Sell Price</label>
                                            <input type="number" name="sellPrice" id="" step="0.01" placeholder="Sell price" value="<?php echo $item['sellPrice']; ?>" required>
                                        </div>
                                        <div class="selectoption">
                                            <label for="">Promotional discount (%)</label>
                                            <input type="number" name="promotional" id="" step="0.01" placeholder="Promotional discount" value="<?php echo $item['promotional']; ?>"  min="0" max="100">
                                        </div>
                                    </div>
                                    <div class="barnd">
                                        <div class="selectoption">
                                            <label for="">Extra Shipping Fee</label>
                                            <input type="number" name="extra_shipfee" id="" step="0.01" placeholder="Extra Shipping Fee" value="<?php echo $item['extra_shipfee']; ?>"  min="0" max="100">
                                        </div>
                                        <div class="selectoption">
                                            <label for="">Commition (%)</label>
                                            <input type="number" name="commtion" id="" step="0.01" placeholder="Commition (%)" value="<?php echo $item['commtion']; ?>">
                                        </div>
                                    </div>

                                    <div class="quantity">
                                        <label>Minimum Quantity</label>
                                        <input type="number" name="minQuantity" required value="<?php echo $item['minQuantity']; ?>">
                                    </div>

                                    <textarea placeholder="Product Description" name="itmDesc" required><?php echo htmlspecialchars($item['itmDesc']); ?></textarea>
                                </div>
                            </div>

                            <div class="discount_info">
                                <div class="toggle-container">
                                    <span id="statusText">Get Discount:</span>
                                    <label class="switch">
                                        <input type="checkbox" id="discountToggle" name="getDiscount" <?php echo $item['getDiscount'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <div class="discount-section" id="discountSection" style="display:<?php echo $item['getDiscount'] ? 'block' : 'none'; ?>;">
                                    <div class="frmdis">
                                        <input type="number" id="qty" placeholder="Quantity" min="1">
                                        <input type="number" id="discount" placeholder="Discount %" min="0" step="0.1">
                                        <button id="addDiscount" type="button">Add</button>
                                    </div>
                                    <table id="discountTable">
                                        <thead>
                                            <tr>
                                                <th>Quantity</th>
                                                <th>Discount (%)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($discounts as $d) {
                                                echo '<tr>
                                                    <td contenteditable="true">' . $d['quatity'] . '</td>
                                                    <td contenteditable="true">' . $d['precent'] . '</td>
                                                    <td><button type="button" class="deleteRow">Delete</button></td>
                                                </tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="discountData" id="discountData" value='<?php echo json_encode($discounts); ?>'>
                                </div>
                            </div>

                            <div class="buttons btnsitems">
                                <button type="submit" class="btn btn-primary" name="btneditItem">Update Item</button>
                                <button type="button" class="btn btn-ghost" onclick="window.history.back();">Cancel</button>
                            </div>
                        </form>

                        <script>
                        function previewImage(event) {
                            const reader = new FileReader();
                            reader.onload = function(){
                                const output = document.getElementById('thumbnail');
                                output.src = reader.result;
                            };
                            reader.readAsDataURL(event.target.files[0]);
                        }
                        </script>

                        <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const discountToggle = document.getElementById("discountToggle");
                            const discountSection = document.getElementById("discountSection");
                            const addBtn = document.getElementById("addDiscount");
                            const discountTable = document.getElementById("discountTable").querySelector("tbody");
                            const discountDataInput = document.getElementById("discountData");

                            // Show/Hide discount section
                            discountToggle.addEventListener("change", () => {
                                discountSection.style.display = discountToggle.checked ? "block" : "none";
                            });

                            // Add discount row
                            addBtn.addEventListener("click", () => {
                                const qty = document.getElementById("qty").value;
                                const discount = document.getElementById("discount").value;

                                if (qty && discount) {
                                    const row = document.createElement("tr");
                                    row.innerHTML = `
                                        <td contenteditable="true">${qty}</td>
                                        <td contenteditable="true">${discount}</td>
                                        <td><button type="button" class="deleteRow">Delete</button></td>
                                    `;
                                    discountTable.appendChild(row);
                                    updateHiddenInput();
                                    document.getElementById("qty").value = "";
                                    document.getElementById("discount").value = "";
                                } else {
                                    alert("Please enter both Quantity and Discount.");
                                }
                            });

                            // Delete row
                            discountTable.addEventListener("click", (e) => {
                                if (e.target.classList.contains("deleteRow")) {
                                    e.target.closest("tr").remove();
                                    updateHiddenInput();
                                }
                            });

                            // Update hidden input whenever a cell is edited
                            discountTable.addEventListener("input", () => {
                                updateHiddenInput();
                            });

                            function updateHiddenInput() {
                                const rows = discountTable.querySelectorAll("tr");
                                const discounts = [];
                                rows.forEach(r => {
                                    const qty = r.cells[0]?.textContent.trim();
                                    const discount = r.cells[1]?.textContent.trim();
                                    if (qty && discount) {
                                        discounts.push({ qty, discount });
                                    }
                                });
                                discountDataInput.value = JSON.stringify(discounts);
                            }
                        });
                        </script>
                        <?php
                        break;

                    case 'deleteitm':
                        $itemId = isset($_GET['itmId'])?$_GET['itmId']:0;
                        $checkitm = checkItem('itmId','tblitems',$itemId);
                        if($checkitm == 0){
                            echo '<script> location.href="manageproducts.php"</script>';
                        }else{
                            $sql =$con->prepare('SELECT catId FROM  tblitems WHERE itmId  = ?');
                            $sql->execute([$itemId]);
                            $resultCat = $sql->fetch();
                            $cat = $resultCat['catId'];

                            $stat = $con->prepare('UPDATE  tblitems SET itmActive = 0 WHERE itmId = ?');
                            $stat ->execute([$itemId]);
                            echo '<script> location.href="manageproducts.php?do=viewcat&catid='.$cat.'"</script>';

                        }
                        break;

                    default:
                        echo "<script>location.href='manageproducts.php'</script>";
                        break;
                }
            ?>
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/manageproducts.js"></script>
</body>