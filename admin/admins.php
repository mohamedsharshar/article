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
        .dashboard-title {
            font-size: 2rem;
            color: #2d3a4b;
            margin-bottom: 2rem;
            font-weight: bold;
        }
        .add-admin-btn {
            background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 22px;
            font-size: 1.08rem;
            font-weight: bold;
            margin-bottom: 18px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(67,97,238,0.07);
            transition: background 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
        }
        .add-admin-btn:hover {
            background: linear-gradient(90deg, #4361ee 0%, #3a86ff 100%);
            box-shadow: 0 4px 16px rgba(67,97,238,0.13);
        }
        .admins-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(67,97,238,0.07);
            overflow: hidden;
            margin-top: 32px;
            font-size: 1.08rem;
            direction: rtl;
        }
        .admins-table thead tr {
            background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
            color: #fff;
        }
        .admins-table th, .admins-table td {
            padding: 16px 18px;
            text-align: right;
            border-bottom: 1px solid #f0f4fa;
        }
        .admins-table th {
            font-weight: bold;
            font-size: 1.1rem;
            letter-spacing: 0.01em;
        }
        .admins-table tbody tr {
            transition: background 0.2s;
        }
        .admins-table tbody tr:hover {
            background: #f1f5f9;
        }
        .admins-table td {
            color: #2d3142;
        }
        .action-btn {
            background: #f8fafc;
            border: none;
            border-radius: 6px;
            color: #3a86ff;
            padding: 7px 12px;
            margin-left: 4px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            box-shadow: 0 1px 4px rgba(67,97,238,0.07);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .action-btn.edit {
            background: #36b9cc;
            color: #fff;
        }
        .action-btn.delete {
            background: #e74a3b;
            color: #fff;
        }
        .action-btn:hover {
            background: #3a86ff;
            color: #fff;
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
        .modal.active, .modal[style*="display: flex"] {
            display: flex !important;
        }
        .modal-content {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(67,97,238,0.13);
            padding: 32px 8px 24px 28px;
            min-width: 340px;
            max-width: 90vw;
            max-height: 80vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
            animation: bounceIn 0.5s;
            align-items: center;
            justify-content: center;
        }
        .form-group, .form-group label, .form-group input, .form-group textarea {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        .form-group {
            margin-bottom: 1.2rem;
            width: 100%;
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
            min-height: 100px;
            max-height: 180px;
            border: 1.5px solid #dbeafe;
            border-radius: 10px;
            background: #f8fafc;
            font-size: 1.08rem;
            color: #222;
            padding: 12px 10px;
            transition: border 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 4px #e3e6f0;
            resize: vertical;
        }
        .form-group textarea:focus {
            border-color: #4262ed;
            box-shadow: 0 2px 8px #4262ed22;
            outline: none;
        }
        .modal-actions {
            display: flex;
            flex-direction: row;
            gap: 1rem;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .modal-actions button {
            min-width: 120px;
            font-size: 1.08rem;
            font-weight: bold;
            border-radius: 8px;
            padding: 10px 0;
            cursor: pointer;
            border: none;
            transition: background 0.2s, color 0.2s;
            margin-bottom: 0;
        }
        .add-admin-btn[type="submit"], .modal-actions .add-admin-btn {
            background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
            color: #fff;
            width: 100%;
            justify-content: center;
            align-items: center;
            display: flex;
        }
        .add-admin-btn[type="submit"]:hover, .modal-actions .add-admin-btn:hover {
            background: linear-gradient(90deg, #4361ee 0%, #3a86ff 100%);
        }
        .delete-btn-confirm {
            background: linear-gradient(90deg, #e63946 0%, #ff6b6b 100%);
            color: #fff;
        }
        .delete-btn-confirm:hover {
            background: linear-gradient(90deg, #ff6b6b 0%, #e63946 100%);
        }
        .close-modal, .close-edit-modal, .close-delete-modal {
            background: #f8fafc;
            color: #3a86ff;
            border: none;
            border-radius: 8px;
            padding: 10px 0;
            font-size: 1.08rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 6px;
            transition: background 0.2s, color 0.2s;
            min-width: 120px;
        }
        .close-modal:hover, .close-edit-modal:hover, .close-delete-modal:hover {
            background: #3a86ff;
            color: #fff;
        }
        @media (max-width: 700px) {
            .main-content {
                padding: 1rem 0.2vw;
            }
            .modal-content {
                padding: 1rem 0.5rem;
            }
            .admins-table th, .admins-table td {
                padding: 10px 6px;
                font-size: 0.98rem;
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
