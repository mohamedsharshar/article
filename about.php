<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>عن الموقع</title>
  <link rel="stylesheet" href="css/index.css">
  <style>
    body {
      min-height: 100vh;
      margin: 0;
      padding: 0;
      background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
      color: #222;
      font-family: 'Cairo', 'Inter', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .about-section {
      background: #fff;
      border-radius: 2rem;
      box-shadow: 0 8px 32px rgba(67,97,238,0.13);
      padding: 3.5rem 2.2rem 2.5rem 2.2rem;
      max-width: 700px;
      width: 98vw;
      margin: 2rem auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1.2rem;
    }
    h1 { color: #3B82F6; margin-bottom: 1.5rem; }
    .about-section p {
      font-size: 1.15rem;
      margin-bottom: 1.2rem;
      text-align: right;
      line-height: 2.1;
      color: #334155;
    }
    @media (max-width: 600px) {
      .about-section { padding: 1.2rem 0.5rem 1.2rem 0.5rem; border-radius: 1.1rem; }
      h1 { font-size: 1.2rem; }
      .about-section p { font-size: 1rem; }
    }
  </style>
</head>
<body>
  <section class="about-section">
    <h1>عن الموقع</h1>
    <p>منصة "مقالات" تهدف إلى نشر المعرفة والمحتوى العربي المميز في مجالات التقنية، التصميم، الذكاء الاصطناعي، وتطوير الذات. نؤمن بقوة الكلمة وأهمية مشاركة الأفكار والخبرات مع الجميع. نسعى لتقديم تجربة قراءة عصرية وملهمة لكل الزوار.</p>
    <a href="index.php" class="back-home-btn" style="display:inline-block;margin-top:1.5rem;color:#3B82F6;background:#F1F5F9;padding:10px 28px;border-radius:1rem;text-decoration:none;font-weight:bold;transition:background 0.2s;">&larr; العودة للرئيسية</a>
  </section>
</body>
</html>
