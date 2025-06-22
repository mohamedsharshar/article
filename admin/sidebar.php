<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الشريط الجانبي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
.sidebar {
  position: fixed;
  right: 0;
  top: 0;
  width: 220px;
  height: 100vh;
  background: #fff;
  box-shadow: -2px 0 16px rgba(67,97,238,0.07);
  z-index: 100;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 32px;
  transition: all 0.2s;
}
.sidebar-logo {
  font-size: 1.5rem;
  color: #3a86ff;
  font-weight: bold;
  margin-bottom: 32px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.sidebar ul {
  list-style: none;
  padding: 0;
  width: 100%;
}
.sidebar ul li {
  width: 100%;
}
.sidebar ul li a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 32px;
  color: #3a86ff;
  text-decoration: none;
  font-size: 1.08rem;
  border-radius: 8px 0 0 8px;
  transition: background 0.2s, color 0.2s;
}
.sidebar ul li a:hover, .sidebar ul li a.active {
  background: #e9f0fb;
  color: #2563eb;
}
.menu-btn {
  display: none;
  position: fixed;
  top: 18px;
  right: 18px;
  z-index: 2001;
  background: #3a86ff;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1.7rem;
  padding: 7px 14px;
  box-shadow: 0 2px 8px #0002;
  cursor: pointer;
}
.sidebar-overlay {
  display: none;
  position: fixed;
  top: 0; right: 0; left: 0; bottom: 0;
  background: rgba(0,0,0,0.18);
  z-index: 2000;
  align-items: flex-start;
  justify-content: flex-end;
}
.sidebar-sheet {
  background: #fff;
  width: 80vw;
  max-width: 320px;
  height: 100vh;
  box-shadow: -2px 0 16px #0002;
  border-radius: 16px 0 0 16px;
  padding: 32px 18px 18px 18px;
  position: relative;
  animation: slideInRight 0.3s;
  direction: rtl;
  display: flex;
  flex-direction: column;
  gap: 18px;
}
@keyframes slideInRight {
  from { transform: translateX(100%); }
  to { transform: translateX(0); }
}
.sidebar-sheet .close-sheet {
  position: absolute;
  left: 12px;
  top: 12px;
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #3a86ff;
  cursor: pointer;
}
.sidebar-sheet ul {
  list-style: none;
  padding: 0;
  margin: 0;
  width: 100%;
}
.sidebar-sheet ul li {
  margin-bottom: 1.2rem;
}
.sidebar-sheet ul li a {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  color: #3a86ff;
  text-decoration: none;
  font-size: 1.1rem;
  padding: 0.5rem 1rem;
  border-radius: 8px 0 0 8px;
  transition: background 0.2s, color 0.2s;
}
.sidebar-sheet ul li a.active, .sidebar-sheet ul li a:hover {
  background: #e9f0fb;
  color: #2563eb;
}
@media (max-width: 700px) {
  .sidebar {
    display: none !important;
  }
  .menu-btn {
    display: block !important;
  }
  .sidebar-overlay {
    display: flex !important;
  }
}
[data-theme="dark"] .sidebar, [data-theme="dark"] .sidebar-sheet {
  background: #232a36 !important;
  color: #e3e6f0 !important;
  box-shadow: 0 2px 8px #0006 !important;
}
[data-theme="dark"] .sidebar-logo, [data-theme="dark"] .sidebar-sheet .sidebar-logo {
  color: #60a5fa !important;
}
[data-theme="dark"] .sidebar ul li a, [data-theme="dark"] .sidebar-sheet ul li a {
  color: #b0b8c9 !important;
}
[data-theme="dark"] .sidebar ul li a.active,
[data-theme="dark"] .sidebar ul li a:hover,
[data-theme="dark"] .sidebar-sheet ul li a.active,
[data-theme="dark"] .sidebar-sheet ul li a:hover {
  background: #232a36 !important;
  color: #60a5fa !important;
}
[data-theme="dark"] .user-info {
  background: #181c24 !important;
  color: #e3e6f0 !important;
  border-color: #232a36 !important;
}
[data-theme="dark"] .user-info .fa-user-circle {
  color: #60a5fa !important;
}
    </style>
</head>
<body>
    <button class="menu-btn" aria-label="القائمة"><i class="fa fa-bars"></i></button>
    <div class="sidebar-overlay" tabindex="-1">
      <nav class="sidebar-sheet">
        <button class="close-sheet" aria-label="إغلاق القائمة"><i class="fa fa-times"></i></button>
        <div class="sidebar-logo"><i class="fa fa-feather"></i> مقالاتي</div>
        <div class="user-info" style="margin-bottom:18px;"><i class="fa fa-user-circle"></i> <span><?php if (isset($_SESSION['admin_username'])) { echo htmlspecialchars($_SESSION['admin_username']); } else { echo '<span style=\'color:#e63946\'>غير مسجل كأدمن</span>'; } ?></span></div>
        <ul>
          <li><a href="dashboard.php"><i class="fa fa-chart-bar"></i> لوحة التحكم</a></li>
          <li><a href="users.php"><i class="fa fa-users"></i> المستخدمين</a></li>
          <li><a href="admins.php"><i class="fa fa-user-shield"></i> المشرفين</a></li>
          <li><a href="manage_articles.php"><i class="fa fa-newspaper"></i> المقالات</a></li>
          <li><a href="manage_comments.php"><i class="fa fa-comments"></i> التعليقات</a></li>
          <li><a href="categories.php"><i class="fa fa-tags"></i> التصنيفات</a></li>
          <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> تسجيل الخروج</a></li>
        </ul>
      </nav>
    </div>
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
            } else {
              echo '<span style=\'color:#e63946\'>غير مسجل كأدمن</span>';
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
        <li><a href="categories.php"><i class="fa fa-tags"></i> التصنيفات</a></li>
        <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> تسجيل الخروج</a></li>
      </ul>
    </aside>
    <script>
    // Responsive sidebar menu logic
    const menuBtn = document.querySelector('.menu-btn');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const closeSheetBtn = document.querySelector('.close-sheet');
    function openSidebarSheet() {
      sidebarOverlay.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
    function closeSidebarSheet() {
      sidebarOverlay.style.display = 'none';
      document.body.style.overflow = '';
    }
    if (menuBtn && sidebarOverlay && closeSheetBtn) {
      menuBtn.addEventListener('click', openSidebarSheet);
      closeSheetBtn.addEventListener('click', closeSidebarSheet);
      sidebarOverlay.addEventListener('click', function(e) {
        if (e.target === sidebarOverlay) closeSidebarSheet();
      });
    }
    window.addEventListener('resize', function() {
      if (window.innerWidth > 700) {
        closeSidebarSheet();
      }
    });
    </script>
</body>
</html>
