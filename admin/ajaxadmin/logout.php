<?php
session_start();

// Remove admin session
unset($_SESSION['admin_id']);

// Optionally destroy all session data
session_destroy();

// Redirect to index page
header("Location: ../index.php");
exit;
?>
