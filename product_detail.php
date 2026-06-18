<?php 
include_once 'header.php'; 

// التحقق الفوري من متغير الاتصال بقاعدة البيانات وتوافقه مع config.php
if (!isset($pdo) || $pdo === null) {
    if (isset($conn) && $conn !== null) {
        $pdo = $conn;
    } elseif (isset($db) && $db !== null) {
        $pdo = $db;
    } else {
        foreach (get_defined_vars() as $var) {
            if ($var instanceof PDO) {
                $pdo = $var;
                break;
            }
        }
    }
}

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='container' style='margin-top: 50px; text-align: center;'><p>قطعة الملابس هذه غير متوفرة حالياً.</p></div>";
    include_once 'footer.php';
    exit();
}

// معالجة نموذج إضافة الكمية للسلة
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    $qty = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];
    
    if($qty > $product['stock_quantity'] || $qty <= 0) {
        $error = "الكمية المطلوبة غير صالحة أو تتجاوز المخزن المتاح!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        $stmt->execute([$user_id, $product_id, $qty, $qty]);
        $success = "تمت إضافة الملابس إلى السلة بنجاح.";
    }
}
?>

<div class="container" style="display: flex; gap: 40px; flex-wrap: wrap; margin-top:50px; direction: rtl; text-align: right;">
    <div style="flex: 1; min-width: 300px;">
        <img src="<?php echo htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="" style="width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    </div>
    
    <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; justify-content: center;">
        <span style="color: var(--accent-color); font-weight: bold;"><?php echo htmlspecialchars($product['category_name'] ? $product['category_name'] : 'عام', ENT_QUOTES, 'UTF-8'); ?></span>
        <h2 style="font-size: 2.5rem; margin: 10px 0;"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
        <p style="font-size: 1.5rem; color: var(--accent-color); font-weight: bold; margin-bottom: 20px;"><?php echo $product['price']; ?> ₪</p>
        
        <p style="color: #666; line-height: 1.6; margin-bottom: 25px;"><?php echo nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')); ?></p>
        <p style="margin-bottom: 15px;">المخزن المتوفر: <b><?php echo $product['stock_quantity']; ?> قطع</b></p>
        
        <?php if(isset($error)) echo "<p style='color:var(--danger); font-weight:bold;'>$error</p>"; ?>
        <?php if(isset($success)) echo "<p style='color:var(--success); font-weight:bold;'>$success</p>"; ?>

        <?php if($product['stock_quantity'] > 0): ?>
            <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                <label for="quantity">الكمية:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" style="padding: 10px; width: 80px; border: 1px solid #ddd; text-align:center;">
                <button type="submit" name="add_to_cart" class="btn" style="padding: 10px 20px; background-color: #27a745; color: white; border: none; border-radius: 4px; cursor: pointer;">إضافة إلى حقيبة المشتريات 🛒</button>
            </form>
        <?php else: ?>
            <p style="color: var(--danger); font-weight: bold;">نفذت الكمية من مخازن Al-Aneeq!</p>
        <?php endif; ?>
        
        <a href="products.php" style="margin-top: 30px; color: #555; text-decoration: underline;">&larr; العودة لجميع الملابس</a>
    </div>
</div>

<?php include_once 'footer.php'; ?>