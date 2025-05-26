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
    <li><a href="manage_articles.php"><i class="fa fa-newspaper"></i> المقالات</a></li>
    <li><a href="manage_comments.php"><i class="fa fa-comments"></i> التعليقات</a></li>
    <li><a href="admins.php"><i class="fa fa-user-shield"></i> المشرفين</a></li>
    <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> تسجيل الخروج</a></li>
  </ul>
</aside>
