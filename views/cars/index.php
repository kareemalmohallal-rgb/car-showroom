<?php
session_start();

require_once "../../config/db.php";
require_once "../../models/Car.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit();
}

$carModel = new Car();

// 📌 Pagination
$limit = 6;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

// 📌 Filters
$search = $_GET['search'] ?? '';
$min = $_GET['min'] ?? '';
$max = $_GET['max'] ?? '';

// 📌 جلب البيانات من Model (أفضل من SQL هنا)
$cars = $carModel->getCarsWithFilters($search, $min, $max, $limit, $offset);
$total = $carModel->countCars($search, $min, $max);

$pages = ceil($total / $limit);
?>