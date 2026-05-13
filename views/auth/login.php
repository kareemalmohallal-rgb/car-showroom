<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 0 10px #ccc;
            text-align: center;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
        }

        button {
            width: 100%;
            padding: 10px;
            background: green;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: darkgreen;
        }
    </style>

</head>
<body>

<div class="box">

    <h2>🔐 تسجيل دخول الأدمن</h2>
<form action="login_process.php" method="POST">

    <input type="text" name="username" placeholder="اسم المستخدم" required>

    <input type="password" name="password" placeholder="كلمة المرور" required>

    <button type="submit">دخول</button>

</form>

<br>

<a href="register.php">
    <button type="button">إنشاء حساب جديد</button>
</a>

</div>

</body>
</html>