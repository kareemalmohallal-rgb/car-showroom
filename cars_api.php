<?php

require_once "config/db.php";

$db = new Database();
$conn = $db->connect();

/* =========================
   ➕ ADD CAR
========================= */
if (isset($_POST['action']) && $_POST['action'] == 'add') {

    $name  = trim($_POST['name']);
    $price = trim($_POST['price']);

    // صورة افتراضية
    $imageName = "default.png";

    // رفع الصورة
    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {

        $imageName = time() . "_" . basename($_FILES['image']['name']);

        $targetPath = __DIR__ . "/uploads/" . $imageName;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $targetPath
        );
    }

    $stmt = $conn->prepare("
        INSERT INTO cars(name, price, image)
        VALUES(?, ?, ?)
    ");

    $stmt->bind_param(
        "sss",
        $name,
        $price,
        $imageName
    );

    if ($stmt->execute()) {

        echo "success";

    } else {

        echo "Database Error: " . $stmt->error;

    }

    exit();
}

/* =========================
   ✏️ UPDATE CAR
========================= */
if (isset($_POST['action']) && $_POST['action'] == 'update') {

    $id    = intval($_POST['id']);
    $name  = trim($_POST['name']);
    $price = trim($_POST['price']);

    // تحقق من السيارة
    $check = $conn->query("
        SELECT * FROM cars 
        WHERE id = $id
    ");

    if ($check->num_rows == 0) {

        echo "السيارة غير موجودة";
        exit();
    }

    $oldCar = $check->fetch_assoc();

    /* =========================
       📸 إذا تم رفع صورة جديدة
    ========================= */
    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {

        $imageName = time() . "_" . basename($_FILES['image']['name']);

        $targetPath = __DIR__ . "/uploads/" . $imageName;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $targetPath
        );

        // حذف الصورة القديمة
        if (
            !empty($oldCar['image']) &&
            file_exists(__DIR__ . "/uploads/" . $oldCar['image'])
        ) {

            unlink(__DIR__ . "/uploads/" . $oldCar['image']);
        }

        $stmt = $conn->prepare("
            UPDATE cars
            SET name=?, price=?, image=?
            WHERE id=?
        ");

        $stmt->bind_param(
            "sssi",
            $name,
            $price,
            $imageName,
            $id
        );

    } else {

        /* =========================
           ✏️ تحديث بدون صورة
        ========================= */
        $stmt = $conn->prepare("
            UPDATE cars
            SET name=?, price=?
            WHERE id=?
        ");

        $stmt->bind_param(
            "ssi",
            $name,
            $price,
            $id
        );
    }

    if ($stmt->execute()) {

        echo "success";

    } else {

        echo "Update Error: " . $stmt->error;
    }

    exit();
}

/* =========================
   📄 GET SINGLE CAR
========================= */
if (isset($_GET['action']) && $_GET['action'] == 'get') {

    $id = intval($_GET['id']);

    $stmt = $conn->prepare("
        SELECT * FROM cars
        WHERE id=?
    ");

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $car = $result->fetch_assoc();

        echo json_encode($car);

    } else {

        echo json_encode([
            "error" => "السيارة غير موجودة"
        ]);
    }

    exit();
}

/* =========================
   ❌ DELETE CAR
========================= */
if (isset($_POST['action']) && $_POST['action'] == 'delete') {

    $id = intval($_POST['id']);

    // جلب السيارة
    $result = $conn->query("
        SELECT * FROM cars
        WHERE id = $id
    ");

    if ($result->num_rows > 0) {

        $car = $result->fetch_assoc();

        // حذف الصورة
        if (
            !empty($car['image']) &&
            file_exists(__DIR__ . "/uploads/" . $car['image'])
        ) {

            unlink(__DIR__ . "/uploads/" . $car['image']);
        }

        // حذف السيارة
        $stmt = $conn->prepare("
            DELETE FROM cars
            WHERE id=?
        ");

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {

            echo "success";

        } else {

            echo "Delete Error";
        }

    } else {

        echo "السيارة غير موجودة";
    }

    exit();
}
/* =========================
   🛒 BUY CAR
========================= */
if (isset($_POST['action']) && $_POST['action'] == 'buy') {

    $car_id = intval($_POST['car_id']);
    $name   = trim($_POST['buyer_name']);
    $phone  = trim($_POST['phone']);

    $stmt = $conn->prepare("
        INSERT INTO purchases(car_id, buyer_name, phone)
        VALUES(?, ?, ?)
    ");

    $stmt->bind_param("iss", $car_id, $name, $phone);

    echo $stmt->execute() ? "success" : $stmt->error;

    exit();
}

?>