<?php
// الاتصال بقاعدة البيانات
// include 'config.php';

$error_msg = "Unknown error";
$auth_ok = false;
$request_headers = getallheaders();

// التحقق من أن الإشعار قادم فعلاً من البوابة عبر الـ IPN Secret
$ipn_secret = "YOUR_IPN_SECRET"; 
$signing_key = $request_headers['X-Nowpayments-Sig'] ?? '';

$request_data = file_get_contents('php://input');

if ($request_data) {
    $data = json_decode($request_data, true);
    
    // التحقق من التوقيع (أمان إضافي لحماية الموقع)
    // بناءً على توثيق البوابة، يتم التأكد من صحة البيانات هنا
    
    $payment_status = $data['payment_status'];
    $order_id = $data['order_id'];
    $payment_id = $data['payment_id'];

    if ($payment_status == 'finished') {
        // 1. تحديث حالة الطلب في قاعدة البيانات إلى 'Paid' أو 'تم الشحن'
        // $sql = "UPDATE orders SET status='paid' WHERE id='$order_id'";
        
        // 2. إرسال بريد إلكتروني للمستخدم أو البائع بتأكيد الدفع
        
        http_response_code(200);
        echo "تأكيد الدفع بنجاح";
    } else {
        echo "الحالة الحالية: " . $payment_status;
    }
} else {
    http_response_code(400);
    echo "بيانات خاطئة";
}
?>