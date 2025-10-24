<?php
    session_start();
    include '../settings/connect.php';
    include '../common/function.php';

    if (isset($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];  
    } elseif (isset($_COOKIE['user_id'])) {
        $user_id = (int) $_COOKIE['user_id'];  
    } else {
        header("Location: ../login.php");
        exit(); 
    }

    include '../common/head.php';
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/traininghistory.css">
</head>
<body>
    <?php
        include 'include/header.php';
        include 'include/clientheader.php';
        include 'include/catecorysname.php';
    ?>
    <div class="titleCatecory">
        <div class="navbarsection">
            <h5>Home/ user's Account/ <strong>Dashboard</strong></h5>
        </div>
        <div class="catecoryname">
            <h2>Dashboard</h2>
        </div>      
        <div class="desgin">

        </div>
    </div>
    <?php
    $sql = $con->prepare('SELECT clientActive FROM tblclient WHERE clientID = ?');
    $sql->execute([$user_id]);
    $check = $sql->fetch(PDO::FETCH_ASSOC);

    if ($check['clientActive'] == 0) {
        echo '
            <div style="
                width: 80%;
                margin: 20px auto;
                padding: 15px;
                background-color: #fff3cd; /* Bootstrap warning yellow */
                border: 1px solid #ffeeba; /* Slight border like bootstrap */
                color: #856404; /* Text color for warning */
                font-family: Arial, sans-serif;
                text-align: center;
                border-radius: 5px;
            ">
                Your account is not yet active. Please contact the admin to activate your account.
            </div>
        ';
    }

    $do =isset($_GET['do'])?$_GET['do']:'manage';
    ?>
    <div class="welcome_note">
        <?php
            $sql= $con->prepare('SELECT clientFname , clientLname FROM tblclient WHERE clientID  = ?');
            $sql->execute([$user_id]);
            $result = $sql->fetch();
            $fullname = $result['clientFname'].' '. $result['clientLname'];
            $initials = strtoupper(substr($result['clientFname'], 0, 1) . substr($result['clientLname'], 0, 1));

            $firstLetter = strtoupper(substr($result['clientFname'], 0, 1));

            // letter → color map
            $colors = [
                'A' => '#4285F4', // Blue
                'B' => '#34A853', // Green
                'C' => '#FBBC05', // Yellow
                'D' => '#EA4335', // Red
                'E' => '#9C27B0',
                'F' => '#FF5722',
                'G' => '#009688',
                'H' => '#795548',
                'I' => '#3F51B5',
                'J' => '#CDDC39',
                'K' => '#607D8B',
                'L' => '#E91E63',
                'M' => '#00BCD4',
                'N' => '#8BC34A',
                'O' => '#FFC107',
                'P' => '#673AB7',
                'Q' => '#FF9800',
                'R' => '#F44336',
                'S' => '#4CAF50',
                'T' => '#03A9F4',
                'U' => '#9E9E9E',
                'V' => '#FFEB3B',
                'W' => '#8E24AA',
                'X' => '#1E88E5',
                'Y' => '#D32F2F',
                'Z' => '#2E7D32',
            ];
            $bgColor = $colors[$firstLetter] ?? '#333';
            echo "<h2> WELCOME , <span>". $fullname ." </span></h2>"
        ?>
    </div>
    <main>
        <?php
            $sql = $con->prepare('SELECT clientBlock FROM  tblclient WHERE clientID  = ?');
            $sql->execute([$user_id]);
            $result_block = $sql->fetch();
            $isBlock = $result_block['clientBlock'];

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
        <?php include 'include/aside.php'?>
        <div class="sections_side">
            <?php
                if($do=='manage'){?>
                    <div class="title_section">
                        <h3>Training & Education</h3>
                        <div class="sereachdiv">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <circle cx="8.80589" cy="8.30589" r="7.49047" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.0156 13.9043L16.9523 16.8334" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input type="text" name="" id="txtsearchtraining" placeholder="Search">
                        </div>
                    </div>
                    <table>
                        <thead>
                            <th>Workshop</th>
                            <th>Date</th> 
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Cost</th>
                            <th>Is Online</th>
                            <th>Booking Date</th>
                            <th>Action</th>
                        </thead>
                        <tbody id="reultfetch"></tbody>
                    </table>
                    <div id="pagination" class="pagination"></div>

                <?php
                }elseif ($do == 'detail') {
                    $workshopID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

                    // Check if workshop exists
                    $check_workshop = checkItem('id', 'workshops', $workshopID);
                    if ($check_workshop == 0) {
                        header("Location: traininghistory.php");
                        exit();
                    } else {
                        // Get workshop details
                        $stmt = $con->prepare('SELECT title, description, workshop_date, start_time, duration_hours, cost, is_online, meeting_link ,location 
                                            FROM workshops 
                                            WHERE id = ?');
                        $stmt->execute([$workshopID]);
                        $workshop = $stmt->fetch(PDO::FETCH_ASSOC);

                        $title        = htmlspecialchars($workshop['title']);
                        $description  = nl2br(htmlspecialchars($workshop['description']));
                        $workshopDate = date("j F Y", strtotime($workshop['workshop_date']));
                        $startTime    = date("H:i", strtotime($workshop['start_time']));
                        $duration     = $workshop['duration_hours'];
                        $cost         = number_format($workshop['cost'], 2);
                        $isOnline     = $workshop['is_online'] ? 'Online' : 'Onsite';
                        $onlineLink   = $workshop['meeting_link'];
                        $location     = $workshop['location'];
                        ?>

                        <div class="title_section">
                            <h3>Workshop Details</h3>
                            <a href="traininghistory.php">Back to List</a>
                        </div>

                        <div class="invoice_info">
                            <div class="workshop_header">
                                <h2><?= $title ?></h2>
                                
                            </div>
                            <div class="daitail">
                                <p><strong>Date:</strong> <?= $workshopDate ?></p>
                                <p><strong>Time:</strong> <?= $startTime ?></p>
                                <p><strong>Duration:</strong> <?= $duration ?> hours</p>
                                <p><strong>Cost:</strong> <?= $cost==0?'Free':$cost.' $' ?> </p>
                                <p><strong>Type:</strong> <?= $isOnline ?></p>

                                <?php if (!empty($onlineLink) && $workshop['is_online'] == 1): ?>
                                    <p><strong>Online Link:</strong> 
                                        <a href="<?= htmlspecialchars($onlineLink) ?>" target="_blank">
                                            <?= htmlspecialchars($onlineLink) ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($location) && $workshop['is_online'] == 0): ?>
                                    <p><strong>Location:</strong> 
                                        
                                            <?= htmlspecialchars($location) ?>
                                        
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="workshop_description">
                                <h4>Description</h4>
                                <p><?= $description ?></p>
                            </div>
                        </div>

                        <div class="invoice_related">
                            <?php
                            // Check if user booked this workshop
                            $stmt = $con->prepare(
                                            'SELECT b.booking_date, d.invoiceID, i.invoiceCode, i.invoiceDate, i.totalAmount, i.method, i.status
                                            FROM tbldetailinvoiceworkshop d
                                            JOIN tblinvoiceworkshop i ON i.invoiceID = d.invoiceID
                                            LEFT JOIN workshop_bookings b ON b.user_id = i.clientID AND b.workshop_id = d.workshopID
                                            WHERE d.workshopID = ? AND i.clientID = ?
                                            ORDER BY i.invoiceDate DESC, b.booking_date DESC
                                            LIMIT 1'
                                        );
                            $stmt->execute([$workshopID, $user_id]);
                            
                            $booking = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($booking) {
                                $bookingDate = date("j F Y", strtotime($booking['booking_date']));
                                $invoiceDate = date("j F Y", strtotime($booking['invoiceDate']));
                                $invoiceCode = htmlspecialchars($booking['invoiceCode']);
                                $totalAmount = number_format($booking['totalAmount'], 2);
                                $method      = htmlspecialchars($booking['method']);
                                $status      = htmlspecialchars($booking['status']);
                                ?>
                                <div class="booking_info">
                                    <h4>Your Booking</h4>
                                    <p><strong>Booking Date:</strong> <?= $bookingDate ?></p>
                                    <p><strong>Invoice No:</strong> <?= $invoiceCode ?></p>
                                    <p><strong>Invoice Date:</strong> <?= $invoiceDate ?></p>
                                    <p><strong>Amount Paid:</strong> <?= $totalAmount ?> $</p>
                                    <p><strong>Payment Method:</strong> <?= $method ?></p>
                                    
                                </div>
                            <?php } else { ?>
                                <div class="alert alert-warning p-2 m-0 text-center">
                                    ⚠️ You haven't booked this workshop yet.
                                </div>
                            <?php } ?>
                        </div>

                    <?php
                    } // end else
                }else{
                    
                }
            ?>
        </div>
    </main>
    <?php include 'include/footer.php' ?>
    <?php include '../common/jslinks.php'?>
    <script src="js/traininghistory.js"></script>
</body>