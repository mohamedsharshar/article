<?php
session_start();
require_once '../db.php';

// إضافة تعليق أدمن
if (isset($_POST['add_comment']) && isset($_POST['article_id'])) {
    $comment = trim($_POST['comment']);
    $article_id = intval($_POST['article_id']);
    if ($comment) {
        $stmt = $pdo->prepare('INSERT INTO comments (article_id, content, is_admin, admin_id, created_at) VALUES (?, ?, 1, ?, NOW())');
        $stmt->execute([$article_id, $comment, $_SESSION['admin_id'] ?? null]);
        header('Location: manage_comments.php?success=1');
        exit();
    }
}

// حذف تعليق
if (isset($_POST['delete_comment_id'])) {
    $comment_id = intval($_POST['delete_comment_id']);
    $pdo->prepare('DELETE FROM comments WHERE id = ?')->execute([$comment_id]);
    header('Location: manage_comments.php?deleted=1');
    exit();
}

// جلب المقالات لاستخدامها في نموذج إضافة تعليق
$articles_list = $pdo->query('SELECT id, title FROM articles ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);

$comments = $pdo->query("SELECT c.*, a.title,
    admins.adminname AS admin_name,
    users.username AS user_name
FROM comments c
JOIN articles a ON c.article_id = a.id
LEFT JOIN admins ON c.admin_id = admins.id
LEFT JOIN users ON c.user_id = users.id
ORDER BY c.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة التعليقات</title>
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
        .add-comment-btn {
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
        .add-comment-btn:hover {
            background: linear-gradient(90deg, #4361ee 0%, #3a86ff 100%);
            box-shadow: 0 4px 16px rgba(67,97,238,0.13);
        }
        .comments-table {
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
        .comments-table thead tr {
            background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
            color: #fff;
        }
        .comments-table th, .comments-table td {
            padding: 16px 18px;
            text-align: right;
            border-bottom: 1px solid #f0f4fa;
        }
        .comments-table th {
            font-weight: bold;
            font-size: 1.1rem;
            letter-spacing: 0.01em;
        }
        .comments-table tbody tr {
            transition: background 0.2s;
        }
        .comments-table tbody tr:hover {
            background: #f1f5f9;
        }
        .comments-table td {
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
        .add-comment-btn[type="submit"], .modal-actions .add-comment-btn {
            background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
            color: #fff;
            width: 100%;
            justify-content: center;
            align-items: center;
            display: flex;
        }
        .add-comment-btn[type="submit"]:hover, .modal-actions .add-comment-btn:hover {
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
        .add-comment-flex-modern {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }
        .add-comment-flex-modern .modern-input {
            order: 1;
            flex: 0 1 80%;
            max-width: 80%;
            min-width: 120px;
            padding: 10px 16px;
            border-radius: 10px;
            border: 1.5px solid #3a86ff;
            background: #f8fafc;
            font-size: 1.08rem;
            color: #2d3142;
            box-shadow: 0 2px 8px #3a86ff11;
            transition: border 0.2s, box-shadow 0.2s;
            outline: none;
            font-family: inherit;
            font-weight: 500;
        }
        .add-comment-flex-modern .modern-input:focus {
            border-color: #4361ee;
            box-shadow: 0 4px 16px #4361ee22;
        }
        .add-comment-flex-modern .modern-select {
            order: 2;
            flex: 0 0 180px;
            min-width: 120px;
            max-width: 220px;
            padding: 10px 16px;
            border-radius: 10px;
            border: 1.5px solid #3a86ff;
            background: #f8fafc;
            font-size: 1.08rem;
            color: #2d3142;
            box-shadow: 0 2px 8px #3a86ff11;
            transition: border 0.2s, box-shadow 0.2s;
            outline: none;
            font-family: inherit;
            font-weight: 500;
        }
        .add-comment-flex-modern .modern-select:focus {
            border-color: #4361ee;
            box-shadow: 0 4px 16px #4361ee22;
        }
        .add-comment-flex-modern .modern-search-btn {
            order: 3;
            flex: 0 1 20%;
            max-width: 20%;
            min-width: 120px;
            background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 22px;
            font-size: 1.08rem;
            font-weight: bold;
            box-shadow: 0 2px 8px #3a86ff22;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: background 0.2s, box-shadow 0.2s;
            cursor: pointer;
            justify-content: center;
        }
        .add-comment-flex-modern .modern-search-btn:hover {
            background: linear-gradient(90deg, #4361ee 0%, #3a86ff 100%);
            box-shadow: 0 4px 16px #4361ee33;
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
        [data-theme="dark"] .comments-table, [data-theme="dark"] .data-table {
            background: #1e293b !important;
            color: #fff !important;
            border-color: #334155 !important;
        }
        [data-theme="dark"] .comments-table thead tr {
            background: linear-gradient(90deg, #334155 0%, #1e293b 100%) !important;
            color: #fff !important;
        }
        [data-theme="dark"] .comments-table th, [data-theme="dark"] .comments-table td {
            border-bottom: 1px solid #334155 !important;
            color: #fff !important;
        }
        [data-theme="dark"] .comments-table tbody tr:hover {
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
        [data-theme="dark"] .add-comment-btn {
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
        [data-theme="dark"] .add-comment-flex-modern .modern-input,
        [data-theme="dark"] .add-comment-flex-modern .modern-select {
            background: #22304a !important;
            color: #fff !important;
            border: 1.5px solid #334155 !important;
            box-shadow: 0 2px 8px #0f172a33 !important;
        }
        [data-theme="dark"] .add-comment-flex-modern .modern-input:focus,
        [data-theme="dark"] .add-comment-flex-modern .modern-select:focus {
            border-color: #60a5fa !important;
            box-shadow: 0 4px 16px #60a5fa33 !important;
        }
        [data-theme="dark"] .add-comment-flex-modern .modern-search-btn {
            background: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%) !important;
            color: #fff !important;
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
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة التعليقات</h1>
    <!-- نموذج إضافة تعليق أدمن -->
    <div class="add-comment-admin-modal" style="margin-bottom:24px;">
        <form action="manage_comments.php" method="post" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <div class="disp add-comment-flex-modern">
                <input type="text" name="comment" placeholder="تعليقك هنا..." required class="modern-input">
                <select name="article_id" required class="modern-select">
                    <option value="">اختر مقالاً</option>
                    <?php foreach($articles_list as $art): ?>
                        <option value="<?= $art['id'] ?>"><?= htmlspecialchars($art['title']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="add_comment" class="action-btn modern-search-btn"><i class="fa fa-paper-plane"></i> إضافة تعليق</button>
            </div>
        </form>
    </div>
    <!-- مودال تأكيد حذف التعليق -->
    <div class="delete-comment-modal modal" id="deleteCommentModal">
        <div class="modal-content">
            <div class="modal-header">تأكيد حذف التعليق</div>
            <div class="form-group">
                <label>التعليق المحدد للحذف</label>
                <div id="deleteCommentContent" style="color:#e63946;font-weight:bold;word-break:break-word;"></div>
            </div>
            <form method="post" id="deleteCommentForm">
                <input type="hidden" name="delete_comment_id" id="delete_comment_id">
                <div class="modal-actions">
                    <button type="submit" class="delete-btn-confirm">حذف التعليق</button>
                    <button type="button" class="close-delete-modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
    <table class="data-table comments-table">
        <thead>
            <tr>
                <th>التعليق</th>
                <th>المقال</th>
                <th>الكاتب</th>
                <th>تاريخ</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody id="commentsTable">
            <?php foreach($comments as $comment): ?>
            <tr>
                <td><?= htmlspecialchars($comment['content']) ?></td>
                <td><?= htmlspecialchars($comment['title']) ?></td>
                <td>
                    <?php
                        if ($comment['is_admin'] == 1 && !empty($comment['admin_name'])) {
                            echo htmlspecialchars($comment['admin_name']) . ' (أدمن)';
                        } elseif ($comment['is_admin'] == 0 && !empty($comment['user_name'])) {
                            echo htmlspecialchars($comment['user_name']) . ' (مستخدم)';
                        } else {
                            echo '-';
                        }
                    ?>
                </td>
                <td><?= htmlspecialchars($comment['created_at']) ?></td>
                <td>
                    <button type="button" class="action-btn delete-btn" onclick="openDeleteCommentModal(<?= $comment['id'] ?>, '<?= htmlspecialchars(addslashes($comment['content'])) ?>')"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
<!-- زر تبديل الوضع الليلي -->
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
        // فتح مودال الحذف مع تمرير بيانات التعليق
        function openDeleteCommentModal(id, content) {
            document.getElementById('delete_comment_id').value = id;
            document.getElementById('deleteCommentContent').textContent = content;
            document.getElementById('deleteCommentModal').classList.add('active');
        }
        // إغلاق المودال
        document.querySelectorAll('.close-delete-modal').forEach(btn => {
            btn.onclick = function() {
                document.getElementById('deleteCommentModal').classList.remove('active');
            };
        });
        // إغلاق عند الضغط خارج المودال
        window.onclick = function(e) {
            if (e.target === document.getElementById('deleteCommentModal')) {
                document.getElementById('deleteCommentModal').classList.remove('active');
            }
        }
        </script>
</body>
</html>
