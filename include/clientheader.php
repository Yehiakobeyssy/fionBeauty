<?php
    $sql=$con->prepare('SELECT clientFname FROM   tblclient WHERE clientID  = ? ');
    $sql->execute(array($user_id));
    $checkuser = $sql->rowCount();
    if( $checkuser == 1){
        $res_name = $sql->fetch();
        $user_Account_name = $res_name['clientFname'];
    }else{
        $user_Account_name = 'My Account';
    }



    if(isset($_SESSION['cart'])){
        $cart_count = array_sum($_SESSION['cart']);
    }else{
        $cart_count = 0;
    }

    if($cart_count > 0 ){
        $dislay= 'block';
    }else{
        $dislay = 'none';
    }
?>

<link href='https://fonts.googleapis.com/css?family=Baloo Bhaijaan' rel='stylesheet'>
<link href='https://fonts.googleapis.com/css?family=Outfit' rel='stylesheet'>
<style>
/* ============================
   Navbar / Company Header
   ============================ */
.navbarcompany {
    display: flex;
    justify-content: space-around;
    align-items: center;
    gap: var(--space-5);
    border-bottom: 1px solid var(--color-primary);
    width: 100%;
    padding: 18px 7%;
    background-color: var(--color-white);
}

.companylogo {
    display: flex;
    align-items: center;
    gap: 8px;
}
.companylogo img {
    width: 40px;
    height: 40px;
}
.companylogo label {
    color: var(--color-primary);
    font-family: 'Baloo Bhaijaan', sans-serif;
    font-size: 16px;
    font-weight: 400;
    line-height: 24px;
    letter-spacing: 0.48px;
}

.nav_user ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 10px;
}
.nav_user li {
    display: inline-block;
}
.nav_user a {
    display: block;
    padding: var(--space-2);
    color: var(--color-black);
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    font-size: 16px;
    font-weight: 400;
    line-height: 24px;
}
.nav_user a:hover,
.nav_user a:focus {
    color: var(--color-primary-variant);
    text-decoration: underline;
}

/* ============================
   Search Component
   ============================ */
