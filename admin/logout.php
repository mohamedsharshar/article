<?php
session_start();
// حذف جميع بيانات الجلسة
session_unset();
session_destroy();
// إعادة التوجيه لصفحة تسجيل الدخول الخاصة بالأدمن
header('Location: login.php');
exit();
