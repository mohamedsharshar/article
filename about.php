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
      transition: background 0.2s, color 0.2s;
    }
    [data-theme="dark"] body {
      background: linear-gradient(120deg, #0F172A 0%, #1E293B 100%) !important;
      color: #fff !important;
    }
    .about-section {
      background: #fff;
      border-radius: 2rem;
      box-shadow: 0 8px 32px rgba(67,97,238,0.13);
      padding: 3.5rem 2.2rem 2.5rem 2.2rem;
      max-width: 1200px;
      width: 96vw;
      margin: 2rem auto;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      gap: 2.2rem;
      transition: background 0.3s, color 0.3s, box-shadow 0.3s;
    }
    [data-theme="dark"] .about-section {
      background: #1E293B !important;
      color: #fff !important;
      box-shadow: 0 8px 32px #0008;
    }
    h1 { color: #3B82F6; margin-bottom: 1.5rem; }
    [data-theme="dark"] h1 { color: #3B82F6; }
    .about-section p {
      font-size: 1.15rem;
      margin-bottom: 1.2rem;
      text-align: right;
      line-height: 2.1;
      color: #334155;
    }
    [data-theme="dark"] .about-section p { color: #CBD5E1; }
    @media (max-width: 900px) {
      .about-section { max-width: 98vw; padding: 2rem 0.5rem 2rem 0.5rem; border-radius: 1.1rem; }
    }
    @media (max-width: 600px) {
      .about-section { padding: 1.2rem 0.2rem 1.2rem 0.2rem; }
      h1 { font-size: 1.2rem; }
      .about-section p { font-size: 1rem; }
    }
  </style>
</head>
<body>
  <section class="about-section">
    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:2.5rem 4rem;justify-content:space-between;">
      <div style="flex:1 1 340px;min-width:320px;max-width:520px;">
        <h1 style="font-size:2.3rem;line-height:1.2;">عن منصة مقالات</h1>
        <p style="font-size:1.18rem;">منصة <b style="color:var(--color-primary,#3B82F6)">مقالات</b> هي وجهتك العربية لنشر وقراءة المقالات المميزة في مجالات التقنية، التصميم، الذكاء الاصطناعي، وتطوير الذات. نؤمن بقوة الكلمة وأهمية مشاركة الأفكار والخبرات مع الجميع، ونوفر بيئة عصرية ملهمة لكل الزوار والكتّاب.</p>
        <div style="margin:1.5rem 0 0.5rem 0;">
          <a href="index.php" class="back-home-btn" style="display:inline-block;color:var(--color-primary,#3B82F6);background:var(--color-slate-100,#F1F5F9);padding:10px 28px;border-radius:1rem;text-decoration:none;font-weight:bold;transition:background 0.2s;">&larr; العودة للرئيسية</a>
        </div>
      </div>
      <div style="flex:1 1 320px;min-width:260px;max-width:400px;display:flex;align-items:center;justify-content:center;">
        <img src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=600&q=80" alt="منصة مقالات" style="width:100%;max-width:320px;height:220px;object-fit:cover;border-radius:1.2rem;box-shadow:0 2px 12px #0002;">
      </div>
    </div>
    <div style="width:100%;margin:2.2rem 0 1.2rem 0;">
      <h2 style="color:var(--color-primary-dark,#2563EB);font-size:1.3rem;margin-bottom:1.2rem;text-align:right;">مميزات المنصة</h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;">
        <div style="background:var(--color-slate-100,#F1F5F9);border-radius:1rem;padding:1.5rem;text-align:center;box-shadow:0 2px 8px #0001;">
          <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="مجتمع نشط" style="width:44px;height:44px;margin-bottom:0.7rem;">
          <div style="font-weight:bold;color:var(--color-primary,#3B82F6);margin-bottom:0.3rem;font-size:1.1rem;">مجتمع نشط</div>
          <div style="color:var(--color-slate-600,#475569);font-size:1.05rem;">تفاعل مستمر بين الكتّاب والقراء عبر التعليقات والمناقشات.</div>
        </div>
        <div style="background:var(--color-slate-100,#F1F5F9);border-radius:1rem;padding:1.5rem;text-align:center;box-shadow:0 2px 8px #0001;">
          <img src="https://cdn-icons-png.flaticon.com/512/1828/1828884.png" alt="دعم الدارك مود" style="width:44px;height:44px;margin-bottom:0.7rem;">
          <div style="font-weight:bold;color:var(--color-primary,#3B82F6);margin-bottom:0.3rem;font-size:1.1rem;">دعم الدارك مود</div>
          <div style="color:var(--color-slate-600,#475569);font-size:1.05rem;">تجربة قراءة مريحة في جميع الأوقات مع الوضع الليلي العصري.</div>
        </div>
        <div style="background:var(--color-slate-100,#F1F5F9);border-radius:1rem;padding:1.5rem;text-align:center;box-shadow:0 2px 8px #0001;">
          <img src="https://cdn-icons-png.flaticon.com/512/3523/3523887.png" alt="محتوى متجدد" style="width:44px;height:44px;margin-bottom:0.7rem;">
          <div style="font-weight:bold;color:var(--color-primary,#3B82F6);margin-bottom:0.3rem;font-size:1.1rem;">محتوى متجدد</div>
          <div style="color:var(--color-slate-600,#475569);font-size:1.05rem;">مقالات جديدة يومياً في مجالات متنوعة وحديثة.</div>
        </div>
        <div style="background:var(--color-slate-100,#F1F5F9);border-radius:1rem;padding:1.5rem;text-align:center;box-shadow:0 2px 8px #0001;">
          <img src="https://cdn-icons-png.flaticon.com/512/3135/3135789.png" alt="واجهة سهلة" style="width:44px;height:44px;margin-bottom:0.7rem;">
          <div style="font-weight:bold;color:var(--color-primary,#3B82F6);margin-bottom:0.3rem;font-size:1.1rem;">واجهة سهلة</div>
          <div style="color:var(--color-slate-600,#475569);font-size:1.05rem;">تصميم بسيط وسهل الاستخدام لجميع الأعمار.</div>
        </div>
      </div>
    </div>
    <div style="width:100%;display:flex;justify-content:center;margin:2.5rem 0 0 0;">
      <img src="https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=1200&q=80" alt="مقالات تقنية" style="width:100%;max-width:700px;border-radius:1.2rem;box-shadow:0 2px 12px #0002;">
    </div>
  </section>
  <script>
    // تفعيل الدارك مود تلقائياً حسب localStorage أو النظام
    if(localStorage.getItem('darkMode') === '1' || (localStorage.getItem('darkMode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
  </script>
</body>
</html>
