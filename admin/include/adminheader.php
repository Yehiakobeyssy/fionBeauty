<style>
/* Header Layout */
header {
  display: flex;
  justify-content: space-between;
  padding: 16px 24px;
  border-bottom: 1px solid var(--color-icon);
  background: var(--color-white);
  box-shadow: 0 4px 30px 0 rgba(131, 98, 234, 0.05);
}

/* Logo */
.logo img {
  width: 45px;
  height: 45px;
}
.logo label {
  margin-left: 20px;
  color: var(--color-primary);
  font-family: "Baloo Bhaijaan";
  font-size: 24px;
  font-style: normal;
  font-weight: 400;
  line-height: 24px;
  letter-spacing: 0.72px;
}

/* Notification Section */
.notification {
  display: flex;
  align-items: center;
  position: relative;
}

.not {
  position: relative;
  margin-right: 20px;
  cursor: pointer;
}

.not svg {
  width: 24px;
  height: 24px;
}

/* Count Badge */
.count {
  position: absolute;
  top: -5px;
  right: -5px;
  background: var(--color-primary);
  color: var(--color-white);
  font-size: 12px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Notification Box (Dropdown) */
.notification-box {
  position: absolute;
  top: 35px;
  right: 60px;
  background: var(--color-white);
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  border-radius: 8px;
  display: none;
  flex-direction: column;
  width: 250px;
  overflow: hidden;
  transition: all 0.3s ease;
  z-index: 10;
}

.notification-box.show {
  display: flex;
  animation: slideDown 0.3s ease forwards;
}

.notification-box h4 {
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
  font-size: 14px;
  color: var(--color-primary);
}

.notification-box .message {
  padding: 10px 15px;
  border-bottom: 1px solid #f0f0f0;
  font-size: 13px;
  color: #333;
}

.notification-box .message:last-child {
  border-bottom: none;
}

/* Admin Info */
.info {
  position: relative;
  cursor: pointer;
}

.info label {
  margin-right: 8px;
  color: var(--color-dark);
font-family: Outfit;
font-size: 14px;
font-style: normal;
font-weight: 500;
line-height: 20px; /* 142.857% */
letter-spacing: 0.07px;
}

/* Dropdown Control Menu */
.control {
  position: absolute;
  top: 30px;
  right: 0;
  background: var(--color-white);
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  border-radius: 8px;
  display: none;
  flex-direction: column;
  overflow: hidden;
  transition: all 0.3s ease;
  min-width: 150px;
  z-index: 10;
}

.control a {
  padding: 10px 15px;
  color: #333;
  text-decoration: none;
  border-bottom: 1px solid #eee;
  display: block;
}

.control a:hover {
  background: var(--color-primary);
  color: var(--color-white);
}

/* Animation for slide-down */
.control.show {
  display: flex;
  animation: slideDown 0.3s ease forwards;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

<header>
  <div class="logo">
    <img src="../images/logo.png" alt="">
    <label>Fion Beauty Supplies</label>
  </div>
  <div class="notification">
    <!-- Notification Icon -->
    <div class="not" id="notIcon">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
        <path fill-rule="evenodd" clip-rule="evenodd"
          d="M13 3C13 2.44772 12.5523 2 12 2C11.4477 2 11 2.44772 11 3V3.57088C7.60769 4.0561 4.99997 6.97352 4.99997 10.5V15.5L4.28237 16.7558C3.71095 17.7558 4.433 19 5.58474 19H8.12602C8.57006 20.7252 10.1362 22 12 22C13.8638 22 15.4299 20.7252 15.874 19H18.4152C19.5669 19 20.289 17.7558 19.7176 16.7558L19 15.5V10.5C19 6.97354 16.3923 4.05614 13 3.57089V3ZM6.99997 16.0311L6.44633 17H17.5536L17 16.0311V10.5C17 7.73858 14.7614 5.5 12 5.5C9.23854 5.5 6.99997 7.73858 6.99997 10.5V16.0311ZM12 20C11.2597 20 10.6134 19.5978 10.2676 19H13.7324C13.3866 19.5978 12.7403 20 12 20Z"
          fill="#667085" />
      </svg>
      <div class="count">1</div>
    </div>

    <!-- Notification Dropdown -->
    <div class="notification-box" id="notificationBox">
      <h4>Notifications</h4>
      <div class="message">No new notifications</div>
      <!-- For future programming, you can dynamically add messages here -->
    </div>

    <!-- Admin Info -->
    <div class="info" id="adminInfo">
      <label><?= $admin_name ?></label>
      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="8" fill="none">
        <path fill-rule="evenodd" clip-rule="evenodd"
          d="M11.5893 0.910742C11.2638 0.585305 10.7362 0.585305 10.4108 0.910742L6.00002 5.32149L1.58928 0.910742C1.26384 0.585305 0.736202 0.585305 0.410765 0.910742C0.0853277 1.23618 0.0853277 1.76382 0.410765 2.08925L5.70539 7.38388C5.86811 7.5466 6.13193 7.5466 6.29465 7.38388L11.5893 2.08925C11.9147 1.76382 11.9147 1.23618 11.5893 0.910742Z"
          fill="#667085" />
      </svg>
      <div class="control" id="controlMenu">
        <a href="changePassword.php">Change Password</a>
        <a href="ajaxadmin/logout.php">Logout</a>
      </div>
    </div>
  </div>
</header>

<script>
  const adminInfo = document.getElementById('adminInfo');
  const controlMenu = document.getElementById('controlMenu');
  const notIcon = document.getElementById('notIcon');
  const notificationBox = document.getElementById('notificationBox');

  // Toggle admin dropdown
  adminInfo.addEventListener('click', (e) => {
    e.stopPropagation();
    controlMenu.classList.toggle('show');
    notificationBox.classList.remove('show'); // close other dropdown
  });

  // Toggle notification dropdown
  notIcon.addEventListener('click', (e) => {
    e.stopPropagation();
    notificationBox.classList.toggle('show');
    controlMenu.classList.remove('show'); // close other dropdown
  });

  // Close all when clicking outside
  document.addEventListener('click', () => {
    controlMenu.classList.remove('show');
    notificationBox.classList.remove('show');
  });
</script>
