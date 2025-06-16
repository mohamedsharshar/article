<?php
session_start();
require_once '../db.php';

// جلب التصنيفات
$categories = $pdo->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);

// جلب بيانات الأدمن الحالي
$current_admin_id = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : null;

// معالجة إضافة مقال جديد من نفس الصفحة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_article'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
        $uploadsDir = '../uploads/articles/';
        if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0777, true);
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($ext, $allowed)) {
            $imageName = uniqid('art_', true) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadsDir . $imageName);
        }
    }
    if ($title && $content) {
        $stmt = $pdo->prepare('INSERT INTO articles (title, content, image, category_id, admin_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$title, $content, $imageName, $category_id, $current_admin_id]);
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
    $edit_category_id = isset($_POST['edit_category_id']) ? intval($_POST['edit_category_id']) : null;
    $imageName = null;
    if (isset($_FILES['edit_image']) && $_FILES['edit_image']['tmp_name']) {
        $uploadsDir = '../uploads/articles/';
        if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0777, true);
        $ext = strtolower(pathinfo($_FILES['edit_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($ext, $allowed)) {
            $imageName = uniqid('art_', true) . '.' . $ext;
            move_uploaded_file($_FILES['edit_image']['tmp_name'], $uploadsDir . $imageName);
        }
    }
    if ($title && $content) {
        if ($imageName) {
            $stmt = $pdo->prepare('UPDATE articles SET title = ?, content = ?, image = ?, category_id = ? WHERE id = ?');
            $stmt->execute([$title, $content, $imageName, $edit_category_id, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE articles SET title = ?, content = ?, category_id = ? WHERE id = ?');
            $stmt->execute([$title, $content, $edit_category_id, $id]);
        }
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
        .add-article-btn {
            background: linear-gradient(90deg, #4262ed 0%, #3a86ff 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-size: 1.15rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 18px 0 #4262ed22, 0 1.5px 6px #3a86ff11;
            transition: background 0.2s, box-shadow 0.2s, transform 0.13s;
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
            min-width: 170px;
            letter-spacing: 0.01em;
        }
        .add-article-btn i {
            font-size: 1.25em;
        }
        .add-article-btn:hover {
            background: linear-gradient(90deg, #3a86ff 0%, #4262ed 100%);
            box-shadow: 0 8px 32px 0 #4262ed33, 0 2px 8px #3a86ff22;
            transform: translateY(-2px) scale(1.04);
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
        .action-btn.view-btn {
            background: #4e73df;
            color: #fff;
            text-decoration: none;
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
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(67,97,238,0.13);
            padding: 32px 8px 24px 28px;
            min-width: 400px;
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
        .add-article-btn[type="submit"], .modal-actions .add-article-btn {
            background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
            color: #fff;
            width: 100%;
            justify-content: center;
            align-items: center;
            display: flex;
        }
        .add-article-btn[type="submit"]:hover, .modal-actions .add-article-btn:hover {
            background: linear-gradient(90deg, #4361ee 0%, #3a86ff 100%);
        }
        .delete-btn-confirm {
            background: linear-gradient(90deg, #e63946 0%, #ff6b6b 100%);
            color: #fff;
        }
        .delete-btn-confirm:hover {
            background: linear-gradient(90deg, #ff6b6b 0%, #e63946 100%);
        }
        .close-modal, .close-edit-modal, .close-view-modal, .close-delete-modal {
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
        .close-modal:hover, .close-edit-modal:hover, .close-view-modal:hover, .close-delete-modal:hover {
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
            .data-table th, .data-table td {
                padding: 10px 6px;
                font-size: 0.98rem;
            }
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<main class="main-content">
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة المقالات</h1>
    <div style="display:flex;gap:10px;align-items:center;margin-bottom:18px;">
        <input type="text" id="searchArticle" placeholder="بحث عن مقال..." class="search-input">
        <button type="button" id="searchArticleBtn" class="action-btn" style="background:linear-gradient(90deg,#3a86ff 0%,#4361ee 100%);color:#fff;font-weight:bold;padding:10px 24px;min-width:120px;display:flex;align-items:center;gap:7px;"><i class="fa fa-search"></i> بحث</button>
        <button class="add-article-btn" onclick="document.querySelector('.add-article-modal').style.display='flex'"><i class="fa fa-plus"></i> إضافة مقال </button>
    </div>
    <table class="data-table articles-table">
        <thead>
            <tr>
                <th>الصورة</th>
                <th>العنوان</th>
                <th>المحتوى المختصر</th>
                <th>تاريخ النشر</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody id="articlesTable">
            <?php foreach($articles as $article): ?>
            <tr>
                <td>
                    <?php if (!empty($article['image'])): ?>
                        <img src="../uploads/articles/<?= htmlspecialchars($article['image']) ?>" alt="صورة المقال" style="width:60px;height:40px;object-fit:cover;border-radius:6px;box-shadow:0 1px 4px #0002;">
                    <?php else: ?>
                        <span style="color:#bbb;font-size:1.2em;">—</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($article['title']) ?></td>
                <td><?= htmlspecialchars(mb_substr($article['content'],0,50)).'...' ?></td>
                <td><?= htmlspecialchars($article['created_at']) ?></td>
                <td>
                    <!-- زر عرض التفاصيل -->
                    <a class="action-btn view-btn" href="article_view.php?id=<?= $article['id'] ?>" target="_blank">
                        <i class="fa fa-eye"></i>
                    </a>
                    <!-- زر تعديل -->
                    <button class="action-btn edit-btn" 
                        data-id="<?= $article['id'] ?>"
                        data-title='<?= htmlspecialchars($article['title'], ENT_QUOTES) ?>'
                        data-content='<?= htmlspecialchars($article['content'], ENT_QUOTES) ?>'
                        data-category_id="<?= $article['category_id'] ?>"
                        onclick="handleEditBtnClickSafe(this)">
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
        <form action="manage_articles.php" method="post" enctype="multipart/form-data">
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
                <div class="form-group">
                    <label for="category_id">التصنيف</label>
                    <select name="category_id" id="category_id" required>
                        <option value="">اختر التصنيف</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="image">صورة المقال</label>
                    <input type="file" name="image" id="image" accept="image/*">
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
        <form action="manage_articles.php" method="post" enctype="multipart/form-data">
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
                <div class="form-group">
                    <label for="edit_category_id">التصنيف</label>
                    <select name="edit_category_id" id="edit_category_id" required>
                        <option value="">اختر التصنيف</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_image">تغيير صورة المقال (اختياري)</label>
                    <input type="file" name="edit_image" id="edit_image" accept="image/*">
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
function openEditModal(id, title, content, category_id) {
    document.querySelector('.edit-article-modal').style.display = 'flex';
    document.getElementById('edit_article_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_content').value = content;
    document.getElementById('edit_category_id').value = category_id;
}
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.onclick = function() {
        const id = this.getAttribute('data-id');
        const title = this.getAttribute('data-title');
        const content = this.getAttribute('data-content');
        const category_id = this.getAttribute('data-category_id');
        openEditModal(id, title, content, category_id);
    };
});
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
// بحث مباشر أو عند الضغط على زر البحث
const searchInput = document.getElementById('searchArticle');
const searchBtn = document.getElementById('searchArticleBtn');
function filterArticles() {
    const value = searchInput.value.trim().toLowerCase();
    document.querySelectorAll('#articlesTable tr').forEach(row => {
        const title = row.children[1].textContent.toLowerCase(); // العنوان
        const content = row.children[2].textContent.toLowerCase(); // المحتوى المختصر
        row.style.display = (title.includes(value) || content.includes(value)) ? '' : 'none';
    });
}
searchInput.addEventListener('input', filterArticles);
searchBtn.addEventListener('click', filterArticles);
function handleEditBtnClickSafe(btn) {
    const id = btn.getAttribute('data-id');
    const title = btn.getAttribute('data-title');
    const content = btn.getAttribute('data-content');
    openEditModal(id, title, content);
}
</script>
</body>
</html>
