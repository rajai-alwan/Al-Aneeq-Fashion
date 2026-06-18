<?php 
include_once 'header.php'; 

if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // فحص كلمة المرور المشفرة بـ password_hash
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "عذراً، البريد الإلكتروني أو كلمة المرور غير صحيحة!";
    }
}
?>

<div class="container" style="max-width: 450px; margin: 60px auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
    <h2 style="text-align: center; margin-bottom: 20px;">تسجيل الدخول للمتجر</h2>
    
    <?php if(isset($error)): ?>
        <p style="color: var(--danger); background: #fdeaea; padding: 10px; border-radius: 4px; text-align: center; margin-bottom: 15px;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <form method="POST" style="display: flex; flex-direction: column; gap: 15px;">
        <div>
            <label style="display:block; margin-bottom:5px;">البريد الإلكتروني:</label>
            <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius:4px;">
        </div>
        <div>
            <label style="display:block; margin-bottom:5px;">كلمة المرور:</label>
            <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius:4px;">
        </div>
        <button type="submit" class="btn" style="margin-top: 10px;">دخول آمن</button>
    </form>
    <p style="text-align: center; margin-top: 15px; font-size: 0.9rem;">ليس لديك حساب؟ <a href="register.php" style="color: var(--accent-color);">سجل معنا الآن</a></p>
</div>

<?php include_once 'footer.php'; ?>