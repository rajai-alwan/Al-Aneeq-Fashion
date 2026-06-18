<?php
include_once 'config.php';

// جدار حماية لمنع غير المسؤولين (Admin Role Security)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// كود الإحصائيات المباشر
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$unread_msg = $pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();

// إحصائية الـ USDT الجديدة: جلب عدد الطلبات التي تم اختيار USDT لها وما زالت بانتظار المراجعة
$pending_usdt = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_method = 'usdt' AND status = 'pending'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم Al-Aneeq | إدارة المتجر</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-wrapper { display: flex; min-height: 80vh; }
        .sidebar { width: 250px; background: #2c3e50; color: #fff; padding: 20px; }
        .sidebar a { display: block; color: #fff; padding: 12px; text-decoration: none; margin-bottom: 5px; border-radius:4px;}
        .sidebar a:hover { background: #34495e; color: var(--accent-color); }
        .main-content { flex: 1; padding: 40px; }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: #fff; padding: 25px; border-radius: 8px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 4px solid var(--accent-color); }
        .stat-card h3 { font-size: 2.5rem; color: #333; margin-top: 10px; }
        
        /* تنسيق إضافي للتنبيهات */
        .alert-box { background: #fff3cd; color: #856404; padding: 15px; border-radius: 6px; margin-bottom: 25px; border: 1px solid #ffeeba; display: flex; justify-content: space-between; align-items: center; }
        .alert-box a { background: #856404; color: #fff; text-decoration: none; padding: 5px 12px; border-radius: 4px; font-size: 0.9rem; }
        .alert-box a:hover { background: #6c5104; }
    </style>
</head>
<body>
<header>
    <h1>لوحة الإدارة - Al-Aneeq Fashion</h1>
    <a href="index.php" style="color: #fff; text-decoration: none;">&rarr; العودة للموقع الرئيسي</a>
</header>

<div class="admin-wrapper">
    <div class="sidebar">
        <h3 style="margin-bottom: 20px; padding-bottom:10px; border-bottom:1px solid #4f5d73;">العمليات الإدارية</h3>
        <a href="admin_dashboard.php" style="background: var(--accent-color); color: #000;">🏠 الإحصائيات العامة</a>
        <a href="admin_products.php">👕 إدارة الملابس والمنتجات</a>
        <a href="admin_orders.php">📦 إدارة طلبات الزبائن</a>
        <a href="admin_users.php">👥 إدارة المستخدمين</a>
        <a href="admin_messages.php">✉️ رسائل اتصل بنا (<?php echo $unread_msg; ?>)</a>
        <a href="logout.php" style="color:#e74c3c; margin-top:30px;">تسجيل الخروج</a>
    </div>

    <div class="main-content">
        <h2>أهلاً بكِ في الإدارة، يسرى مهره</h2>
        <p style="color: #777; margin-bottom: 30px;">متابعة فورية ومباشرة لأداء المتجر الإلكتروني لمشروع الفصل.</p>
        
        <!-- إشعار ذكي يظهر فقط إذا كان هناك طلبات بحاجة لمراجعة التحويل -->
        <?php if ($pending_usdt > 0): ?>
            <div class="alert-box">
                <span>⚠️ يوجد <strong><?php echo $pending_usdt; ?></strong> طلبات دفع عبر USDT بانتظار مراجعة الإيصالات وتأكيدها.</span>
                <a href="admin_orders.php">راجع الطلبات الآن &larr;</a>
            </div>
        <?php endif; ?>

        <div class="stat-grid">
            <div class="stat-card">
                <p>إجمالي قطع الملابس</p>
                <h3><?php echo $total_products; ?></h3>
            </div>
            <div class="stat-card">
                <p>إجمالي الطلبات المستلمة</p>
                <h3><?php echo $total_orders; ?></h3>
            </div>
            
            <!-- بطاقة إحصائيات الـ USDT الجديدة -->
            <div class="stat-card" style="border-top-color: #f39c12;">
                <p>طلبات USDT معلقة 🪙</p>
                <h3 style="color: #e67e22;"><?php echo $pending_usdt; ?></h3>
            </div>

            <div class="stat-card">
                <p>المستخدمين والزبائن</p>
                <h3><?php echo $total_users; ?></h3>
            </div>
            <div class="stat-card" style="border-top-color: var(--danger);">
                <p>رسائل بانتظار القراءة</p>
                <h3><?php echo $unread_msg; ?></h3>
            </div>
        </div>
        
    </div>
</div>
</body>
</html>