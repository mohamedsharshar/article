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
            position: relative;
            transition: margin-inline-end 0.3s;
        }
        @media (max-width: 1100px) {
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
            max-width: 1100px;
            margin-left: auto;
            margin-right: auto;
        }
        @media (max-width: 900px) {
            .main-content {
                padding: 1rem 1vw;
                margin-right: 0 !important;
                margin-left: 0 !important;
                max-width: 100vw;
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
            border-radius: 16px;
            box-shadow: 0 4px 24px #4262ed14;
            padding: 2.5rem 2rem 2.5rem 2rem;
            margin-top: 2.5rem;
            max-width: 800px;
            margin-right: auto;
            margin-left: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2.5rem;
        }
        .charts-section h2 {
            text-align: center;
            color: #4262ed;
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 18px;
            margin-top: 0;
            letter-spacing: 0.01em;
        }
        .charts-section canvas {
            background: #f8fafc;
            border-radius: 12px;
            box-shadow: 0 2px 8px #4262ed0a;
            margin-bottom: 0;
            max-width: 100%;
        }
        .charts-section hr {
            width: 80%;
            border: none;
            border-top: 1.5px solid #e3e6f0;
            margin: 32px 0 24px 0;
        }
        @media (max-width: 700px) {
            .charts-section {
                padding: 1rem 0.5rem;
                max-width: 100vw;
            }
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
            <!-- تم نقل نموذج إضافة مقال إلى إدارة المقالات -->
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
        <hr style="margin:32px 0 24px 0;border:none;border-top:1.5px solid #e3e6f0;">
        <h2 style="font-size:1.2rem;color:#4262ed;margin-bottom:18px;">إحصائيات التعليقات آخر 12 شهر</h2>
        <canvas id="commentsChart"></canvas>
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
          backgroundColor: 'rgba(66,98,237,0.85)',
          borderRadius: 10,
          maxBarThickness: 38
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: { rtl: true, backgroundColor: '#4262ed', titleFont: {size:16}, bodyFont: {size:15}, callbacks: { label: ctx => 'عدد المقالات: ' + ctx.parsed.y } }
        },
        scales: {
          x: { title: { display: true, text: 'الشهر', color:'#4262ed', font:{size:15,weight:'bold'} }, ticks:{font:{size:14}} },
          y: { title: { display: true, text: 'عدد المقالات', color:'#4262ed', font:{size:15,weight:'bold'} }, beginAtZero: true, stepSize: 1, ticks:{font:{size:14}} }
        }
      }
    });
  });
// رسم رسم بياني ديناميكي للتعليقات آخر 12 شهر
fetch('api_comments_stats.php')
  .then(res => res.json())
  .then(stats => {
    const ctx = document.getElementById('commentsChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: stats.labels.map(m => m.replace('-', '/')),
        datasets: [{
          label: 'عدد التعليقات',
          data: stats.data,
          backgroundColor: 'rgba(54,185,204,0.85)',
          borderRadius: 10,
          maxBarThickness: 38
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: { rtl: true, backgroundColor: '#36b9cc', titleFont: {size:16}, bodyFont: {size:15}, callbacks: { label: ctx => 'عدد التعليقات: ' + ctx.parsed.y } }
        },
        scales: {
          x: { title: { display: true, text: 'الشهر', color:'#36b9cc', font:{size:15,weight:'bold'} }, ticks:{font:{size:14}} },
          y: { title: { display: true, text: 'عدد التعليقات', color:'#36b9cc', font:{size:15,weight:'bold'} }, beginAtZero: true, stepSize: 1, ticks:{font:{size:14}} }
        }
      }
    });
  });
</script>
</body>

</html>
<?php
// معالجة إضافة مقال
// تم نقل معالجة إضافة مقال إلى manage_articles.php
// ...existing code...
?>