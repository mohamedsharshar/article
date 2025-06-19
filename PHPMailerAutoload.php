<?php
// تحميل PHPMailer تلقائياً (نسخة خفيفة)
// يمكنك تحميل PHPMailer كاملة من https://github.com/PHPMailer/PHPMailer
// أو عبر Composer: composer require phpmailer/phpmailer
// هنا فقط ملف autoload بسيط إذا وضعت ملفات PHPMailer في نفس المجلد
spl_autoload_register(function($class){
    $file = __DIR__ . "/PHPMailer/src/" . str_replace('\\', '/', $class) . ".php";
    if(file_exists($file)) require $file;
});
