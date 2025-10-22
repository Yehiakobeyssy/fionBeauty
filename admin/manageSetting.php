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

    $do=isset($_GET['do'])?$_GET['do']:'main'
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/manageSetting.css">
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
            <div class="title">
                <h4>Manage Setting</h4>
            </div>
            <div class="container_setting">
                <?php include 'include/settingaside.php'?>
                <div class="setting_edit">
                    <?php
                        switch ($do) {
                            case 'main':
                                // 🟦 Main Setting section
                                
                                // Fetch current settings
                                $stmt = $con->prepare("SELECT * FROM tblsetting WHERE seetingID  = 1");
                                $stmt->execute();
                                $setting = $stmt->fetch(PDO::FETCH_ASSOC);

                                // Handle update
                                if (isset($_POST['updateSetting'])) {
                                    $companyPhone  = $_POST['companyPhone'];
                                    $companyEmail  = $_POST['companyEmail'];
                                    $companyAdd    = $_POST['companyAdd'];
                                    $noteHeader    = $_POST['noteHeader'];
                                    $daysofnewitem = $_POST['daysofnewitem'];

                                    $update = $con->prepare("
                                        UPDATE tblsetting 
                                        SET companyPhone = ?, companyEmail = ?, companyAdd = ?, noteHeader = ?, daysofnewitem = ?
                                        WHERE seetingID  = 1
                                    ");
                                    $update->execute([$companyPhone, $companyEmail, $companyAdd, $noteHeader, $daysofnewitem]);

                                    echo '<div class="card" style="color:var(--color-success);font-weight:600;">✅ Settings updated successfully!</div>';
                                    // Refresh data
                                    $stmt = $con->prepare("SELECT * FROM tblsetting WHERE seetingID  = 1");
                                    $stmt->execute();
                                    $setting = $stmt->fetch(PDO::FETCH_ASSOC);
                                }
                                ?>
                                <div class="card" style="max-width:700px;margin:auto;">
                                    <h2 class="h2 mb-3">🛠️ Main Settings</h2>

                                    <form method="POST">
                                        <div class="mb-3">
                                            <label>📞 Company Phone</label>
                                            <input type="text" name="companyPhone" value="<?= htmlspecialchars($setting['companyPhone']) ?>" class="input">
                                            <small class="hint">Displayed in footer and contact page.</small>
                                        </div>

                                        <div class="mb-3">
                                            <label>📧 Company Email</label>
                                            <input type="email" name="companyEmail" value="<?= htmlspecialchars($setting['companyEmail']) ?>" class="input">
                                            <small class="hint">Used for customer messages and notifications.</small>
                                        </div>

                                        <div class="mb-3">
                                            <label>🏢 Company Address</label>
                                            <input type="text" name="companyAdd" value="<?= htmlspecialchars($setting['companyAdd']) ?>" class="input">
                                            <small class="hint">Appears in invoices and about page.</small>
                                        </div>

                                        <div class="mb-3">
                                            <label>📝 Note Header</label>
                                            <input type="text" name="noteHeader" value="<?= htmlspecialchars($setting['noteHeader']) ?>" class="input">
                                            <small class="hint">Shown at the top of the homepage.</small>
                                        </div>

                                        <div class="mb-3">
                                            <label>🕒 Days for New Items</label>
                                            <input type="number" name="daysofnewitem" value="<?= htmlspecialchars($setting['daysofnewitem']) ?>" class="input">
                                            <small class="hint">Items added within these days will be marked as “New”.</small>
                                        </div>

                                        <div class="text-center mt-3">
                                            <button type="submit" name="updateSetting" class="btn btn-primary">💾 Update</button>
                                        </div>
                                    </form>
                                </div>
                                <?php
                                break;



                            case 'slide_home':
                                
                                $pageSide = 'index.php'; 
                                $uploadDir = '../images/slide/';
                                $adminId = $admin_id;

                                // 🟢 Insert new slide
                                if (isset($_POST['addSlide'])) {
                                    $slideHref = '';
                                    $slideActive = 1;

                                    if (!empty($_FILES['slideScr']['name'])) {
                                        $filename = time() . basename($_FILES['slideScr']['name']);
                                        $targetPath = $uploadDir . $filename;
                                        move_uploaded_file($_FILES['slideScr']['tmp_name'], $targetPath);

                                        $stmt = $con->prepare("INSERT INTO tblslideshow (slideScr, slideHref, adminId, slideActive, pageSide) VALUES (?, ?, ?, ?, ?)");
                                        $stmt->execute([$filename, $slideHref, $adminId, $slideActive, $pageSide]);
                                        echo '<div class="card" style="color:var(--color-success);font-weight:600;">✅ Slide added successfully!</div>';
                                    } else {
                                        echo '<div class="card" style="color:var(--color-danger);font-weight:600;">⚠️ Please choose an image!</div>';
                                    }
                                }

                                // 🟢 Fetch active slides
                                $stmt = $con->prepare("SELECT * FROM tblslideshow WHERE pageSide=? AND slideActive=1 ORDER BY slideID DESC");
                                $stmt->execute([$pageSide]);
                                $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <div class="card" style="max-width:700px;margin:auto;">
                                    <h2 class="h2 mb-3">🏠 Home Page Slides</h2>
                                    <form method="POST" enctype="multipart/form-data" id="slideForm">
                                        <div class="thumbnail-section">
                                            <img id="thumbnail" src="https://via.placeholder.com/400x200?text=Preview" alt="">
                                            <p>Add Slide Image</p>
                                            <label for="file-upload" class="choose-button">
                                                Choose Image 
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path d="M4.16667 5.83301H5C5.44203 5.83301 5.86595 5.65741 6.17851 5.34485C6.49107 5.03229 6.66667 4.60837 6.66667 4.16634C6.66667 3.94533 6.75446 3.73337 6.91074 3.57709C7.06702 3.42081 7.27899 3.33301 7.5 3.33301H12.5C12.721 3.33301 12.933 3.42081 13.0893 3.57709C13.2455 3.73337 13.3333 3.94533 13.3333 4.16634C13.3333 4.60837 13.5089 5.03229 13.8215 5.34485C14.134 5.65741 14.558 5.83301 15 5.83301H15.8333C16.2754 5.83301 16.6993 6.0086 17.0118 6.32116C17.3244 6.63372 17.5 7.05765 17.5 7.49967V14.9997C17.5 15.4417 17.3244 15.8656 17.0118 16.1782C16.6993 16.4907 16.2754 16.6663 15.8333 16.6663H4.16667C3.72464 16.6663 3.30072 16.4907 2.98816 16.1782C2.67559 15.8656 2.5 15.4417 2.5 14.9997V7.49967C2.5 7.05765 2.67559 6.63372 2.98816 6.32116C3.30072 6.0086 3.72464 5.83301 4.16667 5.83301" stroke="#009245" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </label>
                                            <input id="file-upload" name="slideScr" type="file" accept="image/*" onchange="previewImage(event)" style="display:none;">
                                        </div>

                                        

                                        <div class="text-center mt-3">
                                            <button type="submit" name="addSlide" class="btn btn-primary">➕ Add Slide</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- 🖼️ Slide Viewer -->
                                <div class="card mt-3" style="max-width:700px;margin:auto;">
                                    <h2 class="h2 mb-3">📽️ Slide Preview</h2>
                                    <div id="slideViewer" class="slide-viewer text-center">
                                        <?php if (count($slides) > 0): ?>
                                            <img id="currentSlide" src="<?= $uploadDir . htmlspecialchars($slides[0]['slideScr']) ?>" 
                                                data-index="0" width="100%" style="max-height:300px;object-fit:cover;border-radius:8px;">
                                            <p id="slideInfo" class="mt-2"><?= htmlspecialchars($slides[0]['slideHref'] ?: 'No link') ?></p>
                                        <?php else: ?>
                                            <p>No slides yet.</p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="text-center mt-3">
                                        <button id="prevSlide" class="btn btn-ghost">⬅️ Back</button>
                                        <button id="nextSlide" class="btn btn-ghost">Next ➡️</button>
                                        <button id="deleteSlide" class="btn btn-cancel">🗑️ Delete</button>
                                    </div>
                                </div>

                                <script>
                                const slides = <?= json_encode($slides) ?>;
                                let currentIndex = 0;

                                // Preview upload
                                function previewImage(e){
                                    const reader = new FileReader();
                                    reader.onload = function(){
                                        document.getElementById('thumbnail').src = reader.result;
                                    };
                                    reader.readAsDataURL(e.target.files[0]);
                                }

                                // Navigation
                                $('#nextSlide').on('click', function(){
                                    if(slides.length === 0) return;
                                    currentIndex = (currentIndex + 1) % slides.length;
                                    updateSlide();
                                });
                                $('#prevSlide').on('click', function(){
                                    if(slides.length === 0) return;
                                    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                                    updateSlide();
                                });
                                function updateSlide(){
                                    $('#currentSlide').attr('src', '../images/slide/' + slides[currentIndex].slideScr);
                                    $('#slideInfo').text(slides[currentIndex].slideHref || 'No link');
                                }

                                // Delete (AJAX → ajaxadmin/)
                                $('#deleteSlide').on('click', function(){
                                    if(slides.length === 0) return;
                                    const slideID = slides[currentIndex].slideID;
                                    if(confirm('Deactivate this slide?')){
                                        $.post('ajaxadmin/deactivate_slide.php', { id: slideID }, function(res){
                                            alert(res.message);
                                            location.reload();
                                        }, 'json');
                                    }
                                });
                                </script>

                                <?php

                                    
                                break;

                            case 'slide_category':
                                // 🟨 Slide Show → Category Page
                                $pageSide = 'category.php'; 
                                $uploadDir = '../images/slide/';
                                $adminId = $admin_id;

                                // 🟢 Insert new slide
                                if (isset($_POST['addSlide'])) {
                                    $slideHref = '';
                                    $slideActive = 1;

                                    if (!empty($_FILES['slideScr']['name'])) {
                                        $filename = time() . basename($_FILES['slideScr']['name']);
                                        $targetPath = $uploadDir . $filename;
                                        move_uploaded_file($_FILES['slideScr']['tmp_name'], $targetPath);

                                        $stmt = $con->prepare("INSERT INTO tblslideshow (slideScr, slideHref, adminId, slideActive, pageSide) VALUES (?, ?, ?, ?, ?)");
                                        $stmt->execute([$filename, $slideHref, $adminId, $slideActive, $pageSide]);
                                        echo '<div class="card" style="color:var(--color-success);font-weight:600;">✅ Slide added successfully!</div>';
                                    } else {
                                        echo '<div class="card" style="color:var(--color-danger);font-weight:600;">⚠️ Please choose an image!</div>';
                                    }
                                }

                                // 🟢 Fetch active slides
                                $stmt = $con->prepare("SELECT * FROM tblslideshow WHERE pageSide=? AND slideActive=1 ORDER BY slideID DESC");
                                $stmt->execute([$pageSide]);
                                $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <div class="card" style="max-width:700px;margin:auto;">
                                    <h2 class="h2 mb-3">🏠 Category Page Slides</h2>
                                    <form method="POST" enctype="multipart/form-data" id="slideForm">
                                        <div class="thumbnail-section">
                                            <img id="thumbnail" src="https://via.placeholder.com/400x200?text=Preview" alt="">
                                            <p>Add Slide Image</p>
                                            <label for="file-upload" class="choose-button">
                                                Choose Image 
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path d="M4.16667 5.83301H5C5.44203 5.83301 5.86595 5.65741 6.17851 5.34485C6.49107 5.03229 6.66667 4.60837 6.66667 4.16634C6.66667 3.94533 6.75446 3.73337 6.91074 3.57709C7.06702 3.42081 7.27899 3.33301 7.5 3.33301H12.5C12.721 3.33301 12.933 3.42081 13.0893 3.57709C13.2455 3.73337 13.3333 3.94533 13.3333 4.16634C13.3333 4.60837 13.5089 5.03229 13.8215 5.34485C14.134 5.65741 14.558 5.83301 15 5.83301H15.8333C16.2754 5.83301 16.6993 6.0086 17.0118 6.32116C17.3244 6.63372 17.5 7.05765 17.5 7.49967V14.9997C17.5 15.4417 17.3244 15.8656 17.0118 16.1782C16.6993 16.4907 16.2754 16.6663 15.8333 16.6663H4.16667C3.72464 16.6663 3.30072 16.4907 2.98816 16.1782C2.67559 15.8656 2.5 15.4417 2.5 14.9997V7.49967C2.5 7.05765 2.67559 6.63372 2.98816 6.32116C3.30072 6.0086 3.72464 5.83301 4.16667 5.83301" stroke="#009245" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </label>
                                            <input id="file-upload" name="slideScr" type="file" accept="image/*" onchange="previewImage(event)" style="display:none;">
                                        </div>

                                        

                                        <div class="text-center mt-3">
                                            <button type="submit" name="addSlide" class="btn btn-primary">➕ Add Slide</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- 🖼️ Slide Viewer -->
                                <div class="card mt-3" style="max-width:700px;margin:auto;">
                                    <h2 class="h2 mb-3">📽️ Slide Preview</h2>
                                    <div id="slideViewer" class="slide-viewer text-center">
                                        <?php if (count($slides) > 0): ?>
                                            <img id="currentSlide" src="<?= $uploadDir . htmlspecialchars($slides[0]['slideScr']) ?>" 
                                                data-index="0" width="100%" style="max-height:300px;object-fit:cover;border-radius:8px;">
                                            <p id="slideInfo" class="mt-2"><?= htmlspecialchars($slides[0]['slideHref'] ?: 'No link') ?></p>
                                        <?php else: ?>
                                            <p>No slides yet.</p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="text-center mt-3">
                                        <button id="prevSlide" class="btn btn-ghost">⬅️ Back</button>
                                        <button id="nextSlide" class="btn btn-ghost">Next ➡️</button>
                                        <button id="deleteSlide" class="btn btn-cancel">🗑️ Delete</button>
                                    </div>
                                </div>

                                <script>
                                const slides = <?= json_encode($slides) ?>;
                                currentIndex = 0;

                                // Preview upload
                                function previewImage(e){
                                    const reader = new FileReader();
                                    reader.onload = function(){
                                        document.getElementById('thumbnail').src = reader.result;
                                    };
                                    reader.readAsDataURL(e.target.files[0]);
                                }

                                // Navigation
                                $('#nextSlide').on('click', function(){
                                    if(slides.length === 0) return;
                                    currentIndex = (currentIndex + 1) % slides.length;
                                    updateSlide();
                                });
                                $('#prevSlide').on('click', function(){
                                    if(slides.length === 0) return;
                                    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                                    updateSlide();
                                });
                                function updateSlide(){
                                    $('#currentSlide').attr('src', '../images/slide/' + slides[currentIndex].slideScr);
                                    $('#slideInfo').text(slides[currentIndex].slideHref || 'No link');
                                }

                                // Delete (AJAX → ajaxadmin/)
                                $('#deleteSlide').on('click', function(){
                                    if(slides.length === 0) return;
                                    const slideID = slides[currentIndex].slideID;
                                    if(confirm('Deactivate this slide?')){
                                        $.post('ajaxadmin/deactivate_slide.php', { id: slideID }, function(res){
                                            alert(res.message);
                                            location.reload();
                                        }, 'json');
                                    }
                                });
                                </script>

                                <?php

                                    
                                break;

                            case 'social':
                                // 🟦 Social Media Links

                                // Fetch current links
                                $stmt = $con->prepare("SELECT * FROM tblsocialmedia WHERE mediaId = 1");
                                $stmt->execute();
                                $social = $stmt->fetch(PDO::FETCH_ASSOC);

                                // Handle update
                                if(isset($_POST['updateSocial'])){
                                    $facebook = $_POST['facebooklink'];
                                    $instagram = $_POST['instalink'];
                                    $tiktok = $_POST['tiktoklink'];

                                    $update = $con->prepare("UPDATE tblsocialmedia SET facebooklink = ?, instalink = ?, tiktoklink = ? WHERE mediaId = 1");
                                    $update->execute([$facebook, $instagram, $tiktok]);

                                    echo '<div class="card" style="color:var(--color-success);font-weight:600;">✅ Social media links updated!</div>';

                                    // Refresh data
                                    $stmt = $con->prepare("SELECT * FROM tblsocialmedia WHERE mediaId = 1");
                                    $stmt->execute();
                                    $social = $stmt->fetch(PDO::FETCH_ASSOC);
                                }
                                ?>
                                <div class="card" style="max-width:600px;margin:auto;">
                                    <h2 class="h2 mb-3">🌐 Social Media Links</h2>

                                    <form method="POST">
                                        <div class="mb-3">
                                            <label>Facebook</label>
                                            <input type="text" name="facebooklink" value="<?= htmlspecialchars($social['facebooklink']) ?>" class="input" placeholder="https://facebook.com/yourpage">
                                            <small class="hint">Full URL to your Facebook page</small>
                                        </div>

                                        <div class="mb-3">
                                            <label>Instagram</label>
                                            <input type="text" name="instalink" value="<?= htmlspecialchars($social['instalink']) ?>" class="input" placeholder="https://instagram.com/yourpage">
                                            <small class="hint">Full URL to your Instagram profile</small>
                                        </div>

                                        <div class="mb-3">
                                            <label>TikTok</label>
                                            <input type="text" name="tiktoklink" value="<?= htmlspecialchars($social['tiktoklink']) ?>" class="input" placeholder="https://tiktok.com/@yourpage">
                                            <small class="hint">Full URL to your TikTok account</small>
                                        </div>

                                        <div class="text-center mt-3">
                                            <button type="submit" name="updateSocial" class="btn btn-primary">💾 Update</button>
                                        </div>
                                    </form>
                                </div>
                                <?php
                                break;
                            case 'finance':
                                // 🟥 Finance Setting
                                $stmt = $con->prepare("SELECT * FROM tblfinancesetting WHERE SettingID = 1");
                                $stmt->execute();
                                $finance = $stmt->fetch(PDO::FETCH_ASSOC);

                                // Handle update
                                if(isset($_POST['updateFinance'])){
                                    $taxNumber = $_POST['taxNumber'];
                                    $taxPercent = $_POST['taxPercent'];
                                    $includeTax = isset($_POST['includeTax']) ? 1 : 0;
                                    $PK = $_POST['PK'];
                                    $SK = $_POST['SK'];

                                    $update = $con->prepare("UPDATE tblfinancesetting SET taxNumber=?, taxPercent=?, includeTax=?, PK=?, SK=? WHERE SettingID=1");
                                    $update->execute([$taxNumber, $taxPercent, $includeTax, $PK, $SK]);

                                    echo '<div class="card" style="color:var(--color-success);font-weight:600;">✅ Finance settings updated!</div>';

                                    // Refresh data
                                    $stmt = $con->prepare("SELECT * FROM tblfinancesetting WHERE SettingID = 1");
                                    $stmt->execute();
                                    $finance = $stmt->fetch(PDO::FETCH_ASSOC);
                                }
                                ?>
                                <div class="card" style="max-width:700px;margin:auto;">
                                    <h2 class="h2 mb-3">💰 Finance Settings</h2>
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label>Tax Number</label>
                                            <input type="text" name="taxNumber" class="input" value="<?= htmlspecialchars($finance['taxNumber']) ?>" placeholder="GST/HST">
                                        </div>

                                        <div class="mb-3">
                                            <label>Tax Percent (%)</label>
                                            <input type="number" name="taxPercent" class="input" value="<?= htmlspecialchars($finance['taxPercent']) ?>" min="0" max="100" step="0.01">
                                        </div>

                                        <div class="mb-3">
                                            <label>Include Tax</label><br>
                                            <label class="switch">
                                                <input type="checkbox" name="includeTax" <?= $finance['includeTax'] ? 'checked' : '' ?>>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>

                                        <div class="mb-3">
                                            <label>Publishable Key (PK)</label>
                                            <textarea name="PK" id="PK"><?= htmlspecialchars($finance['PK']) ?></textarea>
                                            
                                        </div>

                                        <div class="mb-3">
                                            <label>Secret Key (SK)</label>
                                            <textarea name="SK" id="SK"><?= htmlspecialchars($finance['SK']) ?></textarea>
                                            
                                        </div>

                                        <div class="text-center mt-3">
                                            <button type="submit" name="updateFinance" class="btn btn-primary">💾 Update</button>
                                        </div>
                                    </form>
                                </div>
                                <?php
                                    $stmt = $con->prepare("SELECT * FROM tblpaymentmethods ORDER BY methodID ASC");
                                    $stmt->execute();
                                    $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <div class="card" style="max-width:700px;margin:auto;">
                                    <h2 class="h2 mb-3">💳 Payment Methods</h2>
                                    <table style="width:100%;border-collapse:collapse;">
                                        <tr style="background:var(--color-gary);">
                                            <th style="padding:8px;text-align:left;">Method</th>
                                            <th>Note</th>
                                            <th>Active</th>
                                        </tr>
                                        <?php foreach($methods as $m): ?>
                                        <tr style="border-bottom:1px solid #eee;">
                                            <td style="padding:8px;"><?= htmlspecialchars($m['methodName']) ?></td>
                                            <td>
                                                <textarea class="input payment-note" data-id="<?= $m['methodID'] ?>" placeholder="Enter note" rows="2"><?= htmlspecialchars($m['methodNote']) ?></textarea>
                                            </td>
                                            <td style="text-align:center;">
                                                <label class="switch">
                                                    <input type="checkbox" class="payment-active" data-id="<?= $m['methodID'] ?>" <?= $m['methodActive'] ? 'checked' : '' ?>>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>

                                <script>
                                $(document).ready(function(){
                                    // Update note via AJAX
                                    $('.payment-note').on('input', function(){
                                        let id = $(this).data('id');
                                        let note = $(this).val();
                                        $.post('ajaxadmin/update_payment_note.php', {id:id,note:note});
                                    });

                                    // Toggle active via AJAX
                                    $('.payment-active').on('change', function(){
                                        let id = $(this).data('id');
                                        let active = $(this).is(':checked') ? 1 : 0;
                                        $.post('ajaxadmin/toggle_payment_active.php', {id:id,active:active});
                                    });
                                });
                                </script>
                                <?php
                                break;

                            default:
                                // ⚪ Fallback (optional)
                                echo "<div style='padding:20px;'>Invalid section selected.</div>";
                                break;
                        }
                    ?>
                </div>
            </div>
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/manageSetting.js"></script>
</body>