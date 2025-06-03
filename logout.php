<?php
session_start();
// إزالة جميع متغيرات الجلسة
session_unset();
// تدمير الجلسة
session_destroy();
// إعادة التوجيه للصفحة الرئيسية
header('Location: index.php');
exit;
