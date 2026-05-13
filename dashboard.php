<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: views/auth/login.php");
    exit();
}

require_once "config/db.php";

$db = new Database();
$conn = $db->connect();

/* =========================
   📊 STATISTICS
========================= */
$carsCount = $conn->query("
SELECT COUNT(*) as total FROM cars
")->fetch_assoc()['total'] ?? 0;

$usersCount = $conn->query("
SELECT COUNT(*) as total FROM users
")->fetch_assoc()['total'] ?? 0;

/* =========================
   📸 IMAGES COUNT
========================= */
$imagesCount = 0;

$checkImagesTable = $conn->query("
SHOW TABLES LIKE 'car_images'
");

if ($checkImagesTable && $checkImagesTable->num_rows > 0) {

    $imagesCount = $conn->query("
    SELECT COUNT(*) as total FROM car_images
    ")->fetch_assoc()['total'] ?? 0;
}

/* =========================
   📄 PAGINATION
========================= */
$limit = 6;

$page = isset($_GET['page'])
? (int)$_GET['page']
: 1;

if($page < 1){
    $page = 1;
}

$offset = ($page - 1) * $limit;

$totalCars = $conn->query("
SELECT COUNT(*) as total FROM cars
")->fetch_assoc()['total'];

$totalPages = ceil($totalCars / $limit);

$cars = $conn->query("
SELECT * FROM cars
ORDER BY id DESC
LIMIT $limit OFFSET $offset
");

/* =========================
   📊 CHART DATA
========================= */
$chartLabels = [];
$chartPrices = [];

$chartQuery = $conn->query("
SELECT name, price FROM cars
ORDER BY id DESC LIMIT 5
");

while($chart = $chartQuery->fetch_assoc()){

    $chartLabels[] = $chart['name'];
    $chartPrices[] = $chart['price'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>لوحة التحكم</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
    background:#f4f6f9;
    font-family:Tahoma;
    transition:0.3s;
}

.dark-mode{
    background:#121212 !important;
    color:white !important;
}

.dark-mode .card{
    background:#1f1f1f;
    color:white;
}

.dark-mode .modal-content{
    background:#1f1f1f;
    color:white;
}

.dark-mode .form-control{
    background:#2c2c2c;
    color:white;
    border:none;
}

.dark-mode .pagination .page-link{
    background:#2c2c2c;
    color:white;
    border:none;
}

.navbar{
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

.card-box{
    border-radius:15px;
    padding:25px;
    color:white;
    box-shadow:0 5px 15px rgba(0,0,0,0.15);
    transition:0.3s;
}

.card-box:hover{
    transform:translateY(-5px);
}

.stat-number{
    font-size:30px;
    font-weight:bold;
}

.car-card{
    border:none;
    border-radius:15px;
    overflow:hidden;
    transition:0.3s;
}

.car-card:hover{
    transform:translateY(-5px);
}

.car-card img{
    height:220px;
    object-fit:cover;
}

.modal-content{
    border-radius:15px;
}

.gallery-img{
    width:80px;
    height:80px;
    object-fit:cover;
    border-radius:10px;
    margin:5px;
}

</style>

</head>

<body id="body">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark px-3">

<a class="navbar-brand fw-bold">
🚗 Car Dashboard
</a>

<div class="d-flex gap-2">

<button class="btn btn-secondary btn-sm"
onclick="toggleDarkMode()">
🌙 Dark
</button>

<button class="btn btn-success btn-sm"
onclick="new bootstrap.Modal(document.getElementById('addModal')).show()">
+ إضافة سيارة
</button>
<a href="views/purchases/index.php" class="btn btn-primary btn-sm">
🛒 طلبات الشراء
</a>

<a href="logout.php" class="btn btn-danger btn-sm">
خروج
</a>

</div>

</nav>

<div class="container mt-4">

<h3>
👋 أهلاً،
<?= htmlspecialchars($_SESSION['user']) ?>
</h3>

<!-- STATS -->
<div class="row mt-4">

<div class="col-md-4 mb-3">

<div class="card-box bg-primary text-center">

🚗 السيارات

<div class="stat-number">
<?= $carsCount ?>
</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card-box bg-success text-center">

📸 الصور

<div class="stat-number">
<?= $imagesCount ?>
</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card-box bg-dark text-center">

👤 المستخدمين

<div class="stat-number">
<?= $usersCount ?>
</div>

</div>

</div>

</div>

<!-- CHART -->
<div class="card shadow border-0 mt-4 p-4">

<h4 class="mb-3">
📊 أسعار آخر السيارات
</h4>

<canvas id="carsChart"></canvas>

</div>

<!-- SEARCH -->
<div class="row mt-5 mb-4">

<div class="col-md-6">

<input type="text"
id="searchInput"
class="form-control"
placeholder="🔍 ابحث عن سيارة..."
onkeyup="searchCars()">

</div>

</div>

<!-- CARS -->
<div class="row" id="carsContainer">

<?php while($car = $cars->fetch_assoc()): ?>

<div class="col-md-4 mb-4">

<div class="card shadow car-card">

<img src="<?= !empty($car['image']) && file_exists('uploads/' . $car['image'])
? 'uploads/' . htmlspecialchars($car['image'])
: 'https://via.placeholder.com/400x250?text=No+Image' ?>">

<div class="card-body">

<h5 class="fw-bold">
<?= htmlspecialchars($car['name']) ?>
</h5>

<p class="text-success fw-bold">
💰 <?= htmlspecialchars($car['price']) ?> $
</p>

<div class="d-flex gap-2 flex-wrap">

<button class="btn btn-info btn-sm"
onclick="showCar(<?= $car['id'] ?>)">
تفاصيل
</button>

<button class="btn btn-warning btn-sm"
onclick="openEdit(<?= $car['id'] ?>)">
تعديل
</button>
<button class="btn btn-success btn-sm"
onclick="buyCar(<?= $car['id'] ?>)">
شراء
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

</div>

<!-- PAGINATION -->
<div class="d-flex justify-content-center mt-4">

<nav>

<ul class="pagination">

<?php if($page > 1): ?>

<li class="page-item">

<a class="page-link"
href="?page=<?= $page - 1 ?>">
السابق
</a>

</li>

<?php endif; ?>

<?php for($i = 1; $i <= $totalPages; $i++): ?>

<li class="page-item <?= $i == $page ? 'active' : '' ?>">

<a class="page-link"
href="?page=<?= $i ?>">

<?= $i ?>

</a>

</li>

<?php endfor; ?>

<?php if($page < $totalPages): ?>

<li class="page-item">

<a class="page-link"
href="?page=<?= $page + 1 ?>">
التالي
</a>

</li>

<?php endif; ?>

</ul>

</nav>

</div>

</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal">

<div class="modal-dialog">

<div class="modal-content p-4">

<h4 class="mb-3">
➕ إضافة سيارة
</h4>

<input type="text"
id="add_name"
class="form-control mb-3"
placeholder="اسم السيارة">

<input type="number"
id="add_price"
class="form-control mb-3"
placeholder="السعر">

<input type="file"
id="add_image"
class="form-control mb-3">

<!-- <label class="mb-2">
📸 صور متعددة
</label>

<input type="file"
id="multi_images"
class="form-control mb-3"
multiple> -->

<button class="btn btn-success"
onclick="addCar()">
حفظ السيارة
</button>

</div>

</div>

</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal">

<div class="modal-dialog">

<div class="modal-content p-4">

<h4 class="mb-3">
✏️ تعديل السيارة
</h4>

<input type="hidden" id="edit_id">

<input type="text"
id="edit_name"
class="form-control mb-3">

<input type="number"
id="edit_price"
class="form-control mb-3">

<input type="file"
id="edit_image"
class="form-control mb-3">

<button class="btn btn-warning"
onclick="updateCar()">
حفظ التعديل
</button>

</div>

</div>

</div>

<!-- DETAILS MODAL -->
<div class="modal fade" id="detailsModal">

<div class="modal-dialog modal-lg">

<div class="modal-content p-4">

<h4 class="mb-3">
📄 تفاصيل السيارة
</h4>

<div id="carDetails"></div>

<div id="gallery" class="d-flex flex-wrap mt-3"></div>

</div>

</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/* =========================
   🌙 DARK MODE
========================= */
function toggleDarkMode(){

document.body.classList.toggle("dark-mode");

}

/* =========================
   📊 CHART
========================= */
const ctx = document.getElementById('carsChart');

new Chart(ctx, {

type: 'bar',

data: {

labels: <?= json_encode($chartLabels) ?>,

datasets: [{
label: 'أسعار السيارات',
data: <?= json_encode($chartPrices) ?>,
borderWidth: 1
}]

},

options: {
responsive: true
}

});

/* =========================
   🔍 SEARCH
========================= */
function searchCars() {

let value =
document.getElementById('searchInput').value;

fetch("search.php?search=" + value)

.then(res => res.text())

.then(data => {

document.getElementById('carsContainer').innerHTML = data;

});

}

/* =========================
   🗑 DELETE
========================= */
function deleteCar(id){

if(!confirm("هل تريد حذف السيارة؟")) return;

fetch("cars_api.php",{

method:"POST",

headers:{
"Content-Type":"application/x-www-form-urlencoded"
},

body:"action=delete&id=" + id

})

.then(res => res.text())

.then(data => {

if(data.trim() === "success"){

alert("تم الحذف");

location.reload();

}else{

alert(data);

}

});

}

/* =========================
   ➕ ADD
========================= */
function addCar(){

let formData = new FormData();

formData.append("action","add");

formData.append(
"name",
document.getElementById('add_name').value
);

formData.append(
"price",
document.getElementById('add_price').value
);

let image =
document.getElementById('add_image').files[0];

// if(image){
// formData.append("image",image);
// }

fetch("cars_api.php",{

method:"POST",

body:formData

})

.then(res => res.text())

.then(data => {

if(data.trim() === "success"){

alert("تمت الإضافة");

location.reload();

}else{

alert(data);

}

});

}

/* =========================
   ✏️ OPEN EDIT
========================= */
function openEdit(id){

fetch("cars_api.php?action=get&id=" + id)

.then(res => res.json())

.then(data => {

document.getElementById('edit_id').value =
data.id;

document.getElementById('edit_name').value =
data.name;

document.getElementById('edit_price').value =
data.price;

new bootstrap.Modal(
document.getElementById('editModal')
).show();

});

}

/* =========================
   💾 UPDATE
========================= */
function updateCar(){

let formData = new FormData();

formData.append("action","update");

formData.append(
"id",
document.getElementById('edit_id').value
);

formData.append(
"name",
document.getElementById('edit_name').value
);

formData.append(
"price",
document.getElementById('edit_price').value
);

let image =
document.getElementById('edit_image').files[0];

if(image){
formData.append("image",image);
}

fetch("cars_api.php",{

method:"POST",

body:formData

})

.then(res => res.text())

.then(data => {

if(data.trim() === "success"){

alert("تم التعديل");

location.reload();

}else{

alert(data);

}

});

}

/* =========================
   📄 DETAILS
========================= */
function showCar(id){

fetch("cars_api.php?action=get&id=" + id)

.then(res => res.json())

.then(car => {

document.getElementById("carDetails").innerHTML = `

<h3>${car.name}</h3>

<p class="text-success fw-bold">
💰 ${car.price} $
</p>

<img 
src="uploads/${car.image}"
style="
width:100%;
height:300px;
object-fit:cover;
border-radius:15px;
">

`;

new bootstrap.Modal(
document.getElementById('detailsModal')
).show();

});

}
function buyCar(id){

    let name = prompt("اكتب اسمك:");

    if(!name) return;

    let phone = prompt("رقم الهاتف:");

    let formData = new FormData();

    formData.append("action","buy");
    formData.append("car_id", id);
    formData.append("buyer_name", name);
    formData.append("phone", phone);

    fetch("cars_api.php",{
        method:"POST",
        body:formData
    })

    .then(res => res.text())
    .then(data => {

        if(data.trim() === "success"){
            alert("تم إرسال طلب الشراء بنجاح 🚗");
        }else{
            alert("حدث خطأ: " + data);
        }

    });

}

</script>

</body>
</html>