<?php
// سكريبت تحديث جماعي لربط المقالات القديمة بناشر افتراضي (أول أدمن موجود)
require_once '../db.php';

// جلب أول أدمن
$admin = $pdo->query("SELECT id FROM admins ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$admin) {
    die('لا يوجد أدمن في قاعدة البيانات.');
}
$admin_id = $admin['id'];

// تحديث كل مقال ليس له user_id ولا admin_id
$updated = $pdo->exec("UPDATE articles SET admin_id = $admin_id WHERE admin_id IS NULL AND user_id IS NULL");
echo "تم تحديث $updated مقال قديم ليرتبط بالأدمن رقم $admin_id.\n";
