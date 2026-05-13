<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit();
}

require_once "../../config/db.php";

$db = new Database();
$conn = $db->connect();

$purchases = $conn->query("
    SELECT 
        purchases.*,
        cars.name AS car_name,
        cars.price,
        cars.image
    FROM purchases
    JOIN cars ON cars.id = purchases.car_id
    ORDER BY purchases.id DESC
");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>طلبات الشراء</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
    font-family:Tahoma;
}

.card img{
    height:150px;
    object-fit:cover;
}
</style>
</head>

<body>

<nav class="navbar navbar-dark bg-dark px-3">
    <a class="navbar-brand">🛒 طلبات الشراء</a>

    <a href="../../dashboard.php" class="btn btn-warning btn-sm">
        الرجوع للوحة التحكم
    </a>
</nav>

<div class="container mt-4">

<h3 class="mb-4">📋 جميع طلبات الشراء</h3>

<div class="row">

<?php if($purchases->num_rows > 0): ?>

<?php while($row = $purchases->fetch_assoc()): ?>

<div class="col-md-4 mb-3">

<div class="card shadow">

<img src="../../uploads/<?= $row['image'] ?>" class="card-img-top">

<div class="card-body">

<h5><?= $row['car_name'] ?></h5>

<p>💰 السعر: <?= $row['price'] ?> $</p>

<hr>

<p>👤 العميل: <?= htmlspecialchars($row['buyer_name']) ?></p>

<p>📞 الهاتف: <?= htmlspecialchars($row['phone']) ?></p>

<p>📅 التاريخ: <?= $row['created_at'] ?></p>

<!-- حذف الطلب -->
<form method="POST" action="delete.php">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <button class="btn btn-danger btn-sm w-100">
        حذف الطلب
    </button>
</form>

</div>

</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<p class="text-muted">لا توجد طلبات شراء حالياً</p>

<?php endif; ?>

</div>

</div>

</body>
</html>