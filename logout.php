<?php
include_once 'config.php';

// تفريغ مصفوفة الجلسة تماماً
$_SESSION = array();

// إذا كان هناك كوكي للجلسة (Session Cookie)، قم بحذفه من متصفح الزبون لزيادة الأمان
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// تدمير الجلسة نهائياً من السيرفر
if (session_id() != "") {
    session_destroy();
}

// إعادة توجيه المستخدم أو الأدمن فوراً إلى الصفحة الرئيسية للمتجر
header("Location: index.php");
exit();
?>