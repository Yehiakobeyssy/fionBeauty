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
  display: none; /* hidden by default */
  position: absolute;
  top: -5px;
  right: -5px;
  background: var(--color-primary);
  color: var(--color-white);
  font-size: 12px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  align-items: center;
  justify-content: center;
  text-align: center;
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
  transition: all 0.3s ease;
  z-index: 10;
  max-height: 250px; /* max height before scrolling */
  overflow-y: auto;  /* vertical scroll if content exceeds max-height */
  padding-bottom: 5px;
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

.message {
  padding: 10px 15px;
  border-bottom: 1px solid #f0f0f0;
  font-size: 13px;
  color: #333;
  background-color: #fff; /* unread */
  transition: background 0.3s;
}

.message.read {
  background-color: #f5f5f5; /* read/darker background */
  color: #888;
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

#bottomNotificationPanel {
  position: fixed;
  bottom: 20px; /* distance from bottom */
  right: 20px;  /* distance from right */
  width: 300px;
  max-height: 400px;
  display: flex;
  flex-direction: column-reverse; /* newest at bottom */
  overflow-y: auto;
  pointer-events: none; /* allows clicks through empty areas */
  z-index: 9999;
}

.bottom-message {
  background: #fff;
  margin-top: 10px;
  padding: 10px;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  font-size: 14px;
  pointer-events: auto; /* enable click on messages */
  animation: slideUp 0.5s ease;
}

@keyframes slideUp {
  from { transform: translateY(50px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

</style>

<header>
  <audio id="notifSound" src="notification.mp3" preload="auto"></audio>
  <div id="bottomNotificationPanel" style="display:none; position:fixed; bottom:20px; right:20px; padding:10px 15px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.3); z-index:9999; flex-direction:column; gap:5px;">
  <div id="bottomNotificationList"></div>
</div>
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
      <div class="count" id="countNotification"></div>
    </div>

    <!-- Notification Dropdown -->
    <div class="notification-box" id="notificationBox">
      <h4>Notifications</h4>
      <div id="notificationList"></div>
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
// ------------------------
// Elements
// ------------------------
const adminInfo = document.getElementById('adminInfo');
const controlMenu = document.getElementById('controlMenu');
const notIcon = document.getElementById('notIcon');
const notificationBox = document.getElementById('notificationBox');
const notificationList = document.getElementById('notificationList');
const badge = document.getElementById('countNotification');
const notifSound = document.getElementById('notifSound');
const bottomPanel = document.getElementById('bottomNotificationPanel');
const bottomList = document.getElementById('bottomNotificationList');

// ------------------------
// State
// ------------------------
let lastNotificationId = 0;
let loading = false;
let displayedDropdownIds = new Set();  // Notifications shown in dropdown
let displayedBottomIds = new Set();    // Notifications shown in bottom popup
let lastUnreadCount = 0;

// ------------------------
// Functions
// ------------------------

// Load and update dropdown notifications
async function loadNotifications() {
  if (loading) return;
  loading = true;

  try {
    const response = await fetch(`ajaxadmin/getNotifications.php?after_id=${lastNotificationId}`);
    const data = await response.json();

    if (data.length > 0) {
      const newData = data.filter(item => !displayedDropdownIds.has(item.notificationId));

      newData.forEach(item => {
        // Add to dropdown list
        const div = document.createElement('div');
        div.classList.add('message');
        if (item.seen == 1) div.classList.add('read');
        div.textContent = item.text;
        notificationList.prepend(div);

        displayedDropdownIds.add(item.notificationId);

        // Show bottom popup for NEW unread notifications
        if (item.seen == 0 && !displayedBottomIds.has(item.notificationId)) {
          showBottomNotification(item);
        }
      });

      // Update last notification ID
      lastNotificationId = Math.max(...data.map(n => n.notificationId), lastNotificationId);
    }

    // Update unread count
    const unreadCount = await fetchUnreadCountValue();
    updateNotificationCount(unreadCount);

    // Play sound if new unread notifications arrived
    if (unreadCount > lastUnreadCount) {
      notifSound.play();
    }

    lastUnreadCount = unreadCount;

  } catch (err) {
    console.error(err);
  } finally {
    loading = false;
  }
}

// Update badge number
function updateNotificationCount(count) {
  if (count <= 0) {
    badge.style.display = 'none';
  } else {
    badge.style.display = 'flex';
    badge.textContent = count;
  }
}

// Fetch unread notification count
async function fetchUnreadCountValue() {
  try {
    const response = await fetch('ajaxadmin/getUnreadCount.php');
    const data = await response.json();
    return data.count || 0;
  } catch (err) {
    console.error(err);
    return 0;
  }
}

// Mark notifications as seen
async function markNotificationsSeen() {
  try {
    await fetch('ajaxadmin/markNotificationsSeen.php', { method: 'POST' });
    const unreadCount = await fetchUnreadCountValue();
    updateNotificationCount(unreadCount);
    lastUnreadCount = unreadCount;
  } catch (err) {
    console.error(err);
  }
}

// ------------------------
// Bottom Popup Notification
// ------------------------
function showBottomNotification(notification) {
  // Prevent showing the same notification twice
  if (displayedBottomIds.has(notification.notificationId)) return;

  const div = document.createElement('div');
  div.classList.add('bottom-message');

  // Title
  const title = document.createElement('strong');
  title.textContent = "Notification";
  title.style.display = "block";
  div.appendChild(title);

  // Content
  const content = document.createElement('div');
  content.textContent = notification.text;
  div.appendChild(content);

  bottomList.appendChild(div);

  // Play sound only once per new notification
  notifSound.play();

  displayedBottomIds.add(notification.notificationId);

  // Show panel
  bottomPanel.style.display = 'flex';

  // Hide after 2 seconds
  setTimeout(() => {
    div.remove();
    if (bottomList.children.length === 0) bottomPanel.style.display = 'none';
  }, 2000);
}

// ------------------------
// Event Listeners
// ------------------------
adminInfo.addEventListener('click', e => {
  e.stopPropagation();
  controlMenu.classList.toggle('show');
  notificationBox.classList.remove('show');
});

notIcon.addEventListener('click', async e => {
  e.stopPropagation();
  notificationBox.classList.toggle('show');
  controlMenu.classList.remove('show');

  if (!notificationBox.classList.contains('loaded')) {
    await loadNotifications();
    notificationBox.classList.add('loaded');
  }

  // Mark all as seen when opened
  await markNotificationsSeen();
  document.querySelectorAll('#notificationList .message').forEach(msg => {
    msg.classList.add('read');
  });
});

notificationBox.addEventListener('scroll', () => {
  if (notificationBox.scrollTop + notificationBox.clientHeight >= notificationBox.scrollHeight - 5) {
    loadNotifications();
  }
});

document.addEventListener('click', () => {
  controlMenu.classList.remove('show');
  notificationBox.classList.remove('show');
});

// ------------------------
// Auto-refresh every 1 second
// ------------------------
setInterval(loadNotifications, 1000);

// ------------------------
// Initial load
// ------------------------
loadNotifications();

</script>
