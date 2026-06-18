<?php 
include_once 'header.php'; 

if (isset($_POST['submit_msg'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    if(!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $success_msg = "شكراً لتواصلكِ معنا، تم إرسال رسالتكِ بنجاح لفريق دعم الأناقة Al-Aneeq وسيرد عليكِ المطور قريباً.";
    } else {
        $error_msg = "يرجى تعبئة كافة الحقول الإجبارية أولاً!";
    }
}
?>

<div class="container" style="max-width: 900px; margin-top: 40px;">
    <h2 class="section-title">يسعدنا تواصلكِ معنا دائماً</h2>
    
    <div style="display: flex; gap: 40px; flex-wrap: wrap; margin-top: 30px;">
        <!-- نموذج الإرسال المعالج عبر الداتا بيز -->
        <div style="flex: 2; min-width: 300px; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 20px;">أرسلي لنا رسالة مباشرة</h3>
            
            <?php if(isset($success_msg)) echo "<p style='color:var(--success); background:#e6f4ea; padding:10px; border-radius:4px; margin-bottom:15px;'>$success_msg</p>"; ?>
            <?php if(isset($error_msg)) echo "<p style='color:var(--danger); background:#fdeaea; padding:10px; border-radius:4px; margin-bottom:15px;'>$error_msg</p>"; ?>

            <form method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <label style="display:block; margin-bottom:5px;">الاسم الكامل *</label>
                    <input type="text" name="name" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px;">البريد الإلكتروني *</label>
                    <input type="email" name="email" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px;">موضوع الرسالة</label>
                    <select name="subject" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                        <option value="استفسار عام">استفسار عام عن الموديلات</option>
                        <option value="شكوى أو اقتراح">تقديم شكوى أو اقتراح تطويري</option>
                        <option value="مشكلة في الطلب">مواجهة مشكلة أثناء عملية الشراء</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px;">نص الرسالة التفصيلي *</label>
                    <textarea name="message" rows="5" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;"></textarea>
                </div>
                <button type="submit" name="submit_msg" class="btn">إرسال الرسالة الآن</button>
            </form>
        </div>
        
        <!-- البيانات الثابتة للموقع -->
        <div style="flex: 1; min-width: 250px; display: flex; flex-direction: column; gap: 20px;">
            <div style="background: var(--primary-color); color:#fff; padding: 25px; border-radius: 8px;">
                <h4 style="color: var(--accent-color); margin-bottom: 15px;">معلومات التواصل الثابتة</h4>
                <p style="margin-bottom: 10px;">📍 <b>الموقع الرسمي:</b> غزة - شارع عمر المختار، فلسطين</p>
                <p style="margin-bottom: 10px;">📞 <b>هاتف المطور:</b> +970 5X XXX XXXX</p>
                <p style="margin-bottom: 10px;">✉️ <b>البريد الإلكتروني:</b> support@alaneeqfashion.com</p>
                <p>⏰ <b>ساعات العمل:</b> السبت - الخميس (9 صباحاً - 9 مساءً)</p>
            </div>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>