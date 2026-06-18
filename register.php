<?php 
include_once 'header.php'; 

if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    // التحقق الفوري من تطابق الكلمات والبريد المكرر لمنع الأخطاء البرمجية
    if($password !== $confirm_password) {
        $error = "عذراً، كلمات المرور المدخلة غير متطابقة تماماً!";
    } else {
        $check_email = $pdo->prepare("SELECT count(*) FROM users WHERE email = ?");
        $check_email->execute([$email]);
        if($check_email->fetchColumn() > 0) {
            $error = "البريد الإلكتروني المدخل مسجل مسبقاً في نظام متجر الأزياء!";
        } else {
            // تشفير آمن لكلمة المرور تماشياً مع متطلبات الحماية البرمجية المعتمدة بالمساق
            $hashed_pass = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, 'customer')");
            $stmt->execute([$full_name, $email, $hashed_pass, $phone, $address]);
            
            echo "<script>alert('تم إنشاء حساب الأناقة الخاص بكِ بنجاح! يمكنكِ الآن تسجيل الدخول.'); window.location.href='login.php';</script>";
            exit();
        }
    }
}
?>

<div class="container" style="max-width: 550px; margin: 40px auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
    <h2 style="text-align: center; margin-bottom: 20px;">إنشاء حساب زبون جديد</h2>
    
    <?php if(isset($error)) echo "<p style='color:var(--danger); background:#fdeaea; padding:10px; border-radius:4px; text-align:center; margin-bottom:15px;'>$error</p>"; ?>
    
    <form method="POST" style="display: flex; flex-direction: column; gap: 15px;">
        <div>
            <label style="display:block; margin-bottom:5px;">الاسم الكامل بالكامل *</label>
            <input type="text" name="full_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius:4px;">
        </div>
        <div>
            <label style="display:block; margin-bottom:5px;">البريد الإلكتروني *</label>
            <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius:4px;">
        </div>
        <div>
            <label style="display:block; margin-bottom:5px;">رقم الهاتف</label>
            <input type="text" name="phone" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius:4px;">
        </div>
        <div>
            <label style="display:block; margin-bottom:5px;">العنوان التفصيلي الحالي (للشحن)</label>
            <textarea name="address" rows="2" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius:4px;"></textarea>
        </div>
        <div>
            <label style="display:block; margin-bottom:5px;">كلمة المرور الآمنة *</label>
            <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius:4px;">
        </div>
        <div>
            <label style="display:block; margin-bottom:5px;">تأكيد كلمة المرور المدخلة *</label>
            <input type="password" name="confirm_password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius:4px;">
        </div>
        <button type="submit" class="btn" style="margin-top: 10px;">تأكيد التسجيل والانضمام لعالم الأناقة</button>
    </form>
</div>

<?php include_once 'footer.php'; ?>