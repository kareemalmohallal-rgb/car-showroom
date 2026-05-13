<?php
session_start();

require_once __DIR__ . "/../../config/db.php";

$db = new Database();
$conn = $db->connect();

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            header("Location: login.php?success=1");
            exit();
        } else {
            $error = "❌ اسم المستخدم موجود أو حدث خطأ";
        }

    } else {
        $error = "❌ الرجاء تعبئة جميع الحقول";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء حساب</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: Arial;
        }

        .box {
            max-width: 400px;
            margin: auto;
            margin-top: 80px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<div class="box">

    <h2>🆕 إنشاء حساب جديد</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="text" name="username" class="form-control mb-3" placeholder="اسم المستخدم" required>

        <input type="password" name="password" class="form-control mb-3" placeholder="كلمة المرور" required>

        <button type="submit" class="btn btn-success w-100">
            إنشاء الحساب
        </button>

    </form>

    <br>

    <a href="login.php" class="btn btn-link w-100">
        ← رجوع لتسجيل الدخول
    </a>

</div>

</body>
</html>