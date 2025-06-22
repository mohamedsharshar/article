<?php
session_start();
require_once '../db.php';
// صلاحيات الأدمن والسوبر أدمن
function is_superadmin($admin) {
    return !empty($admin['superadmin']) && $admin['superadmin'] == 1;
}
// جلب بيانات الأدمن الحالي من السيشن
function get_current_admin($pdo) {
    if (!isset($_SESSION['admin_username'])) return null;
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE adminname = ?');
    $stmt->execute([$_SESSION['admin_username']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
$current_admin = get_current_admin($pdo);
// إضافة مشرف جديد (فقط سوبر أدمن)
if (isset($_POST['add_admin'])) {
    if (!is_superadmin($current_admin)) {
        $error = 'غير مصرح لك بإضافة مشرفين. هذه العملية متاحة فقط للسوبر أدمن.';
    } else {
        $adminname = trim($_POST['adminname']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $superadmin = ($email === 'superadmin@example.com') ? 1 : 0;
        if ($adminname && $email && $password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO admins (adminname, email, password, created_at, superadmin) VALUES (?, ?, ?, NOW(), ?)');
            $stmt->execute([$adminname, $email, $hashed, $superadmin]);
            $success = 'تمت إضافة المشرف بنجاح.';
            header('Location: admins.php?success_msg=' . urlencode($success));
            exit();
        }
    }
}
// حذف مشرف (فقط سوبر أدمن)
if (isset($_POST['delete_admin_id'])) {
    $id = intval($_POST['delete_admin_id']);
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
    $stmt->execute([$id]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!is_superadmin($current_admin)) {
        $error = 'غير مصرح لك بحذف المشرفين. هذه العملية متاحة فقط للسوبر أدمن.';
    } elseif (is_superadmin($target) && !is_superadmin($current_admin)) {
        $error = 'لا يمكن حذف سوبر أدمن إلا من سوبر أدمن.';
    } else {
        $pdo->prepare('DELETE FROM admins WHERE id = ?')->execute([$id]);
        header('Location: admins.php?deleted=1');
        exit();
    }
}
// تعديل مشرف (فقط سوبر أدمن)
if (isset($_POST['edit_admin_id'])) {
    $id = intval($_POST['edit_admin_id']);
    $adminname = trim($_POST['edit_adminname']);
    $email = trim($_POST['edit_email']);
    $password = trim($_POST['edit_password']);
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
    $stmt->execute([$id]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!is_superadmin($current_admin)) {
        $error = 'غير مصرح لك بتعديل المشرفين. هذه العملية متاحة فقط للسوبر أدمن.';
    } elseif (is_superadmin($target) && !is_superadmin($current_admin)) {
        $error = 'لا يمكن تعديل سوبر أدمن إلا من سوبر أدمن.';
    } elseif ($adminname && $email) {
        // لا تنفذ أي تعديل إذا لم يكن المستخدم الحالي سوبر أدمن
    } else {
        if ($password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE admins SET adminname = ?, email = ?, password = ? WHERE id = ?');
            $stmt->execute([$adminname, $email, $hashed, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE admins SET adminname = ?, email = ? WHERE id = ?');
            $stmt->execute([$adminname, $email, $id]);
        }
        header('Location: admins.php?edited=1');
        exit();
    }
}
// تفعيل/إيقاف الأدمن (فقط سوبر أدمن)
if (isset($_POST['toggle_active_id'])) {
    $id = intval($_POST['toggle_active_id']);
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
    $stmt->execute([$id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!is_superadmin($current_admin)) {
        $error = 'غير مصرح لك بتفعيل أو إيقاف المشرفين. هذه العملية متاحة فقط للسوبر أدمن.';
    } elseif (is_superadmin($admin) && !is_superadmin($current_admin)) {
        $error = 'لا يمكن إيقاف سوبر أدمن إلا من سوبر أدمن.';
    } else {
        $new_active = $admin['is_active'] ? 0 : 1;
        $stmt = $pdo->prepare('UPDATE admins SET is_active = ? WHERE id = ?');
        $stmt->execute([$new_active, $id]);
        if ($new_active == 0 && isset($_SESSION['adminname']) && $_SESSION['adminname'] === $admin['adminname']) {
            session_unset();
            session_destroy();
        }
        header('Location: admins.php');
        exit();
    }
}
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
        body {
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
        .search-input {
            padding: 10px 18px;
            border-radius: 8px;
            border: 1px solid #dbeafe;
            font-size: 1.05rem;
            width: 100%;
            max-width: 100%;
            background: #f8fafc;
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
        body {
    direction: rtl;
    font-family: 'Cairo', Arial, sans-serif;
    background: #f8fafc;
}
.dashboard, .main-content {
    background: #fff;
    color: #222;
}
[data-theme="dark"] body,
[data-theme="dark"] html {
    background: #0f172a !important;
    color: #fff !important;
}
[data-theme="dark"] .dashboard, [data-theme="dark"] .main-content {
    background: #1e293b !important;
    color: #fff !important;
}
[data-theme="dark"] .admins-table, [data-theme="dark"] .data-table {
    background: #1e293b !important;
    color: #fff !important;
    border-color: #334155 !important;
}
[data-theme="dark"] .admins-table thead tr {
    background: linear-gradient(90deg, #334155 0%, #1e293b 100%) !important;
    color: #fff !important;
}
[data-theme="dark"] .admins-table th, [data-theme="dark"] .admins-table td {
    border-bottom: 1px solid #334155 !important;
    color: #fff !important;
}
[data-theme="dark"] .admins-table tbody tr:hover {
    background: #22304a !important;
}
[data-theme="dark"] .action-btn {
    background: #334155 !important;
    color: #60a5fa !important;
    box-shadow: 0 1px 4px #0f172a33 !important;
}
[data-theme="dark"] .action-btn.edit {
    background: #2563eb !important;
    color: #fff !important;
}
[data-theme="dark"] .action-btn.delete {
    background: #e63946 !important;
    color: #fff !important;
}
[data-theme="dark"] .action-btn:hover {
    background: #60a5fa !important;
    color: #fff !important;
}
[data-theme="dark"] .add-admin-btn {
    background: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%) !important;
    color: #fff !important;
}
[data-theme="dark"] .modal {
    background: rgba(15,23,42,0.85) !important;
}
[data-theme="dark"] .modal-content {
    background: #1e293b !important;
    color: #fff !important;
    box-shadow: 0 4px 24px #0f172a99 !important;
}
[data-theme="dark"] .modal-header {
    color: #60a5fa !important;
}
[data-theme="dark"] .form-group label {
    color: #cbd5e1 !important;
}
[data-theme="dark"] .form-group input, [data-theme="dark"] .form-group textarea, [data-theme="dark"] .form-group select {
    background: #22304a !important;
    color: #fff !important;
    border: 1px solid #334155 !important;
}
[data-theme="dark"] .form-group textarea {
    box-shadow: 0 1px 4px #0f172a33 !important;
}
[data-theme="dark"] .form-group textarea:focus {
    border-color: #60a5fa !important;
    box-shadow: 0 2px 8px #60a5fa33 !important;
}
[data-theme="dark"] .modal-actions button,
[data-theme="dark"] .close-modal,
[data-theme="dark"] .close-edit-modal,
[data-theme="dark"] .close-delete-modal {
    background: #334155 !important;
    color: #60a5fa !important;
}
[data-theme="dark"] .modal-actions button:hover,
[data-theme="dark"] .close-modal:hover,
[data-theme="dark"] .close-edit-modal:hover,
[data-theme="dark"] .close-delete-modal:hover {
    background: #60a5fa !important;
    color: #fff !important;
}
[data-theme="dark"] .delete-btn-confirm {
    background: linear-gradient(90deg, #e63946 0%, #ff6b6b 100%) !important;
    color: #fff !important;
}
[data-theme="dark"] .search-input {
    background: #22304a !important;
    color: #fff !important;
    border: 1px solid #334155 !important;
}
[data-theme="dark"] .dashboard-title {
    color: #fff !important;
}
[data-theme="dark"] .sidebar {
    background: #1e293b !important;
    color: #fff !important;
}
[data-theme="dark"] .modal-content::-webkit-scrollbar {
    background: #22304a;
}
[data-theme="dark"] .modal-content::-webkit-scrollbar-thumb {
    background: #334155;
}
[data-theme="dark"] .modal-content::-webkit-scrollbar-thumb:hover {
    background: #60a5fa;
}
[data-theme="dark"] .main-content::-webkit-scrollbar {
    background: #22304a;
}
[data-theme="dark"] .main-content::-webkit-scrollbar-thumb {
    background: #334155;
}
[data-theme="dark"] .main-content::-webkit-scrollbar-thumb:hover {
    background: #60a5fa;
}
[data-theme="dark"] p[style*="color:red"] {
    color: #ff6b6b !important;
}
[data-theme="dark"] p[style*="color:green"] {
    color: #4ade80 !important;
}
        /* نهاية أنماط الدارك مود الموسعة */
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<main class="main-content">
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة المشرفين</h1>
    <div style="display:flex;gap:10px;align-items:center;margin-bottom:18px;">
        <input type="text" id="searchAdmin" placeholder="بحث عن مشرف..." class="search-input">
        <button type="button" id="searchAdminBtn" class="action-btn" style="background:linear-gradient(90deg,#3a86ff 0%,#4361ee 100%);color:#fff;font-weight:bold;padding:10px 24px;min-width:120px;display:flex;align-items:center;gap:7px;"><i class="fa fa-search"></i> بحث</button>
    </div>
    <table class="data-table admins-table">
        <thead>
            <tr>
                <th>اسم المشرف</th>
                <th>البريد الإلكتروني</th>
                <th>تاريخ الإضافة</th>
                <th>حالة المشرف</th>
                <th>صلاحية</th>
                <th>إجراءات </th>
            </tr>
        </thead>
        <tbody id="adminsTable">
            <?php foreach($admins as $admin): ?>
            <tr>
                <td><?= htmlspecialchars($admin['adminname']) ?></td>
                <td><?= htmlspecialchars($admin['email']) ?></td>
                <td><?= htmlspecialchars($admin['created_at']) ?></td>
                <td>
                    <?php if ($admin['is_active']): ?>
                        <span style="color:green;font-weight:bold;">نشط</span>
                    <?php else: ?>
                        <span style="color:red;font-weight:bold;">موقوف</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($admin['superadmin']) && $admin['superadmin']): ?>
                        <span style="color:#3a86ff;font-weight:bold;"> سوبر أدمن</span>
                    <?php else: ?>
                        <span style="color:#888;">عادي</span>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="toggle_active_id" value="<?= $admin['id'] ?>">
                        <button type="submit" class="action-btn" style="background:<?= $admin['is_active'] ? '#e74a3b' : '#36b9cc' ?>;color:#fff;" title="<?= $admin['is_active'] ? 'إيقاف' : 'تفعيل' ?> المشرف">
                            <i class="fa <?= $admin['is_active'] ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                        </button>
                    </form>
                    <button class="action-btn edit-btn" onclick="openEditAdminModal(<?= $admin['id'] ?>, '<?= htmlspecialchars(addslashes($admin['adminname'])) ?>', '<?= htmlspecialchars(addslashes($admin['email'])) ?>')"><i class="fa fa-edit"></i></button>
                    <button class="action-btn delete-btn" onclick="openDeleteAdminModal(<?= $admin['id'] ?>, '<?= htmlspecialchars(addslashes($admin['adminname'])) ?>')"><i class="fa fa-trash"></i></button>
                    <button class="action-btn add-admin-btn" style="background:linear-gradient(90deg,#3a86ff 0%,#4361ee 100%);color:#fff;padding:7px 16px;font-size:1rem;margin-right:6px;display:inline-flex;align-items:center;gap:5px;" onclick="document.querySelector('.add-admin-modal').classList.add('active')"><i class="fa fa-plus"></i> إضافة</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- مودال إضافة مشرف -->
    <div class="add-admin-modal modal">
        <form action="admins.php" method="post">
            <div class="modal-content">
                <div class="modal-header">إضافة مشرف جديد</div>
                <div class="form-group">
                    <label for="adminname">اسم المشرف</label>
                    <input type="text" name="adminname" id="adminname" required>
                </div>
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="modal-actions">
                    <button type="submit" name="add_admin" class="add-admin-btn">إضافة</button>
                    <button type="button" class="close-modal">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
    <!-- مودال تعديل مشرف -->
    <div class="edit-admin-modal modal">
        <form action="admins.php" method="post">
            <div class="modal-content">
                <div class="modal-header">تعديل مشرف</div>
                <input type="hidden" name="edit_admin_id" id="edit_admin_id">
                <div class="form-group">
                    <label for="edit_adminname">اسم المشرف</label>
                    <input type="text" name="edit_adminname" id="edit_adminname" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">البريد الإلكتروني</label>
                    <input type="email" name="edit_email" id="edit_email" required>
                </div>
                <div class="form-group">
                    <label for="edit_password">كلمة المرور الجديدة (اختياري)</label>
                    <input type="password" name="edit_password" id="edit_password">
                </div>
                <div class="modal-actions">
                    <button type="submit" class="add-admin-btn">حفظ التعديلات</button>
                    <button type="button" class="close-edit-modal">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
    <!-- مودال حذف مشرف -->
    <div class="delete-admin-modal modal" id="deleteAdminModal">
        <div class="modal-content">
            <div class="modal-header">تأكيد حذف المشرف</div>
            <div class="form-group">
                <label>المشرف المحدد للحذف</label>
                <div id="deleteAdminName" style="color:#e63946;font-weight:bold;"></div>
            </div>
            <form method="post" id="deleteAdminForm">
                <input type="hidden" name="delete_admin_id" id="delete_admin_id">
                <div class="modal-actions">
                    <button type="submit" class="delete-btn-confirm">حذف المشرف</button>
                    <button type="button" class="close-delete-modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
    <?php if (isset(
    $error) && !empty($error)): ?>
    <p style="color:red; text-align:center;"> <?= htmlspecialchars($error) ?> </p>
<?php endif; ?>
    <?php if (isset($_GET['success_msg'])): ?>
    <p style="color:green; text-align:center; font-weight:bold;"> <?= $_GET['success_msg'] ?> </p>
<?php endif; ?>
</main>
<button id="themeToggle" aria-label="تبديل الوضع" style="position:absolute;left:2rem;top:1.5rem;background:none;border:none;cursor:pointer;font-size:1.5rem;z-index:10;"><i class="fa fa-moon" style="color:#222;"></i></button>
<script>
function setDarkMode(on) {
  if(on) {
    document.documentElement.setAttribute('data-theme', 'dark');
    localStorage.setItem('adminDarkMode', '1');
    document.getElementById('themeToggle').innerHTML = '<i class="fa fa-sun" style="color:#fff;"></i>';
  } else {
    document.documentElement.removeAttribute('data-theme');
    localStorage.setItem('adminDarkMode', '0');
    document.getElementById('themeToggle').innerHTML = '<i class="fa fa-moon" style="color:#222;"></i>';
  }
}
const themeToggle = document.getElementById('themeToggle');
if(localStorage.getItem('adminDarkMode') === null) {
  setDarkMode(false);
} else if(localStorage.getItem('adminDarkMode') === '1') {
  setDarkMode(true);
} else {
  setDarkMode(false);
}
themeToggle.onclick = function() {
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  setDarkMode(!isDark);
};
</script>
<script>
function openEditAdminModal(id, adminname, email) {
    document.querySelector('.edit-admin-modal').classList.add('active');
    document.getElementById('edit_admin_id').value = id;
    document.getElementById('edit_adminname').value = adminname;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_password').value = '';
}
document.querySelectorAll('.close-edit-modal').forEach(btn => {
    btn.onclick = () => document.querySelector('.edit-admin-modal').classList.remove('active');
});
function openDeleteAdminModal(id, adminname) {
    document.getElementById('delete_admin_id').value = id;
    document.getElementById('deleteAdminName').textContent = adminname;
    document.getElementById('deleteAdminModal').classList.add('active');
}
document.querySelectorAll('.close-delete-modal').forEach(btn => {
    btn.onclick = function() {
        document.getElementById('deleteAdminModal').classList.remove('active');
    };
});
document.querySelectorAll('.close-modal').forEach(btn => {
    btn.onclick = function() {
        document.querySelector('.add-admin-modal').classList.remove('active');
    };
});
window.onclick = function(e) {
    if (e.target === document.querySelector('.add-admin-modal')) {
        document.querySelector('.add-admin-modal').classList.remove('active');
    }
    if (e.target === document.querySelector('.edit-admin-modal')) {
        document.querySelector('.edit-admin-modal').classList.remove('active');
    }
    if (e.target === document.getElementById('deleteAdminModal')) {
        document.getElementById('deleteAdminModal').classList.remove('active');
    }
}
// بحث مباشر أو عند الضغط على زر البحث
const searchInput = document.getElementById('searchAdmin');
const searchBtn = document.getElementById('searchAdminBtn');
function filterAdmins() {
    const value = searchInput.value.trim().toLowerCase();
    document.querySelectorAll('#adminsTable tr').forEach(row => {
        const adminname = row.children[0].textContent.toLowerCase();
        const email = row.children[1].textContent.toLowerCase();
        row.style.display = (adminname.includes(value) || email.includes(value)) ? '' : 'none';
    });
}
searchInput.addEventListener('input', filterAdmins);
searchBtn.addEventListener('click', filterAdmins);
</script>
</body>
</html>
