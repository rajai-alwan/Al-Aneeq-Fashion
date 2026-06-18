<?php
include_once 'config.php';

// جدار حماية لمنع غير المسؤولين
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// معالجة إضافة قطعة ملابس جديدة مع رفع الصورة
if (isset($_POST['add_product'])) {
    $name = sanitize($_POST['name']);
    $desc = sanitize($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock_quantity']);
    $cat_id = intval($_POST['category_id']);
    
    // إعدادات رفع الملف الافتراضية
    $image_url = 'images/default-cloth.jpg'; // صورة افتراضية في حال عدم رفع ملف
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $file_name = $_FILES['product_image']['name'];
        $file_size = $_FILES['product_image']['size'];
        $file_tmp  = $_FILES['product_image']['tmp_name'];
        
        // استخراج امتداد الملف
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // 1. التحقق من الامتداد المسموح به لحماية السيرفر
        if (in_array($file_ext, $allowed_extensions)) {
            // 2. التحقق من حجم الملف (مثلاً: أقصى حجم 3 ميجابايت)
            if ($file_size <= 3 * 1024 * 1024) {
                // إنشاء مجلد images إذا لم يكن موجوداً
                if (!file_exists('images')) {
                    mkdir('images', 0777, true);
                }
                
                // توليد اسم فريد للملف لمنع التكرار
                $new_file_name = uniqid('product_', true) . '.' . $file_ext;
                $target_path = 'images/' . $new_file_name;
                
                // نقل الملف من المجلد المؤقت إلى مجلد المنتجات
                if (move_uploaded_file($file_tmp, $target_path)) {
                    $image_url = $target_path;
                } else {
                    $error_msg = "حدث خطأ أثناء نقل الملف إلى مجلد الصور.";
                }
            } else {
                $error_msg = "حجم الصورة كبير جداً! الحد الأقصى هو 3 ميجابايت.";
            }
        } else {
            $error_msg = "صيغة الملف غير مدعومة! يرجى رفع صور بامتداد JPG, JPEG, PNG, أو WEBP فقط.";
        }
    }

    // إذا لم تظهر أخطاء في الرفع، يتم الحفظ في قاعدة البيانات
    if (!isset($error_msg)) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock_quantity, image_url, category_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $price, $stock, $image_url, $cat_id]);
        $success_msg = "تمت إضافة قطعة الملابس ورفع الصورة بنجاح إلى معروضات المتجر.";
    }
}

// معالجة الحذف المباشر لقطعة ملابس
if (isset($_GET['delete'])) {
    $p_id = intval($_GET['delete']);
    
    // جلب مسار الصورة لحذفها من السيرفر أيضاً لتوفير المساحة
    $img_stmt = $pdo->prepare("SELECT image_url FROM products WHERE product_id = ?");
    $img_stmt->execute([$p_id]);
    $old_image = $img_stmt->fetchColumn();
    
    if ($old_image && $old_image != 'images/default-cloth.jpg' && file_exists($old_image)) {
        unlink($old_image); // حذف الصورة الفيزيائية من مجلد images
    }
    
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$p_id]);
    header("Location: admin_products.php");
    exit();
}

// جلب المنتجات والأقسام للعرض باللوحة الإدارية
$products = $pdo->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.product_id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة الملابس - Al-Aneeq Fashion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>لوحة الإدارة - إدارة معروضات الملابس</h1>
    <a href="admin_dashboard.php" style="color:#fff; text-decoration:none;">&larr; العودة للوحة الإحصائيات العامة</a>
</header>

<div class="container" style="display:flex; gap:30px; flex-wrap:wrap;">
    <!-- نموذج الإضافة المتطور يدعم رفع الملفات وصور الأزياء المباشرة -->
    <div style="flex:1; min-width:300px; background:#fff; padding:25px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.05); height: fit-content;">
        <h3>إضافة قطعة ملابس جديدة</h3>
        
        <?php if(isset($success_msg)) echo "<p style='color:var(--success); margin:10px 0; font-weight:bold;'>$success_msg</p>"; ?>
        <?php if(isset($error_msg)) echo "<p style='color:var(--danger); margin:10px 0; font-weight:bold;'>$error_msg</p>"; ?>
        
        <!-- انتبهي يسرى لخاصية enctype المضافة هنا، هي أساسية لرفع الصور -->
        <form method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:12px; margin-top:15px;">
            <div>
                <label style="display:block; margin-bottom:3px;">اسم القطعة (فستان، طقم، بدلة...) *</label>
                <input type="text" name="name" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
            <div>
                <label style="display:block; margin-bottom:3px;">القسم الفرعي للأزياء *</label>
                <select name="category_id" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['category_id']; ?>"><?php echo sanitize($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display:block; margin-bottom:3px;">السعر الإجباري (بالشيكل ₪) *</label>
                <input type="number" step="0.01" name="price" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
            <div>
                <label style="display:block; margin-bottom:3px;">الكمية المتوفرة بالمخازن كبداية *</label>
                <input type="number" name="stock_quantity" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
            
            <!-- حقل رفع الملف المباشر بدلاً من حقل النص والرابط القديم -->
            <div>
                <label style="display:block; margin-bottom:3px;">اختيار صورة قطعة الملابس من جهازكِ *</label>
                <input type="file" name="product_image" accept="image/*" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; background: #fafafa;">
                <small style="color: #777; display:block; margin-top:2px;">الصيغ المدعومة: JPG, PNG, WEBP (بحد أقصى 3 ميجا)</small>
            </div>
            
            <div>
                <label style="display:block; margin-bottom:3px;">الوصف التفصيلي الدقيق للموديل والخامة</label>
                <textarea name="description" rows="3" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px Pap;"></textarea>
            </div>
            <button type="submit" name="add_product" class="btn" style="width:100%;">تأكيد إدراج قطعة الملابس للنظام</button>
        </form>
    </div>

    <!-- جدول عرض الملابس الحالي والخيارات عليه -->
    <div style="flex:2; min-width:300px;">
        <h3>قائمة جرد الملابس الحالية بالداتا بيز</h3>
        <table>
            <thead>
                <tr>
                    <th>المعاينه</th>
                    <th>اسم قطعة الملابس</th>
                    <th>القسم المربوط</th>
                    <th>السعر</th>
                    <th>المخزون المتاح</th>
                    <th>إجراءات الحذف</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                    <tr>
                        <!-- عرض مصغر للصورة المرفوعة محلياً داخل الجدول للتأكد من نجاح العملية -->
                        <td><img src="<?php echo sanitize($p['image_url']); ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px; border: 1px solid #ddd;"></td>
                        <td><b><?php echo sanitize($p['name']); ?></b></td>
                        <td><?php echo sanitize($p['cat_name']); ?></td>
                        <td><span style="color:var(--accent-color); font-weight:bold;"><?php echo $p['price']; ?> ₪</span></td>
                        <td><?php echo $p['stock_quantity']; ?> قطعة</td>
                        <td>
                            <a href="admin_products.php?delete=<?php echo $p['product_id']; ?>" onclick="return confirm('هل أنتِ متأكدة تماماً يسرى من مسح هذا المنتج وصورته نهائياً من المتجر؟');" style="color:var(--danger); text-decoration:none; font-weight:bold;">حذف نهائي</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>