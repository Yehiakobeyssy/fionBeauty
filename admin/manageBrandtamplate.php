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

    $brandID = isset($_GET['bid'])?$_GET['bid']:0;

    $checkbarnd = checkItem('brandId' ,'tblbrand',  $brandID);
    if($checkbarnd == 0){
        header("Location: managebrands.php");
        exit();
    }
    $stmt = $con->prepare("SELECT * FROM brandPage WHERE BrandID = ?");
    $stmt->execute([$brandID]);
    $brandPage = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql=$con->prepare('SELECT * FROM tblbrand WHERE brandId = ?');
    $sql->execute([$brandID]);
    $brandinfo= $sql->fetch(PDO::FETCH_ASSOC);
?> 
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/manageBrandtamplate.css">
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
            <label for=""><strong>Drag & Drop Images</strong> </label>
            <div class="section section1">
                <div class="image-wrapper" id="section1ImageWrapper" data-section="mainpic">
                    <?php
                        $mainPic = $brandPage['mainpic'] ?? 'default.jpg';
                        $brandName =$brandinfo['brandName']; // fetch brand name from DB
                    ?>
                    <img id="section1Image" src="../images/brands/tamplatepage/<?php echo $mainPic ?>" alt="Brand Image">
                    <label for="" class="brand-name-btn"><?php echo $brandName ?></label>
                </div>
            </div>
            <div class="section section2">
                <div class="image-wrapper" id="section2ImageWrapper" data-section="subpic">
                    <?php
                        $subPic = $brandPage['subpic'] ?? 'default_subpic.jpg';
                        $textslogan = $brandPage['textslogan'] ?? 'Your slogan here';
                        $subtitle = $brandinfo['subtitle'];
                    ?>
                    <img id="section2Image" src="../images/brands/tamplatepage/<?php echo $subPic ?>" alt="Sub Image">
                    <div class="left-text" contenteditable="true" id="section2Slogan">
                        <?php echo $textslogan ?>
                    </div>
                    <!-- Left-side subtitle (not editable) -->
                    <div class="right-text">
                        <?php echo $subtitle ?>
                    </div>
                </div>
            </div>
            <div class="section section3">
                <div class="image-wrapper" id="section3ImageWrapper" data-section="reviewpic">
                    <?php
                        $reviewPic = $brandPage['reviewpic'] ?? 'default_review.jpg';
                        $textslogan1 = $brandPage['textslogan1'] ?? 'Your review slogan here';

                        // Get best 3 reviews (example, adjust table name)
                        
                    ?>

                    <!-- Background image -->
                    <img id="section3Image" src="../images/brands/tamplatepage/<?php echo $reviewPic ?>" alt="Review Image">

                    <!-- Left side slogan -->
                    <div class="section3-left">
                        <div class="slogan1" contenteditable="true" id="section3Slogan1">
                            <?php echo $textslogan1 ?>
                        </div>
                    </div>

                    <!-- Right side reviews -->
                    <div class="section3-right">
                        
                            <div class="review-card">
                                <div class="review-email">Client Name</div>
                                <div class="review-text">Review</div>
                            </div>
                            <div class="review-card">
                                <div class="review-email">Client Name</div>
                                <div class="review-text">Review</div>
                            </div>
                            <div class="review-card">
                                <div class="review-email">Client Name</div>
                                <div class="review-text">Review</div>
                            </div>
                        
                    </div>
                </div>
            </div>
            <div class="section section4">
                <div class="image-wrapper" id="section4ImageWrapper" data-section="statisticpic">
                    <?php
                        $statisticPic = $brandPage['statisticpic'] ?? 'default_statistic.jpg';
                    ?>

                    <img id="section4Image" src="../images/brands/tamplatepage/<?php echo $statisticPic ?>" alt="Statistics Image">

                    <div class="section4-right">
                        <div class="stat-card">
                            <div class="stat-title">Number of Items</div>
                            <div class="stat-value"><?php echo rand(100, 999) ?></div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-title">Number of Sold</div>
                            <div class="stat-value"><?php echo rand(200, 1500) ?></div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-title">Number of Clients</div>
                            <div class="stat-value"><?php echo rand(50, 800) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                $brandName = trim($brandinfo['brandName'] ?? '');
                $word1 = '';
                $word2 = '';

                $parts = explode(' ', $brandName);

                if (count($parts) >= 2) {
                    // Brand has two words
                    $word1 = $parts[0];
                    $word2 = $parts[1];
                } else {
                    // Only one word => split into 2 halves
                    $name = $parts[0];
                    $mid = ceil(strlen($name) / 2);

                    $word1 = substr($name, 0, $mid);
                    $word2 = substr($name, $mid);
                }
            ?>
            <div class="section section5">
                <div class="section5-wrapper image-wrapper" id="section5ImageWrapper" data-section="smallpic">

                    <?php
                        $smallPic = $brandPage['smallpic'] ?? 'default_small.jpg';
                        $textslogan2 = $brandPage['textslogan2'] ?? 'Write something here...';
                    ?>

                    <img id="section5Image" 
                        src="../images/brands/tamplatepage/<?php echo $smallPic ?>" 
                        alt="Small Pic" 
                        class="section5-img">

                    <div class="brand-split brand-split-top"><?php echo $word1 ?></div>
                    <div class="brand-split brand-split-bottom"><?php echo $word2 ?></div>

                </div>

                <div class="section5-slogan" contenteditable="true" id="section5Slogan">
                    <?php echo $textslogan2 ?>
                </div>
            </div>
            <div class="section section6">
                <div class="section6-inner">
                    <!-- LEFT column: two stacked -->
                    <div class="col col-left">
                        <div class="image-wrapper" data-section="pic1" id="pic1Wrapper">
                            <img id="pic1Image" src="../images/brands/tamplatepage/<?php echo $brandPage['pic1'] ?? 'default.jpg' ?>" alt="pic1">
                        </div>
                        <div class="image-wrapper" data-section="pic2" id="pic2Wrapper">
                            <img id="pic2Image" src="../images/brands/tamplatepage/<?php echo $brandPage['pic2'] ?? 'default.jpg' ?>" alt="pic2">
                        </div>
                    </div>

                    <!-- CENTER column: one big image -->
                    <div class="col col-center">
                        <div class="image-wrapper center-wrapper" data-section="pic3" id="pic3Wrapper">
                            <img id="pic3Image" src="../images/brands/tamplatepage/<?php echo $brandPage['pic3'] ?? 'default.jpg' ?>" alt="pic3">
                        </div>
                    </div>

                    <!-- RIGHT column: two stacked -->
                    <div class="col col-right">
                        <div class="image-wrapper" data-section="pic4" id="pic4Wrapper">
                            <img id="pic4Image" src="../images/brands/tamplatepage/<?php echo $brandPage['pic4'] ?? 'default.jpg' ?>" alt="pic4">
                        </div>
                        <div class="image-wrapper" data-section="pic5" id="pic5Wrapper">
                            <img id="pic5Image" src="../images/brands/tamplatepage/<?php echo $brandPage['pic5'] ?? 'default.jpg' ?>" alt="pic5">
                        </div>
                    </div>
                </div>
            </div>
            <div class="section section7">
                <div class="info-text" contenteditable="true" id="section7Info">
                    <?php echo $brandPage['info'] ?? 'Enter info here'; ?>
                </div>
            </div>
            <div class="section section8">
                <div class="save-all-wrapper" style="text-align:center; margin:50px 0;">
                    <button id="saveAllBtn" class="save-btn">Update & Save All Sections</button>
                </div>
            </div>

        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/dashboard.js"></script>
    <script>
        let pageData = {}; // store all sections before final save

        document.querySelectorAll('.image-wrapper').forEach(wrapper => {
            const sectionKey = wrapper.dataset.section;
            const img = wrapper.querySelector('img');

            wrapper.addEventListener('dragover', e => {
                e.preventDefault();
                wrapper.style.borderColor = 'green';
            });

            wrapper.addEventListener('dragleave', e => {
                e.preventDefault();
                wrapper.style.borderColor = '#ccc';
            });

            wrapper.addEventListener('drop', e => {
                e.preventDefault();
                wrapper.style.borderColor = '#ccc';

                const file = e.dataTransfer.files[0];
                if(file && file.type.startsWith('image/')){
                    const reader = new FileReader();
                    reader.onload = function(event){
                        img.src = event.target.result;
                    }
                    reader.readAsDataURL(file);

                    // Save file in JS (we’ll send all at once later)
                    pageData[sectionKey] = file;
                } else {
                    alert('Please drop a valid image file.');
                }
            });
        });

        document.getElementById('saveAllBtn').addEventListener('click', function() {
            const formData = new FormData();
            formData.append('brandID', '<?php echo $brandID ?>');

            // Append all image files
            for(const key in pageData){
                if(pageData[key] instanceof File){ // only files
                    formData.append(key, pageData[key]);
                }
            }

            // Append editable texts manually
            formData.append('textslogan', document.getElementById('section2Slogan')?.innerText || '');
            formData.append('textslogan1', document.getElementById('section3Slogan1')?.innerText || '');
            formData.append('textslogan2', document.getElementById('section5Slogan2')?.innerText || '');
            formData.append('info', document.getElementById('section7Info')?.innerText || '');

            fetch('ajaxadmin/saveAllSections.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    alert('All sections updated successfully!');
                    window.location.reload();
                } else {
                    alert('Error saving sections!');
                }
            })
            .catch(err => console.error(err));
        });


    </script>
</body>