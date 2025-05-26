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
    <style>
        body, html {
            font-family: 'Cairo', Tahoma, Arial, sans-serif;
            background: #f7f8fa;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .dashboard {
            min-height: 100vh;
            background: #f7f8fa;
            /* أضف هامش يمين يساوي عرض السايدبار حتى لا يغطي السايدبار المحتوى */
            margin-right: 220px;
            position: relative;
        }
        @media (max-width: 900px) {
            .dashboard {
                margin-right: 0 !important;
            }
        }
        .dashboard-header {
            background: #fff;
            box-shadow: 0 2px 8px #0001;
            padding: 1.5rem 2rem 1rem 2rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .dashboard-header h1 {
            margin: 0;
            font-size: 2.2rem;
            color: #2d3a4b;
            font-weight: bold;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0 0 1rem 0;
            display: flex;
            gap: 1.5rem;
        }
        nav ul li a {
            color: #4e73df;
            text-decoration: none;
            font-weight: 500;
            padding: 0.3rem 1rem;
            border-radius: 6px;
            transition: background 0.2s;
        }
        nav ul li a.active, nav ul li a:hover {
            background: #e9f0fb;
        }
        .dashboard-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .dashboard-action-btn {
            background: #fff;
            color: #2d3a4b;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 0.7rem 1.2rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 1px 4px #0001;
            transition: background 0.2s, color 0.2s;
        }
        .dashboard-action-btn:hover {
            background: #4e73df;
            color: #fff;
        }
        .main-content {
            padding: 2rem 2.5vw 1rem 2.5vw;
            margin-right: 0;
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
        .stats-cards {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0001;
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 1.2rem;
            min-width: 220px;
            flex: 1 1 220px;
            margin-bottom: 1rem;
        }
        .stat-card i {
            font-size: 2.5rem;
            color: #4e73df;
        }
        .stat-card span {
            color: #888;
            font-size: 1.1rem;
        }
        .stat-card h2 {
            margin: 0;
            font-size: 2.1rem;
            color: #2d3a4b;
            font-weight: bold;
        }
        .charts-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0001;
            padding: 2rem 1.5rem;
            margin-top: 2rem;
            max-width: 700px;
            margin-right: auto;
            margin-left: auto;
        }
        @media (max-width: 900px) {
            .stats-cards {
                flex-direction: column;
                gap: 1rem;
            }
            .main-content {
                padding: 1rem 1vw;
            }
        }
        @media (max-width: 600px) {
            .dashboard-header, .main-content, .charts-section {
                padding: 1rem 0.5rem;
            }
            .stat-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
</head>

<body>
        <?php include 'sidebar.php'; ?>
        <div class="dashboard">
        <header class="dashboard-header">
            <h1> لوحة تحكم الأدمن</h1>
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
            <div class="stats-cards">
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
        <h2 style="font-size:1.2rem;color:#4262ed;margin-bottom:18px;">إحصائيات المقالات آخر 12 شهر</h2>
        <canvas id="articlesChart"></canvas>
    </div>
</main>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
// رسم رسم بياني ديناميكي للمقالات آخر 12 شهر
fetch('api_articles_stats.php')
  .then(res => res.json())
  .then(stats => {
    const ctx = document.getElementById('articlesChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: stats.labels.map(m => m.replace('-', '/')),
        datasets: [{
          label: 'عدد المقالات',
          data: stats.data,
          backgroundColor: '#4262ed',
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: { rtl: true, callbacks: { label: ctx => 'عدد المقالات: ' + ctx.parsed.y } }
        },
        scales: {
          x: { title: { display: true, text: 'الشهر' } },
          y: { title: { display: true, text: 'عدد المقالات' }, beginAtZero: true, stepSize: 1 }
        }
      }
    });
  });
</script>
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