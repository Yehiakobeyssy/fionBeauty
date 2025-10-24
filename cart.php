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
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php'; 
        include 'include/catecorysname.php';
    ?>

    <?php
        $sql= $con->prepare('SELECT clientActive FROM tblclient WHERE clientID  = ?');
        $sql->execute([$user_id]);
        $result= $sql->fetch(PDO::FETCH_ASSOC); 
        
        if ($result && isset($result['clientActive'])) {
            $isAcctive = $result['clientActive'];
        } else {
            $isAcctive = 0; 
        }
        

        if($isAcctive == 0){?>
            <div class="error_img">
                <img src="images/img_app/cart_look.png" alt="">
            </div>
        <?php
        }elseif(empty($_SESSION['cart'])){?>
            <div class="error_img">
                <img src="images/img_app/cartempty.png" alt="">
                <h3>Your Cart  is <span>Empty !</span></h3>
                <label for="">Please fill it with our products and enjoy shopping</label>
                <a href="category.php" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M6.875 16.875C7.22018 16.875 7.5 16.5952 7.5 16.25C7.5 15.9048 7.22018 15.625 6.875 15.625C6.52982 15.625 6.25 15.9048 6.25 16.25C6.25 16.5952 6.52982 16.875 6.875 16.875Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.625 16.875C15.9702 16.875 16.25 16.5952 16.25 16.25C16.25 15.9048 15.9702 15.625 15.625 15.625C15.2798 15.625 15 15.9048 15 16.25C15 16.5952 15.2798 16.875 15.625 16.875Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1.875 3.125H4.375L6.25 13.75H16.25" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6.25 10.417H15.9938C16.066 10.417 16.1361 10.392 16.192 10.3462C16.2479 10.3004 16.2862 10.2367 16.3004 10.1658L17.4254 4.54082C17.4345 4.49546 17.4334 4.44866 17.4222 4.40378C17.4109 4.3589 17.3899 4.31707 17.3606 4.2813C17.3312 4.24554 17.2943 4.21673 17.2525 4.19697C17.2107 4.1772 17.165 4.16696 17.1188 4.16699H5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Continue Shopping ->
                </a>
            </div>  
        <?php
        }else{?>
            <div class="container_cart">
                <div class="titleCatecory">
                    <div class="navbarsection">
                        <h5>Home/ Cart</h5>
                    </div>
                    <div class="catecoryname">
                        <h2></h2>
                    </div>      
                    <div class="desgin">

                    </div>
                </div>
                <div class="title_cart">
                    <h3>My Shopping Cart</h3>
                    <label for="">Please fill in the fields below and click place order to complete your purchase!</label>
                </div>
                
                <div class="tblcart" id="cart-container">
                    <!-- Cart will be loaded here via AJAX -->
                </div>
            </div>
            <div class="delete_all">
                <button id="btnclearall">Clear All </button>
            </div>
            <div class="summarry">
                <div class="promocode">
                    <h4>Discount  Codes</h4>
                    <label for="">Enter your coupon code if you have one</label><br><br>
                    <input type="text" name="" id=""><button class="btn btn-ghost">Apply Coupon</button><br><br><br>
                    <a href="category.php" class="btn btn-primary">Continue Shopping</a>
                </div>
                <div class="summaryvalues" id="grand-total-container">

                </div>
            </div>
        <?php
        }
    ?>

    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    <script src="js/cart.js"></script>
</body>