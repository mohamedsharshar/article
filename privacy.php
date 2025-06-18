<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>سياسة الخصوصية</title>
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
.privacy-section {
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
[data-theme="dark"] .privacy-section {
  background: #1E293B !important;
  color: #fff !important;
  box-shadow: 0 8px 32px #0008;
}
h1 { color: var(--color-primary); margin-bottom: 1.5rem; }
[data-theme="dark"] h1 { color: var(--color-primary); }
.privacy-section p {
  font-size: 1.15rem;
  margin-bottom: 1.2rem;
  text-align: right;
  line-height: 2.1;
}
@media (max-width: 600px) {
  .privacy-section {
    padding: 1.2rem 0.5rem 1.2rem 0.5rem;
    border-radius: 1.1rem;
  }
  h1 { font-size: 1.2rem; }
  .privacy-section p { font-size: 1rem; }
}
  </style>
  <script>
    // تفعيل الدارك مود تلقائياً حسب localStorage أو النظام
    if(localStorage.getItem('darkMode') === '1' || (localStorage.getItem('darkMode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
  </script>
</head>
<body>
  <section class="privacy-section">
    <h1>سياسة الخصوصية</h1>
    <p>نحن نولي أهمية كبيرة لخصوصيتك وحماية بياناتك الشخصية. تهدف هذه السياسة إلى توضيح كيفية جمع واستخدام وحماية المعلومات التي تقدمها لنا عند استخدامك لمنصتنا.</p>
    <p><b>المعلومات التي نجمعها:</b><br>
      - المعلومات التي تقدمها طوعًا عند التسجيل أو التواصل معنا (مثل الاسم والبريد الإلكتروني).<br>
      - بيانات الاستخدام مثل الصفحات التي تزورها ومدة التصفح ونوع الجهاز والمتصفح.
    </p>
    <p><b>كيف نستخدم المعلومات:</b><br>
      - لتحسين تجربتك وتخصيص المحتوى والخدمات.<br>
      - للرد على استفساراتك وتقديم الدعم الفني.<br>
      - لإرسال إشعارات أو تحديثات مهمة حول المنصة.
    </p>
    <p><b>مشاركة المعلومات:</b><br>
      - لا نشارك بياناتك مع أي طرف ثالث إلا في حالات قانونية أو لتحسين الخدمة (مثل خدمات التحليل أو البريد الإلكتروني).<br>
      - قد نكشف عن بعض المعلومات إذا طُلب منا ذلك قانونيًا.
    </p>
    <p><b>حماية البيانات:</b><br>
      - نستخدم أحدث وسائل الأمان لحماية بياناتك من الوصول غير المصرح به أو التلاعب أو الضياع.<br>
      - الوصول للبيانات مقيد فقط للأشخاص المخولين بذلك.
    </p>
    <p><b>حقوقك:</b><br>
      - يمكنك طلب معرفة أو تعديل أو حذف بياناتك في أي وقت عبر التواصل معنا.<br>
      - يمكنك إلغاء الاشتراك في النشرات البريدية في أي وقت.
    </p>
    <p>قد نقوم بتحديث سياسة الخصوصية من وقت لآخر. سيتم نشر أي تغييرات هنا. استمرارك في استخدام الموقع يعني موافقتك على السياسة المحدثة.</p>
    <p style="color:var(--color-primary);font-size:1.05rem;">آخر تحديث: يونيو 2025</p>
    <a href="index.php" class="back-home-btn" style="display:inline-block;margin-top:1.5rem;color:var(--color-primary);background:var(--color-slate-100);padding:10px 28px;border-radius:1rem;text-decoration:none;font-weight:bold;transition:background 0.2s;">&larr; العودة للرئيسية</a>
  </section>
</body>
</html>
