<?php

require_once "controllers/CarController.php";

$controller = new CarController();

$page = $_GET['page'] ?? 'index';
$id   = $_GET['id'] ?? null;

switch ($page) {

    case 'index':
        $controller->index();
        break;

    case 'show':
        $controller->show($id);
        break;

    case 'create':
        $controller->create();
        break;

    case 'store':
        $controller->store();
        break;

    case 'edit':
        $controller->edit($id);
        break;

    case 'update':
        $controller->update($id);
        break;

    case 'delete':
        $controller->delete($id);
        break;

    default:
        echo "404 - Page Not Found";
        break;
}