<?php
include_once 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 1. تعديل الصلاحيات والمناصب التبادلية (أدمن / زبون) للتحكم بالنظام الإداري للموقع
if (isset($_POST['change_role'])) {
    $u_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];
    
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
    $stmt->execute([$new_role, $u_id]);
}

// 2. حذف الحسابات المعطوبة للزبائن شرط عدم امتلاكهم لقيود مبيعات بالداتا بيز لمنع الأخطاء الفادحة برمجياً
if (isset($_GET['delete_user'])) {
    $u_id = intval($_GET['delete_user']);
    
    $check_orders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
    $check_orders->execute([$u_id]);
    if ($check_orders->fetchColumn() > 0) {
        echo "<script>alert('عذراً يسرى، لا يمكن مسح هذا الحساب نظراً لوجود قيود فواتير وطلبيات قائمة للمستخدم بداخل النظام المالي للمتجر!');</script>";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$u_id]);
    }
}

$users = $pdo->query("SELECT user_id, full_name, email, role, phone FROM users ORDER BY user_id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة مستخدمي المتجر - Al-Aneeq Fashion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>لوحة الإدارة - سجل وحسابات كافة المستخدمين</h1>
    <a href="admin_dashboard.php" style="color:#fff; text-decoration:none;">&larr; العودة للوحة الإحصائيات العامة</a>
</header>

<div class="container">
    <h3>حسابات الزبائن والمسؤولين المسجلين بمتجر الأناقة</h3>
    <table>
        <thead>
            <tr>
                <th>معرف المستخدم</th>
                <th>الاسم الكامل</th>
                <th>البريد الإلكتروني الحركي</th>
                <th>رقم الهاتف المدخل</th>
                <th>الدور والصلاحية الحالية برمجياً</th>
                <th>خيارات مسح وحذف القيود المتاحة</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $u): ?>
                <tr>
                    <td>#<?php echo $u['user_id']; ?></td>
                    <td><b><?php echo sanitize($u['full_name']); ?></b></td>
                    <td><?php echo sanitize($u['email']); ?></td>
                    <td><?php echo sanitize($u['phone']); ?></td>
                    <td>
                        <form method="POST" style="display:inline-flex; gap:5px;">
                            <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                            <select name="role" style="padding:4px; border-radius:4px;">
                                <option value="customer" <?php if($u['role'] == 'customer') echo 'selected'; ?>>زبون متجر (Customer)</option>
                                <option value="admin" <?php if($u['role'] == 'admin') echo 'selected'; ?>>مدير لوحة تحكم (Admin)</option>
                            </select>
                            <button type="submit" name="change_role" class="btn" style="padding:4px 8px; font-size:0.75rem; background:#34495e; color:#fff;">حفظ الدور</button>
                        </form>
                    </td>
                    <td>
                        <!-- التأكد من عدم قيام الأدمن بمسح حسابه الشخصي الفعال بالخطأ أثناء التجريب المخبري -->
                        <?php if($u['user_id'] != $_SESSION['user_id']): ?>
                            <a href="admin_users.php?delete_user=<?php echo $u['user_id']; ?>" onclick="return confirm('هل تودين إتمام مسح حساب هذا المستخدم من المتجر؟');" style="color:var(--danger); text-decoration:none; font-weight:bold;">حذف الحساب</a>
                        <?php else: ?>
                            <span style="color:#aaa; font-style:italic;">حسابك الحالي النشط</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>