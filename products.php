<?php 
include_once 'header.php'; 

// إعداد متغيرات الفلترة والبحث
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// بناء الاستعلام الديناميكي آمن برمجياً
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search !== '') {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_id > 0) {
    $query .= " AND category_id = ?";
    $params[] = $category_id;
}

if ($sort === 'price_low') {
    $query .= " ORDER BY price ASC";
} elseif ($sort === 'price_high') {
    $query .= " ORDER BY price DESC";
} else {
    $query .= " ORDER BY created_at DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// جلب التصنيفات لقائمة الفلترة
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<div class="container">
    <h2 class="section-title">تشكيلة الملابس المتاحة</h2>
    
    <!-- شريط الفلترة والترتيب -->
    <div style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <form method="GET" action="products.php" style="display: flex; gap: 15px; flex-wrap: wrap; width: 100%;">
            <input type="text" name="search" value="<?php echo sanitize($search); ?>" placeholder="ابحث عن ملابس..." style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; flex: 1; min-width: 200px;">
            
            <select name="category" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="0">جميع الأقسام</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo $cat['category_id']; ?>" <?php if($category_id == $cat['category_id']) echo 'selected'; ?>>
                        <?php echo sanitize($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="sort" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">ترتيب بحسب (تلقائي)</option>
                <option value="price_low" <?php if($sort === 'price_low') echo 'selected'; ?>>السعر: من الأقل إلى الأعلى</option>
                <option value="price_high" <?php if($sort === 'price_high') echo 'selected'; ?>>السعر: من الأعلى إلى الأقل</option>
            </select>
            
            <button type="submit" class="btn" style="padding: 10px 25px;">تطبيق التصفية</button>
        </form>
    </div>

    <!-- شبكة عرض قطع الملابس -->
    <?php if(empty($products)): ?>
        <p style="text-align: center; font-size: 1.2rem; color: #777; margin: 40px 0;">لم يتم العثور على قطع ملابس تطابق خيارات البحث الحالية.</p>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo sanitize($product['image_url']); ?>" alt="<?php echo sanitize($product['name']); ?>" class="product-image">
                    <div class="product-info">
                        <div class="product-name"><?php echo sanitize($product['name']); ?></div>
                        <div class="product-price"><?php echo $product['price']; ?> ₪</div>
                        <div style="margin-top: auto; display: flex; flex-direction: column; gap: 8px;">
                            <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn" style="background:#efefef; color:#333;">عرض قطعة الملابس</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>