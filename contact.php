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
    }
    h1 { color: #3B82F6; margin-bottom: 1.5rem; }
    .contact-section p {
      font-size: 1.1rem;
      margin-bottom: 1.2rem;
      text-align: right;
      line-height: 2.1;
      color: #334155;
    }
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
    <p>لأي استفسار أو اقتراح أو شراكة، يمكنك التواصل معنا عبر البريد الإلكتروني: <a href="mailto:info@maqalat.com" style="color:#3B82F6;text-decoration:underline;">info@maqalat.com</a> أو عبر نموذج التواصل أدناه.</p>
    <form>
      <input type="text" placeholder="اسمك" required>
      <input type="email" placeholder="بريدك الإلكتروني" required>
      <textarea placeholder="رسالتك" required></textarea>
      <button type="submit">إرسال</button>
    </form>
    <a href="index.php" class="back-home-btn" style="display:inline-block;margin-top:1.5rem;color:#3B82F6;background:#F1F5F9;padding:10px 28px;border-radius:1rem;text-decoration:none;font-weight:bold;transition:background 0.2s;">&larr; العودة للرئيسية</a>
  </section>
</body>
</html>
