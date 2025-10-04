<?php
session_start();

// حذف جميع بيانات الجلسة
session_unset();
session_destroy();

// حذف الكوكيز إذا موجودة
if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, '/'); // تعيين الكوكي لتاريخ ماضي
}

// إعادة التوجيه إلى الصفحة الرئيسية
header("Location: ../index.php");
exit();
?>