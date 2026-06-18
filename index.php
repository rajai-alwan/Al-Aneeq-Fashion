<?php 
include_once 'header.php'; 

// معالجة كود الإضافة السريعة للسلة إذا تم الضغط عليه
if (isset($_POST['add_to_cart_quick'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    $p_id = intval($_POST['product_id']);
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, 1) 
                           ON DUPLICATE KEY UPDATE quantity = quantity + 1");
    $stmt->execute([$user_id, $p_id]);
    echo "<script>alert('تم إضافة قطعة الملابس إلى سلة المشتريات بنجاح!');</script>";
}
?>

<!-- قسم البطل الترحيبي لعالم الأزياء -->
<div class="hero">
    <h2>أناقتكِ وأناقتكَ.. اختيارنا</h2>
    <p>اكتشفوا تشكيلة أزياء صيف وخريف 2026 المميزة والفاخرة بأسعار تنافسية</p>
    
    <!-- شريط البحث عن منتج -->
    <form action="products.php" method="GET" style="margin-bottom: 20px; display:flex; gap:10px;">
        <input type="text" name="search" placeholder="ابحث عن فستان، قميص، بدلة..." style="padding:10px 20px; width:300px; border:none; border-radius:4px;">
        <button type="submit" class="btn" style="padding:10px 20px;">بحث</button>
    </form>
    
    <a href="products.php" class="btn">تسوقي وتسوّق الآن</a>
</div>

<div class="container">
    <h3 class="section-title">أحدث صيحات الموضة والأزياء</h3>
    <div class="product-grid">
        <?php
        // جلب 4 منتجات لعرضها بالرئيسية بحسب الشروط
        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");
        while($product = $stmt->fetch()):
        ?>
            <div class="product-card">
                <img src="<?php echo sanitize($product['image_url']); ?>" alt="<?php echo sanitize($product['name']); ?>" class="product-image">
                <div class="product-info">
                    <div class="product-name"><?php echo sanitize($product['name']); ?></div>
                    <div class="product-price"><?php echo $product['price']; ?> ₪</div>
                    
                    <div style="margin-top:auto; display:flex; flex-direction:column; gap:10px;">
                        <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn" style="background:#efefef; color:#333;">عرض التفاصيل</a>
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <button type="submit" name="add_to_cart_quick" class="btn" style="width:100%;">إضافة للسلة 🛒</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include_once 'footer.php'; ?>