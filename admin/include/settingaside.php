<style>
#web-info-sidebar {
  width: 240px;
  background-color: var(--color-white);
  border: 1px solid #eee;
  padding: 20px 0;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

#web-info-sidebar a {
  display: flex;
  align-items: center;
  gap: 12px;
  color: var(--color-dark);
  text-decoration: none;
  font-size: 15px;
  font-weight: 500;
  padding: 12px 20px;
  transition: all 0.3s ease;
}

#web-info-sidebar a:hover {
  background-color: var(--color-primary-variant);
  color: var(--color-white);
}

.submenu {
  display: none;
  flex-direction: column;
  padding-left: 20px;
}

.submenu a {
  padding: 10px 20px;
  font-size: 14px;
}

#web-info-sidebar a.active {
  background-color: var(--color-primary);
  color: var(--color-white);
}

#web-info-sidebar a.active svg path {
  fill: var(--color-white);
}
</style>
<div id="web-info-sidebar">
    <a href="?do=main" class="<?= $do == 'main' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
            <path d="M18 18H10V12H18V18ZM8 18H0V8H8V18ZM18 10H10V0H18V10ZM8 6H0V0H8V6Z" fill="#667085"/>
        </svg>
        Main Setting
    </a>

    <a href="?do=slide" class="toggle-submenu <?= ($do == 'slide' || $do == 'slide_home' || $do == 'slide_category') ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17" fill="none">
            <path d="M1 1C1 0.447715 1.44772 0 2 0H18C18.5523 0 19 0.447715 19 1V11C19 11.5523 18.5523 12 18 12H11.618L13.2764 14.5528C13.4936 14.8869 13.3948 15.3333 13.0607 15.5505C12.7266 15.7678 12.2802 15.669 12.063 15.3349L10.118 12.4H9.882L7.93701 15.3349C7.71979 15.669 7.2734 15.7678 6.93934 15.5505C6.60527 15.3333 6.50645 14.8869 6.72366 14.5528L8.38203 12H2C1.44772 12 1 11.5523 1 11V1ZM3 2V10H17V2H3Z" fill="#667085"/>
        </svg>
        Slide Show &#9656;
    </a>
    <div class="submenu" style="display: <?= ($do == 'slide_home' || $do == 'slide_category') ? 'flex' : 'none' ?>;">
        <a href="?do=slide_home" class="<?= $do == 'slide_home' ? 'active' : '' ?>">Home Page</a>
        <a href="?do=slide_category" class="<?= $do == 'slide_category' ? 'active' : '' ?>">Category Page</a>
    </div>
    <a href="?do=social" class="<?= $do == 'social' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M12 0C5.37258 0 0 5.37258 0 12C0 18.6274 5.37258 24 12 24C18.6274 24 24 18.6274 24 12C23.9935 5.37498 18.625 0.00650817 12 0ZM14.948 12H12.972V19H10V12H8.5V9.5H10V8.148C10 5.672 11.168 4 14.156 4C15.307 4 15.936 4.083 16.25 4.12V6.516H14.859C13.768 6.516 13.528 7.03 13.528 7.875V9.5H16.2L15.948 12H14.948Z" fill="#667085"/>
        </svg>
        Social Media
    </a>
    <a href="?do=finance" class="<?= $do == 'finance' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M5 15C5 14.4477 5.44772 14 6 14H10C10.5523 14 11 14.4477 11 15C11 15.5523 10.5523 16 10 16H6C5.44772 16 5 15.5523 5 15Z" fill="#667085"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M2 7C2 5.34315 3.34315 4 5 4H19C20.6569 4 22 5.34315 22 7V17C22 18.6569 20.6569 20 19 20H5C3.34315 20 2 18.6569 2 17V7ZM5 6H19C19.5523 6 20 6.44771 20 7V8H4V7C4 6.44772 4.44772 6 5 6ZM20 10V17C20 17.5523 19.5523 18 19 18H5C4.44772 18 4 17.5523 4 17V10H20Z" fill="#667085"/>
        </svg>
        Finance Setting
    </a>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.querySelector("#web-info-sidebar .toggle-submenu");
    const submenu = document.querySelector("#web-info-sidebar .submenu");

    toggle.addEventListener("click", function(e) {
        e.preventDefault();
        submenu.style.display = submenu.style.display === "flex" ? "none" : "flex";
    });
});
</script>