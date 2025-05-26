<?php
session_start();
require_once '../db.php';

// معالجة إضافة مقال جديد من نفس الصفحة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_article'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    if ($title && $content) {
        $stmt = $pdo->prepare('INSERT INTO articles (title, content, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$title, $content]);
        header('Location: manage_articles.php?success=1');
        exit();
    } else {
        header('Location: manage_articles.php?error=1');
        exit();
    }
}

// حذف مقال
if (isset($_POST['delete_article_id'])) {
    $id = intval($_POST['delete_article_id']);
    $stmt = $pdo->prepare('DELETE FROM articles WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: manage_articles.php?deleted=1');
    exit();
}
// تعديل مقال
if (isset($_POST['edit_article_id'])) {
    $id = intval($_POST['edit_article_id']);
    $title = trim($_POST['edit_title']);
    $content = trim($_POST['edit_content']);
    if ($title && $content) {
        $stmt = $pdo->prepare('UPDATE articles SET title = ?, content = ? WHERE id = ?');
        $stmt->execute([$title, $content, $id]);
        header('Location: manage_articles.php?edited=1');
        exit();
    }
}

$articles = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المقالات</title>
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="./css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/manage_articles.css">
    <style>
    /* تصميم خاص بصفحة إدارة المقالات */
    .manage-articles-title {
        color: #2d3142;
        margin-bottom: 32px;
        font-size: 2.2rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .add-article-btn {
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
    }
    .add-article-btn:hover {
        background: linear-gradient(90deg, #4361ee 0%, #3a86ff 100%);
        box-shadow: 0 4px 16px rgba(67,97,238,0.13);
    }
    .data-table {
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
    .data-table thead tr {
        background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
        color: #fff;
    }
    .data-table th, .data-table td {
        padding: 16px 18px;
        text-align: right;
        border-bottom: 1px solid #f0f4fa;
    }
    .data-table th {
        font-weight: bold;
        font-size: 1.1rem;
        letter-spacing: 0.01em;
    }
    .data-table tbody tr {
        transition: background 0.2s;
    }
    .data-table tbody tr:hover {
        background: #f1f5f9;
    }
    .data-table td {
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
    }
    .action-btn:hover {
        background: #3a86ff;
        color: #fff;
    }
    .add-article-modal,
    .edit-article-modal,
    .view-article-modal,
    .delete-article-modal {
        position: fixed;
        top: 0; right: 0; left: 0; bottom: 0;
        background: rgba(60, 60, 90, 0.13);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.3s;
    }
    .add-article-modal.active,
    .edit-article-modal.active,
    .view-article-modal.active,
    .delete-article-modal.active {
        display: flex;
    }
    .add-article-modal form,
    .edit-article-modal form,
    .view-article-modal .view-content,
    .delete-article-modal .delete-content {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 24px rgba(67,97,238,0.13);
        padding: 32px 28px 24px 28px;
        min-width: 320px;
        max-width: 90vw;
        max-height: 80vh;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
        animation: bounceIn 0.5s;
    }
    .add-article-modal input[type="text"],
    .add-article-modal textarea,
    .edit-article-modal input[type="text"],
    .edit-article-modal textarea {
        border: 1px solid #dbeafe;
        border-radius: 8px;
        padding: 10px 8px;
        background: #f1f5f9;
        font-size: 1rem;
        transition: border 0.2s;
        resize: none;
    }
    .add-article-modal input:focus,
    .add-article-modal textarea:focus,
    .edit-article-modal input:focus,
    .edit-article-modal textarea:focus {
        border-color: #3a86ff;
        outline: none;
    }
    .add-article-modal button[type="submit"],
    .edit-article-modal button[type="submit"],
    .delete-btn-confirm {
        background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 0;
        font-size: 1.08rem;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.2s;
    }
    .add-article-modal button[type="submit"]:hover,
    .edit-article-modal button[type="submit"]:hover,
    .delete-btn-confirm:hover {
        background: linear-gradient(90deg, #4361ee 0%, #3a86ff 100%);
    }
    .delete-btn-confirm {
        background: linear-gradient(90deg, #e63946 0%, #ff6b6b 100%);
    }
    .delete-btn-confirm:hover {
        background: linear-gradient(90deg, #ff6b6b 0%, #e63946 100%);
    }
    .add-article-modal .close-modal,
    .edit-article-modal .close-edit-modal,
    .view-article-modal .close-view-modal,
    .delete-article-modal .close-delete-modal {
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
    }
    .add-article-modal .close-modal:hover,
    .edit-article-modal .close-edit-modal:hover,
    .view-article-modal .close-view-modal:hover,
    .delete-article-modal .close-delete-modal:hover {
        background: #3a86ff;
        color: #fff;
    }
    @media (max-width: 700px) {
        .data-table th, .data-table td {
            padding: 10px 6px;
            font-size: 0.98rem;
        }
        .add-article-modal form,
        .edit-article-modal form,
        .view-article-modal .view-content,
        .delete-article-modal .delete-content {
            min-width: 90vw;
            padding: 18px 8px 16px 8px;
        }
    }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<main class="main-content">
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة المقالات</h1>
    <button class="add-article-btn"><i class="fa fa-plus"></i> إضافة مقال جديد</button>
    <table class="data-table">
        <thead>
            <tr>
                <th>العنوان</th>
                <th>المحتوى المختصر</th>
                <th>تاريخ النشر</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody id="articlesTable">
            <?php foreach($articles as $article): ?>
            <tr>
                <td><?= htmlspecialchars($article['title']) ?></td>
                <td><?= htmlspecialchars(mb_substr($article['content'],0,50)).'...' ?></td>
                <td><?= htmlspecialchars($article['created_at']) ?></td>
                <td>
                    <!-- زر عرض التفاصيل -->
                    <button class="action-btn view-btn" onclick="openViewModal('<?= htmlspecialchars(addslashes($article['title'])) ?>', '<?= htmlspecialchars(addslashes($article['content'])) ?>')">
                        <i class="fa fa-eye"></i>
                    </button>
                    <!-- زر تعديل -->
                    <button class="action-btn edit-btn"
                        onclick="openEditModal(<?= $article['id'] ?>, '<?= htmlspecialchars(addslashes($article['title'])) ?>', '<?= htmlspecialchars(addslashes($article['content'])) ?>')">
                        <i class="fa fa-edit"></i>
                    </button>
                    <!-- زر حذف -->
                    <button class="action-btn delete-btn" onclick="openDeleteModal(<?= $article['id'] ?>, '<?= htmlspecialchars(addslashes($article['title'])) ?>')">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- نموذج إضافة مقال جديد (يظهر عند الضغط على الزر) -->
    <div class="add-article-modal" style="display:none;">
        <form action="manage_articles.php" method="post">
            <input type="text" name="title" placeholder="عنوان المقال" required>
            <textarea name="content" rows="4" placeholder="محتوى المقال" required></textarea>
            <button type="submit" name="add_article">إضافة</button>
            <button type="button" class="close-modal">إلغاء</button>
        </form>
    </div>
    <!-- مودال تعديل مقال -->
    <div class="edit-article-modal" style="display:none;">
        <form action="manage_articles.php" method="post">
            <input type="hidden" name="edit_article_id" id="edit_article_id">
            <input type="text" name="edit_title" id="edit_title" placeholder="عنوان المقال" required>
            <textarea name="edit_content" id="edit_content" rows="4" placeholder="محتوى المقال" required></textarea>
            <button type="submit">حفظ التعديلات</button>
            <button type="button" class="close-edit-modal">إلغاء</button>
        </form>
    </div>
    <!-- مودال عرض تفاصيل المقال -->
    <div class="view-article-modal" id="viewArticleModal">
        <div class="view-content">
            <h3 id="viewArticleTitle"></h3>
            <div id="viewArticleContent" style="white-space:pre-line;"></div>
            <button type="button" class="close-view-modal">إغلاق</button>
        </div>
    </div>
    <!-- مودال تأكيد الحذف -->
    <div class="delete-article-modal" id="deleteArticleModal">
        <div class="delete-content">
            <h3>تأكيد حذف المقال</h3>
            <div id="deleteArticleTitle" style="color:#e63946;font-weight:bold;"></div>
            <form method="post" id="deleteArticleForm">
                <input type="hidden" name="delete_article_id" id="delete_article_id">
                <div class="delete-actions">
                    <button type="submit" class="delete-btn-confirm">حذف</button>
                    <button type="button" class="close-delete-modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</main>
<script src="./js/articles.js"></script>
<script>
function openEditModal(id, title, content) {
    document.querySelector('.edit-article-modal').style.display = 'flex';
    document.getElementById('edit_article_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_content').value = content;
}
document.querySelectorAll('.close-edit-modal').forEach(btn => {
    btn.onclick = () => document.querySelector('.edit-article-modal').style.display = 'none';
});
function openViewModal(title, content) {
    document.getElementById('viewArticleTitle').textContent = title;
    document.getElementById('viewArticleContent').textContent = content;
    document.getElementById('viewArticleModal').classList.add('active');
}
document.querySelector('.close-view-modal').onclick = function() {
    document.getElementById('viewArticleModal').classList.remove('active');
};
function openDeleteModal(id, title) {
    document.getElementById('delete_article_id').value = id;
    document.getElementById('deleteArticleTitle').textContent = '"' + title + '"';
    document.getElementById('deleteArticleModal').classList.add('active');
}
document.querySelector('.close-delete-modal').onclick = function() {
    document.getElementById('deleteArticleModal').classList.remove('active');
};
// إغلاق المودال عند الضغط خارج النموذج
window.onclick = function(e) {
    if (e.target === document.getElementById('viewArticleModal')) {
        document.getElementById('viewArticleModal').classList.remove('active');
    }
    if (e.target === document.getElementById('deleteArticleModal')) {
        document.getElementById('deleteArticleModal').classList.remove('active');
    }
}
</script>
</body>
</html>
