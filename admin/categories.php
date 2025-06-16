<?php
// admin/categories.php
require_once '../db.php';

// إضافة تصنيف جديد
$error = '';
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    try {
        $stmt = $pdo->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)');
        $stmt->execute([$name, $slug]);
        header('Location: categories.php');
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = 'اسم التصنيف أو الـ slug موجود بالفعل!';
        } else {
            $error = 'حدث خطأ أثناء الإضافة.';
        }
    }
}

// تعديل تصنيف
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    try {
        $stmt = $pdo->prepare('UPDATE categories SET name=?, slug=? WHERE id=?');
        $stmt->execute([$name, $slug, $id]);
        header('Location: categories.php');
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = 'اسم التصنيف أو الـ slug موجود بالفعل!';
        } else {
            $error = 'حدث خطأ أثناء التعديل.';
        }
    }
}

// حذف تصنيف
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id=?');
    $stmt->execute([$id]);
    header('Location: categories.php');
    exit;
}

// جلب كل التصنيفات مع دعم البحث
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE name LIKE ? OR slug LIKE ?');
    $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->query('SELECT * FROM categories');
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// لو فيه تعديل
$editCategory = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id=?');
    $stmt->execute([$id]);
    $editCategory = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة التصنيفات</title>
    <link rel="stylesheet" href="css/manage_articles.css">
    <style>
        body {
            direction: rtl;
            font-family: 'Cairo', Arial, sans-serif;
            background: #f8fafc;
        }
        .categories-container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(67,97,238,0.13);
            padding: 32px 32px 24px 32px;
            overflow: hidden;
        }
        .categories-title {
            color: #2d3142;
            margin-bottom: 32px;
            font-size: 2.2rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .category-form {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .category-form input[type="text"] {
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1.08rem;
            background: #f8fafc;
            transition: border 0.2s;
        }
        .category-form input[type="text"]:focus {
            border: 1.5px solid #3a86ff;
            outline: none;
        }
        .category-form button, .category-form a {
            background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 22px;
            font-size: 1.08rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(67,97,238,0.07);
            transition: background 0.2s, box-shadow 0.2s;
            text-decoration: none;
            margin-right: 4px;
        }
        .category-form button:hover, .category-form a:hover {
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
            margin-top: 12px;
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
        .data-table tr:last-child td {
            border-bottom: none;
        }
        .data-table a {
            color: #3a86ff;
            font-weight: bold;
            text-decoration: none;
            margin: 0 4px;
        }
        .data-table a:hover {
            text-decoration: underline;
        }
        @media (max-width: 700px) {
            .categories-container {
                padding: 12px 2vw;
            }
            .category-form {
                flex-direction: column;
                gap: 8px;
            }
            .data-table th, .data-table td {
                padding: 8px 6px;
            }
        }
    </style>
</head>
<body>
    <div class="categories-container">
        <a href="dashboard.php" style="display:inline-block;margin-bottom:18px;background:linear-gradient(90deg,#3a86ff 0%,#4361ee 100%);color:#fff;padding:10px 22px;border-radius:8px;text-decoration:none;font-weight:bold;box-shadow:0 2px 8px rgba(67,97,238,0.07);transition:background 0.2s,box-shadow 0.2s;">&larr; الرجوع للوحة التحكم</a>
        <?php if (!empty($error)): ?>
            <div style="background:#ffe0e0;color:#d32f2f;padding:10px 18px;border-radius:8px;margin-bottom:18px;font-weight:bold;">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <div class="categories-title"><i class="fa fa-tags"></i> إدارة التصنيفات</div>
        <form method="get" id="searchForm" style="margin-bottom:18px;display:flex;gap:10px;width:100%;margin-right:auto;margin-left:auto;">
            <input type="text" name="search" id="searchInput" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" placeholder="ابحث عن تصنيف..." style="flex:8 1 0%;padding:10px 14px;border-radius:8px;border:1px solid #e2e8f0;font-size:1.08rem;">
            <button type="submit" style="flex:2 1 0%;background:linear-gradient(90deg,#3a86ff 0%,#4361ee 100%);color:#fff;border:none;border-radius:8px;padding:10px 22px;font-size:1.08rem;font-weight:bold;cursor:pointer;">بحث</button>
        </form>
        <script>
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (searchInput.value.trim() === '') {
                    window.location.href = 'categories.php';
                } else {
                    searchForm.submit();
                }
            }, 350);
        });
        </script>
        <div style="display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap;">
            <div style="flex:1;min-width:260px;">
                <form method="post" class="category-form">
                    <?php if ($editCategory): ?>
                        <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
                        <input type="text" name="name" value="<?= $editCategory['name'] ?>" required placeholder="اسم التصنيف">
                        <input type="text" name="slug" value="<?= $editCategory['slug'] ?>" required placeholder="Slug">
                        <div style="display:flex;justify-content:space-between;gap:10px;">
                            <button type="submit" name="edit" style="flex:1;margin-right:0;">تعديل</button>
                            <a href="categories.php" style="flex:1;text-align:center;">إلغاء</a>
                        </div>
                    <?php else: ?>
                        <input type="text" name="name" required placeholder="اسم التصنيف">
                        <input type="text" name="slug" required placeholder="Slug">
                        <div style="display:flex;justify-content:space-between;gap:10px;">
                            <button type="submit" name="add" style="flex:1;margin-right:0;">إضافة</button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>الرقم</th>
                    <th>الاسم</th>
                    <th>Slug</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><?= $cat['name'] ?></td>
                        <td><?= $cat['slug'] ?></td>
                        <td>
                            <a href="categories.php?edit=<?= $cat['id'] ?>">تعديل</a> |
                            <a href="categories.php?delete=<?= $cat['id'] ?>" onclick="return confirm('متأكد أنك عايز تحذف التصنيف؟');">حذف</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
