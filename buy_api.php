<?php
session_start();
require_once "config/db.php";

$db = new Database();
$conn = $db->connect();

/* =========================
   🔐 لازم تسجيل دخول
========================= */
if (!isset($_SESSION['user'])) {
    echo "login_required";
    exit();
}

/* =========================
   ➕ إنشاء طلب شراء
========================= */
if (isset($_POST['action']) && $_POST['action'] == 'buy') {

    $car_id     = intval($_POST['car_id']);
    $buyer_name = $_SESSION['user'];
    $phone      = trim($_POST['phone']);

    $stmt = $conn->prepare("
        INSERT INTO car_orders (car_id, buyer_name, phone)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("iss", $car_id, $buyer_name, $phone);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    exit();
}

/* =========================
   📄 جلب الطلبات (Dashboard)
========================= */
if (isset($_GET['action']) && $_GET['action'] == 'list') {

    $sql = "
        SELECT o.*, c.name AS car_name, c.price
        FROM car_orders o
        JOIN cars c ON c.id = o.car_id
        ORDER BY o.id DESC
    ";

    $result = $conn->query($sql);

    $orders = [];

    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    echo json_encode($orders);
    exit();
}
?>