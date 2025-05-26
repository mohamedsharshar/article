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
    if ($username && $email) {
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
        $stmt->execute([$username, $email, $id]);
        header('Location: users.php?edited=1');
        exit();
    }
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
        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0001;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .users-table th, .users-table td {
            padding: 1rem 0.7rem;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }
        .users-table th {
            background: #f3f6fa;
            color: #4e73df;
            font-weight: bold;
        }
        .users-table tr:last-child td {
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
        .add-user-btn {
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
        .add-user-btn:hover {
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
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة المستخدمين</h1>
    <input type="text" id="searchUser" placeholder="بحث عن مستخدم..." class="search-input" style="margin-bottom:18px;padding:10px 18px;border-radius:8px;border:1px solid #dbeafe;font-size:1.05rem;width:320px;max-width:100%;background:#f8fafc;">
    <table class="data-table users-table">
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
                    <button class="action-btn edit-btn" onclick="openEditUserModal(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['username'])) ?>', '<?= htmlspecialchars(addslashes($user['email'])) ?>')"><i class="fa fa-edit"></i></button>
                    <button class="action-btn delete-btn" onclick="openDeleteUserModal(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['username'])) ?>')"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

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
</main>
<script src="js/users.js"></script>
<script>
// بحث مباشر
const searchInput = document.getElementById('searchUser');
searchInput.addEventListener('input', function() {
    const value = this.value.trim().toLowerCase();
    document.querySelectorAll('#usersTable tr').forEach(row => {
        const username = row.children[0].textContent.toLowerCase();
        const email = row.children[1].textContent.toLowerCase();
        row.style.display = (username.includes(value) || email.includes(value)) ? '' : 'none';
    });
});

// مودال التعديل
function openEditUserModal(id, username, email) {
    document.querySelector('.edit-user-modal').style.display = 'flex';
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
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
window.onclick = function(e) {
    if (e.target === document.getElementById('deleteUserModal')) {
        document.getElementById('deleteUserModal').classList.remove('active');
    }
}
</script>
</body>
</html>
