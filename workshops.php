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
?>    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/workshops.css">
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php'; 
        include 'include/catecorysname.php';
    ?>
    <main class="display">
        <h1 class="">Upcoming Workshops</h1>
        <div class="workshops-list">
            <?php
                $stmt = $con->prepare("SELECT * FROM workshops WHERE ActiveWorkshop	= 1 AND workshop_date >= CURDATE() ORDER BY workshop_date, start_time");
                $stmt->execute();
                $workshops = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($workshops) {
                    foreach ($workshops as $row) {

                        echo '<div class="card mb-3">';
                        echo '<h2 >' . htmlspecialchars($row['title']) . '</h2>';
                        echo '<p >' . htmlspecialchars($row['description']) . '</p>';
                        echo '<p>
                                <strong>Date:</strong> ' . $row['workshop_date'] . ' 
                                <strong>Time:</strong> ' . substr($row['start_time'],0,5) . ' 
                                <strong>Duration:</strong> ' . $row['duration_hours'] . ' hours
                              </p>';
                        echo '<p><strong>Location:</strong> ' . htmlspecialchars($row['location']) . '</p>';
                        if ($row['cost'] == 0) {
                            echo '<label class="txtprice"><strong>Price:</strong> <span style="color: var(--color-primary); font-weight: bold;">Free</span></label>';
                        } else {
                            echo '<label class="txtprice"><strong>Price:</strong> $' . number_format($row['cost'], 2) . '</label>';
                        }

                        // التحقق من تسجيل الدخول و حالة العميل
                        if ($user_id > 0) {
                            $clientStmt = $con->prepare("SELECT clientActive, clientBlock FROM tblclient WHERE clientID = :id");
                            $clientStmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                            $clientStmt->execute();
                            $client = $clientStmt->fetch(PDO::FETCH_ASSOC);

                            if ($client && $client['clientActive'] == 1 && $client['clientBlock'] == 0) {
                                // تحقق مما إذا كان المستخدم قد حجز هذه الورشة بالفعل
                                $bookingStmt = $con->prepare("SELECT COUNT(*) FROM workshop_bookings WHERE user_id = :user_id AND workshop_id = :workshop_id");
                                $bookingStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                                $bookingStmt->bindParam(':workshop_id', $row['id'], PDO::PARAM_INT);
                                $bookingStmt->execute();
                                $alreadyBooked = $bookingStmt->fetchColumn();

                                if ($alreadyBooked > 0) {
                                    echo '<p class="text-success">You have already booked this workshop.</p>';
                                } else {
                                    if ($row['cost'] == 0) {
                                        // Free workshop – direct booking
                                        echo '<a href="booking.php?id=' . $row['id'] . '" class="btn btn-primary">Book Workshop</a>';
                                    } else {
                                        // Paid workshop – add to cart flow
                                        echo '<button class="btn btn-primary book-workshop" data-id="' . $row['id'] . '">Book Workshop</button>';
                                        echo '<span class="booking-msg ml-2"></span>'; 
                                    }
                                }

                            } else {
                                echo '<p class="text-danger">Your account is inactive or blocked. Contact support.</p>';
                            }
                        } else {
                            echo '<p class="text-warning">Please <a href="login.php">login</a> before booking.</p>';
                        }

                        echo '</div>';
                    }
                } else {
                    echo "<p class='text-center mt-3'>No upcoming workshops available.</p>";
                }
            ?>
        </div>
</main>
<div id="notification-container" style="
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
"></div>

    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    <script src="js/workshops.js"></script>
</body>