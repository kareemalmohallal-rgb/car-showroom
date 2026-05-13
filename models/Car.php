<?php

require_once "config/db.php";

class Car {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // 📌 جلب كل السيارات
    public function getAllCars() {
        $sql = "SELECT * FROM cars ORDER BY id DESC";
        $result = $this->conn->query($sql);

        $cars = [];

        while ($row = $result->fetch_assoc()) {
            $cars[] = $row;
        }

        return $cars;
    }

    // 📌 جلب سيارة واحدة
    public function getCarById($id) {
        $sql = "SELECT * FROM cars WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // 📌 إضافة سيارة
    public function insertCar($data) {

        $sql = "INSERT INTO cars 
        (name, brand, model, price, description, image, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($sql);

        $image = isset($data['image']) ? $data['image'] : null;

        $stmt->bind_param(
            "sssiss",
            $data['name'],
            $data['brand'],
            $data['model'],
            $data['price'],
            $data['description'],
            $image
        );

        return $stmt->execute();
    }

    // 📌 تحديث سيارة
    public function updateCar($id, $data) {

        if (isset($data['image'])) {

            $sql = "UPDATE cars SET 
                name=?, 
                brand=?, 
                model=?, 
                price=?, 
                description=?, 
                image=? 
                WHERE id=?";

            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param(
                "sssissi",
                $data['name'],
                $data['brand'],
                $data['model'],
                $data['price'],
                $data['description'],
                $data['image'],
                $id
            );

        } else {

            $sql = "UPDATE cars SET 
                name=?, 
                brand=?, 
                model=?, 
                price=?, 
                description=? 
                WHERE id=?";

            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param(
                "sssssi",
                $data['name'],
                $data['brand'],
                $data['model'],
                $data['price'],
                $data['description'],
                $id
            );
        }

        return $stmt->execute();
    }

    // 📌 حذف سيارة
    public function deleteCar($id) {
        $sql = "DELETE FROM cars WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function getCarsWithFilters($search, $min, $max, $limit, $offset)
{
    $sql = "SELECT * FROM cars WHERE 1=1";

    if ($search) {
        $sql .= " AND (name LIKE '%$search%' OR brand LIKE '%$search%')";
    }

    if ($min) {
        $sql .= " AND price >= $min";
    }

    if ($max) {
        $sql .= " AND price <= $max";
    }

    $sql .= " LIMIT $limit OFFSET $offset";

    return $this->conn->query($sql);
}

public function countCars($search, $min, $max)
{
    $sql = "SELECT COUNT(*) as total FROM cars WHERE 1=1";

    if ($search) {
        $sql .= " AND (name LIKE '%$search%' OR brand LIKE '%$search%')";
    }

    if ($min) {
        $sql .= " AND price >= $min";
    }

    if ($max) {
        $sql .= " AND price <= $max";
    }

    return $this->conn->query($sql)->fetch_assoc()['total'];
}
}
