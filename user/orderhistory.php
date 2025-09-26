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
    <link rel="stylesheet" href="css/orderhistory.css">
</head>
<body>
    <?php
        include 'include/header.php';
        include 'include/clientheader.php';
        include 'include/catecorysname.php';
    ?>
    <div class="titleCatecory">
        <div class="navbarsection">
            <h5>Home/ user's Account/  <strong>Oders</strong></h5>
        </div>
        <div class="catecoryname">
            <h2>order History</h2>
        </div>      
        <div class="desgin">

        </div>
    </div>
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
        <?php include 'include/aside.php' ?>
        <div class="sections_side">
            <?php
                if($do == 'manage'){?>
                    <div class="title_section">
                        <h3>Order History</h3>
                        <div class="sereachdiv">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <circle cx="8.80589" cy="8.30589" r="7.49047" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.0156 13.9043L16.9523 16.8334" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input type="text" name="" id="txtsearchorder" placeholder="Search">
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tblorders">
                        </tbody>
                    </table>
                    <div id="pagination"></div>
                <?php
                }elseif($do=='detail'){

                }else{
                    header("Location: ../login.php");
                    exit(); 
                }
            ?>
            
        </div>
    </main>
    <?php include 'include/footer.php' ?>
    <?php include '../common/jslinks.php'?>
    <script src="js/orderhistory.js"></script>
</body>