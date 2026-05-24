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


    $stat = $con->prepare('SELECT COUNT(*) AS totalitem FROM  tblitems WHERE itmActive = 1');
    $stat->execute();
    $result = $stat->fetch(PDO::FETCH_ASSOC);
    if ($result['totalitem'] < 6) {
        header('Location: commingsoon.php');
        exit(); // Always call exit after redirect
    } 
?>    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/program.css?v=1.1">
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php';
    ?>
    <div class="programs_container">

        <?php
            $sql = "SELECT ProgramID, ProgramName, ProgramLogo 
                    FROM tblprogramm 
                    WHERE ProgramActive = 1";

            $stmt = $con->prepare($sql);
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        ?>

            <div class="program_card" data-id="<?php echo $row['ProgramID']; ?>">

                <div class="program_logo">
                    <img src="images/programs/<?php echo $row['ProgramLogo']; ?>" 
                        alt="<?php echo $row['ProgramName']; ?>">
                </div>

                <div class="program_name">
                    <?php echo $row['ProgramName']; ?>
                </div>

            </div>

        <?php } ?>

    </div>
    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    <script src="js/program.js?v=1.1"></script>
</body>