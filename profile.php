<?php 
include_once 'header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// تحديث الملف الشخصي عند الإرسال بالنموذج التفاعلي
if (isset($_POST['update_profile'])) {
    $name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE user_id = ?");
    $stmt->execute([$name, $phone, $address, $user_id]);
    $_SESSION['full_name'] = $name;
    $msg = "تم تحديث بياناتكِ بنجاح.";
}

// استخراج البيانات المحدثة للمستخدم للعرض الفوري بالنموذج
$user = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$user->execute([$user_id]);
$userData = $user->fetch();

// استخراج أرشيف وتاريخ طلبات المشتريات السابقة للزبون
$orders_stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$orders_stmt->execute([$user_id]);
$my_orders = $orders_stmt->fetchAll();
?>

<div class="container" style="display: flex; gap: 40px; flex-wrap: wrap;">
    <!-- تعديل المعلومات للزبون -->
    <div style="flex: 1; min-width: 300px; background:#fff; padding:30px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.05);">
        <h3>تحديث الملف الشخصي للزبون</h3>
        <?php if(isset($msg)) echo "<p style='color:var(--success); margin:10px 0;'>$msg</p>"; ?>
        <form method="POST" style="display:flex; flex-direction:column; gap:15px; margin-top:20px;">
            <div>
                <label style="display:block; margin-bottom:5px;">الاسم الكامل</label>
                <input type="text" name="full_name" value="<?php echo sanitize($userData['full_name']); ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">البريد الإلكتروني (غير قابل للتعديل)</label>
                <input type="text" value="<?php echo sanitize($userData['email']); ?>" disabled style="width:100%; padding:10px; border:1px solid #eee; background:#fafafa; border-radius:4px;">
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">رقم الهاتف الحالي</label>
                <input type="text" name="phone" value="<?php echo sanitize($userData['phone']); ?>" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">عنوان الشحن والطلب الافتراضي</label>
                <textarea name="address" rows="3" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;"><?php echo sanitize($userData['address']); ?></textarea>
            </div>
            <button type="submit" name="update_profile" class="btn">تعديل وحفظ البيانات</button>
        </form>
    </div>

    <!-- سجل الطلبات السابقة والمدخلة بالنظام المالي للمتجر -->
    <div style="flex: 2; min-width: 300px;">
        <h3>أرشيف وتاريخ طلبات الشراء السابقة</h3>
        <?php if(empty($my_orders)): ?>
            <p style="margin-top:20px; color:#777;">لم تقومي بإجراء أي عمليات شراء مسبقة من المتجر حتى الآن.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>تاريخ العملية</th>
                        <th>مجموع الفاتورة</th>
                        <th>حالة الطلب الحالية</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($my_orders as $ord): ?>
                        <tr>
                            <td>#<?php echo $ord['order_id']; ?></td>
                            <td><?php echo $ord['order_date']; ?></td>
                            <td><b><?php echo $ord['total_amount']; ?> ₪</b></td>
                            <td>
                                <span style="padding:4px 8px; border-radius:4px; font-size:0.85rem; font-weight:bold;
                                    background: <?php 
                                        if($ord['status']=='pending') echo '#f1c40f; color:#000;';
                                        elseif($ord['status']=='delivered') echo '#27ae60; color:#fff;';
                                        else echo '#34495e; color:#fff;';
                                    ?>">
                                    <?php echo $ord['status']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'footer.php'; ?>