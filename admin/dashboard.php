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
body {
    direction: rtl;
    font-family: 'Cairo', Arial, sans-serif;
    background: #f8fafc;
}
.dashboard {
    max-width: 1200px;
    margin: 40px auto;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(67,97,238,0.13);
    padding: 0;
    overflow: hidden;
    display: block;
    min-height: 80vh;
}
.dashboard-header {
    background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
    color: #fff;
    padding: 32px 32px 16px 32px;
    border-bottom-left-radius: 18px;
    border-bottom-right-radius: 18px;
    box-shadow: 0 2px 12px rgba(67,97,238,0.08);
}
.dashboard-header h1 {
    margin: 0 0 12px 0;
    font-size: 2.2rem;
    font-weight: bold;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: fadeInDown 1s;
}
.dashboard-header nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    gap: 18px;
}
.dashboard-header nav ul li a {
    color: #fff;
    font-weight: 600;
    text-decoration: none;
    padding: 6px 18px;
    border-radius: 6px;
    transition: background 0.2s;
    font-size: 1.08rem;
    display: inline-block;
}
.dashboard-header nav ul li a.active,
.dashboard-header nav ul li a:hover {
    background: #4361ee;
    color: #fff;
}
.main-content {
    flex: 1;
    padding: 40px 40px 32px 40px;
    background: #f8fafc;
    min-height: 80vh;
    animation: fadeIn 1.2s;
    margin-right: 0;
}
.dashboard-title {
    color: #2d3142;
    margin-bottom: 32px;
    font-size: 2.2rem;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 10px;
}
.stats-cards {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    margin-bottom: 40px;
}
.stat-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 24px rgba(67,97,238,0.07);
    padding: 24px 32px;
    display: flex;
    align-items: center;
    gap: 18px;
    min-width: 200px;
    flex: 1;
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    animation: fadeInUp 1s;
}
.stat-card:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 8px 32px rgba(67,97,238,0.13);
    background: #e2eafc;
}
.stat-card i {
    font-size: 2.5rem;
    color: #3a86ff;
    animation: bounceIn 1.2s;
}
.stat-card span {
    color: #6c757d;
    font-size: 1.1rem;
}
.stat-card h2 {
    margin: 0;
    color: #2d3142;
    font-size: 2rem;
}
.charts-section {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 24px rgba(67,97,238,0.07);
    padding: 24px;
    margin-top: 24px;
    animation: fadeIn 1.2s;
}
.dashboard-actions {
    display: flex;
    gap: 12px;
    margin-top: 12px;
    flex-wrap: wrap;
}
.dashboard-action-btn {
    background: #fff;
    color: #3a86ff;
    border: none;
    border-radius: 6px;
    padding: 8px 18px;
    font-weight: bold;
    font-size: 1rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 8px rgba(67,97,238,0.07);
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.dashboard-action-btn:hover {
    background: #4361ee;
    color: #fff;
    box-shadow: 0 4px 16px rgba(67,97,238,0.13);
}
[data-theme="dark"] body,
[data-theme="dark"] html {
    background: #0f172a !important;
    color: #fff !important;
}
[data-theme="dark"] .dashboard {
    background: #1e293b !important;
    color: #fff !important;
    box-shadow: 0 8px 32px #0004 !important;
}
[data-theme="dark"] .dashboard-header {
    background: #1e293b !important;
    color: #fff !important;
    box-shadow: 0 2px 12px #0006 !important;
}
[data-theme="dark"] .dashboard-header nav ul li a {
    color: #fff !important;
}
[data-theme="dark"] .dashboard-header nav ul li a.active,
[data-theme="dark"] .dashboard-header nav ul li a:hover {
    background: #0f172a !important;
    color: #60a5fa !important;
}
[data-theme="dark"] .stat-card {
    background: #1e293b !important;
    color: #fff !important;
    box-shadow: 0 2px 8px #0006 !important;
}
[data-theme="dark"] .stat-card i {
    color: #60a5fa !important;
}
[data-theme="dark"] .stat-card span {
    color: #b0b8c9 !important;
}
[data-theme="dark"] .stat-card h2 {
    color: #fff !important;
}
[data-theme="dark"] .charts-section {
    background: #1e293b !important;
    color: #fff !important;
    box-shadow: 0 4px 24px #0006 !important;
}
[data-theme="dark"] .charts-section h2 {
    color: #60a5fa !important;
}
[data-theme="dark"] .charts-section canvas {
    background: #0f172a !important;
}
[data-theme="dark"] .dashboard-action-btn {
    background: #1e293b !important;
    color: #fff !important;
    border-color: #232a36 !important;
}
[data-theme="dark"] .dashboard-action-btn:hover {
    background: #60a5fa !important;
    color: #fff !important;
}
[data-theme="dark"] .dashboard-title {
    color: #fff !important;
}
[data-theme="dark"] .main-content {
    background: #1e293b !important;
    color: #fff !important;
}
[data-theme="dark"] .sidebar {
    background: #1e293b !important;
    color: #fff !important;
    box-shadow: 0 2px 8px #0006 !important;
}
[data-theme="dark"] nav ul li a {
    color: #fff !important;
}
[data-theme="dark"] nav ul li a.active,
[data-theme="dark"] nav ul li a:hover {
    background: #0f172a !important;
    color: #60a5fa !important;
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
<button id="themeToggle" aria-label="تبديل الوضع" style="position:absolute;left:2rem;top:1.5rem;background:none;border:none;cursor:pointer;font-size:1.5rem;z-index:10;"><i class="fa fa-moon"></i></button>
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
    // نظام الوضع الليلي للوحة التحكم
    function setDarkMode(on) {
      if(on) {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('adminDarkMode', '1');
        document.getElementById('themeToggle').innerHTML = '<i class="fa fa-sun"></i>';
      } else {
        document.documentElement.removeAttribute('data-theme');
        localStorage.setItem('adminDarkMode', '0');
        document.getElementById('themeToggle').innerHTML = '<i class="fa fa-moon"></i>';
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
</body>

</html>
<?php
// معالجة إضافة مقال
// تم نقل معالجة إضافة مقال إلى manage_articles.php
// ...existing code...
?>