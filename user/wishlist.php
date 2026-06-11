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

    $do= (isset($_GET['do']))?$_GET['do']:'manage'
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/wishlist.css?v=1.1">
</head>
<body>
    <?php
        include 'include/header.php';
        include 'include/clientheader.php';
    ?>
    <div class="titleCatecory">
        <div class="navbarsection">
            <h5>Home/ user's Account/  <strong>Wishlist</strong></h5>
        </div> 
        <div class="catecoryname">
            <h2>Wishlist</h2>
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
    <div class="fion_container">
        <div class="fion_aside">
            <?php   include 'include/catecorysname.php'; ?>
        </div>
        <div class="fion_page">
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
                <?php include 'include/aside.php' ?>
                <div class="sections_side">
                    <?php
                        $sql = $con->prepare("
                            SELECT 
                                i.itmId,
                                i.itmName,
                                i.itmDesc,
                                i.sellPrice,
                                i.mainpic
                            FROM tblfavoriteitm f
                            INNER JOIN tblitems i ON i.itmId = f.itemID
                            WHERE f.clientID = ?
                        ");

                        $sql->execute([$user_id]);
                        $favorites = $sql->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div class="wishlist_container">

                        <?php if (count($favorites) > 0): ?>

                            <?php foreach ($favorites as $item): ?>

                                <div class="wishlist_item" data-id="<?= $item['itmId']; ?>">

                                    <!-- IMAGE -->
                                    <div class="wishlist_img">
                                        <img src="../images/items/<?= $item['mainpic']; ?>" alt="">
                                    </div>

                                    <!-- CONTENT -->
                                    <div class="wishlist_content">

                                        <h3><?= htmlspecialchars($item['itmName']); ?></h3>

                                        <p>
                                            <?= substr(strip_tags($item['itmDesc']), 0, 120); ?>...
                                        </p>

                                        <div class="wishlist_bottom">

                                            <span class="price">
                                                $<?= number_format($item['sellPrice'], 2); ?>
                                            </span>

                                            <div class="wishlist_actions">

                                                <button class="btn_cart" data-id="<?= $item['itmId']; ?>">
                                                    Add to Cart
                                                </button>

                                                <button class="btn_remove" data-id="<?= $item['itmId']; ?>">
                                                    Remove
                                                </button>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <p class="empty_wishlist">Your wishlist is empty.</p>

                        <?php endif; ?>

                    </div>
                </div>
            </main>
        </div>
  </div>
    
    <?php include 'include/footer.php' ?>
    <?php include '../common/jslinks.php'?>
    <script src="js/wishlist.js"></script>
</body>