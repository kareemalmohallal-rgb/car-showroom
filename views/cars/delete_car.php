<?php
require_once "../../config/db.php";

$db = new Database();
$conn = $db->connect();

if (isset($_POST['id'])) {

    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error_sql";
    }

} else {
    echo "no_id";
}
?>