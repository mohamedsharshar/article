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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body, html {
            font-family: 'Cairo', Tahoma, Arial, sans-serif;
            background: #f7f8fa;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .main-content {
            padding: 2rem 2vw 1rem 2vw;
            margin-right: 220px;
        }
        @media (max-width: 900px) {
            .main-content {
                margin-right: 0 !important;
            }
        }
        .admins-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0001;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .admins-table th, .admins-table td {
            padding: 1rem 0.7rem;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }
        .admins-table th {
            background: #f3f6fa;
            color: #4e73df;
            font-weight: bold;
        }
        .admins-table tr:last-child td {
            border-bottom: none;
        }
        .action-btn {
            background: #4e73df;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 0.4rem 1rem;
            margin: 0 0.2rem;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .action-btn.edit {
            background: #36b9cc;
        }
        .action-btn.delete {
            background: #e74a3b;
        }
        .action-btn:hover {
            opacity: 0.9;
        }
        .add-admin-btn {
            background: #1cc88a;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.7rem 1.5rem;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .add-admin-btn:hover {
            background: #17a673;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            right: 0; left: 0; top: 0; bottom: 0;
            background: rgba(0,0,0,0.25);
            align-items: center;
            justify-content: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px #0002;
            padding: 2rem 2.5rem;
            min-width: 320px;
            max-width: 95vw;
            animation: fadeInDown 0.7s;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modal-header {
            font-size: 1.3rem;
            color: #2d3a4b;
            font-weight: bold;
            margin-bottom: 1.2rem;
        }
        .modal-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            justify-content: flex-end;
        }
        .modal-actions button {
            min-width: 90px;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            color: #444;
            font-weight: 500;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 0.5rem 0.7rem;
            border: 1px solid #e3e6f0;
            border-radius: 6px;
            font-size: 1rem;
            background: #f9fafb;
            color: #222;
        }
        .form-group textarea {
            min-height: 80px;
        }
        @media (max-width: 700px) {
            .main-content {
                padding: 1rem 0.2vw;
            }
            .modal-content {
                padding: 1rem 0.5rem;
            }
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<main class="main-content">
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة المشرفين</h1>
    <table class="data-table admins-table">
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
