<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Al-Aneeq Fashion | متجر الأزياء الفاخرة</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">
        <h1>Al-Aneeq Fashion</h1>
    </div>
    <nav>
        <a href="index.php">الرئيسية</a>
        <a href="products.php">الأزياء</a>
        <a href="contact.php">اتصل بنا</a>
        <a href="cart.php">سلة التسوق</a>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="profile.php">حسابي (<?php echo sanitize($_SESSION['full_name']); ?>)</a>
            <?php if($_SESSION['role'] == 'admin'): ?>
                <a href="admin_dashboard.php" style="color: #f1c40f;">لوحة التحكم</a>
            <?php endif; ?>
            <a href="logout.php" style="color: #e74c3c;">خروج</a>
        <?php else: ?>
            <a href="login.php">تسجيل الدخول</a>
            <a href="register.php">إنشاء حساب</a>
        <?php endif; ?>
    </nav>
</header>