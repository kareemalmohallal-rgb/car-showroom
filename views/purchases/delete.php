<?php
session_start();

require_once "../../config/db.php";

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM purchases WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "خطأ في الحذف";
    }
}
?>