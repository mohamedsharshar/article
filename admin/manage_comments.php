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
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
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
    <table class="data-table">
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
