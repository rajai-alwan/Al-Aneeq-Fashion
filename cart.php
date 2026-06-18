<?php 
include_once 'header.php'; 

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container' style='text-align:center; padding:50px 20px;'><p style='font-size:1.2rem; margin-bottom:20px;'>يرجى تسجيل الدخول أولاً لتتمكن من استعراض حقيبة التسوق الخاصة بك.</p><a href='login.php' class='btn'>تسجيل الدخول</a></div>";
    include_once 'footer.php';
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. معالجة تحديث الكميات بالـ POST
if (isset($_POST['update_qty'])) {
    $cart_id = intval($_POST['cart_id']);
    $new_qty = intval($_POST['quantity']);
    if($new_qty > 0) {
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND user_id = ?");
        $stmt->execute([$new_qty, $cart_id, $user_id]);
    }
}

// 2. معالجة حذف عنصر من السلة
if (isset($_POST['delete_item'])) {
    $cart_id = intval($_POST['cart_id']);
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
}

// جلب عناصر السلة الحالية للزبون المذكور
$stmt = $pdo->prepare("SELECT c.cart_id, c.quantity, p.product_id, p.name, p.price, p.image_url, p.stock_quantity FROM cart_items c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$subtotal = 0;
?>

<div class="container">
    <h2 class="section-title">حقيبة تسوق الأزياء الخاصة بكِ</h2>
    
    <?php if(empty($cart_items)): ?>
        <div style="text-align: center; margin: 50px 0;">
            <p style="font-size: 1.3rem; color: #777; margin-bottom: 20px;">حقيبة التسوق فارغة حالياً.. تصفحي أحدث الموديلات لتعبئتها!</p>
            <a href="products.php" class="btn">تصفح الملابس الآن</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>الاسم</th>
                    <th>السعر المفرد</th>
                    <th>الكمية</th>
                    <th>الإجمالي الفرعي</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cart_items as $item): 
                    $item_total = $item['price'] * $item['quantity'];
                    $subtotal += $item_total;
                ?>
                    <tr>
                        <td><img src="<?php echo sanitize($item['image_url']); ?>" width="60" height="60" style="object-fit: cover; border-radius:4px;"></td>
                        <td><b><?php echo sanitize($item['name']); ?></b></td>
                        <td><?php echo $item['price']; ?> ₪</td>
                        <td>
                            <form method="POST" style="display: inline-flex; gap: 5px;">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_quantity']; ?>" style="width: 60px; text-align: center; padding:5px;">
                                <button type="submit" name="update_qty" class="btn" style="padding: 5px 10px; font-size: 0.8rem; background:#333; color:#fff;">تحديث</button>
                            </form>
                        </td>
                        <td><b><?php echo $item_total; ?> ₪</b></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <button type="submit" name="delete_item" class="btn" style="background:var(--danger); color:#fff; padding: 5px 10px; font-size: 0.8rem;">حذف 🗑️</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 30px; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: left;">
            <p style="font-size: 1.2rem; margin-bottom: 10px;">المجموع الفرعي: <b><?php echo $subtotal; ?> ₪</b></p>
            <p style="font-size: 1.4rem; color: var(--accent-color); font-weight: bold; margin-bottom: 20px;">المجموع الكلي المكتمل: <b><?php echo $subtotal; ?> ₪</b></p>
            <a href="checkout.php" class="btn" style="background: var(--success); color:#fff; font-size: 1.1rem; padding: 12px 35px;">تأكيد شراء الطلب والانتقال للدفع &larr;</a>
        </div>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>