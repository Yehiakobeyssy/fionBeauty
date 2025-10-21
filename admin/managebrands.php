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

    $do= isset($_GET['do'])?$_GET['do']:'manage';
    
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/managebrands.css">
</head> 
<body> 
    <?php include 'include/adminheader.php' ?>
    <main>
        <?php include 'include/adminaside.php'?>
        <div class="container_info">
            <div class="addnewcustomer">
                <a href="managebrands.php?do=add">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                        <path d="M5.66667 12.4167C5.66667 12.8769 6.03976 13.25 6.5 13.25C6.96024 13.25 7.33333 12.8769 7.33333 12.4167V7.83333H11.9167C12.3769 7.83333 12.75 7.46024 12.75 7C12.75 6.53976 12.3769 6.16667 11.9167 6.16667H7.33333V1.58333C7.33333 1.1231 6.96024 0.75 6.5 0.75C6.03976 0.75 5.66667 1.1231 5.66667 1.58333V6.16667H1.08333C0.623096 6.16667 0.25 6.53976 0.25 7C0.25 7.46024 0.623096 7.83333 1.08333 7.83333H5.66667V12.4167Z" fill="#FAFAFA"/>
                    </svg>
                    Add Brand
                </a>
            </div>
            <?php
                if($do == 'manage'){?>
                    <div class="mainstatistic card">
                        <div class="title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M4 6C4 5.44772 4.44772 5 5 5H13L19 11L13 17H5C4.44772 17 4 16.5523 4 16V6Z" stroke="#009245" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="8.5" cy="10.5" r="1.5" fill="#009245"/>
                            </svg>
                            
                        </div>
                        <?php
                        // 🟢 Count all customers
                        $stmt = $con->prepare("SELECT COUNT(*) FROM  tblbrand");
                        $stmt->execute();
                        $totalbrand = $stmt->fetchColumn();

                        // 🟢 Count active customers
                        $stmt = $con->prepare("SELECT COUNT(*) FROM tblbrand WHERE brandActive = 1");
                        $stmt->execute();
                        $activebrand = $stmt->fetchColumn();

                            // 🟢 Count inactive customers
                        $stmt = $con->prepare("SELECT COUNT(*) FROM tblbrand WHERE brandActive = 0");
                        $stmt->execute();
                        $inactivebrand = $stmt->fetchColumn();
                        ?>

                        <div class="card_info_client">
                            <div class="part">
                                <label>All Brands</label>
                                <span><?= $totalbrand ?></span>
                            </div>
                            <div class="part">
                                <label>Active Brands</label>
                                <span><?= $activebrand ?></span>
                            </div>
                            <div class="part">
                                <label>Inactive Brands</label>
                                <span><?= $inactivebrand ?></span>
                            </div>
                            
                        </div>
                    </div>
                    <div class="sectiontablecontaint">
                        <div class="filter-section">
                            <div class="filter-item">
                                <label for="search">Search</label>
                                <input type="text" id="search" placeholder="Search Brand ....">
                            </div>

                            <div class="filter-item">
                                <label for="date">Date</label>
                                <input type="date" id="date">
                            </div>

                            <div class="filter-item">
                                <label for="status">Active</label>
                                <select id="status">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="tblbrands">
                            <table>
                                <thead>
                                    <th>Logo</th>
                                    <th>Brand Name</th>
                                    <th>Brand Since</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </thead>
                                <tbody id="tblresult">

                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php
                }elseif($do == 'add'){?>
                    <?php
                        if(isset($_POST['btnaddBrand'])){
                            $filename = ''; // default if no image uploaded
                            if (!empty($_FILES['brandIcon']['name'])) {
                                $files = $_FILES['brandIcon'];
                                $ext = strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));
                                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                if (in_array($ext, $allowed)) {
                                    $filename = time() . rand(1000, 9999) . "." . $ext;
                                    $destination = "../images/brands/" . $filename;
                                    if (!move_uploaded_file($files['tmp_name'], $destination)) {
                                        echo "<script>alert('Error uploading image');</script>";
                                        $filename = '';
                                    }
                                } else {
                                    echo "<script>alert('Invalid file type');</script>";
                                }
                            }
                            $brandName = $_POST['brandName'];
                            $brandActive = 1;

                            $sql=$con->prepare('INSERT INTO tblbrand (brandName,brandIcon,brandActive) VALUES (?,?,?)');
                            $sql->execute([$brandName,$filename,$brandActive]);

                            echo "<div class='susseccs_massage'><h5>New Brand Successfully added</h5></div>";

                        }
                    ?>
                    <div class="new_Brand">
                        <div class="title_form">
                            <h3>Add New Brand</h3>
                        </div>
                        <form method="POST" enctype="multipart/form-data" id="slideForm">
                            <div class="thumbnail-section">
                                <img id="thumbnail" src="https://via.placeholder.com/400x200?text=Preview" alt=""><br>
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
                                <input id="file-upload" name="brandIcon" type="file" accept="image/*" onchange="previewImage(event)" style="display:none;">
                            </div>
                            <input type="text" name="brandName" id="" placeholder="Brand Name" required>
                            <div class="btncontrol">
                                <button type="reset" class="btn btn-outboder">Cancel</button>
                                <button type="submit" class="btn btn-inboder" name="btnaddBrand">Add Brand</button>
                            </div>
                        </form>
                    </div>
                    <script>
                        function previewImage(e){
                            const reader = new FileReader();
                            reader.onload = function(){
                                document.getElementById('thumbnail').src = reader.result;
                            };
                            reader.readAsDataURL(e.target.files[0]);
                        }
                    </script>
                <?php
                }elseif($do=='edid'){?>
                    <?php
                        // Assuming you have a connection $con and a valid brandId from GET or POST
                        if(isset($_GET['brandId'])){
                            $brandId = $_GET['brandId'];
                            $stmt = $con->prepare("SELECT * FROM tblbrand WHERE brandId = ?");
                            $stmt->execute([$brandId]);
                            $brand = $stmt->fetch(PDO::FETCH_ASSOC);

                            if(!$brand){
                                echo "<script>alert('Brand not found');window.location='managebrands.php';</script>";
                                exit;
                            }
                        } else {
                            echo "<script>alert('No Brand ID provided');window.location='managebrands.php';</script>";
                            exit;
                        }

                        // Update logic
                        if(isset($_POST['btnEditBrand'])){
                            $brandName = $_POST['brandName'];
                            $filename = $brand['brandIcon']; // keep old icon if no new upload

                            // Handle new image upload
                            if (!empty($_FILES['brandIcon']['name'])) {
                                $files = $_FILES['brandIcon'];
                                $ext = strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));
                                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                                if (in_array($ext, $allowed)) {
                                    // delete old file if exists
                                    if (!empty($brand['brandIcon']) && file_exists("../images/brands/" . $brand['brandIcon'])) {
                                        unlink("../images/brands/" . $brand['brandIcon']);
                                    }

                                    // upload new image
                                    $filename = time() . rand(1000, 9999) . "." . $ext;
                                    $destination = "../images/brands/" . $filename;

                                    if (!move_uploaded_file($files['tmp_name'], $destination)) {
                                        echo "<script>alert('Error uploading new image');</script>";
                                        $filename = $brand['brandIcon'];
                                    }
                                } else {
                                    echo "<script>alert('Invalid file type');</script>";
                                }
                            }

                            // Update brand data
                            $sql = $con->prepare("UPDATE tblbrand SET brandName = ?, brandIcon = ? WHERE brandId = ?");
                            $sql->execute([$brandName, $filename, $brandId]);

                            echo "<div class='susseccs_massage'><h5>Brand Successfully Updated</h5></div>";

                            // Refresh data to show the new image immediately
                            $stmt = $con->prepare("SELECT * FROM tblbrand WHERE brandId = ?");
                            $stmt->execute([$brandId]);
                            $brand = $stmt->fetch(PDO::FETCH_ASSOC);
                        }
                    ?>

                    <div class="new_Brand">
                        <div class="title_form">
                            <h3>Edit Brand</h3>
                        </div>
                        <form method="POST" enctype="multipart/form-data" id="editBrandForm">
                            <div class="thumbnail-section">
                                <img id="thumbnail" 
                                    src="<?php echo !empty($brand['brandIcon']) ? '../images/brands/'.$brand['brandIcon'] : 'https://via.placeholder.com/400x200?text=Preview'; ?>" 
                                    alt="Brand Icon" 
                                    style="max-width:400px;max-height:200px;"><br>
                                <label for="file-upload" class="choose-button">
                                    Change Image 
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
                                <input id="file-upload" name="brandIcon" type="file" accept="image/*" onchange="previewImage(event)" style="display:none;">
                            </div>
                            <input type="text" name="brandName" value="<?php echo htmlspecialchars($brand['brandName']); ?>" placeholder="Brand Name" required>
                            <div class="btncontrol">
                                <a href="brands.php" class="btn btn-outboder">Cancel</a>
                                <button type="submit" class="btn btn-inboder" name="btnEditBrand">Update Brand</button>
                            </div>
                        </form>
                    </div>

                    <script>
                        function previewImage(e){
                            const reader = new FileReader();
                            reader.onload = function(){
                                document.getElementById('thumbnail').src = reader.result;
                            };
                            reader.readAsDataURL(e.target.files[0]);
                        }
                    </script>

                <?php
                }elseif($do=='delete'){
                    
                    if(isset($_GET['brandId'])){
                        $brandId = $_GET['brandId'];
                        $stmt = $con->prepare("SELECT * FROM tblbrand WHERE brandId = ?");
                        $stmt->execute([$brandId]);
                        $brand = $stmt->fetch(PDO::FETCH_ASSOC);

                        if(!$brand){
                            echo "<script>alert('Brand not found');window.location='managebrands.php';</script>";
                            exit;
                        }
                    } else {
                        echo "<script>alert('No Brand ID provided');window.location='managebrands.php';</script>";
                        exit;
                    }

                    // Handle POST (confirmation)
                    if(isset($_POST['confirmAction'])){
                        $newStatus = $brand['brandActive'] == 1 ? 0 : 1;
                        $sql = $con->prepare("UPDATE tblbrand SET brandActive = ? WHERE brandId = ?");
                        $sql->execute([$newStatus, $brandId]);

                        $msg = $newStatus == 1 ? "Brand Activated Successfully" : "Brand Deactivated Successfully";
                        echo "<div class='susseccs_massage'><h5>$msg</h5></div>";
                        echo "<script>setTimeout(()=>{window.location='managebrands.php';},1500);</script>";
                        exit;
                    }

                    // Texts depending on status
                    $actionText = $brand['brandActive'] == 1 ? "Deactivate" : "Activate";
                    $questionText = $brand['brandActive'] == 1 
                        ? "Are you sure you want to deactivate this brand?" 
                        : "Are you sure you want to activate this brand?";
                    $buttonClass = $brand['brandActive'] == 1 ? "btn-danger" : "btn-success";
                    ?>

                    <div class="new_Brand">
                        <div class="title_form">
                            <h3><?php echo $actionText; ?> Brand</h3>
                        </div>

                        <form method="POST" class="confirm_form">
                            <div class="brand_info" style="text-align:center;">
                                <img src="<?php echo !empty($brand['brandIcon']) ? '../images/brands/'.$brand['brandIcon'] : 'https://via.placeholder.com/200x100?text=No+Image'; ?>" 
                                    alt="Brand Icon" 
                                    style="width:200px;height:auto;border-radius:10px;margin-bottom:15px;">
                                <h4><?php echo htmlspecialchars($brand['brandName']); ?></h4>
                                <p><?php echo $questionText; ?></p>
                            </div>

                            <div class="btncontrol" style="display:flex;justify-content:center;gap:10px;">
                                <a href="managebrands.php" class="btn btn-outboder">Cancel</a>
                                <button type="submit" name="confirmAction" class="btn <?php echo $buttonClass; ?>">
                                    Yes, <?php echo $actionText; ?>
                                </button>
                            </div>
                        </form>
                    </div>


                <?php
                }else{
                    echo '<script>location.href="managebrands.php"</script>';
                }
            ?>
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/managebrands.js"></script>
</body>