.search_component {
    position: relative;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 20px;
}
.search_component form {
    display: flex;
    width: 100%;
}
.input_select {
    
    display: flex;
    position: relative;
    border-radius: 4px;
    background: var(--color-bg);
    padding: 8px;
}
.input_select input {
    width: 350px;
    flex: 1;
    border: none;
    background: transparent;
    color: var(--color-dark);
    font-family: 'Outfit', sans-serif;
    font-size: 12px;
}
.input_select select {
    width: 100px;
    border: none;
    background: transparent;
    border-left: 1px solid var(--color-muted);
    padding: 0 8px;
}
.search_component button {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: none;
    background: var(--color-primary-variant);
    box-shadow: 0 4px 4px rgba(2, 230, 97, 0.29);
    cursor: pointer;
}
.dropdownsmartsearch {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: var(--color-bg);
    border-radius: 0 0 4px 4px;
    padding: 8px;
    display: none;
    z-index: 5;
}
.dropdownsmartsearch a {
    display: block;
    padding: 8px;
    text-decoration: none;
    color: var(--color-dark);
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

/* ============================
   User Account / Cart
   ============================ */
.useracountnav {
    display: flex;
    align-items: center;
    gap: var(--space-3);
}
.useracountnav label {
    color: var(--color-dark);
    font-family: 'Outfit', sans-serif;
    font-size: 12px;
    line-height: 18px;
}
.cart {
    position: relative;
    cursor: pointer;
}
.cartnumber {
    display: none;
    position: absolute;
    top: -10px;
    left: 15px;
    width: 23px;
    height: 23px;
    border-radius: 50%;
    background-color: var(--color-primary);
    color: var(--color-white) !important;
    text-align: center;
    font-size: 12px;
    line-height: 18px;
    padding: 2px 0;
}
.cartnumber label{
    color: var(--color-white)
}

/* ============================
   Popup / Flash Download
   ============================ */
.flash_download,
.flash_downloader {
    display: none;
    position: absolute;
    top: 120px;
    background: var(--color-card);
    border-radius: var(--radius);
    border: 1px solid #E6E6E6;
    width: 200px;
    flex-direction: column;
    padding-bottom: 12px;
    z-index: 6;
}
.flash_download ul,
.flash_downloader ul {
    list-style: none;
    margin: 0;
    padding: 0;
}
.flash_download ul li,
.flash_downloader ul li {
    padding: 12px 16px;
}
.flash_download a,
.flash_downloader a {
    text-decoration: none;
    color: var(--color-dark);
}
.flash_download span,
.flash_downloader span {
    font-family: 'Outfit', sans-serif;
    font-size: 14px;
    line-height: 21px;
    margin-left: 10px;
    color: #666;
}

/* ============================
   Popup Navbar (mobile)
   ============================ */
.popup{
    display: none;
}
.popupnavbar {
    display: none;
    position: absolute;
    top: 0;
    right: 0;
    width: 40%;
    height: 100vh;
    background: var(--color-white);
    box-shadow: -10px 0 10px -5px rgba(0,0,0,0.75);
    z-index: 6;
}
.popupnavbar img{
    width: 50px;
    height: 50px;
}
.popupnavbar .title {
    display: flex;
    align-items: center;
    justify-content: space-around;
    padding: 15px;
    border-bottom: 1px solid rgba(0,0,0,0.3);
}
.popupnavbar .title label {
    font-family: 'Baloo Bhaijaan', sans-serif;
    font-size: 24px;
    line-height: 24px;
    letter-spacing: 0.48px;
    color: var(--color-dark);
}


/* ============================
   Responsive Adjustments
   ============================ */
@media (max-width: 1366px) {
    .navbarcompany { padding: var(--space-4); justify-content: space-between; }
    .nav_user a { font-size: 14px; }
}
@media (max-width: 1279px) {
    .nav_user, .useracountnav { display: none; }
    .navbarcompany { flex-direction: column; align-items: flex-start; }
    .popup { display: block; position: absolute; top: 75px; right: 20px; }
    .companylogo { width: 100%; margin-left: 20px; margin-bottom: 5px; text-align: left; }
    .search_component { width: 100%; margin-left: 0; justify-content: center; }
    .input_select { width: 80%; }
    .input_select input { width: 100%; }
    .flash_download,.flash_downloader { top:200px !important}

}
@media  (max-width: 785px){
    .flash_download,.flash_downloader { top:250px !important}
    .popup { display: block; position: absolute; top: 110px; right: 20px; }
}
@media (max-width: 580px) {
    .popup { display: block; position: absolute; top: 140px; right: 20px; }
    
}
@media (max-width: 480px) {
    .popupnavbar { width: 60%; }
    .navbarcompany { padding: 12px 5%; }
    .popup { display: block; position: absolute; top: 170px; right: 20px; }
    
}

</style>
<div class="navbarcompany">
    <div class="companylogo">
        <img src="images/logo.png" alt="" srcset="">
        <label for="">Fion Beauty Supplies</label>
    </div>
    <div class="nav_user">
        <ul>
            <li ><a href="index.php" id="frmhome">Home</a></li>
            <li><a href="contactus.php" id="frmcontact">Contact</a></li>
            <li><a href="aboutus.php" id="frmabout">About</a></li>
            <li><a href="workshops.php" id="frmtraining">Training</a></li>
            <li><a href="login.php" id="frmlogin">Sign Up</a></li>
        </ul>
    </div>
    <div class="search_component">
            <div class="input_select">
                <input type="text" name="" id="txtsearch" class="keyword" placeholder="What are you looking for?">
                <select name="" id="">
                    <option value="">All</option>
                    
                </select>
            </div>
            <button id="searchButton" class="btnkeyword" >
                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                    <path d="M17.1666 16.6667L14.0186 13.513M15.7631 9.29824C15.7631 10.8802 15.1347 12.3974 14.0161 13.5161C12.8974 14.6347 11.3802 15.2632 9.79823 15.2632C8.21623 15.2632 6.69903 14.6347 5.5804 13.5161C4.46176 12.3974 3.83331 10.8802 3.83331 9.29824C3.83331 7.71625 4.46176 6.19905 5.5804 5.08041C6.69903 3.96178 8.21623 3.33333 9.79823 3.33333C11.3802 3.33333 12.8974 3.96178 14.0161 5.08041C15.1347 6.19905 15.7631 7.71625 15.7631 9.29824V9.29824Z" stroke="#F5F5F5" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
        <div class="dropdownsmartsearch">
            <div id="itemsContainer"></div>
        </div>
    </div>
    <div class="useracountnav">
        <div class="cart subnav">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M8.25 20.25C8.66421 20.25 9 19.9142 9 19.5C9 19.0858 8.66421 18.75 8.25 18.75C7.83579 18.75 7.5 19.0858 7.5 19.5C7.5 19.9142 7.83579 20.25 8.25 20.25Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M18.75 20.25C19.1642 20.25 19.5 19.9142 19.5 19.5C19.5 19.0858 19.1642 18.75 18.75 18.75C18.3358 18.75 18 19.0858 18 19.5C18 19.9142 18.3358 20.25 18.75 20.25Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2.25 3.75H5.25L7.5 16.5H19.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7.5 12.5H19.1925C19.2792 12.5001 19.3633 12.4701 19.4304 12.4151C19.4975 12.3601 19.5434 12.2836 19.5605 12.1986L20.9105 5.44859C20.9214 5.39417 20.92 5.338 20.9066 5.28414C20.8931 5.23029 20.8679 5.18009 20.8327 5.13717C20.7975 5.09426 20.7532 5.05969 20.703 5.03597C20.6528 5.01225 20.598 4.99996 20.5425 5H6" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <label for="">Cart</label>
            <div class="cartnumber" style="display:<?= $dislay ?>">
                <label for="" id="numberofitems"><?= $cart_count ?></label>
            </div>
        </div>
        <div class="user_acount subnav">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                <path d="M24 27V24.3333C24 22.9188 23.5224 21.5623 22.6722 20.5621C21.8221 19.5619 20.669 19 19.4667 19H11.5333C10.331 19 9.17795 19.5619 8.32778 20.5621C7.47762 21.5623 7 22.9188 7 24.3333V27" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.5 14C18.9853 14 21 11.9853 21 9.5C21 7.01472 18.9853 5 16.5 5C14.0147 5 12 7.01472 12 9.5C12 11.9853 14.0147 14 16.5 14Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <label for=""><?php echo $user_Account_name ?></label>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" class="opennavclinetbig">
                <path d="M8.24268 8.63333L11.5427 5.33333L12.4854 6.276L8.24268 10.5187L4.00002 6.276L4.94268 5.33333L8.24268 8.63333Z" fill="black"/>
            </svg>
            <div class="flash_download">
                <ul>
                    <li>
                        <a href="user/dashboard.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M18 18H10V12H18V18ZM8 18H0V8H8V18ZM18 10H10V0H18V10ZM8 6H0V0H8V6Z" fill="#CCCCCC"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="user/orderhistory.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M0 16V8.99998H7L3.783 12.22C4.33247 12.7819 4.98837 13.2286 5.71241 13.5343C6.43644 13.8399 7.21411 13.9982 8 14C9.23925 13.9981 10.4475 13.6126 11.4589 12.8964C12.4702 12.1802 13.2349 11.1684 13.648 9.99998H13.666C13.78 9.67498 13.867 9.33998 13.925 8.99998H15.937C15.6934 10.9332 14.7527 12.7111 13.2913 13.9999C11.83 15.2887 9.9485 15.9999 8 16H7.99C6.93982 16.0031 5.89944 15.7979 4.9291 15.3963C3.95876 14.9946 3.07772 14.4044 2.337 13.66L0 16ZM2.074 6.99998H0.0619998C0.305476 5.06745 1.24564 3.29013 2.70616 2.00138C4.16667 0.712642 6.04719 0.00101454 7.995 -2.12263e-05H8C9.05036 -0.00334717 10.0909 0.201765 11.0615 0.603435C12.032 1.0051 12.9132 1.59535 13.654 2.33998L16 -2.12263e-05V6.99998H9L12.222 3.77998C11.672 3.21745 11.0153 2.77029 10.2903 2.46465C9.56537 2.15901 8.78674 2.00104 8 1.99998C6.76074 2.00181 5.55246 2.38732 4.54114 3.10355C3.52982 3.81978 2.76508 4.8316 2.352 5.99998H2.334C2.219 6.32498 2.132 6.65998 2.075 6.99998H2.074Z" fill="#CCCCCC"/>
                            </svg>
                            <span>Order History</span>
                        </a>
                    </li>
                    <li>
                        <a href="user/traininghistory.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#CCCCCC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <!-- Makeup Brush -->
                                <path d="M2 22l2-2 6-6 2 2-6 6-2 2z"/>
                                <path d="M14.5 5.5l4 4"/>
                                <path d="M16 2c1.333 1.333 2 3 2 3s-1.667.667-3 2"/>
                                
                                <!-- Palette -->
                                <circle cx="17" cy="17" r="3"/>
                                <circle cx="19" cy="14" r="1"/>
                                <circle cx="15" cy="16" r="1"/>
                                <circle cx="18" cy="19" r="1"/>
                                
                                <!-- Lipstick -->
                                <rect x="7" y="4" width="2" height="6" rx="0.5"/>
                                <path d="M7 4l1-2 1 2"/>
                            </svg>
                            <span>Training</span>
                        </a>

                    </li>
                    <li>
                        <a href="user/wishlist.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M11.9997 21.0542C-7.99987 10.0001 6.00011 -1.99991 11.9997 5.58815C18.0001 -1.99991 32.0001 10.0001 11.9997 21.0542Z" stroke="#CCCCCC" stroke-width="1.5"/>
                            </svg>
                            <span>Wishlist</span>
                        </a>
                    </li>
                    <li>
                        <a href="user/info.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M11.8199 20H8.17988C7.95182 20 7.73059 19.9221 7.55289 19.7792C7.37519 19.6362 7.25169 19.4368 7.20288 19.214L6.79588 17.33C6.25294 17.0921 5.73812 16.7946 5.26088 16.443L3.42388 17.028C3.20645 17.0973 2.97183 17.0902 2.759 17.0078C2.54617 16.9254 2.36794 16.7727 2.25388 16.575L0.429884 13.424C0.31703 13.2261 0.274667 12.9958 0.309727 12.7708C0.344787 12.5457 0.455193 12.3392 0.622884 12.185L2.04788 10.885C1.98308 10.2961 1.98308 9.70189 2.04788 9.113L0.622884 7.816C0.454956 7.66177 0.344399 7.45507 0.309333 7.22978C0.274268 7.00449 0.316774 6.77397 0.429884 6.576L2.24988 3.423C2.36394 3.22532 2.54218 3.07259 2.755 2.99019C2.96783 2.90778 3.20245 2.90066 3.41988 2.97L5.25688 3.555C5.50088 3.375 5.75488 3.207 6.01788 3.055C6.26988 2.913 6.52988 2.784 6.79588 2.669L7.20388 0.787C7.25246 0.564198 7.37572 0.364688 7.55323 0.221549C7.73074 0.0784098 7.95185 0.000239966 8.17988 0H11.8199C12.0479 0.000239966 12.269 0.0784098 12.4465 0.221549C12.6241 0.364688 12.7473 0.564198 12.7959 0.787L13.2079 2.67C13.7505 2.9079 14.265 3.20539 14.7419 3.557L16.5799 2.972C16.7972 2.90292 17.0316 2.91017 17.2442 2.99256C17.4568 3.07495 17.6349 3.22753 17.7489 3.425L19.5689 6.578C19.8019 6.985 19.7209 7.5 19.3759 7.817L17.9509 9.117C18.0157 9.70589 18.0157 10.3001 17.9509 10.889L19.3759 12.189C19.7209 12.507 19.8009 13.021 19.5689 13.428L17.7489 16.581C17.6349 16.7785 17.4568 16.931 17.2442 17.0134C17.0316 17.0958 16.7972 17.1031 16.5799 17.034L14.7419 16.449C14.2651 16.8004 13.7506 17.0976 13.2079 17.335L12.7959 19.214C12.7471 19.4366 12.6238 19.6359 12.4463 19.7788C12.2688 19.9218 12.0478 19.9998 11.8199 20ZM5.61988 14.229L6.43988 14.829C6.62488 14.965 6.81788 15.09 7.01688 15.204C7.20488 15.313 7.39788 15.411 7.59588 15.5L8.52888 15.909L8.98588 18H11.0159L11.4729 15.908L12.4059 15.499C12.8129 15.319 13.1999 15.096 13.5589 14.833L14.3799 14.233L16.4209 14.883L17.4359 13.125L15.8529 11.682L15.9649 10.67C16.0149 10.227 16.0149 9.78 15.9649 9.338L15.8529 8.326L17.4369 6.88L16.4209 5.121L14.3799 5.771L13.5589 5.171C13.1997 4.90669 12.8131 4.68173 12.4059 4.5L11.4729 4.091L11.0159 2H8.98588L8.52689 4.092L7.59588 4.5C7.18807 4.67861 6.80136 4.90198 6.44288 5.166L5.62188 5.766L3.58188 5.116L2.56488 6.88L4.14788 8.321L4.03588 9.334C3.98588 9.777 3.98588 10.224 4.03588 10.666L4.14788 11.678L2.56488 13.121L3.57988 14.879L5.61988 14.229ZM9.99588 14C8.93502 14 7.9176 13.5786 7.16746 12.8284C6.41731 12.0783 5.99588 11.0609 5.99588 10C5.99588 8.93913 6.41731 7.92172 7.16746 7.17157C7.9176 6.42143 8.93502 6 9.99588 6C11.0568 6 12.0742 6.42143 12.8243 7.17157C13.5745 7.92172 13.9959 8.93913 13.9959 10C13.9959 11.0609 13.5745 12.0783 12.8243 12.8284C12.0742 13.5786 11.0568 14 9.99588 14ZM9.99588 8C9.60424 8.0004 9.22133 8.11577 8.89467 8.33181C8.568 8.54785 8.31195 8.85505 8.15828 9.21528C8.00462 9.57552 7.9601 9.97295 8.03026 10.3583C8.10041 10.7436 8.28215 11.0998 8.55293 11.3828C8.8237 11.6657 9.17159 11.863 9.55344 11.95C9.93529 12.037 10.3343 12.01 10.7009 11.8724C11.0676 11.7347 11.3858 11.4924 11.616 11.1756C11.8462 10.8587 11.9783 10.4812 11.9959 10.09V10.49V10C11.9959 9.46957 11.7852 8.96086 11.4101 8.58579C11.035 8.21071 10.5263 8 9.99588 8Z" fill="#CCCCCC"/>
                            </svg>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="ajax/logout.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M16 18H7C6.46957 18 5.96086 17.7893 5.58579 17.4142C5.21071 17.0391 5 16.5304 5 16V12H7V16H16V2H7V6H5V2C5 1.46957 5.21071 0.960859 5.58579 0.585786C5.96086 0.210714 6.46957 0 7 0H16C16.5304 0 17.0391 0.210714 17.4142 0.585786C17.7893 0.960859 18 1.46957 18 2V16C18 16.5304 17.7893 17.0391 17.4142 17.4142C17.0391 17.7893 16.5304 18 16 18ZM9 13V10H0V8H9V5L14 9L9 13Z" fill="#CCCCCC"/>
                            </svg>
                            <span>Log-out</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="help subnav">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                <path d="M9.028 14.23C9.24933 14.23 9.436 14.154 9.588 14.002C9.73933 13.8487 9.815 13.662 9.815 13.442C9.815 13.2207 9.73867 13.034 9.586 12.882C9.43333 12.73 9.24667 12.654 9.026 12.654C8.80467 12.654 8.618 12.7303 8.466 12.883C8.314 13.0357 8.238 13.2223 8.238 13.443C8.238 13.6643 8.31467 13.851 8.468 14.003C8.62 14.155 8.80667 14.231 9.028 14.231V14.23ZM8.512 10.92H9.477C9.50233 10.4847 9.584 10.1313 9.722 9.86C9.86 9.588 10.1437 9.24133 10.573 8.82C11.0197 8.37333 11.3517 7.97333 11.569 7.62C11.787 7.26667 11.896 6.858 11.896 6.394C11.896 5.606 11.6193 4.978 11.066 4.51C10.512 4.042 9.85667 3.808 9.1 3.808C8.38067 3.808 7.77 4.00333 7.268 4.394C6.766 4.78533 6.39833 5.23467 6.165 5.742L7.085 6.123C7.245 5.759 7.474 5.43733 7.772 5.158C8.07 4.878 8.5 4.738 9.062 4.738C9.71067 4.738 10.184 4.916 10.482 5.272C10.7813 5.628 10.931 6.01933 10.931 6.446C10.931 6.79267 10.8373 7.102 10.65 7.374C10.4633 7.64667 10.22 7.92067 9.92 8.196C9.34 8.73067 8.96 9.184 8.78 9.556C8.60133 9.92733 8.512 10.3817 8.512 10.919V10.92ZM9.003 18C7.759 18 6.589 17.764 5.493 17.292C4.39767 16.8193 3.44467 16.178 2.634 15.368C1.82333 14.5587 1.18167 13.6067 0.709 12.512C0.236333 11.4173 0 10.2477 0 9.003C0 7.759 0.236 6.589 0.708 5.493C1.18067 4.39767 1.822 3.44467 2.632 2.634C3.44133 1.82333 4.39333 1.18167 5.488 0.709C6.58267 0.236333 7.75233 0 8.997 0C10.241 0 11.411 0.236 12.507 0.708C13.6023 1.18067 14.5553 1.822 15.366 2.632C16.1767 3.44133 16.8183 4.39333 17.291 5.488C17.7637 6.58267 18 7.75233 18 8.997C18 10.241 17.764 11.411 17.292 12.507C16.8193 13.6023 16.178 14.5553 15.368 15.366C14.5587 16.1767 13.6067 16.8183 12.512 17.291C11.4173 17.7637 10.2477 18 9.003 18ZM9 17C11.2333 17 13.125 16.225 14.675 14.675C16.225 13.125 17 11.2333 17 9C17 6.76667 16.225 4.875 14.675 3.325C13.125 1.775 11.2333 1 9 1C6.76667 1 4.875 1.775 3.325 3.325C1.775 4.875 1 6.76667 1 9C1 11.2333 1.775 13.125 3.325 14.675C4.875 16.225 6.76667 17 9 17Z" fill="black"/>
            </svg>
            <label for="">Help</label>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8.24268 8.63333L11.5427 5.33333L12.4854 6.276L8.24268 10.5187L4.00002 6.276L4.94268 5.33333L8.24268 8.63333Z" fill="black"/>
            </svg>
        </div>
    </div>
    <div class="popup" id="openBtn" >
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M4 6H20M7 12H20M10 18H20" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
</div>
<div class="popupnavbar">
    <div class="title">
        <img src="images/logo.png" alt="" srcset="">
        <label for="">Fion Beauty Supplies</label>
    </div>
    <div class="nav_user_pop">
        <ul>
            <li ><a href="index.php">Home</a></li>
            <li><a href="contactus.php">Contact</a></li>
            <li><a href="aboutus.php">About</a></li>
            <li><a href="workshops.php">Training</a></li>
            <li><a href="login.php" >Sign Up</a></li>
        </ul>
    </div>
    <div class="useracountnav_pop">
        <div class="cart subnav_pop">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M8.25 20.25C8.66421 20.25 9 19.9142 9 19.5C9 19.0858 8.66421 18.75 8.25 18.75C7.83579 18.75 7.5 19.0858 7.5 19.5C7.5 19.9142 7.83579 20.25 8.25 20.25Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M18.75 20.25C19.1642 20.25 19.5 19.9142 19.5 19.5C19.5 19.0858 19.1642 18.75 18.75 18.75C18.3358 18.75 18 19.0858 18 19.5C18 19.9142 18.3358 20.25 18.75 20.25Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2.25 3.75H5.25L7.5 16.5H19.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7.5 12.5H19.1925C19.2792 12.5001 19.3633 12.4701 19.4304 12.4151C19.4975 12.3601 19.5434 12.2836 19.5605 12.1986L20.9105 5.44859C20.9214 5.39417 20.92 5.338 20.9066 5.28414C20.8931 5.23029 20.8679 5.18009 20.8327 5.13717C20.7975 5.09426 20.7532 5.05969 20.703 5.03597C20.6528 5.01225 20.598 4.99996 20.5425 5H6" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <label for="">Cart</label>
        </div>
        <div class="user_acount subnav_pop">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                <path d="M24 27V24.3333C24 22.9188 23.5224 21.5623 22.6722 20.5621C21.8221 19.5619 20.669 19 19.4667 19H11.5333C10.331 19 9.17795 19.5619 8.32778 20.5621C7.47762 21.5623 7 22.9188 7 24.3333V27" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.5 14C18.9853 14 21 11.9853 21 9.5C21 7.01472 18.9853 5 16.5 5C14.0147 5 12 7.01472 12 9.5C12 11.9853 14.0147 14 16.5 14Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <label for=""><?php echo $user_Account_name ?></label>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8.24268 8.63333L11.5427 5.33333L12.4854 6.276L8.24268 10.5187L4.00002 6.276L4.94268 5.33333L8.24268 8.63333Z" fill="black"/>
            </svg>
        </div>
        <div class="dietilclient">
        <div class="flash_downloader">
                <ul>
                    <li>
                        <a href="user/dashboard.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M18 18H10V12H18V18ZM8 18H0V8H8V18ZM18 10H10V0H18V10ZM8 6H0V0H8V6Z" fill="#CCCCCC"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="user/orderhistory.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M0 16V8.99998H7L3.783 12.22C4.33247 12.7819 4.98837 13.2286 5.71241 13.5343C6.43644 13.8399 7.21411 13.9982 8 14C9.23925 13.9981 10.4475 13.6126 11.4589 12.8964C12.4702 12.1802 13.2349 11.1684 13.648 9.99998H13.666C13.78 9.67498 13.867 9.33998 13.925 8.99998H15.937C15.6934 10.9332 14.7527 12.7111 13.2913 13.9999C11.83 15.2887 9.9485 15.9999 8 16H7.99C6.93982 16.0031 5.89944 15.7979 4.9291 15.3963C3.95876 14.9946 3.07772 14.4044 2.337 13.66L0 16ZM2.074 6.99998H0.0619998C0.305476 5.06745 1.24564 3.29013 2.70616 2.00138C4.16667 0.712642 6.04719 0.00101454 7.995 -2.12263e-05H8C9.05036 -0.00334717 10.0909 0.201765 11.0615 0.603435C12.032 1.0051 12.9132 1.59535 13.654 2.33998L16 -2.12263e-05V6.99998H9L12.222 3.77998C11.672 3.21745 11.0153 2.77029 10.2903 2.46465C9.56537 2.15901 8.78674 2.00104 8 1.99998C6.76074 2.00181 5.55246 2.38732 4.54114 3.10355C3.52982 3.81978 2.76508 4.8316 2.352 5.99998H2.334C2.219 6.32498 2.132 6.65998 2.075 6.99998H2.074Z" fill="#CCCCCC"/>
                            </svg>
                            <span>Order History</span>
                        </a>
                    </li>
                    <li>
                        <a href="user/traininghistory.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#CCCCCC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <!-- Makeup Brush -->
                                <path d="M2 22l2-2 6-6 2 2-6 6-2 2z"/>
                                <path d="M14.5 5.5l4 4"/>
                                <path d="M16 2c1.333 1.333 2 3 2 3s-1.667.667-3 2"/>
                                
                                <!-- Palette -->
                                <circle cx="17" cy="17" r="3"/>
                                <circle cx="19" cy="14" r="1"/>
                                <circle cx="15" cy="16" r="1"/>
                                <circle cx="18" cy="19" r="1"/>
                                
                                <!-- Lipstick -->
                                <rect x="7" y="4" width="2" height="6" rx="0.5"/>
                                <path d="M7 4l1-2 1 2"/>
                            </svg>
                            <span>Training</span>
                        </a>

                    </li>
                    <li>
                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M11.9997 21.0542C-7.99987 10.0001 6.00011 -1.99991 11.9997 5.58815C18.0001 -1.99991 32.0001 10.0001 11.9997 21.0542Z" stroke="#CCCCCC" stroke-width="1.5"/>
                            </svg>
                            <span>Wishlist</span>
                        </a>
                    </li>
                    <li>
                        <a href="user/info.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M11.8199 20H8.17988C7.95182 20 7.73059 19.9221 7.55289 19.7792C7.37519 19.6362 7.25169 19.4368 7.20288 19.214L6.79588 17.33C6.25294 17.0921 5.73812 16.7946 5.26088 16.443L3.42388 17.028C3.20645 17.0973 2.97183 17.0902 2.759 17.0078C2.54617 16.9254 2.36794 16.7727 2.25388 16.575L0.429884 13.424C0.31703 13.2261 0.274667 12.9958 0.309727 12.7708C0.344787 12.5457 0.455193 12.3392 0.622884 12.185L2.04788 10.885C1.98308 10.2961 1.98308 9.70189 2.04788 9.113L0.622884 7.816C0.454956 7.66177 0.344399 7.45507 0.309333 7.22978C0.274268 7.00449 0.316774 6.77397 0.429884 6.576L2.24988 3.423C2.36394 3.22532 2.54218 3.07259 2.755 2.99019C2.96783 2.90778 3.20245 2.90066 3.41988 2.97L5.25688 3.555C5.50088 3.375 5.75488 3.207 6.01788 3.055C6.26988 2.913 6.52988 2.784 6.79588 2.669L7.20388 0.787C7.25246 0.564198 7.37572 0.364688 7.55323 0.221549C7.73074 0.0784098 7.95185 0.000239966 8.17988 0H11.8199C12.0479 0.000239966 12.269 0.0784098 12.4465 0.221549C12.6241 0.364688 12.7473 0.564198 12.7959 0.787L13.2079 2.67C13.7505 2.9079 14.265 3.20539 14.7419 3.557L16.5799 2.972C16.7972 2.90292 17.0316 2.91017 17.2442 2.99256C17.4568 3.07495 17.6349 3.22753 17.7489 3.425L19.5689 6.578C19.8019 6.985 19.7209 7.5 19.3759 7.817L17.9509 9.117C18.0157 9.70589 18.0157 10.3001 17.9509 10.889L19.3759 12.189C19.7209 12.507 19.8009 13.021 19.5689 13.428L17.7489 16.581C17.6349 16.7785 17.4568 16.931 17.2442 17.0134C17.0316 17.0958 16.7972 17.1031 16.5799 17.034L14.7419 16.449C14.2651 16.8004 13.7506 17.0976 13.2079 17.335L12.7959 19.214C12.7471 19.4366 12.6238 19.6359 12.4463 19.7788C12.2688 19.9218 12.0478 19.9998 11.8199 20ZM5.61988 14.229L6.43988 14.829C6.62488 14.965 6.81788 15.09 7.01688 15.204C7.20488 15.313 7.39788 15.411 7.59588 15.5L8.52888 15.909L8.98588 18H11.0159L11.4729 15.908L12.4059 15.499C12.8129 15.319 13.1999 15.096 13.5589 14.833L14.3799 14.233L16.4209 14.883L17.4359 13.125L15.8529 11.682L15.9649 10.67C16.0149 10.227 16.0149 9.78 15.9649 9.338L15.8529 8.326L17.4369 6.88L16.4209 5.121L14.3799 5.771L13.5589 5.171C13.1997 4.90669 12.8131 4.68173 12.4059 4.5L11.4729 4.091L11.0159 2H8.98588L8.52689 4.092L7.59588 4.5C7.18807 4.67861 6.80136 4.90198 6.44288 5.166L5.62188 5.766L3.58188 5.116L2.56488 6.88L4.14788 8.321L4.03588 9.334C3.98588 9.777 3.98588 10.224 4.03588 10.666L4.14788 11.678L2.56488 13.121L3.57988 14.879L5.61988 14.229ZM9.99588 14C8.93502 14 7.9176 13.5786 7.16746 12.8284C6.41731 12.0783 5.99588 11.0609 5.99588 10C5.99588 8.93913 6.41731 7.92172 7.16746 7.17157C7.9176 6.42143 8.93502 6 9.99588 6C11.0568 6 12.0742 6.42143 12.8243 7.17157C13.5745 7.92172 13.9959 8.93913 13.9959 10C13.9959 11.0609 13.5745 12.0783 12.8243 12.8284C12.0742 13.5786 11.0568 14 9.99588 14ZM9.99588 8C9.60424 8.0004 9.22133 8.11577 8.89467 8.33181C8.568 8.54785 8.31195 8.85505 8.15828 9.21528C8.00462 9.57552 7.9601 9.97295 8.03026 10.3583C8.10041 10.7436 8.28215 11.0998 8.55293 11.3828C8.8237 11.6657 9.17159 11.863 9.55344 11.95C9.93529 12.037 10.3343 12.01 10.7009 11.8724C11.0676 11.7347 11.3858 11.4924 11.616 11.1756C11.8462 10.8587 11.9783 10.4812 11.9959 10.09V10.49V10C11.9959 9.46957 11.7852 8.96086 11.4101 8.58579C11.035 8.21071 10.5263 8 9.99588 8Z" fill="#CCCCCC"/>
                            </svg>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="ajax/logout.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M16 18H7C6.46957 18 5.96086 17.7893 5.58579 17.4142C5.21071 17.0391 5 16.5304 5 16V12H7V16H16V2H7V6H5V2C5 1.46957 5.21071 0.960859 5.58579 0.585786C5.96086 0.210714 6.46957 0 7 0H16C16.5304 0 17.0391 0.210714 17.4142 0.585786C17.7893 0.960859 18 1.46957 18 2V16C18 16.5304 17.7893 17.0391 17.4142 17.4142C17.0391 17.7893 16.5304 18 16 18ZM9 13V10H0V8H9V5L14 9L9 13Z" fill="#CCCCCC"/>
                            </svg>
                            <span>Log-out</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="help subnav_pop">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                <path d="M9.028 14.23C9.24933 14.23 9.436 14.154 9.588 14.002C9.73933 13.8487 9.815 13.662 9.815 13.442C9.815 13.2207 9.73867 13.034 9.586 12.882C9.43333 12.73 9.24667 12.654 9.026 12.654C8.80467 12.654 8.618 12.7303 8.466 12.883C8.314 13.0357 8.238 13.2223 8.238 13.443C8.238 13.6643 8.31467 13.851 8.468 14.003C8.62 14.155 8.80667 14.231 9.028 14.231V14.23ZM8.512 10.92H9.477C9.50233 10.4847 9.584 10.1313 9.722 9.86C9.86 9.588 10.1437 9.24133 10.573 8.82C11.0197 8.37333 11.3517 7.97333 11.569 7.62C11.787 7.26667 11.896 6.858 11.896 6.394C11.896 5.606 11.6193 4.978 11.066 4.51C10.512 4.042 9.85667 3.808 9.1 3.808C8.38067 3.808 7.77 4.00333 7.268 4.394C6.766 4.78533 6.39833 5.23467 6.165 5.742L7.085 6.123C7.245 5.759 7.474 5.43733 7.772 5.158C8.07 4.878 8.5 4.738 9.062 4.738C9.71067 4.738 10.184 4.916 10.482 5.272C10.7813 5.628 10.931 6.01933 10.931 6.446C10.931 6.79267 10.8373 7.102 10.65 7.374C10.4633 7.64667 10.22 7.92067 9.92 8.196C9.34 8.73067 8.96 9.184 8.78 9.556C8.60133 9.92733 8.512 10.3817 8.512 10.919V10.92ZM9.003 18C7.759 18 6.589 17.764 5.493 17.292C4.39767 16.8193 3.44467 16.178 2.634 15.368C1.82333 14.5587 1.18167 13.6067 0.709 12.512C0.236333 11.4173 0 10.2477 0 9.003C0 7.759 0.236 6.589 0.708 5.493C1.18067 4.39767 1.822 3.44467 2.632 2.634C3.44133 1.82333 4.39333 1.18167 5.488 0.709C6.58267 0.236333 7.75233 0 8.997 0C10.241 0 11.411 0.236 12.507 0.708C13.6023 1.18067 14.5553 1.822 15.366 2.632C16.1767 3.44133 16.8183 4.39333 17.291 5.488C17.7637 6.58267 18 7.75233 18 8.997C18 10.241 17.764 11.411 17.292 12.507C16.8193 13.6023 16.178 14.5553 15.368 15.366C14.5587 16.1767 13.6067 16.8183 12.512 17.291C11.4173 17.7637 10.2477 18 9.003 18ZM9 17C11.2333 17 13.125 16.225 14.675 14.675C16.225 13.125 17 11.2333 17 9C17 6.76667 16.225 4.875 14.675 3.325C13.125 1.775 11.2333 1 9 1C6.76667 1 4.875 1.775 3.325 3.325C1.775 4.875 1 6.76667 1 9C1 11.2333 1.775 13.125 3.325 14.675C4.875 16.225 6.76667 17 9 17Z" fill="black"/>
            </svg>
            <label for="">Help</label>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8.24268 8.63333L11.5427 5.33333L12.4854 6.276L8.24268 10.5187L4.00002 6.276L4.94268 5.33333L8.24268 8.63333Z" fill="black"/>
            </svg>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        
        $('.flash_download').hide();
        $('#frmhome').css({color: 'var(--color-primary)'});
        $('#frmhome').css({'text-decoration': 'underline'});
        if(window.location.href.indexOf("index")>0){
            $('#frmhome').css({color: 'var(--color-primary)'});
            $('#frmhome').css({'text-decoration': 'underline'});
        }else{
            $('#frmhome').css({color: '#000'});
            $('#frmhome').css({'text-decoration': 'none'});
        }
        // frmlogin
        if(window.location.href.indexOf("login.php")>0){
            $('#frmlogin').css({color: 'var(--color-primary)'});
            $('#frmlogin').css({'text-decoration': 'underline'});
        }else{
            $('#frmlogin').css({color: '#000'});
            $('#frmlogin').css({'text-decoration': 'none'});
        }

        // frmcontact
        if(window.location.href.indexOf("contactus.php")>0){
            $('#frmcontact').css({color: 'var(--color-primary)'});
            $('#frmcontact').css({'text-decoration': 'underline'});
        }else{
            $('#frmcontact').css({color: '#000'});
            $('#frmcontact').css({'text-decoration': 'none'});
        }

        //frmabout
        if(window.location.href.indexOf("aboutus.php")>0){
            $('#frmabout').css({color: 'var(--color-primary)'});
            $('#frmabout').css({'text-decoration': 'underline'});
        }else{
            $('#frmabout').css({color: '#000'});
            $('#frmabout').css({'text-decoration': 'none'});
        }

        //<li><a href="workshops.php" id="frmtraining">Training</a></li>
        if(window.location.href.indexOf("workshops.php")>0){
            $('#frmtraining').css({color: 'var(--color-primary)'});
            $('#frmtraining').css({'text-decoration': 'underline'});
        }else{
            $('#frmtraining').css({color: '#000'});
            $('#frmtraining').css({'text-decoration': 'none'});
        }
//smart search         
        // ===== Smart Search Autocomplete =====

// Load history from localStorage
var historyItems = JSON.parse(localStorage.getItem('smartsearch')) || [];

// Show dropdown on typing
$('.input_select input').on('keyup', function () {
    var searchTerm = $(this).val().trim().toLowerCase();
    $('#searchButton').val(searchTerm);

    if (!searchTerm) {
        displayDropdown([]);
        return;
    }

    // Fetch DB items via AJAX
    $.ajax({
        url: 'ajax/displayitems.php',
        method: 'GET',
        data: { keyword: searchTerm },
        dataType: 'json',
        success: function (resp) {
            // Adjust if your PHP returns { success: true, data: [...] }
            var apiItems = resp.data || resp || [];

            // Filter DB items to only those starting with searchTerm
            var dbItems = apiItems
                .map(item => item.itmName)
                .filter(name => name.toLowerCase().startsWith(searchTerm));

            // Filter history items to only those starting with searchTerm
            var filteredHistory = historyItems
                .filter(item => item.toLowerCase().startsWith(searchTerm));

            // Combine DB first, then history, remove duplicates
            var combined = Array.from(new Set(dbItems.concat(filteredHistory)));

            // Display in dropdown (max 10)
            displayDropdown(combined);
        },
        error: function (err) {
            console.error('API error', err);
            displayDropdown([]);
        }
    });
});

// Save typed item to history on blur
$('#txtsearch').on('blur', function () {
    var newItem = $(this).val().trim();
    if (newItem && !historyItems.includes(newItem)) {
        historyItems.unshift(newItem);       // Add to front
        if (historyItems.length > 20) {      // Limit to 20 items
            historyItems = historyItems.slice(0, 20);
        }
        localStorage.setItem('smartsearch', JSON.stringify(historyItems));
    }
});

// Display the dropdown items
function displayDropdown(items) {
    var container = document.getElementById('itemsContainer');
    container.innerHTML = '';

    if (!items.length) {
        $('.dropdownsmartsearch').hide();
        return;
    }

    items.slice(0, 10).forEach(function (item) {
        var link = document.createElement('a');
        link.href = 'category.php?keyword=' + encodeURIComponent(item);
        link.className = 'result-item';

        // Use different icon: DB vs history
        var icon = document.createElement('i');
        if (historyItems.includes(item)) {
            icon.className = 'fa-solid fa-clock-rotate-left'; // history
        } else {
            icon.className = 'fa-solid fa-magnifying-glass';   // DB item
        }

        link.appendChild(icon);
        link.appendChild(document.createTextNode(' ' + item));
        container.appendChild(link);
    });

    $('.dropdownsmartsearch').show();
}
$('#txtsearch').on('keydown', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        var firstItem = document.querySelector('#itemsContainer a');
        if (firstItem) {
            window.location.href = firstItem.href;
        }
        e.preventDefault(); // prevent form submission if inside a form
    }
});

