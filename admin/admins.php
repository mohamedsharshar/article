<?php
session_start();
require_once '../db.php';
$admins = $pdo->query("SELECT * FROM admins ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المشرفين</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'sidebar.php'; ?>
<main class="main-content">
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة المشرفين</h1>
    <table class="data-table">
        <thead>
            <tr>
                <th>اسم المشرف</th>
                <th>البريد الإلكتروني</th>
                <th>تاريخ الإضافة</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody id="adminsTable">
            <?php foreach($admins as $admin): ?>
            <tr>
                <td><?= htmlspecialchars($admin['username']) ?></td>
                <td><?= htmlspecialchars($admin['email']) ?></td>
                <td><?= htmlspecialchars($admin['created_at']) ?></td>
                <td>
                    <button class="action-btn edit-btn"><i class="fa fa-edit"></i></button>
                    <button class="action-btn delete-btn"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
<script src="js/admins.js"></script>
</body>
</html>
