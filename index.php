<?php
session_start();
require_once 'db.php';

$message = '';
$mode = $_POST['mode'] ?? 'secure';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!isset($pdo)) {
        $message = "<div class='alert alert-warning'>خطأ في الاتصال بقاعدة البيانات.</div>";
    } else {
        if ($mode === 'vulnerable') {
            // ⚠️ النسخة غير الآمنة (حقن SQL و XSS)
            try {
                $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
                $result = $pdo->query($query);
                $user = $result->fetch();

                if ($user) {
                    $_SESSION['user'] = $username;
                    $_SESSION['mode'] = 'vulnerable';
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $message = "<div class='alert alert-danger'>خطأ في اسم المستخدم أو كلمة المرور لـ " . $username . "</div>";
                }
            } catch (PDOException $e) {
                $message = "<div class='alert alert-danger'>خطأ SQL: " . $e->getMessage() . "</div>";
            }
        } else {
            // 🛡️ النسخة المحمية المعالجة (PDO Prepared Statements & htmlspecialchars)
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->execute([':username' => $username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user'] = $user['username'];
                    $_SESSION['mode'] = 'secure';
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $safe_input = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
                    $message = "<div class='alert alert-danger'>خطأ في اسم المستخدم أو كلمة المرور لـ " . $safe_input . "</div>";
                }
            } catch (PDOException $e) {
                $message = "<div class='alert alert-danger'>حدث خطأ أثناء معالجة الطلب.</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <h2>تسجيل الدخول</h2>

            <?php if (!empty($message)) echo $message; ?>

            <form method="POST" action="index.php">
                <div class="mode-selector">
                    <label class="mode-option">
                        <input type="radio" name="mode" value="secure" <?php echo ($mode === 'secure') ? 'checked' : ''; ?>>
                        <span>🛡️ معالج (آمن)</span>
                    </label>
                    <label class="mode-option">
                        <input type="radio" name="mode" value="vulnerable" <?php echo ($mode === 'vulnerable') ? 'checked' : ''; ?>>
                        <span>⚠️ غير آمن</span>
                    </label>
                </div>

                <div class="form-group">
                    <label for="username">اسم المستخدم</label>
                    <input type="text" id="username" name="username" class="form-control" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn-submit">تسجيل الدخول</button>
            </form>
        </div>
    </div>
</body>
</html>
