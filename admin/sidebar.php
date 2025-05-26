<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الشريط الجانبي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        .sidebar {
            background: #fff;
            min-height: 100vh;
            width: 220px;
            box-shadow: 0 2px 8px #0001;
            position: fixed;
            right: 0;
            top: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            /* align-items: flex-end; */
            padding: 1.5rem 1rem 1rem 0.5rem;
        }
        .sidebar .sidebar-header {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-bottom: 2rem;
        }
        .sidebar .sidebar-header i {
            font-size: 2.2rem;
            color: #4e73df;
        }
        .sidebar .sidebar-header span {
            font-size: 1.2rem;
            color: #2d3a4b;
            font-weight: bold;
        }
        .sidebar-logo {
            font-size: 1.6rem;
            color: #4262ed;
            font-weight: bold;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-logo i {
            font-size: 1.4rem;
            color: #4262ed;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 100%;
        }
        .sidebar ul li {
            margin-bottom: 1.2rem;
        }
        .sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            color: #4e73df;
            text-decoration: none;
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            border-radius: 8px 0 0 8px;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar ul li a.active, .sidebar ul li a:hover {
            background: #e9f0fb;
            color: #2d3a4b;
        }
        .sidebar .sidebar-footer {
            margin-top: auto;
            width: 100%;
            text-align: left;
        }
        .sidebar .sidebar-footer a {
            color: #e74a3b;
            font-weight: bold;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px 0 0 8px;
            transition: background 0.2s;
        }
        .sidebar .sidebar-footer a:hover {
            background: #fbe9e9;
        }
        @media (max-width: 900px) {
            .sidebar {
                width: 60px;
                padding: 1.5rem 0.2rem 1rem 0.2rem;
                align-items: center;
            }
            .sidebar .sidebar-header span {
                display: none;
            }
            .sidebar ul li a span {
                display: none;
            }
            .sidebar .sidebar-footer a span {
                display: none;
            }
        }
    </style>
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-logo">
    <i class="fa fa-feather"></i> مقالاتي
  </div>
  <div class="user-info" style="margin-bottom:18px;">
    <i class="fa fa-user-circle"></i>
    <span>
      <?php
        if (isset($_SESSION['admin_username'])) {
          echo htmlspecialchars($_SESSION['admin_username']);
        } elseif (isset($_SESSION['username'])) {
          echo htmlspecialchars($_SESSION['username']);
        } else {
          echo 'مستخدم';
        }
      ?>
    </span>
  </div>
  <ul>
    <li><a href="dashboard.php"><i class="fa fa-chart-bar"></i> لوحة التحكم</a></li>
    <li><a href="users.php"><i class="fa fa-users"></i> المستخدمين</a></li>
    <li><a href="admins.php"><i class="fa fa-user-shield"></i> المشرفين</a></li>
    <li><a href="manage_articles.php"><i class="fa fa-newspaper"></i> المقالات</a></li>
    <li><a href="manage_comments.php"><i class="fa fa-comments"></i> التعليقات</a></li>
    <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> تسجيل الخروج</a></li>
  </ul>
</aside>
</body>
</html>
