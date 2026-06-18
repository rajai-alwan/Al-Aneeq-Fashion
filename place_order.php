<?php
include_once 'config.php';

// حيلة برمجة ذكية: البحث التلقائي عن متغير الاتصال بقاعدة البيانات مهما كان اسمه في ملف config
if (!isset($pdo) || $pdo === null) {
    if (isset($conn) && $conn !== null) {
        $pdo = $conn;
    } elseif (isset($db) && $db !== null) {
        $pdo = $db;
    } else {
        // إذا لم يجد الأسماء الشائعة، يبحث في كل المتغيرات المعرفة عن نوع الاتصال PDO
        foreach (get_defined_vars() as $var) {
            if ($var instanceof PDO) {
                $pdo = $var;
                break;
            }
        }
    }
}

// إذا فشل تماماً في العثور على الاتصال
if (!isset($pdo) || $pdo === null) {
    die("خطأ: لم يتم العثور على متغير الاتصال بقاعدة البيانات. يرجى التأكد من المسمى داخل ملف config.php");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// جلب تفاصيل السلة أولاً لحساب الفاتورة الإجمالية
$stmt = $pdo->prepare("SELECT c.quantity, p.product_id, p.title, p.price, p.stock_quantity FROM cart_items c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

// جلب عنوان المستخدم الافتراضي لنسخه للطلب
$user_stmt = $pdo->prepare("SELECT address FROM users WHERE user_id = ?");
$user_stmt->execute([$user_id]);
$user_address = $user_stmt->fetchColumn();

// احتساب المجموع الكلي
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// معالجة البيانات عند إرسال الفورم (تأكيد الطلب)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $shipping_address = !empty($_POST['shipping_address']) ? trim($_POST['shipping_address']) : ($user_address ? $user_address : 'عنوان غير محدد بعد');
    $payment_method = $_POST['payment_method'];
    $receipt_filename = null;

    // إذا اختار المستخدم الدفع عبر USDT، نقوم برفع صورة الإيصال
    if ($payment_method === 'usdt') {
        if (isset($_FILES['payment_receipt']) && $_FILES['payment_receipt']['error'] === 0) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $file_info = pathinfo($_FILES['payment_receipt']['name']);
            $extension = strtolower($file_info['extension']);

            if (in_array($extension, $allowed_extensions)) {
                if (!is_dir('uploads/receipts/')) {
                    mkdir('uploads/receipts/', 0777, true);
                }
                
                $receipt_filename = 'receipt_' . uniqid() . '.' . $extension;
                $target_path = 'uploads/receipts/' . $receipt_filename;
                
                move_uploaded_file($_FILES['payment_receipt']['tmp_name'], $target_path);
            } else {
                echo "<script>alert('امتداد ملف الإيصال غير مسموح به! الرجاء رفع صورة فقط.');</script>";
                $payment_method = null;
            }
        } else {
            echo "<script>alert('الرجاء رفع صورة إيصال تحويل الـ USDT لإتمام الطلب.');</script>";
            $payment_method = null;
        }
    }

    if ($payment_method !== null) {
        try {
            $pdo->beginTransaction();
            
            // 1. إدراج الطلب الرئيسي في جدول orders
            $order_stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status, payment_method, payment_receipt) VALUES (?, ?, ?, 'pending', ?, ?)");
            $order_stmt->execute([$user_id, $total_amount, $shipping_address, $payment_method, $receipt_filename]);
            $order_id = $pdo->lastInsertId();
            
            // 2. إدراج التفاصيل في جدول order_items وتحديث مخزون الملابس
            $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            $update_stock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
            
            foreach ($cart_items as $item) {
                $item_stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                $update_stock->execute([$item['quantity'], $item['product_id']]);
            }
            
            // 3. مسح السلة
            $clear_cart = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $clear_cart->execute([$user_id]);
            
            $pdo->commit();
            
            echo "<script>alert('تم تسجيل طلب المشتريات بنجاح من متجر Al-Aneeq Fashion!'); window.location.href='profile.php';</script>";
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("حدث خطأ طارئ أثناء معالجة عملية الشراء: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إتمام عملية الشراء والدفع - Al-Aneeq Fashion</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .checkout-container { max-width: 600px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: right; direction: rtl; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .payment-methods { display: flex; gap: 20px; margin-top: 10px; }
        .payment-methods label { background: #f8f9fa; border: 2px solid #ddd; padding: 15px; border-radius: 6px; width: 50%; text-align: center; cursor: pointer; display: block; transition: 0.3s; }
        .payment-methods input[type="radio"] { display: none; }
        .payment-methods input[type="radio"]:checked + label { border-color: #2c3e50; background: #eef2f7; font-weight: bold; }
        .receipt-upload { background: #fff3cd; border: 1px solid #ffeeba; padding: 20px; border-radius: 6px; margin-top: 20px; display: none; }
        .btn-order { background: #27a745; color: #fff; border: none; width: 100%; padding: 12px; font-size: 1.1rem; border-radius: 4px; cursor: pointer; font-weight: bold; margin-top: 20px; }
        .btn-order:hover { background: #218838; }
    </style>
</head>
<body>

<div class="checkout-container">
    <h2>🔒 إتمام الشراء وتأكيد الفاتورة</h2>
    <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">
    
    <p>إجمالي الفاتورة المطلوب دفعها: <strong style="color: #27a745; font-size: 1.3rem;"><?php echo $total_amount; ?> ₪</strong></p>
    
    <form method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label for="shipping_address">📍 عنوان التوصيل الحالي للطلب:</label>
            <input type="text" name="shipping_address" id="shipping_address" class="form-control" value="<?php echo htmlspecialchars($user_address ? $user_address : '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="ادخل عنوان التوصيل بالتفصيل هنا" required>
        </div>

        <div class="form-group">
            <label>💳 اختر طريقة الدفع المفضلة لديك:</label>
            <div class="payment-methods">
                <input type="radio" name="payment_method" value="cod" id="cod" checked onclick="toggleReceipt(false)">
                <label for="cod">💵 الدفع عند التوصيل</label>

                <input type="radio" name="payment_method" value="usdt" id="usdt" onclick="toggleReceipt(true)">
                <label for="usdt">🪙 دفع رقمي USDT</label>
            </div>
        </div>

        <div id="receipt_section" class="receipt-upload">
            <h4 style="margin-top: 0; color: #856404;">خطوات الدفع عبر العملة الرقمية USDT:</h4>
            <p style="font-size: 0.9rem; line-height: 1.5; color: #665214;">
                يرجى تحويل القيمة المقابلة بالفاتورة إلى عنوان محفظتنا على شبكة <strong>TRC-20</strong> التالي:<br>
                <code style="background: #fff; padding: 4px 8px; border-radius: 4px; display: block; margin-top: 5px; font-size: 1rem; border: 1px solid #e1d3a8; word-break: break-all;">TYxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</code>
            </p>
            <label style="margin-top: 15px;">📁 قم برفع لقطة شاشة (صورة الإيصال المتضمنة هاش العملية):</label>
            <input type="file" name="payment_receipt" accept="image/*" style="margin-top: 5px;">
        </div>

        <button type="submit" name="place_order" class="btn-order">تأكيد إتمام الطلب الآن ←</button>
    </form>
</div>

<script>
function toggleReceipt(show) {
    var section = document.getElementById('receipt_section');
    if(show) {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}
</script>

</body>
</html>