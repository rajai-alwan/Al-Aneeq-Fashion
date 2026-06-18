<?php
include_once 'config.php';

// جدار حماية لمنع غير المسؤولين
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// تحديث حالة الطلبات المستلمة (شحن، دفع، إلغاء، توصيل)
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->execute([$new_status, $order_id]);
}

// استعلام ربط الجداول لعرض اسم الزبون المشتري مع تفاصيل طلبيته الكلية
$orders = $pdo->query("SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة طلبات المبيعات - Al-Aneeq Fashion</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* تنسيقات مخصصة لتنظيم مظهر الأعمدة الجديدة */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; text-align: center; background: #fff; }
        th, td { padding: 12px; border: 1px solid #ddd; }
        thead { background-color: #2c3e50; color: white; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: bold; }
        .badge-usdt { background-color: #f39c12; color: #fff; }
        .badge-cod { background-color: #7f8c8d; color: #fff; }
        .btn-view { background-color: #2980b9; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 0.85rem; display: inline-block; }
        .btn-view:hover { background-color: #3498db; }
    </style>
</head>
<body>
<header>
    <h1>لوحة الإدارة - إدارة طلبات - فواتير الزبائن الحالية</h1>
    <a href="admin_dashboard.php" style="color:#fff; text-decoration:none;">&larr; العودة للوحة الإحصائيات العامة</a>
</header>

<div class="container" style="padding: 20px;">
    <h3>سجل عمليات الفواتير المسجلة بالنظام</h3>
    <table>
        <thead>
            <tr>
                <th>رقم الفاتورة</th>
                <th>اسم الزبون المشتري</th>
                <th>تاريخ الطلبية</th>
                <th>إجمالي الفاتورة</th>
                <th>عنوان التوصيل المختار</th>
                <th>وسيلة الدفع</th>
                <th>إيصال التحويل (USDT)</th>
                <th>حالة معالجة الطلب الحالية</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $ord): ?>
                <tr>
                    <td>#<?php echo $ord['order_id']; ?></td>
                    <td><b><?php echo htmlspecialchars($ord['full_name'], ENT_QUOTES, 'UTF-8'); ?></b></td>
                    <td><?php echo $ord['order_date']; ?></td>
                    <td><b><?php echo $ord['total_amount']; ?> ₪</b></td>
                    <td><?php echo htmlspecialchars($ord['shipping_address'], ENT_QUOTES, 'UTF-8'); ?></td>
                    
                    <td>
                        <?php if (isset($ord['payment_method']) && $ord['payment_method'] == 'usdt'): ?>
                            <span class="badge badge-usdt">USDT (TRC-20) 🪙</span>
                        <?php else: ?>
                            <span class="badge badge-cod">عند الاستلام</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if (!empty($ord['payment_receipt'])): ?>
                            <a href="uploads/receipts/<?php echo $ord['payment_receipt']; ?>" target="_blank" class="btn-view">👁️ عرض الإيصال</a>
                        <?php else: ?>
                            <span style="color: #95a5a6;">—</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <form method="POST" style="display:inline-flex; gap:5px;">
                            <input type="hidden" name="order_id" value="<?php echo $ord['order_id']; ?>">
                            <select name="status" style="padding:5px; border-radius:4px;">
                                <option value="pending" <?php if($ord['status'] == 'pending') echo 'selected'; ?>>بانتظار المراجعة (Pending)</option>
                                <option value="paid" <?php if($ord['status'] == 'paid') echo 'selected'; ?>>تم الدفع المالي (Paid)</option>
                                <option value="shipped" <?php if($ord['status'] == 'shipped') echo 'selected'; ?>>خارج للشحن (Shipped)</option>
                                <option value="delivered" <?php if($ord['status'] == 'delivered') echo 'selected'; ?>>تم التوصيل للزبون (Delivered)</option>
                                <option value="cancelled" <?php if($ord['status'] == 'cancelled') echo 'selected'; ?>>ملغي من الإدارة (Cancelled)</option>
                            </select>
                            <button type="submit" name="update_status" class="btn" style="padding:5px 10px; font-size:0.8rem; background:#2c3e50; color:#fff;">حفظ الحالة</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>