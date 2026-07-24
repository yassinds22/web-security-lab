<?php
session_start();
require_once 'db.php';

$message = '';
$user_info = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🛡️ تنقية المدخلات الأساسية وزيادة الأمان
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (isset($pdo)) {
        try {
            /* 
             * 🛡️ 1. الحماية من ثغرة SQL Injection:
             * الحل: استخدام الجمل المجهزة (Prepared Statements) مع ربط المعاملات (Parameter Binding).
             * يضمن هذا أن محرك قاعدة البيانات يعامل المدخلات كبيانات فقط وليس كجزء من كود الاستعلام!
             */
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            /* 
             * 🛡️ التحقق الآمن من كلمة المرور المشفرة
             */
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['mode'] = 'secure';
                header('Location: dashboard.php');
                exit();
            } else {
                $safe_input = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
                $message = "<div class='alert alert-danger'>اسم المستخدم أو كلمة المرور غير صحيحة للمستخدم: <strong>" . $safe_input . "</strong></div>";
            }
        } catch (PDOException $e) {
            // عدم إظهار تفاصيل أخطاء قاعدة البيانات الحساسة للمستخدم النهائي
            $message = "<div class='alert alert-danger'>حدث خطأ أثناء معالجة الطلب. يرجى المحاولة لاحقاً.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>" . ($db_error ?? "يرجى تهيئة قاعدة البيانات أولاً.") . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحة الدخول - النموذج الآمن (معالج)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>المختبر التعليمي لأمن الويب</h1>
            <p>شرح وفهم طرق الحماية والمعالجة من ثغرات SQL Injection و XSS في PHP</p>
        </header>

        <div class="nav-tabs">
            <a href="index.php" class="nav-btn">🏠 الرئيسية</a>
            <a href="login_vulnerable.php" class="nav-btn">⚠️ النسخة غير الآمنة (ضعيفة)</a>
            <a href="login_secure.php" class="nav-btn secure-active">🛡️ النسخة المحمية (معالجة)</a>
        </div>

        <div class="card">
            <span class="badge badge-success">🛡️ نموذج آمن ومعالج بالكامل</span>
            <h2>تسجيل الدخول (كود محمّي)</h2>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                هذه الصفحة تستخدم أفضل الممارسات الأمنية للحماية من هجمات حقن SQL والسكربتات الخبيثة.
            </p>

            <?php if (!empty($message)) echo $message; ?>

            <form method="POST" action="login_secure.php">
                <div class="form-group">
                    <label for="username">اسم المستخدم:</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="أدخل اسم المستخدم" required>
                </div>
                <div class="form-group">
                    <label for="password">كلمة المرور:</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="أدخل كلمة المرور" required>
                </div>
                <button type="submit" class="btn-submit btn-success">تسجيل الدخول (آمن)</button>
            </form>

            <div class="explanation-section">
                <div class="explanation-title">
                    <span>🛡️</span> حلول الحماية البرمجية المطبقة في هذه الصفحة:
                </div>
                
                <h4 style="color: #10b981; margin-top: 1rem;">1. الحماية من SQL Injection بواسطة Prepared Statements:</h4>
                <p style="color: var(--text-secondary); font-size: 0.95rem; margin-top: 0.25rem;">
                    نقوم بفصل الاستعلام البرمجي عن البيانات باستخدام المعاملات المجهزة (Placeholders):
                </p>
                <div class="code-block">
                    <span class="code-comment">// كود آمن - استخدام الاستعلامات المجهزة PDO Prepared Statements</span><br>
                    $stmt = $pdo-><span class="code-func">prepare</span>(<span class="code-string">"SELECT * FROM users WHERE username = :username"</span>);<br>
                    $stmt-><span class="code-func">execute</span>([<span class="code-string">':username'</span> => $username]);<br>
                    $user = $stmt-><span class="code-func">fetch</span>();
                </div>

                <h4 style="color: #10b981; margin-top: 1.5rem;">2. الحماية من XSS بواسطة HTML Entity Encoding:</h4>
                <p style="color: var(--text-secondary); font-size: 0.95rem; margin-top: 0.25rem;">
                    نستخدم دالة <code style="color:var(--accent-blue);">htmlspecialchars()</code> لتحويل كافة الرموز الخاصة في نصوص المدخلات إلى نصوص آمنة للعرض:
                </p>
                <div class="code-block">
                    <span class="code-comment">// كود آمن - تشفير مخرجات HTML قبل الطباعة</span><br>
                    $safe_input = <span class="code-func">htmlspecialchars</span>($username, ENT_QUOTES, <span class="code-string">'UTF-8'</span>);<br>
                    echo <span class="code-string">"اسم المستخدم: "</span> . $safe_input;
                </div>

                <h4 style="color: #10b981; margin-top: 1.5rem;">3. تشفير كلمات المرور بحساب Hashing:</h4>
                <p style="color: var(--text-secondary); font-size: 0.95rem; margin-top: 0.25rem;">
                    لا نبحث عن كلمة المرور النصية في قاعدة البيانات مباشرة، بل نستخدم <code style="color:var(--accent-blue);">password_verify()</code> للتحقق من التجزئة الأمنية:
                </p>
                <div class="code-block">
                    <span class="code-comment">// كود آمن - التحقق من تشفير Bcrypt / Argon2</span><br>
                    <span class="code-keyword">if</span> ($user && <span class="code-func">password_verify</span>($password, $user[<span class="code-string">'password'</span>])) {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="code-comment">// نجاح تسجيل الدخول</span><br>
                    }
                </div>
            </div>
        </div>
    </div>
</body>
</html>
