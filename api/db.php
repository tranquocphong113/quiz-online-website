<?php
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$dbname = "quiz_online";
$username = "root";
$password = "";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Kết nối database thất bại",
        "error" => $e->getMessage()
    ]);
    exit;
}
?>