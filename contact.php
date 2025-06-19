<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تواصل معنا</title>
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
    .contact-section {
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
      transition: background 0.3s, color 0.3s, box-shadow 0.3s;
    }
    [data-theme="dark"] .contact-section {
      background: #1E293B !important;
      color: #fff !important;
      box-shadow: 0 8px 32px #0008;
    }
    h1 { color: #3B82F6; margin-bottom: 1.5rem; }
    [data-theme="dark"] h1 { color: #3B82F6; }
    .contact-section p {
      font-size: 1.1rem;
      margin-bottom: 1.2rem;
      text-align: right;
      line-height: 2.1;
      color: #334155;
    }
    [data-theme="dark"] .contact-section p { color: #CBD5E1; }
    .contact-section form {
      margin-top: 1.5rem;
      max-width: 500px;
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    .contact-section input, .contact-section textarea {
      width: 100%;
      padding: 10px 8px;
      border-radius: 8px;
      border: 1px solid #dbeafe;
      font-size: 1rem;
      background: #f8fafc;
      color: #222;
      resize: none;
      transition: background 0.2s, color 0.2s, border 0.2s;
    }
    [data-theme="dark"] .contact-section input, [data-theme="dark"] .contact-section textarea {
      background: #222e3a;
      color: #fff;
      border: 1px solid #334155;
    }
    .contact-section textarea { min-height: 90px; }
    .contact-section button {
      background: linear-gradient(90deg,#3a86ff 0%,#4262ed 100%);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 10px 0;
      font-size: 1.08rem;
      font-weight: bold;
      width: 100%;
      cursor: pointer;
      transition: background 0.2s;
    }
    .contact-section button:hover {
      background: linear-gradient(90deg,#4262ed 0%,#3a86ff 100%);
    }
    @media (max-width: 600px) {
      .contact-section { padding: 1.2rem 0.5rem 1.2rem 0.5rem; border-radius: 1.1rem; }
      h1 { font-size: 1.2rem; }
      .contact-section p, .contact-section input, .contact-section textarea { font-size: 0.97rem; }
    }
  </style>
</head>
<body>
  <section class="contact-section">
    <h1>تواصل معنا</h1>
    <p>لأي استفسار أو اقتراح أو شراكة، يمكنك التواصل معنا عبر البريد الإلكتروني: <a href="mailto:mmshsh05@gmail.com" style="color:#3B82F6;text-decoration:underline;">mmshsh05@gmail.com</a> أو عبر نموذج التواصل أدناه.</p>
    <form id="contactForm">
      <input type="text" name="name" placeholder="اسمك" required>
      <input type="email" name="email" placeholder="بريدك الإلكتروني" required>
      <textarea name="message" placeholder="رسالتك" required></textarea>
      <button type="submit">إرسال</button>
      <div id="contactMsg" style="margin-top:1rem;font-size:1.05rem;"></div>
    </form>
    <a href="index.php" class="back-home-btn" style="display:inline-block;margin-top:1.5rem;color:#3B82F6;background:#F1F5F9;padding:10px 28px;border-radius:1rem;text-decoration:none;font-weight:bold;transition:background 0.2s;">&larr; العودة للرئيسية</a>
  </section>
  <script>
    // تفعيل الدارك مود تلقائياً حسب localStorage أو النظام
    if(localStorage.getItem('darkMode') === '1' || (localStorage.getItem('darkMode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
    // تفعيل إرسال النموذج عبر AJAX
    const contactForm = document.getElementById('contactForm');
    if(contactForm) {
      contactForm.onsubmit = async function(e) {
        e.preventDefault();
        const formData = new FormData(contactForm);
        const msg = document.getElementById('contactMsg');
        msg.textContent = 'جاري الإرسال...';
        msg.style.color = '#3B82F6';
        const res = await fetch('send_contact.php', {
          method: 'POST',
          body: formData
        });
        let data = {};
        try { data = await res.json(); } catch { data = { success: false, message: 'خطأ في الاتصال بالخادم.' }; }
        if(data.success) {
          msg.style.color = '#198754';
          msg.textContent = 'تم إرسال رسالتك بنجاح! سيتم الرد عليك قريباً.';
          contactForm.reset();
        } else {
          msg.style.color = '#e63946';
          msg.textContent = data.message || 'حدث خطأ، حاول مرة أخرى.';
        }
      };
    }
  </script>
</body>
</html>
