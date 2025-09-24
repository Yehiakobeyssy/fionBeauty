<style>
    footer{
        margin-top: 40px;
        background-color: var(--color-primary);
        padding: 20px;
        display: flex;
        justify-content: space-around;
    }
    .footer1{
        background-color: var(--color-primary);
        color: var(--color-bg);
        text-align: center;
        padding: 15px;
        border-top: var(--color-bg) 1px solid;
    }
    .footer_logo{
        display: flex;
        flex-direction: column;
    }
    .footer_logo img{
        width: 75px;
        height: 75px;
    }
    .footer_logo label{
        color: var(--color-bg);
        font-size: 20px;
        font-style: normal;
        font-weight: 400;
        line-height: 24px; /* 120% */
        letter-spacing: 0.6px;
    }

    .support,.account_links,.quick_links{
        color: var(--color-bg);
        display: flex;
        flex-direction: column;
    }
    .support h4,.account_links h4,.quick_links h4,.folowus h4{
        color: var(--color-bg);
        font-family: Outfit;
        font-size: 20px;
        font-style: normal;
        font-weight: 500;
        line-height: 28px;
        margin-bottom: 20px;
    }
    .support label{
        margin: 5px 0;
        font-family: Outfit;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: 24px;
    }
    .account_links a,.quick_links a{
        color: var(--color-bg);
        margin: 5px 0;
        font-family: Outfit;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: 24px;
    }
    .folowus a{
        margin: 0 7px;
    }
    /* لأجهزة الموبايل (شاشات صغيرة) */
@media (max-width: 768px) {
    footer {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .footer_logo {
        margin-bottom: 20px;
        align-items: center;
    }

    .support, 
    .account_links, 
    .quick_links, 
    .folowus {
        margin-bottom: 20px;
        width: 100%;
        align-items: center;
    }

    .support h4,
    .account_links h4,
    .quick_links h4,
    .folowus h4 {
        margin-bottom: 10px;
    }

    .folowus .logos {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }
}

/* لأجهزة التابلت (شاشات متوسطة) */
@media (max-width: 1024px) {
    footer {
        flex-wrap: wrap;
        justify-content: center;
    }

    .support, 
    .account_links, 
    .quick_links, 
    .folowus {
        flex: 1 1 45%;
        margin: 15px;
    }
}

</style>
<?php
    $sql=$con->prepare('SELECT companyAdd,companyPhone,companyEmail FROM    tblsetting WHERE seetingID   = 1 ');
    $sql->execute();
    $supportinfo=$sql->fetch();
    
?>
<footer>
    <div class="footer_logo">
        <img src="images/logo_white.png" alt="" srcset="">
        <label for="">fionBeauty</label>
    </div>
    <div class="support">
        <h4>Support</h4>
        <label for=""><?php echo $supportinfo['companyAdd'] ?></label>
        <label for=""><?php echo $supportinfo['companyEmail'] ?></label>
        <label for=""><?php echo $supportinfo['companyPhone'] ?></label>
    </div>
    <div class="account_links">
        <h4>Account</h4>
        <a href="">My Account</a>
        <a href="">Login /Register</a>
        <a href="">Cart</a>
        <a href="">Wishlist</a>
        <a href="">Shop</a>
    </div>
    <div class="quick_links">
        <h4>Quick Link</h4>
        <a href="">Privacy Policy</a>
        <a href="">Terms Of Use</a>
        <a href="">FAQ</a>
        <a href="">Contact</a>
    </div>
    <div class="folowus">
        <h4>Follow Us</h4>
        <div class="logos">
            <a href="">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 24 24">
                    <path d="M22.675 0h-21.35C.597 0 0 .6 0 1.333v21.333C0 23.4.597 24 1.325 24h11.495V14.706H9.691v-3.62h3.129V8.413c0-3.1 1.894-4.788 4.659-4.788 1.325 0 2.466.097 2.797.141v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.31h3.587l-.467 3.62h-3.12V24h6.116C23.403 24 24 23.4 24 22.667V1.333C24 .6 23.403 0 22.675 0z"/>
                </svg>
            </a>
            <a href="">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.337 3.608 1.312.975.975 1.25 2.242 1.312 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.337 2.633-1.312 3.608-.975.975-2.242 1.25-3.608 1.312-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.337-3.608-1.312-.975-.975-1.25-2.242-1.312-3.608C2.175 15.584 2.163 15.204 2.163 12s.012-3.584.07-4.85c.062-1.366.337-2.633 1.312-3.608.975-.975 2.242-1.25 3.608-1.312C8.416 2.175 8.796 2.163 12 2.163zm0 1.687c-3.17 0-3.548.012-4.796.07-1.026.047-1.58.216-1.947.384-.49.213-.84.467-1.209.836-.369.369-.623.719-.836 1.209-.168.367-.337.921-.384 1.947-.058 1.248-.07 1.626-.07 4.796s.012 3.548.07 4.796c.047 1.026.216 1.58.384 1.947.213.49.467.84.836 1.209.369.369.719.623 1.209.836.367.168.921.337 1.947.384 1.248.058 1.626.07 4.796.07s3.548-.012 4.796-.07c1.026-.047 1.58-.216 1.947-.384.49-.213.84-.467 1.209-.836.369-.369.623-.719.836-1.209.168-.367.337-.921.384-1.947.058-1.248.07-1.626.07-4.796s-.012-3.548-.07-4.796c-.047-1.026-.216-1.58-.384-1.947-.213-.49-.467-.84-.836-1.209-.369-.369-.719-.623-1.209-.836-.367-.168-.921-.337-1.947-.384-1.248-.058-1.626-.07-4.796-.07zm0 3.841a5.999 5.999 0 1 1 0 11.998 5.999 5.999 0 0 1 0-11.998zm0 9.899a3.9 3.9 0 1 0 0-7.8 3.9 3.9 0 0 0 0 7.8zm6.406-10.845a1.44 1.44 0 1 1-2.881 0 1.44 1.44 0 0 1 2.881 0z"/>
                </svg>
            </a>
            <a href="">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 24 24">
                    <path d="M12.001 0h3.014c.073 1.513.637 2.948 1.616 4.074 1.01 1.178 2.527 1.877 4.091 1.939v3.128c-1.422-.034-2.84-.465-4.01-1.236-.537-.357-1.01-.803-1.414-1.312-.016 2.993.008 5.988-.012 8.982-.046 1.395-.402 2.785-1.107 3.982-.873 1.508-2.325 2.637-3.975 3.055-1.517.404-3.194.287-4.63-.33-1.498-.641-2.723-1.859-3.37-3.352-.73-1.646-.84-3.598-.382-5.348.495-1.965 1.824-3.676 3.624-4.62 1.74-.934 3.899-1.09 5.792-.418.008 1.454-.016 2.908-.024 4.362-.998-.322-2.152-.285-3.095.254-.785.452-1.392 1.242-1.6 2.137-.285 1.17.07 2.5.957 3.322.861.834 2.265 1.115 3.367.602 1.003-.457 1.637-1.533 1.652-2.631.03-2.79.016-5.581.024-8.371-.002-2.23.01-4.459-.012-6.688z"/>
                </svg>
            </a>
        </div>
    </div>
</footer>
<div class="footer1">
     &copy; Copyright Fion Beauty 2025. All right reserved
</div>