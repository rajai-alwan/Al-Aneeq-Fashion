<?php
// تفاصيل الطلب من قاعدة البيانات أو السلة
$order_id = 123; // رقم الطلب في نظامك
$amount = 50.00; // المبلغ بالدولار الأمريكي
$currency = "usd";
$pay_currency = "usdttrc20"; // تحديد USDT على شبكة TRC20

$api_key = "YOUR_NOWPAYMENTS_API_KEY"; // ضع مفتاح الـ API الخاص بك هنا

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.nowpayments.io/v1/payment");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, TRUE);

$fields = [
    "price_amount" => $amount,
    "price_currency" => $currency,
    "pay_currency" => $pay_currency,
    "order_id" => $order_id,
    "order_description" => "الدفع للطلب رقم #" . $order_id,
    "ipn_callback_url" => "https://yourdomain.com/crypto_webhook.php", // رابط استقبال النتيجة
    "success_url" => "https://yourdomain.com/success.php",
    "cancel_url" => "https://yourdomain.com/cancel.php"
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "x-api-key: " . $api_key
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['invoice_url'])) {
    // حفظ معرّف الدفع (payment_id) في قاعدة بياناتك لربطه بالطلب
    $payment_id = $result['payment_id'];
    // تحديث جدول الطلبات بـ payment_id وحالة "انتظار الدفع"
    
    // إعادة توجيه المستخدم لصفحة الدفع الخاصة بالبوابة
    header("Location: " . $result['invoice_url']);
    exit();
} else {
    echo "حدث خطأ أثناء إنشاء الفاتورة: " . ($result['message'] ?? 'خطأ غير معروف');
}
?>