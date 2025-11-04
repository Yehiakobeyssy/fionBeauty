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

    $do=isset($_GET['do'])?$_GET['do']:'manage'
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/manageWorkshops.css?v=1.1">
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
            <div class="btnadd">
                <a href="manageWorkshops.php?do=add" class="btn btn-success">New Workshop</a>
            </div>
            <?php
                if($do=='manage'){?>
                    <div class="statistic_cat">
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
                        <?php
                            // Assuming you already have $con as your PDO connection

                            // 1. Total Revenue
                            $stmt = $con->prepare("SELECT COALESCE(SUM(totalAmount), 0) AS totalRevenue FROM tblinvoiceworkshop  WHERE status != 6");
                            $stmt->execute();
                            $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['totalRevenue'];

                            // 2. All Workshops
                            $stmt = $con->prepare("SELECT COUNT(*) AS totalWorkshops FROM workshops WHERE  ActiveWorkshop = 1");
                            $stmt->execute();
                            $totalWorkshops = $stmt->fetch(PDO::FETCH_ASSOC)['totalWorkshops'];

                            // 3. Upcoming Workshops
                            $stmt = $con->prepare("SELECT COUNT(*) AS upcomingWorkshops FROM workshops WHERE workshop_date >= CURDATE() AND ActiveWorkshop = 1");
                            $stmt->execute();
                            $upcomingWorkshops = $stmt->fetch(PDO::FETCH_ASSOC)['upcomingWorkshops'];

                            // 4. Total Client Bookings
                            $stmt = $con->prepare("SELECT COUNT(*) AS totalBookings FROM workshop_bookings");
                            $stmt->execute();
                            $totalBookings = $stmt->fetch(PDO::FETCH_ASSOC)['totalBookings'];

                            // 5. Upcoming Client Bookings
                            $stmt = $con->prepare("
                                SELECT COUNT(*) AS upcomingClientBookings 
                                FROM workshop_bookings wb
                                JOIN workshops w ON wb.workshop_id = w.id
                                WHERE w.workshop_date >= CURDATE()
                            ");
                            $stmt->execute();
                            $upcomingClientBookings = $stmt->fetch(PDO::FETCH_ASSOC)['upcomingClientBookings'];
                            ?>

                            <!-- 🎨 Dashboard HTML -->
                            <div class="totalbalance">
                                <label>Total Revenue</label>
                                <h5><?= number_format($totalRevenue, 2) ?> $</h5>
                            </div>

                            <div class="allstatistic">
                                <div class="cardstatistic">
                                    <label>All Workshops</label>
                                    <h5><?= $totalWorkshops ?></h5>
                                </div>
                                <div class="cardstatistic">
                                    <label>Upcoming Workshops</label>
                                    <h5><?= $upcomingWorkshops ?></h5>
                                </div>
                                <div class="cardstatistic">
                                    <label>Total Client Bookings</label>
                                    <h5><?= $totalBookings ?></h5>
                                </div>
                                <div class="cardstatistic">
                                    <label>Upcoming Client Bookings</label>
                                    <h5><?= $upcomingClientBookings ?></h5>
                                </div>
                            </div>

                    </div>
                    <div class="filter_section">
                        <div class="dates">
                            <input type="date" name="" id="txtdateofworkshop">
                        </div>
                        <div class="searchbar">
                            <input type="text" name="" id="txtSearchbar" placeholder="Search....">
                        </div>
                        <div class="paid">
                            <select name="" id="txtpaid">
                                <option value="">All</option>
                                <option value="0">Free</option>
                                <option value="1">Cost</option>
                            </select>
                        </div>
                    </div>
                    <div class="tbl">
                        <table>
                            <thead>
                                <th>Title</th>
                                <th>Date of Workshop</th>
                                <th>Hour</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Client Booked</th>
                                <th>Action  </th>
                            </thead>
                            <tbody id="fetchworkshop"></tbody>
                        </table>
                    </div>
                <?php
                }elseif($do =='add'){?>
                    
                    <?php
                        if(isset($_POST['btnaddWorkshop'])){
                            $title = $_POST['title'];
                            $description = $_POST['description'];
                            $workshop_date = $_POST['date'];
                            $start_time = $_POST['time'];
                            $duration_hours  =$_POST['duration'];
                            $cost =$_POST['cost'];
                            $location =$_POST['location'];
                            $is_online =isset($_POST['isonline'])?1:0;
                            $meeting_link =$_POST['meetinlink'];

                            $sql= $con->prepare('INSERT INTO workshops (title,description,workshop_date,start_time,duration_hours,cost,location,is_online,meeting_link)
                                                VALUES (?,?,?,?,?,?,?,?,?)');
                            $sql->execute([
                                    $title,
                                    $description,
                                    $workshop_date,
                                    $start_time,
                                    $duration_hours,
                                    $cost,
                                    $location,
                                    $is_online,
                                    $meeting_link,
                                ]);

                            echo '
                                <div class="conformnewWorkshop ">
                                    You Added new Workshop Successfully
                                </div>
                            ';

                            $notificationText = "New workshop created: $title by admin $admin_name";
                            $stmt = $con->prepare("INSERT INTO tblNotification (text) VALUES (?)");
                            $stmt->execute([$notificationText]);
                            $notificationId = $con->lastInsertId();
                            $admins = $con->query("SELECT adminID  FROM  tbladmin WHERE admin_block = 0")->fetchAll(PDO::FETCH_COLUMN);
                            $stmtSeen = $con->prepare("INSERT INTO tblseennotification (notificationId, adminID, seen) VALUES (?, ?, 0)");
                            foreach ($admins as $adminId) {
                                $stmtSeen->execute([$notificationId, $adminId]);
                            }
                        }
                    ?> 
                    <div class="container_new">
                        <div class="title_form">New Workshop</div>
                        <form action="" method="post">
                            <label for="">Titel</label>
                            <input type="text" name="title" required>

                            <label for="">Description</label>
                            <textarea name="description" rows="3"></textarea>

                            <div class="specific">
                            <div class="time">
                                <label>Date</label>
                                <input type="date" name="date" required required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="time">
                                <label>Time</label>
                                <input type="time" name="time" required>
                            </div>
                            </div>

                            <div class="specific">
                            <div class="time">
                                <label>Duration</label>
                                <input type="number" name="duration">
                            </div>
                            <div class="time">
                                <label>Cost</label>
                                <input type="number" name="cost" required>
                            </div>
                            </div>

                            <div class="location">
                            <label>is online</label>
                            <label class="switch">
                                <input type="checkbox" id="isOnline" name="isonline">
                                <span class="slider"></span>
                            </label>

                            <input type="text" id="adresse" placeholder="Adresse" name="location">
                            <input type="text" id="link" placeholder="Link" name="meetinlink">
                            </div>
                            <div class="btncontrol">
                                <button type="reset" class="btn btn-outboder">Cancel</button>
                                <button type="submit" class="btn btn-inboder"  name="btnaddWorkshop">Add Workshop</button>
                            </div>
                        </form>
                        </div>
                        <script>
                        const checkbox = document.getElementById('isOnline');
                        const adresse = document.getElementById('adresse');
                        const link = document.getElementById('link');

                        // Default state
                        adresse.classList.add('show');

                        checkbox.addEventListener('change', () => {
                            if (checkbox.checked) {
                            adresse.classList.remove('show');
                            link.classList.add('show');
                            } else {
                            link.classList.remove('show');
                            adresse.classList.add('show');
                            }
                        });
                        </script>
                <?php
                }elseif($do=='edit'){
                    // Get workshop ID from URL
                    $wid = isset($_GET['wid']) ? (int)$_GET['wid'] : 0;

                    // Fetch the existing data
                    $stmt = $con->prepare("SELECT * FROM workshops WHERE id = ?");
                    $stmt->execute([$wid]);
                    $workshop = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$workshop) {
                        echo "<div class='error'>Workshop not found!</div>";
                        exit;
                    }

                    // If form submitted, update data
                    if (isset($_POST['btneditWorkshop'])) {
                        $title = $_POST['title'];
                        $description = $_POST['description'];
                        $workshop_date = $_POST['date'];
                        $start_time = $_POST['time'];
                        $duration_hours = $_POST['duration'];
                        $cost = $_POST['cost'];
                        $location = $_POST['location'];
                        $is_online = isset($_POST['isonline']) ? 1 : 0;
                        $meeting_link = $_POST['meetinlink'];

                        $sql = $con->prepare("UPDATE workshops 
                                            SET title=?, description=?, workshop_date=?, start_time=?, duration_hours=?, cost=?, location=?, is_online=?, meeting_link=? 
                                            WHERE id=?");
                        $sql->execute([
                            $title,
                            $description,
                            $workshop_date,
                            $start_time,
                            $duration_hours,
                            $cost,
                            $location,
                            $is_online,
                            $meeting_link,
                            $wid
                        ]);

                        echo '
                            <div class="conformnewWorkshop">
                                Workshop Updated Successfully
                            </div>
                        ';

                        // Refresh data
                        $stmt = $con->prepare("SELECT * FROM workshops WHERE id = ?");
                        $stmt->execute([$wid]);
                        $workshop = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                    ?>

                    <div class="container_new">
                        <div class="title_form">Edit Workshop</div>
                        <form action="" method="post">
                            <label>Titel</label>
                            <input type="text" name="title" required value="<?= htmlspecialchars($workshop['title']) ?>">

                            <label>Description</label>
                            <textarea name="description" rows="3"><?= htmlspecialchars($workshop['description']) ?></textarea>

                            <div class="specific">
                                <div class="time">
                                    <label>Date</label>
                                    <input type="date" name="date" required value="<?= htmlspecialchars($workshop['workshop_date']) ?> "  min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="time">
                                    <label>Time</label>
                                    <input type="time" name="time" required value="<?= htmlspecialchars($workshop['start_time']) ?>">
                                </div>
                            </div>

                            <div class="specific">
                                <div class="time">
                                    <label>Duration</label>
                                    <input type="number" name="duration" value="<?= htmlspecialchars($workshop['duration_hours']) ?>">
                                </div>
                                <div class="time">
                                    <label>Cost</label>
                                    <input type="number" name="cost" required value="<?= htmlspecialchars($workshop['cost']) ?>">
                                </div>
                            </div>

                            <div class="location">
                                <label>is online</label>
                                <label class="switch">
                                    <input type="checkbox" id="isOnline_edid" name="isonline" <?= $workshop['is_online'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>

                                <input type="text" id="adresse_edid" placeholder="Adresse" name="location" value="<?= htmlspecialchars($workshop['location']) ?>">
                                <input type="text" id="link_edid" placeholder="Link" name="meetinlink" value="<?= htmlspecialchars($workshop['meeting_link']) ?>">
                            </div>

                            <div class="btncontrol">
                                <button type="reset" class="btn btn-outboder">Cancel</button>
                                <button type="submit" class="btn btn-inboder" name="btneditWorkshop">Update Workshop</button>
                            </div>
                        </form>
                    </div>

                    <script>
                    const checkbox_edit = document.getElementById('isOnline_edid');
                    const adresse_edit = document.getElementById('adresse_edid');
                    const link_edit = document.getElementById('link_edid');

                    // Set initial visibility based on DB value
                    if (checkbox_edit.checked) {
                        adresse_edit.classList.remove('show');
                        link_edit.classList.add('show');
                    } else {
                        link_edit.classList.remove('show');
                        adresse_edit.classList.add('show');
                    }

                    // Change event listener
                    checkbox_edit.addEventListener('change', () => {
                        if (checkbox_edit.checked) {
                            adresse_edit.classList.remove('show');
                            link_edit.classList.add('show');
                        } else {
                            link_edit.classList.remove('show');
                            adresse_edit.classList.add('show');
                        }
                    });
                    </script>
                <?php
                } elseif ($do == 'view') {
                    $wid = isset($_GET['wid']) ? (int)$_GET['wid'] : 0;

                    // 1️⃣ Get workshop info
                    $stmt = $con->prepare("SELECT * FROM workshops WHERE id = ?");
                    $stmt->execute([$wid]);
                    $workshop = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$workshop) {
                        echo "<div class='alert alert-danger'>Workshop not found.</div>";
                    } else {
                        // 2️⃣ Get workshop statistics
                        $stmt2 = $con->prepare("
                            SELECT 
                                COUNT(DISTINCT wb.user_id) AS total_clients,
                                COALESCE(SUM(i.totalAmount), 0) AS total_revenue
                            FROM workshop_bookings wb
                            LEFT JOIN tbldetailinvoiceworkshop di ON wb.workshop_id = di.workshopID
                            LEFT JOIN tblinvoiceworkshop i ON di.invoiceID = i.invoiceID
                            WHERE wb.workshop_id = ?
                        ");
                        $stmt2->execute([$wid]);
                        $stats = $stmt2->fetch(PDO::FETCH_ASSOC);

                        $total_clients = $stats['total_clients'] ?? 0;
                        $total_revenue = $stats['total_revenue'] ?? 0;

                        // 3️⃣ Calculate time left until workshop
                        $workshop_datetime = strtotime($workshop['workshop_date'] . ' ' . $workshop['start_time']);
                        $now = time();
                        $time_left = $workshop_datetime - $now;
                        $show_timer = $time_left > 0;
                        ?>

                        <!-- START WORKSHOP VIEW -->
                        <section class="workshop-view">

                            <!-- 🟦 TOP INFO + STATISTICS -->
                            <div class="workshop-header">
                                <div class="workshop-info">
                                    <h2><?= htmlspecialchars($workshop['title']) ?></h2>
                                    <div class="workshop-meta">
                                        <span><i class="fa fa-calendar"></i> <?= date('d M Y', strtotime($workshop['workshop_date'])) ?></span><br>
                                        <span><i class="fa fa-clock"></i> <?= date('H:i', strtotime($workshop['start_time'])) ?></span><br>
                                        <span><i class="fa fa-hourglass-half"></i> <?= htmlspecialchars($workshop['duration_hours']) ?> hours</span><br>
                                        <span>
                                            <?php if ($workshop['is_online']): ?>
                                                <i class="fa fa-globe"></i> Online (<a href="<?= htmlspecialchars($workshop['meeting_link']) ?>" target="_blank">Join Link</a>)
                                            <?php else: ?>
                                                <i class="fa fa-map-marker-alt"></i> <?= htmlspecialchars($workshop['location']) ?>
                                            <?php endif; ?>
                                        </span><br>
                                    </div>
                                </div>

                                <div class="workshop-statistics">
                                    <div class="stat-item">
                                        <label>Total Revenue</label>
                                        <h4>$<?= number_format($total_revenue, 2) ?></h4>
                                    </div>
                                    <div class="stat-item">
                                        <label>Booked Participants</label>
                                        <h4><?= $total_clients ?></h4>
                                    </div>

                                    <?php if ($show_timer): ?>
                                        <div class="timer-box" id="workshop-timer"
                                            data-timestamp="<?= $workshop_datetime ?>">
                                            Starts in: <span id="time-remaining"></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- 🟩 BOOKINGS TABLE -->
                            <div class="workshop-bookings">
                                <h3>Client Bookings</h3>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Client Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Invoice Code</th>
                                            <th>Invoice Date</th>
                                            <th>Amount</th>
                                            <th>Transaction ID</th>
                                            <th>Method</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt3 = $con->prepare("
                                            SELECT 
                                                c.clientFname, c.clientLname, c.clientPhoneNumber, c.clientEmail,
                                                i.invoiceCode, i.invoiceDate, i.totalAmount, i.transactionID, i.method,
                                                di.detailID  AS detailID
                                            FROM tblclient c
                                            JOIN tblinvoiceworkshop i ON c.clientID = i.clientID
                                            JOIN tbldetailinvoiceworkshop di ON i.invoiceID = di.invoiceID
                                            JOIN workshop_bookings wb ON wb.workshop_id = di.workshopID AND wb.user_id = c.clientID
                                            WHERE di.workshopID = ? AND i.status != 6
                                            ORDER BY i.invoiceDate DESC
                                        ");
                                        $stmt3->execute([$wid]);
                                        $rows = $stmt3->fetchAll(PDO::FETCH_ASSOC);

                                        if ($stmt3->rowCount() > 0) {
                                            foreach ($rows as $row) {
                                                $btnLabel = $row['totalAmount'] > 0 ? 
                                                            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                <circle cx="12" cy="12" r="11.25" stroke="#F59E0B" stroke-width="1.5"/>
                                                                <path d="M12 8v4l3 3" stroke="#F59E0B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>' : 
                                                            '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                                <path d="M16.6667 4.64286H4.44449L5.5556 17.5H14.4445L15.5556 4.64286H3.33337M10 7.85714V14.2857M12.7778 7.85714L12.2223 14.2857M7.22226 7.85714L7.77782 14.2857M7.77782 4.64286L8.33337 2.5H11.6667L12.2223 4.64286" stroke="#E01212" stroke-width="1.56" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>';
                                                $btnClass = $row['totalAmount'] > 0 ? 'refund-btn' : 'delete-btn';
                                                $btntitle = $row['totalAmount'] > 0 ? 'Refount':'Delete';
                                                echo "
                                                <tr>
                                                    <td>{$row['clientFname']} {$row['clientLname']}</td>
                                                    <td>{$row['clientPhoneNumber']}</td>
                                                    <td>{$row['clientEmail']}</td>
                                                    <td>{$row['invoiceCode']}</td>
                                                    <td>" . date('d. M Y H:i', strtotime($row['invoiceDate'])) . "</td>
                                                    <td>\${$row['totalAmount']}</td>
                                                    <td>{$row['transactionID']}</td>
                                                    <td><span class='payment-badge'>{$row['method']}</span></td>
                                                    <td>
                                                        <button class='refund-action' 
                                                            title='{$btntitle}'
                                                            data-invoice='{$row['invoiceCode']}'
                                                            data-transaction='{$row['transactionID']}'
                                                            data-amount='{$row['totalAmount']}'
                                                            data-detailid='{$row['detailID']}'
                                                            data-workshop='{$wid}' >
                                                            {$btnLabel}
                                                        </button>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='9' style='text-align:center;color:#888;'>No bookings yet.</td></tr>";
                                        }
                                        ?>
                                        </tbody>
                                </table>
                            </div>

                        </section>

                        <!-- TIMER SCRIPT -->
                        <?php if ($show_timer): ?>
                        <script>
                            const timerBox = document.getElementById('workshop-timer');
                            const display = document.getElementById('time-remaining');
                            const targetTime = parseInt(timerBox.dataset.timestamp) * 1000;

                            function updateTimer() {
                                const now = new Date().getTime();
                                const diff = targetTime - now;

                                if (diff <= 0) {
                                    timerBox.style.display = 'none';
                                    clearInterval(interval);
                                    return;
                                }

                                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                                // Always format with leading zeros for better look
                                const pad = n => n.toString().padStart(2, '0');

                                display.textContent = `${pad(days)}d ${pad(hours)}h ${pad(minutes)}m ${pad(seconds)}s`;
                            }

                            updateTimer();
                            const interval = setInterval(updateTimer, 1000); // Update every second
                        </script>
                        <?php endif; ?>


                        <?php
                    }
                } elseif($do =='delete'){
                    $wid = isset($_GET['wid']) ? (int)$_GET['wid'] : 0;

                    // Check if this workshop has bookings
                    $stmt = $con->prepare("SELECT COUNT(*) FROM workshop_bookings WHERE workshop_id = ?");
                    $stmt->execute([$wid]);
                    $bookings_count = $stmt->fetchColumn();

                    if ($bookings_count == 0) {
                        // No bookings — safe to delete
                        if (isset($_POST['confirmDelete'])) {
                            // Soft delete workshop
                            $update = $con->prepare("UPDATE workshops SET ActiveWorkshop = 0 WHERE id = ?");
                            $update->execute([$wid]);
                            echo '
                                <div class="alert alert-danger">
                                    Workshop deleted successfully.
                                </div>
                                <script>
                                    setTimeout(function(){ window.location.href = "manageWorkshops.php"; }, 2000);
                                </script>
                            ';
                        } else {
                            ?>
                            <div class="alert alert-danger deleteworkshop">
                                Are you sure you want to delete this workshop?
                                <form method="post" style="margin-top:10px;">
                                    <button type="submit" name="confirmDelete" class="btn btn-danger">Yes, Delete</button>
                                    <a href="manageWorkshops.php" class="btn btn-secondary">Cancel</a>
                                </form>
                            </div>
                            <?php
                        }
                    } else {
                        // Workshop has bookings — show warning and action options
                        ?>
                        <div class="alert alert-danger deleteworkshop">
                            ⚠️ This workshop has <b><?= $bookings_count ?></b> booked clients.<br>
                            You must <b>refund or delete all bookings</b> before deleting the workshop.
                        </div>

                        <div class="action-buttons">
                            <?php
                            // Check if the workshop has any paid bookings
                            $stmtCheck = $con->prepare("
                                SELECT COUNT(*) 
                                FROM tblinvoiceworkshop i
                                INNER JOIN tbldetailinvoiceworkshop di ON di.invoiceID = i.invoiceID
                                WHERE di.workshopID = ? AND i.totalAmount > 0
                            ");
                            $stmtCheck->execute([$wid]);
                            $hasPaid = $stmtCheck->fetchColumn() > 0;

                            // Set label and color
                            $btnLabel = $hasPaid ? 'Refund All' : 'Delete All Bookings';
                            $btnClass = $hasPaid ? 'btn-warning' : 'btn-danger';
                            ?>

                            <!-- Dynamic Button -->
                            <button type="button" 
                                    class="btn <?= $btnClass ?> refund-all-btn" 
                                    data-workshop="<?= $wid ?>" 
                                    data-paid="<?= $hasPaid ? 1 : 0 ?>">
                                <?= $btnLabel ?>
                            </button>
                        </div>

                        <div class="workshop-bookings">
                            <h3>Existing Bookings</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Invoice Code</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $con->prepare("
                                        SELECT 
                                            c.clientFname, c.clientLname, c.clientEmail, c.clientPhoneNumber,
                                            i.invoiceCode, i.totalAmount, i.status
                                        FROM workshop_bookings wb
                                        JOIN tblclient c ON wb.user_id = c.clientID
                                        LEFT JOIN tbldetailinvoiceworkshop di ON wb.workshop_id = di.workshopID
                                        LEFT JOIN tblinvoiceworkshop i ON di.invoiceID = i.invoiceID
                                        WHERE wb.workshop_id = ? AND i.status != 6
                                    ");
                                    $stmt->execute([$wid]);
                                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if ($rows) {
                                        foreach ($rows as $r) {
                                            $statusText = $r['status'] == 6 ? 'Refunded' : 'Active';
                                            echo "
                                            <tr>
                                                <td>{$r['clientFname']} {$r['clientLname']}</td>
                                                <td>{$r['clientEmail']}</td>
                                                <td>{$r['clientPhoneNumber']}</td>
                                                <td>{$r['invoiceCode']}</td>
                                                <td>\${$r['totalAmount']}</td>
                                                <td>{$statusText}</td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No bookings found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                                }

            ?>
            
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/manageWorkshops.js?v=1.1"></script>

</body>