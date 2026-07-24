<?php
session_start();
require_once 'db.php';

$message = '';
$user_info = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($pdo)) {
        try {
            /* 
             * 🔴 1. ثغرة SQL Injection (حقن SQL):
             * السبب: دمج مدخلات المستخدم مباشرة في استعلام SQL باستخدام النص المجرد (String Concatenation) 
             * بدون استخدام Prepared Statements أو تنقية (Sanitization).
             */
            $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
            
            // تنفيذ الاستعلام مباشرة (غير آمن - يسمح بـ SQL Injection)
            $result = $pdo->query($query);
            $user_info = $result ? $result->fetch() : null;

            // إذا لم ينطبق الاستعلام المباشر (لأن كلمة المرور في القاعدة مشفرة)، نتحقق من التشفير للدخول العادي
            if (!$user_info && !empty($username)) {
                try {
                    $check_stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u");
                    $check_stmt->execute([':u' => $username]);
                    $candidate = $check_stmt->fetch();
                    if ($candidate && password_verify($password, $candidate['password'])) {
                        $user_info = $candidate;
                    }
                } catch (PDOException $e) {}
            }

            if ($user_info) {
                $_SESSION['user'] = $username;
                $_SESSION['mode'] = 'vulnerable';
                header('Location: dashboard.php');
                exit();
            } else {
                $message = "<div class='alert alert-danger'>فشل تسجيل الدخول للمستخدم: " . $username . "</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>خطأ SQL: " . $e->getMessage() . "</div>";
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
    <title>صفحة الدخول - النموذج غير الآمن (ضعيف)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>المختبر التعليمي لأمن الويب</h1>
            <p>شرح وفهم ثغرات SQL Injection و XSS في تطبيقات PHP</p>
        </header>

        <div class="nav-tabs">
            <a href="index.php" class="nav-btn">🏠 الرئيسية</a>
            <a href="login_vulnerable.php" class="nav-btn vulnerable-active">⚠️ النسخة غير الآمنة (ضعيفة)</a>
            <a href="login_secure.php" class="nav-btn">🛡️ النسخة المحمية (معالجة)</a>
        </div>

        <div class="card">
            <span class="badge badge-danger">⚠️ نموذج يحتوي على ثغرات (للتعليم فقط)</span>
            <h2>تسجيل الدخول (كود ضعيف)</h2>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                هذه الصفحة توضح الكود البرمجي الذي يقع فيه الكثير من المطورين الجدد عند عدم حماية المدخلات.
            </p>

            <?php if (!empty($message)) echo $message; ?>

            <form method="POST" action="login_vulnerable.php">
                <div class="form-group">
                    <label for="username">اسم المستخدم:</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="أدخل اسم المستخدم" >
                </div>
                <div class="form-group">
                    <label for="password">كلمة المرور:</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="أدخل كلمة المرور" >
                </div>
                <button type="submit" class="btn-submit btn-danger">تسجيل الدخول (غير آمن)</button>
            </form>

            <div class="explanation-section">
                <div class="explanation-title">
                    <span>🔍</span> التحليل البرمجي للثغرات في هذه الصفحة:
                </div>
                
                <h4 style="color: #ef4444; margin-top: 1rem;">1. ثغرة SQL Injection:</h4>
                <p style="color: var(--text-secondary); font-size: 0.95rem; margin-top: 0.25rem;">
                    في الكود الحالي، يتم دمج نص المخرجات مباشرة داخل الاستعلام:
                </p>
                <div class="code-block">
                    <span class="code-comment">// كود غير آمن - يسمح بتغيير منطق استعلام SQL</span><br>
                    $query = <span class="code-string">"SELECT * FROM users WHERE username = '$username' AND password = '$password'"</span>;<br>
                    $pdo->query($query);
                </div>

                <h4 style="color: #ef4444; margin-top: 1.5rem;">2. ثغرة Cross-Site Scripting (XSS):</h4>
                <p style="color: var(--text-secondary); font-size: 0.95rem; margin-top: 0.25rem;">
                    يتم طباعة مدخلات المتصفح مباشرة داخل صفحة HTML دون تشفير أو تنقية:
                </p>
                <div class="code-block">
                    <span class="code-comment">// كود غير آمن - يطبع مدخلات المستخدم مباشرة في المتصفح</span><br>
                    echo <span class="code-string">"فشل تسجيل الدخول للمستخدم: "</span> . $username;
                </div>
            </div>
        </div>
    </div>
</body>
</html>
