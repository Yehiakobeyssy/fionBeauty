<style>
    header {
        background-color: var(--color-primary);
        padding: 12px 35px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    header a {
        color: var(--color-white);
        text-decoration: none;
        font-weight: 500;
    }

    .phoneinfo {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .shoptitle {
        color: var(--color-white);
        font-size: 16px;
    }
    .shoptitle label {
        margin-right: 12px;
    }
    .shoptitle a {
        background: var(--color-white);
        color: var(--color-primary);
        padding: 4px 10px;
        border-radius: var(--radius);
        font-size: 14px;
        transition: all 0.2s ease;
    }
    .shoptitle a:hover {
        background: var(--color-primary-variant);
        color: var(--color-white);
    }

    .langugeuse select, .small select {
        background: none;
        border: none;
        color: var(--color-white);
        padding: 0;
        font-size: 16px; 
        width: 100%;
        cursor: pointer;
    }

    .langugeuse select::-ms-expand {
        display: none; /* إخفاء السهم في إنترنت إكسبلورر */
    }

    .langugeuse select option, 
    .small select option {
        background: var(--color-primary);
        color: var(--color-white);
    }
    
    .langugeuse select option:hover {
        background: var(--color-primary-variant);
        color: var(--color-white); 
    }
    .small{
        display: none;
    }

/* للشاشات الصغيرة */
/* للشاشات الصغيرة */
@media (max-width: 768px) {
    header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .big{
        display: none;
    }
    .small{
        display: flex;
    }

    .phoneinfo {
        width: 100%;
        justify-content: space-between;
        padding: 0 15px; /* optional, for some side spacing */
        margin-bottom: 8px;

    }
    .shoptitle {
        margin-top: 10px;
    }
}

/* للشاشات الصغيرة جداً (≤ 480px) */
@media (max-width: 480px) {
    .shoptitle {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .shoptitle a {
        margin-top: 5px;
    }
}


</style>
<?php
    $sql= $con->prepare('SELECT companyPhone,noteHeader FROM tblsetting WHERE seetingID = 1');
    $sql->execute();
    $headerinfo=$sql->fetch();
    $companyPhone = $headerinfo['companyPhone'];
    $noteHeader = $headerinfo['noteHeader'];
?>
<header>
    <div class="phoneinfo">
        <div class="shopphone">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                <path d="M17.436 4.375C18.9194 4.77396 20.2719 5.55567 21.3581 6.64184C22.4442 7.72801 23.226 9.08051 23.6249 10.5639" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.5305 7.7569C17.4203 7.99628 18.2317 8.46524 18.8832 9.11681C19.5348 9.76838 20.0038 10.5797 20.2431 11.4695" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10.115 13.6518C11.0224 15.5074 12.5263 17.0049 14.3859 17.9043C14.522 17.9688 14.6727 17.9966 14.8229 17.9851C14.9731 17.9736 15.1178 17.9231 15.2425 17.8386L17.9812 16.0134C18.1022 15.9326 18.2414 15.8833 18.3862 15.8698C18.5311 15.8564 18.677 15.8793 18.8107 15.9364L23.9339 18.1326C24.1079 18.2066 24.2532 18.335 24.3479 18.4987C24.4426 18.6623 24.4815 18.8523 24.4589 19.04C24.2967 20.307 23.6784 21.4714 22.7196 22.3154C21.7608 23.1593 20.5273 23.6249 19.25 23.625C15.3049 23.625 11.5214 22.0578 8.73179 19.2682C5.94218 16.4786 4.375 12.6951 4.375 8.75001C4.37512 7.4728 4.84074 6.23942 5.68471 5.28079C6.52867 4.32215 7.6931 3.70398 8.96 3.54201C9.14771 3.51938 9.33769 3.55833 9.50134 3.65302C9.66499 3.74771 9.79345 3.893 9.86738 4.06701L12.0654 9.19451C12.1219 9.327 12.1449 9.47139 12.1322 9.61487C12.1195 9.75835 12.0716 9.89648 11.9928 10.017L10.1728 12.7978C10.0901 12.923 10.0414 13.0675 10.0313 13.2171C10.0212 13.3668 10.05 13.5166 10.115 13.6518V13.6518Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <a href="tel:+<?php echo $companyPhone ?>"><?php echo $companyPhone ?> </a>
        </div>
        
        <div class="langugeuses small">
            <select name="" id="">
                <option value="">English</option>
                
            </select>
        </div>
    </div>
    <div class="shoptitle">
        <label for=""><?php echo $noteHeader ?></label>
        <a href=""><strong>shop Now</strong></a>
    </div>
    <div class="langugeuse big" >
        <select name="" id="">
            <option value="">English</option>
            
        </select>
    </div>
</header>