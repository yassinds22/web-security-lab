<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$user = $_SESSION['user'];
$mode = $_SESSION['mode'] ?? 'secure';
$is_secure = ($mode === 'secure');

// في الوضع الآمن نعالج النص بحماية htmlspecialchars
// في الوضع غير الآمن نطبعه مباشرة لإظهار الثغرة (XSS)
$display_name = $is_secure ? htmlspecialchars($user, ENT_QUOTES, 'UTF-8') : $user;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - <?php echo $is_secure ? 'الصفحة الآمنة' : 'الصفحة غير الآمنة'; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card dashboard-card <?php echo $is_secure ? 'card-secure' : 'card-vulnerable'; ?>">
            <div class="dashboard-badge <?php echo $is_secure ? 'badge-secure' : 'badge-vulnerable'; ?>">
                <?php echo $is_secure ? '🛡️ الصفحة الآمنة' : '⚠️ الصفحة غير الآمنة'; ?>
            </div>

            <h2>لوحة التحكم</h2>

            <div class="welcome-message">
                <h3>أهلاً بك <strong><?php echo $display_name; ?></strong> في <?php echo $is_secure ? 'الصفحة الآمنة' : 'الصفحة غير الآمنة'; ?></h3>
            </div>

            <p class="dashboard-desc">
                <?php if ($is_secure): ?>
                    تم تسجيل دخولك بنجاح في البيئة المحمية. البيانات والجلسة مؤمنة بالكامل من ثغرات SQL Injection و XSS.
                <?php else: ?>
                    تم تسجيل دخولك في البيئة غير الآمنة. هذه الصفحة تعرض مدخلاتك مباشرة دون تنقية لإظهار كيفية حدوث ثغرات XSS.
                <?php endif; ?>
            </p>

            <div class="dashboard-actions">
                <a href="logout.php" class="btn-submit btn-logout">تسجيل الخروج</a>
            </div>
        </div>
    </div>
</body>
</html>
