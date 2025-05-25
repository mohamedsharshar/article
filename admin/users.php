<?php
session_start();
require_once '../db.php';
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'sidebar.php'; ?>
<main class="main-content">
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة المستخدمين</h1>
    <input type="text" id="searchUser" placeholder="بحث عن مستخدم..." class="search-input">
    <table class="data-table">
        <thead>
            <tr>
                <th>اسم المستخدم</th>
                <th>البريد الإلكتروني</th>
                <th>تاريخ التسجيل</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody id="usersTable">
            <?php foreach($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
                <td>
                    <button class="action-btn edit-btn"><i class="fa fa-edit"></i></button>
                    <button class="action-btn delete-btn"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
<script src="js/users.js"></script>
</body>
</html>
