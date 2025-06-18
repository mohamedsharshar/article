<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الشروط والأحكام</title>
  <link rel="stylesheet" href="css/index.css">
  <style>
:root {
  --color-primary: #3B82F6;
  --color-primary-dark: #2563EB;
  --color-slate-50: #F8FAFC;
  --color-slate-100: #F1F5F9;
  --color-slate-200: #E2E8F0;
  --color-slate-300: #CBD5E1;
  --color-slate-400: #94A3B8;
  --color-slate-500: #64748B;
  --color-slate-600: #475569;
  --color-slate-700: #334155;
  --color-slate-800: #1E293B;
  --color-slate-900: #0F172A;
  --color-white: #FFFFFF;
  --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
}

body {
  min-height: 100vh;
  margin: 0;
  padding: 0;
  background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
  color: var(--color-slate-900);
  font-family: var(--font-sans);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s, color 0.2s;
}
[data-theme="dark"] body {
  background: linear-gradient(120deg, #0F172A 0%, #1E293B 100%) !important;
  color: var(--color-white) !important;
}
.terms-section {
  background: var(--color-white);
  border-radius: 2rem;
  box-shadow: 0 8px 32px rgba(67,97,238,0.13);
  padding: 3.5rem 2.2rem 2.5rem 2.2rem;
  max-width: 700px;
  width: 98vw;
  margin: 2rem auto;
  position: relative;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.2rem;
  transition: background 0.3s, color 0.3s, box-shadow 0.3s;
}
[data-theme="dark"] .terms-section {
  background: #1E293B !important;
  color: #fff !important;
  box-shadow: 0 8px 32px #0008;
}
h1 { color: var(--color-primary); margin-bottom: 1.5rem; }
[data-theme="dark"] h1 { color: var(--color-primary); }
.terms-section p, .terms-section ul {
  font-size: 1.15rem;
  margin-bottom: 1.2rem;
  text-align: right;
  line-height: 2.1;
}
@media (max-width: 600px) {
  .terms-section {
    padding: 1.2rem 0.5rem 1.2rem 0.5rem;
    border-radius: 1.1rem;
  }
  h1 { font-size: 1.2rem; }
  .terms-section p, .terms-section ul { font-size: 1rem; }
}
  </style>
</head>
<body>
  <section class="terms-section">
    <h1>الشروط والأحكام</h1>
    <p>باستخدامك لهذا الموقع، فإنك توافق على الشروط والأحكام التالية التي تهدف إلى حماية جميع الأطراف وضمان تجربة آمنة وعادلة للجميع.</p>
    <ul style="margin-right:1.5rem;">
      <li><b>الاستخدام السليم:</b> يجب استخدام المنصة للأغراض المشروعة فقط، ويُمنع نشر أي محتوى مخالف للقوانين أو الأعراف.</li>
      <li><b>حقوق الملكية الفكرية:</b> جميع المحتويات المنشورة (نصوص، صور، شعارات) محمية بحقوق الملكية الفكرية ولا يجوز نسخها أو إعادة نشرها دون إذن.</li>
      <li><b>المحتوى المقدم من المستخدم:</b> أنت مسؤول عن أي محتوى تقوم بنشره، ويحق للإدارة حذف أو تعديل أي محتوى مخالف.</li>
      <li><b>التسجيل والحساب:</b> يجب تقديم معلومات صحيحة عند التسجيل، ويحق للإدارة تعليق أو حذف أي حساب مخالف.</li>
      <li><b>التعديلات على الشروط:</b> تحتفظ الإدارة بحق تعديل الشروط في أي وقت وسيتم إشعار المستخدمين بالتغييرات.</li>
      <li><b>حدود المسؤولية:</b> المنصة غير مسؤولة عن أي أضرار مباشرة أو غير مباشرة ناتجة عن استخدام الموقع.</li>
    </ul>
    <p>استمرارك في استخدام الموقع يعني موافقتك على هذه الشروط. إذا لم توافق على أي بند، يرجى التوقف عن استخدام المنصة.</p>
    <p style="color:var(--color-primary);font-size:1.05rem;">آخر تحديث: يونيو 2025</p>
    <a href="index.php" class="back-home-btn" style="display:inline-block;margin-top:1.5rem;color:var(--color-primary);background:var(--color-slate-100);padding:10px 28px;border-radius:1rem;text-decoration:none;font-weight:bold;transition:background 0.2s;">&larr; العودة للرئيسية</a>
  </section>
  <script>
    // تفعيل الدارك مود تلقائياً حسب localStorage أو النظام
    if(localStorage.getItem('darkMode') === '1' || (localStorage.getItem('darkMode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
  </script>
</body>
</html>
