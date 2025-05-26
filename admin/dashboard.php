<?php
session_start();
// تحقق من تسجيل دخول الأدمن (يمكنك تعديل هذا حسب نظامك)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
require_once '../db.php';

// جلب إحصائيات المستخدمين
$users_count = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$articles_count = $pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
$comments_count = $pdo->query('SELECT COUNT(*) FROM comments')->fetchColumn();
$admins_count = $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();

// جلب المقالات الأحدث
$latest_articles = $pdo->query('SELECT * FROM articles ORDER BY created_at DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم الأدمن</title>
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>

<body>
    <div class="dashboard">
        <header class="dashboard-header">
            <h1> لوحة تحكم الأدمن</h1>
            <div class="user-info">
    <i class="fa fa-user-circle"></i>
    <p>مرحبًا، <?= isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'مشرف' ?></p>
</div>
            <nav>
                <ul>
                    <li><a href="dashboard.php" class="active">الرئيسية</a></li>
                    <li><a href="logout.php">تسجيل الخروج</a></li>
                </ul>
                <div class="dashboard-actions">
                    <a href="users.php" class="dashboard-action-btn"><i class="fa fa-users"></i> إدارة المستخدمين</a>
                    <a href="manage_articles.php" class="dashboard-action-btn"><i class="fa fa-newspaper"></i> إدارة المقالات</a>
                    <a href="manage_comments.php" class="dashboard-action-btn"><i class="fa fa-comments"></i> إدارة التعليقات</a>
                    <a href="admins.php" class="dashboard-action-btn"><i class="fa fa-user-shield"></i> إدارة المشرفين</a>
                </div>
            </nav>
        </header>
        <main class="main-content">
            <h1 class="dashboard-title animate__animated animate__fadeInDown">لوحة التحكم</h1>
            <div class="stats-cards"></div>
            <div class="stat-card animate__animated animate__fadeInUp">
                <i class="fa fa-users"></i>
                <div>
                    <span>المستخدمين</span>
                    <h2><?= $users_count ?></h2>
                </div>
            </div>
            <div class="stat-card animate__animated animate__fadeInUp">
                <i class="fa fa-newspaper"></i>
                <div>
                    <span>المقالات</span>
                    <h2><?= $articles_count ?></h2>
                </div>
            </div>
            <div class="stat-card animate__animated animate__fadeInUp">
                <i class="fa fa-comments"></i>
                <div>
                    <span>التعليقات</span>
                    <h2><?= $comments_count ?></h2>
                </div>
            </div>
            <div class="stat-card animate__animated animate__fadeInUp">
                <i class="fa fa-user-shield"></i>
                <div>
                    <span>المشرفين</span>
                    <h2><?= $admins_count ?></h2>
                </div>
            </div>
    </div>
    <div class="charts-section">
        <canvas id="articlesChart"></canvas>
    </div>
    </main>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="./js/dashboard.js"></script>
</body>

</html>
<?php
// معالجة إضافة مقال
if (isset($_POST['add_article'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    if ($title && $content) {
        $stmt = $pdo->prepare('INSERT INTO articles (title, content, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$title, $content]);
        header('Location: dashboard.php');
        exit();
    }
}
// معالجة حذف مقال
if (isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $pdo->prepare('DELETE FROM articles WHERE id = ?')->execute([$id]);
    $pdo->prepare('DELETE FROM comments WHERE article_id = ?')->execute([$id]);
    header('Location: dashboard.php');
    exit();
}
// إضافة تعليق أدمن فقط
if (isset($_POST['add_comment']) && isset($_POST['article_id'])) {
    $comment = trim($_POST['comment']);
    $article_id = intval($_POST['article_id']);
    if ($comment) {
        $stmt = $pdo->prepare('INSERT INTO comments (article_id, content, is_admin, created_at) VALUES (?, ?, 1, NOW())');
        $stmt->execute([$article_id, $comment]);
        header('Location: dashboard.php?comments=' . $article_id);
        exit();
    }
}
// تعديل تعليق أدمن فقط
if (isset($_POST['edit_comment_id']) && isset($_POST['edit_comment_content']) && isset($_GET['comments'])) {
    $comment_id = intval($_POST['edit_comment_id']);
    $content = trim($_POST['edit_comment_content']);
    if ($content) {
        // تحديث التعليق فقط إذا كان أدمن أو يوزر (حسب is_admin)
        $stmt = $pdo->prepare('UPDATE comments SET content = ? WHERE id = ?');
        $stmt->execute([$content, $comment_id]);
    }
    header('Location: dashboard.php?comments=' . intval($_GET['comments']));
    exit();
}
// حذف تعليق أدمن أو يوزر
if (isset($_POST['delete_comment_id']) && isset($_GET['comments'])) {
    $comment_id = intval($_POST['delete_comment_id']);
    $pdo->prepare('DELETE FROM comments WHERE id = ?')->execute([$comment_id]);
    header('Location: dashboard.php?comments=' . intval($_GET['comments']));
    exit();
}
?>