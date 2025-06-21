<?php
session_start();
require_once '../db.php';

// حذف مستخدم
if (isset($_POST['delete_user_id'])) {
    $id = intval($_POST['delete_user_id']);
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: users.php?deleted=1');
    exit();
}

// تعديل مستخدم
if (isset($_POST['edit_user_id'])) {
    $id = intval($_POST['edit_user_id']);
    $username = trim($_POST['edit_username']);
    $email = trim($_POST['edit_email']);
    $is_active = isset($_POST['edit_is_active']) ? 1 : 0;
    if ($username && $email) {
        try {
            $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, is_active = ? WHERE id = ?');
            $stmt->execute([$username, $email, $is_active, $id]);
            header('Location: users.php?edited=1');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'اسم المستخدم أو البريد الإلكتروني مستخدم من قبل.';
            } else {
                $error = 'حدث خطأ أثناء تعديل المستخدم.';
            }
        }
    }
}

// إضافة مستخدم جديد
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if ($username && $email && $password) {
        try {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password, created_at, is_active) VALUES (?, ?, ?, NOW(), ?)');
            $stmt->execute([$username, $email, $hashed, $is_active]);
            header('Location: users.php?success=1');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'اسم المستخدم أو البريد الإلكتروني مستخدم من قبل.';
            } else {
                $error = 'حدث خطأ أثناء إضافة المستخدم.';
            }
        }
    }
}

