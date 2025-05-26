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

$comments = $pdo->query("SELECT c.*, a.title, IF(is_admin=1,'مشرف','مستخدم') as author_type FROM comments c JOIN articles a ON c.article_id = a.id ORDER BY c.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
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
        .comments-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0001;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .comments-table th, .comments-table td {
            padding: 1rem 0.7rem;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }
        .comments-table th {
            background: #f3f6fa;
            color: #4e73df;
            font-weight: bold;
        }
        .comments-table tr:last-child td {
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
        .add-comment-btn {
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
        .add-comment-btn:hover {
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
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة التعليقات</h1>
    <!-- نموذج إضافة تعليق أدمن -->
    <div class="add-comment-admin-modal" style="margin-bottom:24px;">
        <form action="manage_comments.php" method="post" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <select name="article_id" required style="padding:7px 12px;border-radius:8px;border:1px solid #dbeafe;min-width:160px;">
                <option value="">اختر مقالاً</option>
                <?php foreach($articles_list as $art): ?>
                    <option value="<?= $art['id'] ?>"><?= htmlspecialchars($art['title']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="comment" placeholder="تعليقك هنا..." required style="flex:1;padding:7px 12px;border-radius:8px;border:1px solid #dbeafe;">
            <button type="submit" name="add_comment" class="action-btn" style="background:linear-gradient(90deg,#3a86ff 0%,#4361ee 100%);color:#fff;">إضافة تعليق</button>
        </form>
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
                <td><?= $comment['author_type'] ?></td>
                <td><?= htmlspecialchars($comment['created_at']) ?></td>
                <td>
                    <form method="post" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا التعليق؟');">
                        <input type="hidden" name="delete_comment_id" value="<?= $comment['id'] ?>">
                        <button type="submit" class="action-btn delete-btn"><i class="fa fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
<script src="js/comments.js"></script>
</body>
</html>