// Navigate to category page on click
document.getElementById('itemsContainer').addEventListener('mousedown', function (event) {
    var target = event.target.closest('a');
    if (!target) return;
    event.preventDefault();
    window.location.href = target.href;
});

// Hide dropdown when mouse leaves
$('.dropdownsmartsearch').on('mouseleave', function() {
    $(this).hide();
});

// Optional: show dropdown on focus
$('.input_select input').on('focus', function() {
    if ($('#txtsearch').val().trim() !== '') {
        $('.dropdownsmartsearch').show();
    }
});

// stop smart search 

$('.opennavclinetbig').click(function(){
    $('.flash_download').slideToggle(500);
})
$('.user_acount').click(function(){
    $('.flash_downloader').slideToggle(500);
})

$.ajax({
    type: 'POST',
    url: 'ajax/count_cart.php',
    success: function(response) {
        // Update the content of #numberofitems with the count
        $('#numberofitems').text(response);
        if (parseInt(response) === 0) {
            $('.cartnumber').hide();
        } else {
            $('.cartnumber').show();
        }

    },
    error: function(error) {
        // Handle errors, if any
        console.error(error);
    }
});


$('.cart').click(function(){
    location.href="cart.php";
})

   
});
const openBtn = document.getElementById('openBtn');
const popupNavbar = document.querySelector('.popupnavbar');

openBtn.addEventListener('click', (e) => {
    // لمنع انتشار الحدث إلى document
    e.stopPropagation(); 
    if (popupNavbar.style.display === 'block') {
        popupNavbar.style.display = 'none';
    } else {
        popupNavbar.style.display = 'block';
    }
});

// إخفاء الـ popup عند النقر خارجها
document.addEventListener('click', (e) => {
    if (!popupNavbar.contains(e.target) && e.target !== openBtn) {
        popupNavbar.style.display = 'none';
    }
});

// لمنع النقر داخل الـ popup من إخفائه
popupNavbar.addEventListener('click', (e) => {
    e.stopPropagation();
});
</script>
