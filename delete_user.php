<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once 'models/UserModel.php';
$userModel = new UserModel();

$token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';

if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid CSRF token"
    ]);
    exit;
}

// Thực hiện xóa user
$userId =  $_GET['id'] ?? null;

if ($userId) {
     $userModel->deleteUserById($userId);//Delete existing user
    echo json_encode([
        "status" => "ok",
        "message" => "User deleted successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Missing user ID"
    ]);
}
?> 