<?php
include_once 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 1. تحديد الرسائل كمقروءة لإلغاء إشعار التنبيه الأحمر باللوحة الرئيسية
if (isset($_GET['read'])) {
    $msg_id = intval($_GET['read']);
    $stmt = $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE message_id = ?");
    $stmt->execute([$msg_id]);
    header("Location: admin_messages.php");
    exit();
}

// 2. معالجة حذف الرسالة المعينة نهائياً
if (isset($_GET['delete'])) {
    $msg_id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE message_id = ?");
    $stmt->execute([$msg_id]);
    header("Location: admin_messages.php");
    exit();
}

$messages = $pdo->query("SELECT * FROM contacts ORDER BY submitted_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة الرسائل والبريد الوارد - Al-Aneeq Fashion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>لوحة الإدارة - بريد ورسائل الزبائن الواردة</h1>
    <a href="admin_dashboard.php" style="color:#fff; text-decoration:none;">&larr; العودة للوحة الإحصائيات العامة</a>
</header>

<div class="container">
    <h3>أرشيف البريد والرسائل المستلمة بالمتجر</h3>
    <table>
        <thead>
            <tr>
                <th>اسم المرسل</th>
                <th>بريده الإلكتروني</th>
                <th>الموضوع</th>
                <th>نص الرسالة المرسلة</th>
                <th>تاريخ الوصول</th>
                <th>الخيارات المتوفرة للتحكم</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($messages as $m): ?>
                <tr style="<?php if(!$m['is_read']) echo 'background-color: #fcf8e3; font-weight:bold;'; ?>">
                    <td><?php echo sanitize($m['name']); ?></td>
                    <td><?php echo sanitize($m['email']); ?></td>
                    <td><span style="color:var(--accent-color);"><?php echo sanitize($m['subject']); ?></span></td>
                    <td style="text-align:right; max-width:300px;"><?php echo nl2br(sanitize($m['message'])); ?></td>
                    <td><?php echo $m['submitted_at']; ?></td>
                    <td>
                        <?php if(!$m['is_read']): ?>
                            <a href="admin_messages.php?read=<?php echo $m['message_id']; ?>" style="color:var(--success); text-decoration:none; margin-left:10px;">تعيين كمقروء ✔️</a>
                        <?php endif; ?>
                        <a href="admin_messages.php?delete=<?php echo $m['message_id']; ?>" onclick="return confirm('هل تودين حذف الرسالة نهائياً يسرى؟');" style="color:var(--danger); text-decoration:none;">مسح 🗑️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>