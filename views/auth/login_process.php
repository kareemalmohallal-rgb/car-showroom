<?php
session_start();

require_once __DIR__ . "/../../config/db.php";

// 📌 إنشاء الاتصال الصحيح
$db = new Database();
$conn = $db->connect();

// 📌 حماية من الفراغ
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../login.php");
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    die("❌ الرجاء إدخال البيانات");
}

// 📌 جلب المستخدم
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {

        $_SESSION['user'] = $user['username'];

        header("Location: ../../dashboard.php");
        exit();

    } else {
        echo "❌ كلمة المرور غير صحيحة";
    }

} else {
    echo "❌ اسم المستخدم غير موجود";
}
?>