// تفعيل/إيقاف المستخدم
if (isset($_POST['toggle_active_id'])) {
    $id = intval($_POST['toggle_active_id']);
    // جلب حالة المستخدم قبل التغيير
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $new_active = $user['is_active'] ? 0 : 1;
    $stmt = $pdo->prepare('UPDATE users SET is_active = ? WHERE id = ?');
    $stmt->execute([$new_active, $id]);
    // إذا كان المستخدم الحالي هو نفسه الذي تم تعطيله، اعمل له تسجيل خروج
    if ($new_active == 0 && isset($_SESSION['username']) && $_SESSION['username'] === $user['username']) {
        session_unset();
        session_destroy();
        // لا تقم بإعادة التوجيه
    }
    header('Location: users.php');
    exit();
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين</title>
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
        .add-user-btn {
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
        .add-user-btn:hover {
            background: linear-gradient(90deg, #4361ee 0%, #3a86ff 100%);
            box-shadow: 0 4px 16px rgba(67,97,238,0.13);
        }
        .users-table {
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
        .users-table thead tr {
            background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
            color: #fff;
        }
        .users-table th, .users-table td {
            padding: 16px 18px;
            text-align: right;
            border-bottom: 1px solid #f0f4fa;
        }
        .users-table th {
            font-weight: bold;
            font-size: 1.1rem;
            letter-spacing: 0.01em;
        }
        .users-table tbody tr {
            transition: background 0.2s;
        }
        .users-table tbody tr:hover {
            background: #f1f5f9;
        }
        .users-table td {
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
        .add-btn {
            background: linear-gradient(90deg,#3a86ff 0%,#4361ee 100%) !important;
            color: #fff !important;
            padding: 7px 16px !important;
            font-size: 1rem !important;
            margin-right: 6px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
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
        .modal-header {
            font-size: 1.2rem;
            font-weight: bold;
            color: #3a86ff;
            margin-bottom: 1rem;
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
        .add-user-btn[type="submit"], .modal-actions .add-user-btn {
            background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
            color: #fff;
            width: 100%;
            justify-content: center;
            align-items: center;
            display: flex;
        }
        .add-user-btn[type="submit"]:hover, .modal-actions .add-user-btn:hover {
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
        /* أنماط الوضع المظلم الموسعة */
        [data-theme="dark"] body,
        [data-theme="dark"] html {
            background: #0f172a !important;
            color: #fff !important;
        }
        [data-theme="dark"] .dashboard, [data-theme="dark"] .main-content {
            background: #1e293b !important;
            color: #fff !important;
        }
        [data-theme="dark"] .users-table, [data-theme="dark"] .data-table {
            background: #1e293b !important;
            color: #fff !important;
            border-color: #334155 !important;
        }
        [data-theme="dark"] .users-table thead tr {
            background: linear-gradient(90deg, #334155 0%, #1e293b 100%) !important;
            color: #fff !important;
        }
        [data-theme="dark"] .users-table th, [data-theme="dark"] .users-table td {
            border-bottom: 1px solid #334155 !important;
            color: #fff !important;
        }
        [data-theme="dark"] .users-table tbody tr:hover {
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
        [data-theme="dark"] .add-btn, [data-theme="dark"] .add-user-btn {
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
<button id="themeToggle" aria-label="تبديل الوضع" style="position:absolute;left:2rem;top:1.5rem;background:none;border:none;cursor:pointer;font-size:1.5rem;z-index:10;"><i class="fa fa-moon" style="color:#222;"></i></button>
<main class="main-content">
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة المستخدمين</h1>
    <div style="display:flex;gap:10px;align-items:center;margin-bottom:18px;">
        <input type="text" id="searchUser" placeholder="بحث عن مستخدم..." class="search-input">
        <button type="button" id="searchUserBtn" class="action-btn" style="background:linear-gradient(90deg,#3a86ff 0%,#4361ee 100%);color:#fff;font-weight:bold;padding:10px 24px;min-width:120px;display:flex;align-items:center;gap:7px;"><i class="fa fa-search"></i> بحث</button>
    </div>
    <table class="data-table users-table">
        <thead>
            <tr>
                <th>اسم المستخدم</th>
                <th>البريد الإلكتروني</th>
                <th>تاريخ التسجيل</th>
                <th>الحالة</th>
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
                    <?php if ($user['is_active']): ?>
                        <span style="color:green;font-weight:bold;">نشط</span>
                    <?php else: ?>
                        <span style="color:red;font-weight:bold;">موقوف</span>
                    <?php endif; ?>
                </td>
                <td style="display:flex;gap:4px;align-items:center;">
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="toggle_active_id" value="<?= $user['id'] ?>">
                        <button type="submit" class="action-btn" style="background:<?= $user['is_active'] ? '#e74a3b' : '#36b9cc' ?>;color:#fff;" title="<?= $user['is_active'] ? 'إيقاف' : 'تفعيل' ?> المستخدم">
                            <i class="fa <?= $user['is_active'] ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                        </button>
                    </form>
                    <button class="action-btn edit-btn" onclick="openEditUserModal(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['username'])) ?>', '<?= htmlspecialchars(addslashes($user['email'])) ?>', <?= $user['is_active'] ?>)"><i class="fa fa-edit"></i></button>
                    <button class="action-btn delete-btn" onclick="openDeleteUserModal(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['username'])) ?>')"><i class="fa fa-trash"></i></button>
                    <button class="action-btn add-btn" style="background:linear-gradient(90deg,#3a86ff 0%,#4361ee 100%);color:#fff;padding:7px 16px;font-size:1rem;margin-right:6px;display:inline-flex;align-items:center;gap:5px;" title="إضافة مستخدم جديد" onclick="document.querySelector('.add-user-modal').classList.add('active')">
                        <i class="fa fa-plus"></i> إضافة
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- مودال إضافة مستخدم -->
    <div class="add-user-modal modal">
        <form action="users.php" method="post">
            <div class="modal-content">
                <div class="modal-header">إضافة مستخدم جديد</div>
                <div class="form-group">
                    <label for="username">اسم المستخدم</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="is_active" checked> نشط</label>
                </div>
                <div class="modal-actions">
                    <button type="submit" name="add_user" class="add-user-btn">إضافة</button>
                    <button type="button" class="close-modal">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
    <!-- مودال تعديل مستخدم -->
    <div class="modal edit-user-modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">تعديل مستخدم</div>
            <form action="users.php" method="post">
                <input type="hidden" name="edit_user_id" id="edit_user_id">
                <div class="form-group">
                    <label for="edit_username">اسم المستخدم</label>
                    <input type="text" name="edit_username" id="edit_username" placeholder="اسم المستخدم" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">البريد الإلكتروني</label>
                    <input type="email" name="edit_email" id="edit_email" placeholder="البريد الإلكتروني" required>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="edit_is_active" id="edit_is_active"> نشط</label>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="action-btn edit">حفظ التعديلات</button>
                    <button type="button" class="close-edit-user-modal action-btn" onclick="document.querySelector('.edit-user-modal').style.display='none'">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- مودال حذف مستخدم -->
    <div class="modal delete-user-modal" id="deleteUserModal">
        <div class="modal-content">
            <div class="modal-header">تأكيد حذف المستخدم</div>
            <div id="deleteUserName" style="color:#e63946;font-weight:bold;text-align:center;margin-bottom:1rem;"></div>
            <form method="post" id="deleteUserForm">
                <input type="hidden" name="delete_user_id" id="delete_user_id">
                <div class="modal-actions">
                    <button type="submit" class="delete-btn-confirm action-btn delete">حذف</button>
                    <button type="button" class="close-delete-user-modal action-btn" onclick="document.getElementById('deleteUserModal').classList.remove('active')">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
    <?php if (isset($error) && !empty($error)): ?>
        <p style="color:red; text-align:center; font-weight:bold;"> <?= htmlspecialchars($error) ?> </p>
    <?php endif; ?>
</main>
<script src="./js/users.js"></script>
<script>
// بحث مباشر أو عند الضغط على زر البحث
const searchInput = document.getElementById('searchUser');
const searchBtn = document.getElementById('searchUserBtn');
function filterUsers() {
    const value = searchInput.value.trim().toLowerCase();
    document.querySelectorAll('#usersTable tr').forEach(row => {
        const username = row.children[0].textContent.toLowerCase();
        const email = row.children[1].textContent.toLowerCase();
        row.style.display = (username.includes(value) || email.includes(value)) ? '' : 'none';
    });
}
searchInput.addEventListener('input', filterUsers);
searchBtn.addEventListener('click', filterUsers);

// مودال التعديل
function openEditUserModal(id, username, email, is_active) {
    document.querySelector('.edit-user-modal').style.display = 'flex';
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_is_active').checked = is_active == 1;
}
document.querySelectorAll('.close-edit-user-modal').forEach(btn => {
    btn.onclick = () => document.querySelector('.edit-user-modal').style.display = 'none';
});

// مودال الحذف
function openDeleteUserModal(id, username) {
    document.getElementById('delete_user_id').value = id;
    document.getElementById('deleteUserName').textContent = '"' + username + '"';
    document.getElementById('deleteUserModal').classList.add('active');
}
document.querySelector('.close-delete-user-modal').onclick = function() {
    document.getElementById('deleteUserModal').classList.remove('active');
};
// مودال الإضافة
// إصلاح زر الإغلاق لمودال الإضافة
if(document.querySelector('.close-modal')) {
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.onclick = function() {
            document.querySelector('.add-user-modal').classList.remove('active');
        };
    });
}
window.onclick = function(e) {
    if (e.target === document.getElementById('deleteUserModal')) {
        document.getElementById('deleteUserModal').classList.remove('active');
    }
    if (e.target === document.querySelector('.add-user-modal')) {
        document.querySelector('.add-user-modal').classList.remove('active');
    }
    if (e.target === document.querySelector('.edit-user-modal')) {
        document.querySelector('.edit-user-modal').style.display = 'none';
    }
}

// الوضع المظلم
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
</body>
</html>
