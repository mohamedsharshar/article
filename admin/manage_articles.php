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
    <link rel="stylesheet" href="./css/manage_articles.css">
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
        .articles-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0001;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .articles-table th, .articles-table td {
            padding: 1rem 0.7rem;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }
        .articles-table th {
            background: #f3f6fa;
            color: #4e73df;
            font-weight: bold;
        }
        .articles-table tr:last-child td {
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
        .add-article-btn {
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
        .add-article-btn:hover {
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
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة المقالات</h1>
    <button class="add-article-btn"><i class="fa fa-plus"></i> إضافة مقال جديد</button>
    <table class="data-table articles-table">
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
    <div class="add-article-modal modal" style="display:none;">
        <form action="manage_articles.php" method="post">
            <div class="modal-content">
                <div class="modal-header">إضافة مقال جديد</div>
                <div class="form-group">
                    <label for="title">عنوان المقال</label>
                    <input type="text" name="title" id="title" placeholder="أدخل عنوان المقال" required>
                </div>
                <div class="form-group">
                    <label for="content">محتوى المقال</label>
                    <textarea name="content" id="content" rows="4" placeholder="أدخل محتوى المقال" required></textarea>
                </div>
                <div class="modal-actions">
                    <button type="submit" name="add_article" class="add-article-btn">إضافة مقال</button>
                    <button type="button" class="close-modal">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
    <!-- مودال تعديل مقال -->
    <div class="edit-article-modal modal" style="display:none;">
        <form action="manage_articles.php" method="post">
            <div class="modal-content">
                <div class="modal-header">تعديل مقال</div>
                <input type="hidden" name="edit_article_id" id="edit_article_id">
                <div class="form-group">
                    <label for="edit_title">عنوان المقال</label>
                    <input type="text" name="edit_title" id="edit_title" placeholder="أدخل عنوان المقال" required>
                </div>
                <div class="form-group">
                    <label for="edit_content">محتوى المقال</label>
                    <textarea name="edit_content" id="edit_content" rows="4" placeholder="أدخل محتوى المقال" required></textarea>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="add-article-btn">حفظ التعديلات</button>
                    <button type="button" class="close-edit-modal">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
    <!-- مودال عرض تفاصيل المقال -->
    <div class="view-article-modal modal" id="viewArticleModal">
        <div class="modal-content">
            <div class="modal-header">تفاصيل المقال</div>
            <div class="form-group">
                <label>العنوان</label>
                <div id="viewArticleTitle" style="font-weight:bold;"></div>
            </div>
            <div class="form-group">
                <label>المحتوى</label>
                <div id="viewArticleContent" style="white-space:pre-line;"></div>
            </div>
            <div class="modal-actions">
                <button type="button" class="close-view-modal">إغلاق</button>
            </div>
        </div>
    </div>
    <!-- مودال تأكيد الحذف -->
    <div class="delete-article-modal modal" id="deleteArticleModal">
        <div class="modal-content">
            <div class="modal-header">تأكيد حذف المقال</div>
            <div class="form-group">
                <label>المقال المحدد للحذف</label>
                <div id="deleteArticleTitle" style="color:#e63946;font-weight:bold;"></div>
            </div>
            <form method="post" id="deleteArticleForm">
                <input type="hidden" name="delete_article_id" id="delete_article_id">
                <div class="modal-actions">
                    <button type="submit" class="delete-btn-confirm">حذف المقال</button>
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
