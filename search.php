<?php

require_once "config/db.php";

$db = new Database();
$conn = $db->connect();

$search = $_GET['search'] ?? '';

$sql = "
SELECT * FROM cars
WHERE name LIKE ?
ORDER BY id DESC
";

$stmt = $conn->prepare($sql);

$like = "%$search%";

$stmt->bind_param("s", $like);

$stmt->execute();

$result = $stmt->get_result();

while($car = $result->fetch_assoc()):
?>

<div class="col-md-4 mb-4">

<div class="card shadow car-card">

<img src="<?= !empty($car['image']) && file_exists('uploads/' . $car['image'])
? 'uploads/' . htmlspecialchars($car['image'])
: 'https://via.placeholder.com/400x250?text=No+Image' ?>"
class="card-img-top">

<div class="card-body">

<h5 class="fw-bold">
<?= htmlspecialchars($car['name']) ?>
</h5>

<p class="text-success fw-bold">
💰 <?= htmlspecialchars($car['price']) ?> $
</p>

<div class="d-flex gap-2">

<button class="btn btn-info btn-sm"
onclick="showCar(<?= $car['id'] ?>)">
تفاصيل
</button>

<button class="btn btn-warning btn-sm"
onclick="openEdit(<?= $car['id'] ?>)">
تعديل
</button>

<button class="btn btn-danger btn-sm"
onclick="deleteCar(<?= $car['id'] ?>)">
حذف
</button>

</div>

</div>

</div>

</div>

<?php endwhile; ?>