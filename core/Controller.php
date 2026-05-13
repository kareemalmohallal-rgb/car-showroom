<?php

require_once "models/Car.php";

class CarController {

    private $carModel;

    public function __construct() {
        $this->carModel = new Car();
    }

    // 📌 عرض كل السيارات
    public function index() {
        $cars = $this->carModel->getAllCars();
        require_once "views/cars/index.php";
    }

    // 📌 صفحة إضافة سيارة
    public function create() {
        require_once "views/cars/create.php";
    }

    // 📌 حفظ سيارة جديدة
    public function store() {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = [
                'name'        => $_POST['name'],
                'brand'       => $_POST['brand'],
                'model'       => $_POST['model'],
                'price'       => $_POST['price'],
                'description' => $_POST['description']
            ];

            // 📌 رفع الصورة
            if (!empty($_FILES['image']['name'])) {

                $imageName = time() . "_" . $_FILES['image']['name'];
                $target = "uploads/" . $imageName;

                move_uploaded_file($_FILES['image']['tmp_name'], $target);

                $data['image'] = $imageName;
            }

            $this->carModel->insertCar($data);

            header("Location: index.php?action=index");
            exit();
        }
    }

    // 📌 عرض سيارة واحدة
    public function show($id) {
        $car = $this->carModel->getCarById($id);
        require_once "views/cars/show.php";
    }

    // 📌 صفحة التعديل
    public function edit($id) {
        $car = $this->carModel->getCarById($id);
        require_once "views/cars/edit.php";
    }

    // 📌 تحديث السيارة
    public function update($id) {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = [
                'name'        => $_POST['name'],
                'brand'       => $_POST['brand'],
                'model'       => $_POST['model'],
                'price'       => $_POST['price'],
                'description' => $_POST['description']
            ];

            if (!empty($_FILES['image']['name'])) {

                $imageName = time() . "_" . $_FILES['image']['name'];
                $target = "uploads/" . $imageName;

                move_uploaded_file($_FILES['image']['tmp_name'], $target);

                $data['image'] = $imageName;
            }

            $this->carModel->updateCar($id, $data);

            header("Location: index.php?action=index");
            exit();
        }
    }

    // 📌 حذف سيارة
    public function delete($id) {
        $this->carModel->deleteCar($id);

        header("Location: index.php?action=index");
        exit();
    }
}