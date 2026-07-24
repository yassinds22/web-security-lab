# المختبر التعليمي لأمن الويب (PHP Security Educational Lab)

تطبيق تعليمي تفاعلي بلغة PHP يهدف إلى شرح وتوضيح الفرق بين التعامل غير الآمن مع المدخلات (المسبب لثغرات **SQL Injection** و **XSS**) والتعامل الآمن باستخدام **PDO Prepared Statements** وتشفير الـ HTML.

---

## 🚀 المميزات الرئيسية

- **نموذج غير آمن (Vulnerable Mode):**
  - يوضح كيفية حدوث ثغرة حقن SQL عند دمج مدخلات المستخدم مباشرة في استعلامات الهيكلة.
  - يوضح ثغرة Reflected XSS عند طباعة البيانات في لوحة التحكم بدون تنقية.

- **نموذج آمن (Secure Mode):**
  - استخدام الاستعلامات المجهزة `PDO Prepared Statements` مع Parameter Binding.
  - حماية كلمات المرور باستخدام تشفير `password_hash()` والتحقق بـ `password_verify()`.
  - معالجة المخرجات باستخدام `htmlspecialchars()` لمنع هجمات XSS.

- **لوحة تحكم ديناميكية (Dashboard):**
  - توثق نجاح تسجيل الدخول مع الترحيب بـ "أهلاً بك [اسم المستخدم] في الصفحة الآمنة / غير الآمنة".
  - تمكين زر تسجيل الخروج وإدارة الجلسات `session_start()`.

---

## 🛠️ متطلبات التشغيل

- بيئة خادم محلي مثل **XAMPP** / **WAMP** / **LAMP** (PHP 7.4+ & MySQL / MariaDB).
- قاعدة بيانات باسم `lab_security_db`.

---

## 📂 إعداد قاعدة البيانات

1. قم بإنشاء قاعدة البيانات باسم `lab_security_db`.
2. استورد ملف `schema.sql` في قاعدة البيانات.
3. اضبط إعدادات الاتصال في ملف `db.php`.

---

## 👤 بيانات الدخول الافتراضية

- **آدمين (Admin):**
  - اسم المستخدم: `admin`
  - كلمة المرور: `admin123`
- **طالب (Student):**
  - اسم المستخدم: `student`
  - كلمة المرور: `student123`

---

## 🧪 أمثلة عملية واختبارات الثغرات (Payloads & Exploitation)

### 1️⃣ ثغرات حقن SQL (SQL Injection - SQLi)
تحدث الثغرة في **الوضع غير الآمن** لأن الكود يقوم بدمج المدخلات مباشرة داخل استعلام SQL:
```sql
SELECT * FROM users WHERE username = '$username' AND password = '$password'
```

#### الطرق والأمثلة للتجربة:

- **الطريقة 1: التجاوز بالتعليق (Comment Bypass)**
  - **حقل اسم المستخدم:** `admin' -- ` (ملاحظة: توجد مسافة بعد شرطتين `-- `)
  - **حقل كلمة المرور:** اتركها فارغة أو أدخل أي رمز.
  - **النتيجة:** يصبح الاستعلام: `SELECT * FROM users WHERE username = 'admin' -- ' AND password = ''`
  - **الشرح:** يتم إلغاء باقي الاستعلام وتتجاوز كلمة المرور لتسجيل الدخول كـ `admin`.

- **الطريقة 2: الشرط المنطقي الصائب دائماً (Always True Condition)**
  - **حقل اسم المستخدم:** `' OR '1'='1`
  - **حقل كلمة المرور:** `' OR '1'='1`
  - **النتيجة:** يصبح الاستعلام: `WHERE username = '' OR '1'='1' AND password = '' OR '1'='1'`
  - **الشرح:** يتحقق الشرط دائماً (`TRUE`) مما يتيح الدخول بأول حساب في قاعدة البيانات.

- **الطريقة 3: إجبار اسم المستخدم مع الشرط (Specific Account Bypass)**
  - **حقل اسم المستخدم:** `admin' OR 1=1 -- `
  - **النتيجة:** يتم إجبار القاعدة على مطابقة حساب الأدمن مباشرة وتجاهل كلمة المرور.

- **الطريقة 4: حقن الاستعلامات المدمجة (UNION-based SQLi)**
  - **حقل اسم المستخدم:** `' UNION SELECT 1, 'hacked_user', 'hacked@lab.local', 'pass', 'administrator', NOW() -- `
  - **النتيجة:** دمج صف مصنوع من قبل المهاجم والدخول به.

---

### 2️⃣ ثغرات السكربتات عبر المواقع (Cross-Site Scripting - Reflected XSS)
تحدث الثغرة في **الوضع غير الآمن** لأن صفحة لوحة التحكم `dashboard.php` تطبع `$_SESSION['user']` مباشرة في HTML بدون استخدام `htmlspecialchars()`.

#### الطرق والأمثلة للتجربة:

- **الطريقة 1: التنبيه البسيط (Simple Script Alert)**
  - **اسم المستخدم:** `<script>alert('تم اكتشاف ثغرة XSS!')</script>`
  - **كلمة المرور (عبر SQLi):** `' OR '1'='1`
  - **النتيجة:** عند التوجيه للوحة التحكم غير الآمنة، سيظهر لك نافذة alert برمجية.

- **الطريقة 2: الاستدعاء عن طريق حدث الصور (Image Event Handler XSS)**
  - **اسم المستخدم:** `<img src="invalid-image.jpg" onerror="alert('XSS via Image Tag!')">`
  - **النتيجة:** عند فشل تحميل الصورة، يتم تنفيذ كود JavaScript المكتوب في `onerror`.

- **الطريقة 3: حقن وسوم وتعديل تصميم الواجهة (HTML Injection / Defacement)**
  - **اسم المستخدم:** `<h1 style="color: red; background: yellow; padding: 10px;">⚠️ تم اختراق هذه الصفحة!</h1>`
  - **النتيجة:** يتم تغيير شكل لوحة التحكم وحقن عناصر HTML جديدة.

- **الطريقة 4: سرقة بيانات الكوكي والطلب الخارجي (Stealing Session Cookies)**
  - **اسم المستخدم:** `<script>console.log('Session Cookie:', document.cookie);</script>`
  - **النتيجة:** طباعة جلسات المستخدم في كونسول المتصفح (Developer Console).

---

## 🛡️ كيف تتم الحماية في الوضع الآمن (Secure Mode)؟

1. **ضد SQL Injection:**
   استخدام الجمل المجهزة (Prepared Statements):
   ```php
   $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
   $stmt->execute([':username' => $username]);
   ```
2. **ضد XSS:**
   تشفير وترميز جميع المخرجات النصية المطبوعة للمستخدم:
   ```php
   $safe_username = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
   echo "أهلاً بك " . $safe_username;
   ```

---

## 📜 التنسيق والأناقة

تم تصميم الواجهة باستعارة ألوان ورموز عصرية في `style.css` لتوفير تجربة مستخدم واضحة وجذابة.
